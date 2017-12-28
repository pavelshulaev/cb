<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 10.02.2016
 * Time: 0:45
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Config;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\SystemException;
use Rover\CB\Helper\Log;
use Rover\CB\Model\Rest;
use Rover\CB\Model\Rest\Contact;
use Rover\CB\Rest\Row;
use Rover\Fadmin\Options\Settings;
use \Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Rover\Fadmin\Tab;
use \Rover\Fadmin\Options as FadminOptions;

if (!Loader::includeModule("rover.fadmin")
	|| !Loader::includeModule('rover.params'))
	throw new SystemException('rover.fadmin or rover.params modules not found');

Loc::loadMessages(__FILE__);
/**
 * Class Options
 *
 * @package Rover\CB\Config
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Options extends FadminOptions
{
	const MODULE_ID = 'rover.cb';

    const INPUT__ENABLED    = 'enabled';
    const INPUT__SITE_NAME  = 'site';
    const INPUT__LOGIN      = 'login';
    const INPUT__API_KEY    = 'api-key';
    const INPUT__LOG_ENABLED= 'log-enabled';

    /**
     * @var bool|mixed
     */
	protected $dependenceStatus = false;

    /**
     * @var string
     */
    protected static $curSiteId;

    /**
     * connected flag
     * @var bool
     */
    protected $connected;

    /**
     * Options constructor.
     *
     * @param $moduleId
     * @throws SystemException
     */
	protected function __construct($moduleId)
	{
		$dependence = new Dependence();

		if (!$dependence->checkCritical()->getResult())
		    throw new SystemException(implode('<br>', $dependence->getResult()));

		$this->dependenceStatus = $dependence
            ->checkBase()
            ->checkCookieDir()
            ->checkTrialExpired()
            ->getResult();

		parent::__construct($moduleId);

		if (!$this->dependenceStatus){
            $this->message->addError(implode('<br>',
                $dependence->getErrors()), true);

            Log::addError('dependence error(s)', $dependence->getErrors());
        }

		// check elapsed demo-days
		if (!$dependence->checkTrialElapsedDays()->getResult())
            $this->message->addOk($dependence->getErrors(), true);
	}

	protected function __clone() {}
	protected function __wakeup() {}

    /**
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getConfig()
	{
		return array(
            'tabs'      => Tabs::get($this),
            'settings'  => array(
                Settings::BOOL_CHECKBOX => true,
            )
        );
	}

	/**
	 * @return bool
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function getDependenceStatus()
	{
		return $this->dependenceStatus;
	}

    /**
     * @param $moduleId
     * @return void|static
     * @throws NotImplementedException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getInstance($moduleId)
    {
        throw new NotImplementedException();
    }

    /**
     * @return self|\Rover\Fadmin\Options
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function get()
	{
		return parent::getInstance(self::MODULE_ID);
	}

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getCurSiteId()
    {
        if (empty(self::$curSiteId)) {
            require_once(Application::getDocumentRoot() . "/bitrix/modules/main/include/mainpage.php");
            self::$curSiteId = \CMainPage::GetSiteByHost();
        }

        return self::$curSiteId;
    }

    /**
     * @return bool
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function beforeAddValuesFromRequest()
    {
        Rest::clearCookie();

        return true;
    }

    /**
     * @param array $params
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function beforeGetTabInfo(array &$params)
    {
        /**
         * @var Tab $tab
         */
        $params['label'] = htmlspecialchars_decode($params['label']);

        try {
            $params['label'] .= $this->isConnected()
                ? ' [' . Loc::getMessage('rover-cb__connected') . ']'
                : ' [' . Loc::getMessage('rover-cb__disconnected') . ']';
        } catch (\Exception $e) {
            $this->message->addError($e->getMessage());
        }
    }

    /**
     * @param bool $reload
     * @return bool
     * @throws \Bitrix\Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function isConnected($reload = false)
    {
        if (is_null($this->connected) || $reload)
            try{
                $this->connected = Row::getInstance()->isAuth();
            } catch (\Exception $e) {
                $this->connected = false;
                $this->message->addError($e->getMessage());
                Log::addError('connection error:', $e->getMessage());
            }

        return $this->connected;
    }

    /**
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function isEnabled($reload = false)
    {
        return $this->getNormalValue(self::INPUT__ENABLED, '', $reload);
    }

    /**
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getLogin($reload = false)
    {
        return $this->getNormalValue(self::INPUT__LOGIN, '', $reload);
    }

    /**
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getApiKey($reload = false)
    {
        return $this->getNormalValue(self::INPUT__API_KEY, '', $reload);
    }

    /**
     * @param bool $reload
     * @return mixed|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getSiteName($reload = false)
    {
        return $this->getNormalValue(self::INPUT__SITE_NAME, '', $reload);
    }
}