<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/module_admin.php');
global $APPLICATION, $cbErrors;

if (empty($cbErrors))
    \CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
else
    \CAdminMessage::ShowMessage(
        array(
            "TYPE"      => "ERROR",
            "MESSAGE"   => Loc::getMessage("MOD_INST_ERR"),
            "DETAILS"   => implode("<br/>", $cbErrors),
            "HTML"      => true
        ));

?><form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK")?>">
<form>