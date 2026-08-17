<?php

namespace Rover\CB\Service;

use Bitrix\Main\Application;
use Rover\CB\Helper\Log;
use Rover\CB\Options;

/**
 * Версия модуля на Маркетплейсе — для плашки "доступно обновление" на странице
 * настроек. Никакой лицензионной логики: модуль бесплатный, только сравнение
 * версий.
 *
 * @package Rover\CB\Service
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Version
{
    /**
     * Ответ маркетплейса не документирует названия атрибутов, поэтому здесь
     * возвращается узел "MODULE" как есть.
     *
     * @param bool $reload
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getModuleInfo(bool $reload = false): array
    {
        $cacheId = crc32(__METHOD__);
        $cache   = Application::getInstance()->getManagedCache();

        if (!$cache->read(86400, $cacheId) || $reload) {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_client_partner.php');

            $moduleInfo = [];

            try {
                $stableVersionsOnly = \COption::GetOptionString('main', 'stable_versions_only', 'Y');
                $arUpdateList       = \CUpdateClientPartner::GetUpdatesList(
                    $errorMessage,
                    LANG,
                    $stableVersionsOnly,
                    [Options::MODULE_ID],
                    ['fullmoduleinfo' => 'Y']
                );

                if (isset($arUpdateList['MODULE'])) {
                    foreach ($arUpdateList['MODULE'] as $module) {
                        if (($module['@']['ID'] ?? null) == Options::MODULE_ID) {
                            $moduleInfo = $module;
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::addError('Version::getModuleInfo', $e->getMessage());
                $moduleInfo = [];
            }

            $cache->set($cacheId, $moduleInfo);
        }

        return $cache->get($cacheId) ?: [];
    }

    /**
     * @param bool $reload
     * @return string|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getLatestVersion(bool $reload = false): ?string
    {
        return self::getModuleInfo($reload)['#']['VERSION'][0]['@']['ID'] ?? null;
    }

    /**
     * @return string|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getInstalledVersion(): ?string
    {
        return Dependence::getVersion(Options::MODULE_ID) ?: null;
    }
}
