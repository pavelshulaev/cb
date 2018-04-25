<?php
use \Rover\CB\Config\Dependence;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.05.2017
 * Time: 16:36
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
$MESS['rover-cb__php_version_error']	    = "Версия php ниже #min_php_version#";
$MESS['rover-cb__no_curl_error']	        = "Не найдена библиотека CURL";
$MESS['rover-cb__no_mod_rewrite_error']	    = "Не найден модуль mod_rewrite";
$MESS['rover-cb__no_intl_error']	        = "Не найдено расширение php-intl";
$MESS['rover-cb__rover-fadmin_not_found']	= 'Не найден модуль «<a href="http://marketplace.1c-bitrix.ru/solutions/rover.fadmin/">Конструктор административной части</a>» (rover.fadmin)';

$MESS['rover-cb__main-version-error']      = 'Требуется модуль «Главный модуль» (main) версии ' . Dependence::MIN_VERSION__MAIN . ' или старше. Обновите его в <a href="/bitrix/admin/update_system.php">разделе обновления платформы</a>.';
$MESS['rover-cb__fadmin-version-error']    = 'Требуется модуль «<a href="http://marketplace.1c-bitrix.ru/solutions/rover.fadmin/">Конструктор административной части</a>» (rover.fadmin) версии ' . Dependence::MIN_VERSION__FADMIN . ' или старше. Обновите его в <a href="/bitrix/admin/update_system_partner.php">разделе обновления решений</a>.';

$MESS['rover-cb__is_trial']       = 'Решение работает в демо-режиме.<br>Вы можете преобрести «AmoCRM - интеграция с веб-формами и почтовыми событиями» на <a href="http://marketplace.1c-bitrix.ru/solutions/rover.cb/">Битрикс Маркетплейс</a>.';
$MESS['rover-cb__trial_expired']  = 'Демо-период истёк.<br>Вы можете преобрести «AmoCRM - интеграция с веб-формами и почтовыми событиями» на <a href="http://marketplace.1c-bitrix.ru/solutions/rover.cb/">Битрикс Маркетплейс</a>.';
$MESS['rover-cb__writable-error'] = 'Файл "#path#" недоступен для записи';
$MESS['rover-cb__mkdir-error']    = 'Не удалось создать директрию "#dir#"';
