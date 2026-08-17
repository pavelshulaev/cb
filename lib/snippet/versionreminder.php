<?php

namespace Rover\CB\Snippet;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Rover\CB\Options;
use Rover\CB\Service\Reminder;
use Rover\CB\Service\Version;

Loc::loadMessages(__FILE__);

/**
 * Плашка со статусом версии модуля, показываемая вверху страницы настроек.
 * Лицензионного блока нет — модуль бесплатный.
 *
 * @package Rover\CB\Snippet
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class VersionReminder
{
    /**
     * @return void
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function show(): void
    {
        Reminder::processRequest();

        // fix /bitrix/themes/.default/pubstyles.css
        echo '<style>.adm-workarea .rover-cb__reminder-alert{box-sizing: border-box;}</style>';

        echo self::render();
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function render(): string
    {
        $version = self::getVersionStatus();
        if ($version && Reminder::isDismissed('version', $version['key'])) {
            $version = null;
        }

        return self::getMarkup($version);
    }

    /**
     * @return array{text: string, state: string, key: string}|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getVersionStatus(): ?array
    {
        $installedVersion = Version::getInstalledVersion();
        if (!$installedVersion) {
            return null;
        }

        $latestVersion = Version::getLatestVersion();
        $isActual      = !$latestVersion || version_compare($installedVersion, $latestVersion, '>=');

        $text = Loc::getMessage('rover-cb__reminder-version-installed', ['#version#' => $installedVersion]) . ' '
            . ($isActual
                ? Loc::getMessage('rover-cb__reminder-version-actual')
                : Loc::getMessage('rover-cb__reminder-version-outdated', [
                    '#version#' => $latestVersion,
                    '#link#'    => '/bitrix/admin/update_system_partner.php?lang=' . LANGUAGE_ID . '&addmodule=' . Options::MODULE_ID,
                ]));

        return [
            'text'  => $text,
            'state' => $isActual ? 'success' : 'warning',
            'key'   => $isActual ? 'actual' : ('outdated:' . $latestVersion),
        ];
    }

    /**
     * @param array{text: string, state: string, key: string}|null $version
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getMarkup(?array $version): string
    {
        if (!$version) {
            return '';
        }

        Extension::load(['ui.buttons', 'ui.alerts', 'ui.buttons.icons']);

        ob_start();
        ?>
        <div class="ui-alert ui-alert-<?= $version['state'] ?> rover-cb__reminder-alert">
            <div class="rover-cb__reminder">
                <div class="rover-cb__reminder-row"><span><?= $version['text'] ?></span></div>
                <?php if ($version['state'] == 'warning'): ?>
                <div class="rover-cb__reminder-row"><span><?= Loc::getMessage('rover-cb__reminder-version-motivation') ?></span></div>
                <?php endif; ?>
            </div>
            <a href="<?= Reminder::getDismissLink('version', $version['key']) ?>"
               class="ui-alert-close-btn rover-cb__reminder-close"></a>
        </div>

        <style>
            .rover-cb__reminder {
                display: block;
                width: 100%;
            }

            .rover-cb__reminder-row {
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
            }

            .rover-cb__reminder-row:last-child {
                margin-bottom: 0;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
