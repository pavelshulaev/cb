<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 28.12.2017
 * Time: 17:55
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
namespace Rover\CB;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
/**
 * Class Rest
 *
 * @package Rover\CB
 * @author  Pavel Shulaev (https://rover-it.me)
 */
abstract class Rest
{
    const URL__REQUEST = '/api/auth/request/';
    const URL__AUTH    = '/api/auth/auth/';

    /** @var string */
    protected $siteName;

    /** @var */
    protected $login;

    /** @var string */
    protected $apiKey;

    /** @var bool */
    protected static $accessId;

    /** @var array */
    protected static $instances = [];

    /**
     * Rest constructor.
     *
     * @param $siteName
     * @param $login
     * @param $apiKey
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    private function __construct($siteName, $login, $apiKey)
    {
        $siteName = trim($siteName);

        if (!strlen($siteName))
            throw new ArgumentNullException('siteName');

        if (!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $siteName))
            throw new ArgumentOutOfRangeException('siteName');

        $login = trim($login);
        if (!strlen($login))
            throw new ArgumentNullException('login');

        $apiKey = trim($apiKey);
        if (!strlen($apiKey))
            throw new ArgumentNullException('apiKey');

        $this->siteName = $siteName;
        $this->login    = $login;
        $this->apiKey   = $apiKey;

        $this->auth();
    }

    private function __clone() {}
    private function __wakeup() {}

    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isAuth(): bool
    {
        return strlen(self::$accessId) > 0;
    }

    /**
     * @throws ArgumentNullException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function auth()
    {
        if (!self::isAuth())
            self::$accessId = $this->getAccessId($this->getSalt());
    }

    /**
     * @return mixed
     * @throws ArgumentNullException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getSalt()
    {
        $params = array(
            'v'     => '1.0',
            'login' => $this->login
        );

        $result = $this->requestPost(self::URL__REQUEST, $params);

        if (array_key_exists('salt', $result))
            return $result['salt'];

        throw new ArgumentNullException('salt');
    }

    /**
     * @param $salt
     * @return mixed
     * @throws ArgumentNullException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getAccessId($salt)
    {
        $salt = trim($salt);
        if (!$salt)
            throw new ArgumentNullException('salt');

        $params = array(
            'v'     => '1.0',
            'login' => $this->login,
            'hash'  => md5($salt . $this->apiKey)
        );

        $result = $this->requestPost(self::URL__AUTH, $params);

        if (array_key_exists('access_id', $result))
            return $result['access_id'];

        throw new ArgumentNullException('access_id');
    }

    /**
     * @param bool $reload
     * @return static
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getInstance(bool $reload = false): Rest
    {
        return self::build(get_called_class(), $reload);
    }

    /**
     * @param      $className
     * @param bool $reload
     * @return mixed
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function build($className, bool $reload = false)
    {
        if (!strlen($className) || !(class_exists($className)))
            throw new ArgumentOutOfRangeException('className');

        if (!isset(self::$instances[$className]) || $reload)
        {
            $restObject = new $className(Options::getSite(), Options::getLogin(), Options::getApiKey());

            if (!$restObject instanceof Rest)
                throw new ArgumentOutOfRangeException('instance');

            self::$instances[$className] = $restObject;
        }

        return self::$instances[$className];
    }

    /**
     * @param       $url
     * @param array $data
     * @return mixed
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestPost($url, array $data = array())
    {
        return $this->request($url, $data);
    }

    /**
     * @param       $url
     * @param array $data
     * @return mixed
     * @throws SystemException
     * @throws ArgumentException
     * @author Pavel Shulaev (https://rover-it.me)3
     */
    private function request($url, array $data = array())
    {
        // add access_id
        if (($url != self::URL__REQUEST)
            && ($url != self::URL__AUTH))
        {
            if (!$this->isAuth())
                throw new SystemException('Request not authorized');

            $data['access_id'] = self::$accessId;
        }

        $requestUrl = $this->siteName . $url;

        $clientOptions = [
            'socketTimeout' => 10,
            'streamTimeout' => 20,
            'compress'      => true,
            'disableSslVerification' => true
        ];

        $client = new HttpClient($clientOptions);
        $client->setHeader('User-Agent', "Rover-CB-API-client/1.1");
        $body = Json::encode($data);

        $client->query(HttpClient::HTTP_POST, $requestUrl, $body);
        $response   = Json::decode($client->getResult());
        $status     = $client->getStatus();

        if (in_array($status, [200, 201, 204]))
            return $response;

        $errorMessage = $response['ERROR'] ?? ($response['message'] ?? 'error');

        $errorCode = $response['code'] ?? 'n/a';

        $errorMessage .= ' (' . $errorCode . ')';

        throw new SystemException($errorMessage, $status);
    }

    /**
     * @param array $data
     * @param array $filter
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function filter(array $data, array $filter): array
    {
        $result = array();
        foreach ($data['data'] as $tableId => $tableData)
        {
            $tableData['id'] = $tableId;

            foreach ($filter as $filterField => $filterValue)
            {
                if (!isset($tableData[$filterField]))
                    continue(2);

                if ($tableData[$filterField] != $filterValue)
                    continue(2);
            }

            unset($tableData['id']);

            $result[$tableId] = $tableData;
        }

        $data['data'] = $result;
        $data['count']= count($result);

        return $data;
    }
}