<?php

namespace Rover\CB\Service;

use Bitrix\Main\Application;

/**
 * Скрытые пользователем напоминания на странице настроек модуля.
 *
 * Значение хранится в опциях пользователя, поэтому скрытие индивидуально и не
 * влияет на других администраторов.
 *
 * @package Rover\CB\Service
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Reminder
{
    const OPTION_CATEGORY = 'rover.cb.reminder';
    const REQUEST_PARAM   = 'rover_cb_dismiss_reminder';
    const DISMISS_DAYS    = 7;

    /**
     * @param string   $type имя напоминания; оно же имя опции пользователя
     * @param string   $key  состояние, при котором скрытие действует
     * @param int|null $days срок скрытия; null — навсегда
     * @return void
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function dismiss(string $type, string $key, int $days = null): void
    {
        $value = ['key' => $key];

        if ($days) {
            $value['until'] = time() + $days * 86400;
        }

        \CUserOptions::SetOption(self::OPTION_CATEGORY, $type, $value);
    }

    /**
     * Скрытие действует, пока не изменилось состояние, при котором его закрыли,
     * и пока не вышел срок. Без срока — бессрочно.
     *
     * @param string $type
     * @param string $key
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isDismissed(string $type, string $key): bool
    {
        $option = \CUserOptions::GetOption(self::OPTION_CATEGORY, $type, null);

        if (!is_array($option) || (($option['key'] ?? null) !== $key)) {
            return false;
        }

        return !isset($option['until']) || (time() < $option['until']);
    }

    /**
     * Обрабатывает клик по крестику напоминания: пришедший в query-строке
     * запрос вида "version:actual" разбирается на тип/ключ, скрытие сохраняется
     * на сервере, после чего — редирект на ту же страницу без этого параметра
     * (чтобы обновление страницы не гасило напоминание повторно).
     *
     * @return void
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function processRequest(): void
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $raw     = trim((string)$request->get(self::REQUEST_PARAM));

        if ($raw === '') {
            return;
        }

        [$type, $key] = array_pad(explode(':', $raw, 2), 2, '');

        if (!in_array($type, ['version'], true) || $key === '') {
            return;
        }

        self::dismiss($type, $key, self::DISMISS_DAYS);

        global $APPLICATION;
        LocalRedirect($APPLICATION->GetCurPageParam('', [self::REQUEST_PARAM]));
    }

    /**
     * @param string $type
     * @param string $key
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getDismissLink(string $type, string $key): string
    {
        global $APPLICATION;

        $param = self::REQUEST_PARAM . '=' . urlencode($type . ':' . $key);

        return htmlspecialcharsbx($APPLICATION->GetCurPageParam($param));
    }
}
