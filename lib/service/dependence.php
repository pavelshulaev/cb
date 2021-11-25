<?php
namespace Rover\CB\Service;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 16.12.2016
 * Time: 18:30
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
use Bitrix\Main\ArgumentNullException;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;
use Rover\CB\Helper\Log;
use Rover\CB\Rest\Tables;

Loc::loadMessages(__FILE__);
/**
 * Class Dependence
 *
 * @package Rover\CB\Config
 * @author  Pavel Shulaev (https://rover-it.me)
 */

class Dependence
{
	const MIN_VERSION__MAIN     = '15.5.4';
    const MIN_VERSION__PHP      = 50306;
    /**
     * connected flag
     *
     * @var bool
     */
    public static $connected;

	/** @var array */
	protected $errors = array();

    /**
     * @param bool $reload
     * @return bool
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isConnected(bool $reload = false): bool
    {
        if (is_null(self::$connected) || $reload)
            try {
                self::$connected = Tables::getInstance()->isAuth();
            } catch (\Exception $e) {
                self::$connected = false;
                Log::addError('connection error:', $e->getMessage());
            }

        return self::$connected;
    }

    /**
	 * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function getResult(): bool
    {
		return empty($this->getErrors());
	}

	/**
	 * @param $error
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected function addError($error)
	{
        $this->errors[] = trim($error);
	}


	/**
	 * @return $this
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function checkPhpVer(): Dependence
    {
        if (PHP_VERSION_ID < self::MIN_VERSION__PHP)
            $this->addError(Loc::getMessage('rover-cb__php_version_error', array(
                '#min_php_version#' => self::MIN_VERSION__PHP
            )));

		return $this;
	}

    /**
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function checkModRewrite(): Dependence
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $contents = ob_get_clean();

        if (strpos($contents, 'mod_rewrite') === false)
            $this->addError(Loc::getMessage('rover-cb__no_mod_rewrite_error'));

        return $this;
    }

	/**
	 * @return $this
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function checkMainVer(): Dependence
    {
		if (!CheckVersion(self::getVersion('main'), self::MIN_VERSION__MAIN))
			$this->addError(Loc::getMessage('rover-cb__main-version-error'));

		return $this;
	}

    /**
     * @param $dir
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function checkExists($dir): Dependence
    {
        if (!file_exists($dir) && !mkdir($dir))
            $this->addError(Loc::getMessage('rover-cb__mkdir-error', array('#dir#' => $dir)));

        return $this;
    }

    /**
     * @param $path
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function checkWritable($path): Dependence
    {
        if (!is_writable($path))
            $this->addError(Loc::getMessage('rover-cb__writable-error', array('#path#' => $path)));

        return $this;
    }

    /**
     * @param $dir
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function checkDir($dir): Dependence
    {
        return $this->checkExists($dir)
            ->checkWritable($dir);
    }

	/**
	 * @return $this
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function checkBase(): Dependence
    {
		$this->reset();

		return $this
			->checkPhpVer()
			->checkMainVer()
			->checkModRewrite();
	}

    /**
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function checkCritical(): Dependence
    {
        $this->reset();

        return $this
            ->checkPhpVer()
            ->checkMainVer();
    }

	/**
	 * @param $moduleName
	 * @return bool|string
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getVersion($moduleName)
	{
		$moduleName = preg_replace("/[^a-zA-Z0-9_.]+/i", "", trim($moduleName));
		if ($moduleName == '')
			return false;

		if (!ModuleManager::isModuleInstalled($moduleName))
			return false;

		if ($moduleName == 'main')
		{
			if (!defined("SM_VERSION"))
				include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/version.php");

			return SM_VERSION;
		}

		$modulePath = getLocalPath("modules/".$moduleName."/install/version.php");
		if ($modulePath === false)
			return false;

		$arModuleVersion = array();
		include($_SERVER["DOCUMENT_ROOT"] . $modulePath);

		return array_key_exists("VERSION", $arModuleVersion)
			? $arModuleVersion["VERSION"]
			: false;
	}

    /**
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function reset(): Dependence
    {
		$this->errors = array();

		return $this;
	}

	/**
	 * @return array
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public function getErrors(): array
    {
		return $this->errors;
	}
}