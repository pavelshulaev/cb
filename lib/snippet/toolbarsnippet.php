<?php

namespace Rover\CB\Snippet;

use Bitrix\Main\Security\Random;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;

/**
 * Панель кнопок над содержимым страницы.
 *
 * Заменяет собой компонент bitrix:main.interface.toolbar со старым табличным
 * видом. Ключи кнопок по возможности сохранены прежними, чтобы вызывающий код
 * менялся минимально: TEXT, TITLE, LINK, LINK_PARAM, HTML и MENU те же.
 *
 * Кнопки раскладываются по блокам явно:
 *
 *     ToolbarSnippet::show(['LEFT' => [$button], 'RIGHT' => [$button]]);
 *
 * Кнопка:
 *
 *     'TEXT'       — подпись; разметка внутри допускается и не экранируется
 *     'TITLE'      — всплывающая подсказка
 *     'LINK'       — адрес; без него получится <button>, а не <a>
 *     'TARGET'     — цель ссылки
 *     'ONCLICK'    — обработчик
 *     'CLASS'      — модификаторы оформления: ui-btn-primary, ui-btn-icon-list
 *                    и прочие; базовый ui-btn добавляется сам
 *     'ID'         — идентификатор элемента
 *     'DISABLED'   — кнопка недоступна
 *     'LINK_PARAM' — произвольные атрибуты строкой, как в старом тулбаре
 *     'MENU'       — выпадающий список: [['TEXT', 'LINK', 'ONCLICK', 'TITLE'], ...];
 *                    TEXT пункта меню — тоже разметка и не экранируется (BX.Main.MenuItem
 *                    требует 'html' для HTML-содержимого, иначе экранирует его как текст)
 *     'HTML'       — готовая разметка вместо кнопки
 *     'CALLBACK'   — код, который сам печатает вывод; нужен для компонентов
 *                    вроде main.ui.filter, они возвращать строку не умеют
 *
 * @package Rover\CB\Snippet
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class ToolbarSnippet
{
    /** @var bool стили нужны один раз на страницу */
    protected static bool $styleShown = false;

    /**
     * @param array $config
     * @return void
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function show(array $config): void
    {
        echo self::render($config);
    }

    /**
     * @param array $config
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function render(array $config): string
    {
        $blocks = self::getBlocks($config);

        if (!$blocks['LEFT'] && !$blocks['RIGHT']) {
            return '';
        }

        Extension::load(['ui.buttons', 'ui.buttons.icons']);

        $menus = [];

        $left  = self::renderItems($blocks['LEFT'], $menus);
        $right = self::renderItems($blocks['RIGHT'], $menus);

        return self::renderStyle()
            . '<div class="adm-toolbar-panel-container">'
            . '<div class="adm-toolbar-panel-flexible-space">' . $left . '</div>'
            . '<div class="adm-toolbar-panel-align-right">' . $right . '</div>'
            . '</div>'
            . self::renderMenusScript($menus);
    }

    /**
     * На узком экране кнопки переносятся на несколько строк и слипаются:
     * у ядра между строками отступа нет.
     *
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function renderStyle(): string
    {
        if (self::$styleShown) {
            return '';
        }

        self::$styleShown = true;

        return '<style>.adm-toolbar-panel-container { row-gap: 12px; }</style>';
    }

    /**
     * @param array $config
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getBlocks(array $config): array
    {
        return [
            'LEFT'  => is_array($config['LEFT'] ?? null) ? $config['LEFT'] : [],
            'RIGHT' => is_array($config['RIGHT'] ?? null) ? $config['RIGHT'] : [],
        ];
    }

    /**
     * @param array $items
     * @param array $menus выпадающие списки, накапливаются для общего скрипта
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function renderItems(array $items, array &$menus): string
    {
        $result = '';

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            if (isset($item['CALLBACK']) && is_callable($item['CALLBACK'])) {
                ob_start();
                call_user_func($item['CALLBACK']);
                $result .= ob_get_clean();

                continue;
            }

            if (isset($item['HTML'])) {
                $result .= $item['HTML'];

                continue;
            }

            if (!empty($item['MENU']) && is_array($item['MENU'])) {
                $result .= self::renderMenuButton($item, $menus);

                continue;
            }

            $result .= self::renderButton($item);
        }

        return $result;
    }

    /**
     * @param array $item
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function renderButton(array $item): string
    {
        $isLink     = isset($item['LINK']) && mb_strlen((string)$item['LINK']);
        $attributes = self::getAttributes($item);

        if ($isLink) {
            $attributes['href'] = $item['LINK'];

            if (!empty($item['TARGET'])) {
                $attributes['target'] = $item['TARGET'];
            }
        }

        $tag = $isLink ? 'a' : 'button';

        if (!$isLink) {
            $attributes['type'] = 'button';

            if (!empty($item['DISABLED'])) {
                $attributes['disabled'] = 'disabled';
            }
        }

        return '<' . $tag . self::buildAttributes($attributes, $item)
            . '>' . ($item['TEXT'] ?? '') . '</' . $tag . '>';
    }

    /**
     * Выпадающая кнопка собирается штатным BX.UI.Button: своё меню повторяло бы
     * его поведение и рано или поздно разошлось бы с оформлением ядра.
     *
     * @param array $item
     * @param array $menus
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function renderMenuButton(array $item, array &$menus): string
    {
        $id = !empty($item['ID'])
            ? (string)$item['ID']
            : 'rover-cb__toolbar-menu-' . mb_strtolower(Random::getString(8));

        $menuItems = [];
        foreach ($item['MENU'] as $menuItem) {
            if (!is_array($menuItem)) {
                continue;
            }

            // 'text' экранируется ядром (BX.Main.MenuItem предупреждает об этом
            // в консоли); разметка в TEXT пункта меню требует именно 'html'
            $entry = ['html' => (string)($menuItem['TEXT'] ?? '')];

            if (!empty($menuItem['LINK'])) {
                $entry['href'] = (string)$menuItem['LINK'];
            }

            if (!empty($menuItem['ONCLICK'])) {
                $entry['onclick'] = (string)$menuItem['ONCLICK'];
            }

            if (!empty($menuItem['TITLE'])) {
                $entry['title'] = (string)$menuItem['TITLE'];
            }

            $menuItems[] = $entry;
        }

        $menus[] = [
            'id'   => $id,
            'text' => (string)($item['TEXT'] ?? ''),
            // базовый ui-btn BX.UI.Button ставит сам, здесь только модификаторы
            'className' => trim((string)($item['CLASS'] ?? '')),
            'items'     => $menuItems,
        ];

        return '<span id="' . htmlspecialcharsbx($id) . '"'
            . ' style="margin-left: var(--ui-btn-margin-left); margin-right: var(--ui-btn-margin-left);"></span>';
    }

    /**
     * @param array $menus
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function renderMenusScript(array $menus): string
    {
        if (!$menus) {
            return '';
        }

        $script = '<script>BX.ready(function () {';

        foreach ($menus as $menu) {
            $onclick = [];
            foreach ($menu['items'] as $index => $menuItem) {
                if (isset($menuItem['onclick'])) {
                    $onclick[$index] = $menuItem['onclick'];
                    unset($menu['items'][$index]['onclick']);
                }
            }

            // каждая кнопка в своей области видимости: на панели их может быть
            // несколько, и переменные не должны пересекаться
            $script .= '(function () {'
                . 'var container = document.getElementById(' . Json::encode($menu['id']) . ');'
                . 'if (!container) { return; }'
                . 'var items = ' . Json::encode(array_values($menu['items'])) . ';';

            // обработчики нельзя протащить через json, поэтому вешаем их после
            foreach ($onclick as $index => $handler) {
                $script .= 'items[' . (int)$index . '].onclick = function () {' . $handler . '};';
            }

            $script .= 'new BX.UI.Button({'
                . 'text: ' . Json::encode($menu['text']) . ','
                . 'className: ' . Json::encode($menu['className']) . ','
                . 'dropdown: true,'
                . 'menu: {items: items, closeByEsc: true, offsetTop: 5}'
                . '}).renderTo(container);'
                . '})();';
        }

        $script .= '});</script>';

        return $script;
    }

    /**
     * @param array $item
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getAttributes(array $item): array
    {
        $class = 'ui-btn';

        if (!empty($item['CLASS'])) {
            $class .= ' ' . $item['CLASS'];
        }

        if (!empty($item['DISABLED'])) {
            $class .= ' ui-btn-disabled';
        }

        $attributes = ['class' => $class];

        if (!empty($item['ID'])) {
            $attributes['id'] = $item['ID'];
        }

        if (!empty($item['TITLE'])) {
            $attributes['title'] = $item['TITLE'];
        }

        if (!empty($item['ONCLICK'])) {
            $attributes['onclick'] = $item['ONCLICK'];
        }

        return $attributes;
    }

    /**
     * Адрес и произвольные атрибуты не экранируются: в старом тулбаре они
     * выводились как есть, и в модуле на это рассчитывают.
     *
     * @param array $attributes
     * @param array $item
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function buildAttributes(array $attributes, array $item): string
    {
        $result = '';

        foreach ($attributes as $name => $value) {
            $result .= ' ' . $name . '="'
                . ($name == 'href'
                    ? (preg_match('/^(https?:\/\/|\/|#)/', (string)$value) ? $value : '#')
                    : htmlspecialcharsbx((string)$value)) . '"';
        }

        if (!empty($item['LINK_PARAM'])) {
            $result .= ' ' . $item['LINK_PARAM'];
        }

        return $result;
    }
}
