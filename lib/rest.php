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

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Rover\CB\Config\Dependence;
use Rover\CB\Config\Options;
use Rover\CB\Helper\Log;

/**
 * Class Rest
 *
 * @package Rover\CB
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Rest
{
    const URL__REQUEST = '/api/auth/request/';
    const URL__AUTH    = '/api/auth/auth/';

    const TYPE__POST    = 'post';
    const TYPE__GET     = 'get';

    /**
     * @var string
     */
    protected $siteName;

    /**
     * @var
     */
    protected $login;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var bool
     */
    protected static $accessId;

    /**
     * @var array
     */
    protected static $instances = array();

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
    public static function isAuth()
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
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getInstance($reload = false)
    {
        return self::build(get_called_class(), $reload);
    }

    /**
     * @param      $className
     * @param bool $reload
     * @return mixed
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function build($className, $reload = false)
    {
        if (!strlen($className) || !class_exists($className))
            throw new ArgumentOutOfRangeException('className');

        if (!isset(self::$instances[$className]) || $reload){

            $options    = Options::get();
            $restObject = new $className($options->getSiteName(),
                $options->getLogin(), $options->getApiKey());

            if (!$restObject instanceof Rest)
                throw new ArgumentOutOfRangeException('instance');

            self::$instances[$className] = $restObject;
        }

        return self::$instances[$className];
    }

    /**
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function clearCookie()
    {
        file_put_contents(self::getCookiePath(), '');
    }

    /**
     * @param       $url
     * @param array $data
     * @return mixed
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function requestPost($url, array $data = array())
    {
        return $this->request(self::TYPE__POST, $url, $data);
    }

    /**
     * @param       $url
     * @param array $data
     * @return mixed
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function requestGet($url, array $data = array())
    {
        return $this->request(self::TYPE__GET, $url, $data);
    }

    /**
     * @param       $type
     * @param       $url
     * @param array $data
     * @return mixed
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)3
     */
    public function request($type, $url, array $data = array())
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

        if ($type == self::TYPE__GET)
            $requestUrl .= '?' . http_build_query($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // curl_setopt($ch, CURLOPT_USERAGENT, "AmoCRM-API-client/1.0");
        curl_setopt($ch, CURLOPT_URL, $requestUrl);

        if ($type == self::TYPE__POST) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Json::encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, self::getCookiePath());
        curl_setopt($ch, CURLOPT_COOKIEJAR, self::getCookiePath());

        // for ssl
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);

        Log::addNote("REQUEST\ntype: {$type}\nurl: {$url}\ndata: " . print_r($data, 1) . "\n");

        $out    = curl_exec($ch);
        $code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $response = strlen($out)
            ? Json::decode($out)
            : array();

        Log::addNote("RESPONSE\n" . print_r($response, 1) . "\n");

        if ((($code == 200) || ($code == 201) || ($code == 204))
            && array_key_exists('code', $response) && $response['code'] == 0)
            return $response;

        // clear cookie
        self::clearCookie();

        $errorMessage = isset($response['ERROR'])
            ? $response['ERROR']
            : (isset($response['message'])
                ? $response['message']
                : 'error');

        $errorCode = isset($response['code'])
            ? $response['code']
            : 'n/a';

        $errorMessage .= ' (' . $errorCode . ')';

        Log::addError("REQUEST ERROR\n" . $errorMessage . "\n");

        throw new SystemException($errorMessage, $code);
    }

    /**
     * @return string
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCookiePath()
    {
        $dir = Application::getDocumentRoot() . '/upload/rover.cb/';

        $dependence = new Dependence();
        if (!$dependence->checkDir($dir)->getResult())
            throw new SystemException(implode('<br>', $dependence->getErrors()));

        return $dir . 'cookie.txt';
    }
}