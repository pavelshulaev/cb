<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Rover\CB\Config\Dependence;
use \Bitrix\Main\SystemException;

Loc::LoadMessages(__FILE__);
/**
 * Class rover_amocrm
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
class rover_cb extends CModule
{
	var $MODULE_ID = "rover.cb";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;


    /**
     * rover_amocrm constructor.
     */
	function __construct()
	{
		global $cbErrors;

		$arModuleVersion    = array();
		$cbErrors           = array();

		require(dirname(__FILE__) . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
		} else
			$cbErrors[] = Loc::getMessage('rover_acrm__version_info_error');

		$this->MODULE_NAME          = Loc::getMessage("rover-cb__MODULE_NAME");
		$this->MODULE_DESCRIPTION   = Loc::getMessage("rover-cb__MODULE_DESC");
		$this->PARTNER_NAME         = GetMessage("rover-cb__PARTNER_NAME");
		$this->PARTNER_URI          = GetMessage("rover-cb__PARTNER_URI");
	}

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function installEvents()
	{
		/*$eventManager = EventManager::getInstance();

        $this->unInstallEvents();

		$eventManager->registerEventHandler("form", "onAfterResultAdd", $this->MODULE_ID, "\\RoverAmoCRMEvents", "onAfterResultAddCRM", 10000);
		$eventManager->registerEventHandler("main", "OnBeforeEventAdd", $this->MODULE_ID, "\\RoverAmoCRMEvents", "onBeforeEventAdd", 10000);
	*/}

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function unInstallEvents()
	{
		/*$eventManager = EventManager::getInstance();

		$eventManager->unRegisterEventHandler("form", "onAfterResultAdd", $this->MODULE_ID, "\\RoverAmoCRMEvents", "onAfterResultAddCRM");
		$eventManager->unRegisterEventHandler("main", "OnBeforeEventAdd", $this->MODULE_ID, "\\RoverAmoCRMEvents", "onBeforeEventAdd");
	*/}

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function DoInstall()
	{
		global $cbErrors;

		require_once dirname(__FILE__) . '/../lib/config/dependence.php';

		if (class_exists('\Rover\CB\Config\Dependence')){
            $dependence = new Dependence();
            $depErrors  = $dependence->checkBase()->getErrors();

            $cbErrors = array_merge($cbErrors, $depErrors);
        } else
		    $cbErrors[] = Loc::getMessage('rover_acrm__dependence_error');

        $this->InstallDB();

        global $cbErrors;

		if (empty($cbErrors)) {
			ModuleManager::registerModule($this->MODULE_ID);
			$this->installEvents();
		}

        global $APPLICATION, $cbErrors;

		if (!empty($cbErrors))
            ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover-cb__install_title"),
    		dirname(__FILE__) . '/message.php');
	}

    /**
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function DoUninstall()
	{
        global $APPLICATION, $step;
        $step = intval($step);

        $this->UnInstallDB();
        $this->unInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover-cb__uninstall_title"),
            dirname(__FILE__) . '/unMessage.php');
	}

    /**
     * @return bool|void
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function InstallDB()
    {
	    //$this->runQueryFromFile(dirname(__FILE__) . '/install.sql');
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
	function UnInstallDB()
    {
        //$this->runQueryFromFile(dirname(__FILE__) . '/uninst.sql');
    }
}