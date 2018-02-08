<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;
use \Rover\Fadmin\Layout\Admin\Form;
use \Rover\CB\Config\Options;

if (Loader::includeSharewareModule($mid) == Loader::MODULE_DEMO_EXPIRED)
	throw new SystemException('demo period for module "' . $mid . '" expired!');

if (!Loader::includeModule($mid))
	throw new SystemException('module "' . $mid . '" not found!');

if (!Loader::includeModule('rover.fadmin'))
	throw new SystemException('module rover.fadmin not found!');

Loc::LoadMessages(__FILE__);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");


$form = new Form(Options::get());
$form->show();