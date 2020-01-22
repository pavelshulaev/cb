<?php
use \Rover\CB\Config\Tabs;
use \Rover\CB\Config\Options;

$MESS[Tabs::TAB__MAIN . '_label'] = 'Основные настройки';
$MESS[Tabs::TAB__MAIN . '_descr'] = 'Настройка модуля';

$MESS[Options::INPUT__ENABLED . '_label']   = 'Интеграция включена';
$MESS[Options::INPUT__ENABLED . '_help']    = 'Включение/отключение <u>всей</u> интеграции';
$MESS[Options::INPUT__LOGIN . '_label']     = "Логин пользователя";
$MESS[Options::INPUT__LOGIN . '_help']      = 'Логин вашего пользователя в «Клиентской базе»';
$MESS[Options::INPUT__API_KEY . '_label']   = "Ключ для авторизации в API";
$MESS[Options::INPUT__API_KEY . '_help']    = 'Находится в настройках модуля API вашей «Клиентской базы» напротив имени выбранного пользователя. Если выбранного пользователя нет в списке, то вы там же можетеего добавить';
$MESS[Options::INPUT__SITE_NAME . '_label'] = "Путь к коробочной «Клиентской базе»";
$MESS[Options::INPUT__SITE_NAME . '_help']  = 'Укажите вместе с <code>https://</code>, напр. <code>https://mysite.ru</code> или <code>https://mysite2.ru/cb</code>';
$MESS[Options::INPUT__LOG_ENABLED . '_label']  = 'Логирование включено';
$MESS[Options::INPUT__LOG_ENABLED . '_help']   = 'Лог будет вестись в файлах <b>note.log</b> (текущий размер #note-file-size#) и <b>error.log</b> (текущий размер #error-file-size#) в папке /upload/rover.cb/log/';
$MESS[Options::INPUT__LOG_ENABLED . '_disabled_help']   = 'Папка /upload/rover.cb/log/ недоступна для записи';
