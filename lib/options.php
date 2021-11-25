<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 11/25/2021
 * Time: 12:07 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\NotImplementedException;
use Rover\CB\Service\Tools;

/**
 * Class Options
 *
 * @package Rover\CB
 * @author  Pavel Shulaev (https://rover-it.me)
 *
 * @method static getSite
 * @method static getLogin
 * @method static getApiKey
 * @method static isEnabled
 * @method static isLogEnabled
 */
class Options
{
    public const MODULE_ID      = 'rover.cb';
    public const SITE           = 'site';
    public const LOG_ENABLED    = 'log-enabled';
    public const API_KEY        = 'api-key';
    public const LOGIN          = 'login';
    public const ENABLED        = 'enabled';

    /** @var string */
    public static $curSiteId;

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCurSiteId(): string
    {
        if (empty(self::$curSiteId)) {
            require_once(Application::getDocumentRoot() . "/bitrix/modules/main/include/mainpage.php");
            self::$curSiteId = \CMainPage::GetSiteByHost();
        }

        return self::$curSiteId;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws NotImplementedException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function __call($name, $arguments)
    {
        return self::__callStatic($name, $arguments);
    }

    /**
     * @param $methodName
     * @param $arguments
     * @return bool|string|void
     * @throws NotImplementedException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function __callStatic($methodName, $arguments)
    {
        if ((mb_strpos($methodName, 'get') === 0) || (mb_strpos($methodName, 'set') === 0))
        {
            $optionName = mb_substr($methodName, 3);
            $optionName = Tools::fromCamelCase($optionName);

            if (mb_strpos($methodName, 'get') === 0)
                return Option::get(self::MODULE_ID, $optionName, $arguments[0], $arguments[1]);

            Option::set(self::MODULE_ID, $optionName, $arguments[0], $arguments[1]);
            return;
        }

        if (mb_strpos($methodName, 'is') === 0)
        {
            $optionName = substr($methodName, 2);
            $optionName = Tools::fromCamelCase($optionName);

            return Option::get(self::MODULE_ID, $optionName, null, $arguments[0] ?? '') == 'Y';
        }

        throw new NotImplementedException($methodName);
    }
}