<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Rover\CB\Admin;
use Rover\CB\Options;
use Rover\CB\Rest;

Loc::LoadMessages(__FILE__);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");

if (!Loader::includeModule($mid))
{
    ShowError('Module ' . $mid . ' no found');
    return;
}

$arAllOptions = Admin::getAllOptions();

$aTabs = Admin::getMap();

if((isset($request['save']) || isset($request['apply']) ) && check_bitrix_sessid())
{
    Rest::clearCookie();

    foreach ($aTabs as $tab)
        __AdmSettingsSaveOptions($mid, $arAllOptions[$tab['DIV']]);
}


// update values
$arAllOptions = Admin::getAllOptions();
?>
<style>
    .adm-detail-content-cell-l{
        vertical-align: top;
    }
</style>
<form method="post" name="rover_cb_options" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
    $tabControl = new CAdminTabControl("rover_cb_Settings_tab", $aTabs);
    $tabControl->Begin();

    echo bitrix_sessid_post();

    foreach ($aTabs as $tab)
    {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($mid, $arAllOptions[$tab['DIV']]);
    }

    $tabControl->Buttons(array());
    $tabControl->End();
    ?></form>
<script>
    BX.hint_replace(BX('enabled'), '<?=CUtil::JSEscape(Loc::getMessage(Options::ENABLED . '_hint')); ?>');
    BX.hint_replace(BX('login'), '<?=CUtil::JSEscape(Loc::getMessage(Options::LOGIN . '_hint')); ?>');
    BX.hint_replace(BX('api_key'), '<?=CUtil::JSEscape(Loc::getMessage(Options::API_KEY . '_hint')); ?>');
    BX.hint_replace(BX('site'), '<?=CUtil::JSEscape(Loc::getMessage(Options::SITE . '_hint')); ?>');
    BX.hint_replace(BX('log_enabled'), '<?=CUtil::JSEscape(Loc::getMessage(Options::LOG_ENABLED . '_hint')); ?>');
</script>
