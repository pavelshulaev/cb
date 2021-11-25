<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 11/25/2021
 * Time: 12:02 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Localization\Loc;
use Rover\CB\Service\Dependence;

/**
 * Class Admin
 *
 * @package Rover\CB
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Admin
{
    public const TAB__MAIN = 'settings';

    /**
     * @return array[]
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getMap(): array
    {
        return [
            [
                'TAB'   => Loc::getMessage(self::TAB__MAIN . '_label')
                    . Dependence::isConnected()
                        ? ' [' . Loc::getMessage('rover-cb__connected') . ']'
                        : ' [' . Loc::getMessage('rover-cb__disconnected') . ']',
                'DIV'   => self::TAB__MAIN,
                'TITLE' => Loc::getMessage(self::TAB__MAIN . '_descr')
            ],
        ];
    }

    /**
     * @return array[]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getAllOptions(): array
    {
        return [
            self::TAB__MAIN => self::getMainTab(),
        ];
    }

    /**
     * @return array[]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getMainTab(): array
    {
        return [
            //[Options::ENABLED, Loc::getMessage(Options::ENABLED . '_label'), '', ["checkbox"]],
            [Options::SITE, Loc::getMessage(Options::SITE . '_label'), '', ['text', 40]],
            [Options::LOGIN, Loc::getMessage(Options::LOGIN . '_label'), '', ['text', 40]],
            [Options::API_KEY, Loc::getMessage(Options::API_KEY . '_label'), '', ['text', 40]],
            [Options::LOG_ENABLED, Loc::getMessage(Options::LOG_ENABLED . '_label'), '', ["checkbox"]],
        ];
    }

    /**
     * @param      $help
     * @param bool $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCheckboxHelp($help, bool $br = false): string
    {
        $help = trim($help);
        if (!strlen($help))
            return '';

        return '>' . ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span';
    }

    /**
     * @param       $help
     * @param false $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getHelp($help, bool $br = false): string
    {
        $help = trim($help);
        if (!strlen($help))
            return '';

        return ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span>';
    }
}