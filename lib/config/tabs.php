<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 18.02.2016
 * Time: 15:48
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Config;

use Bitrix\Main\ArgumentNullException;
use \Bitrix\Main\Localization\Loc;
use Rover\CB\Helper\Log;
use Rover\Fadmin\Helper\InputFactory;

Loc::loadMessages(__FILE__);

/**
 * Class Tabs
 *
 * @package Rover\CB\Config
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Tabs
{
	const TAB__MAIN = 'settings';

    /**
     * @param Options $options
     * @return array
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function get(Options $options)
	{
		return $options->getDependenceStatus()
            ? array(
                self::getMainTab(),
            ) : array();
	}

    /**
     * @return array
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getMainTab()
	{
		return array(
			'name'          => self::TAB__MAIN,
			'label'         => Loc::getMessage(self::TAB__MAIN . '_label'),
			'description'   => Loc::getMessage(self::TAB__MAIN . '_descr'),
			'inputs'        => self::getMainTabInputs()
        );
	}

    /**
     * @return array
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getMainTabInputs()
	{
		$connectionInputs = array(
            InputFactory::getCheckbox(Options::INPUT__ENABLED, 'Y'),
            InputFactory::getText(Options::INPUT__SITE_NAME),
            InputFactory::getText(Options::INPUT__LOGIN),
            InputFactory::getPassword(Options::INPUT__API_KEY),
        );

		$logEnabled         = InputFactory::getCheckbox(Options::INPUT__LOG_ENABLED, 'N');
		$logEnabled['help'] = str_replace(array(
		    '#note-file-size#',
		    '#error-file-size#',
        ), array (
            Log::getFileSize(Log::getPath(Log::FILE__NOTE)),
            Log::getFileSize(Log::getPath(Log::FILE__ERROR))
        ), $logEnabled['help']);

        $connectionInputs[] = $logEnabled;

		return $connectionInputs;
	}
}