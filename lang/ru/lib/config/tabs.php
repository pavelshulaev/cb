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
$MESS[Options::INPUT__API_KEY . '_help']    = 'Находится в настройках вашего пользователя «Клиентской базы».';
$MESS[Options::INPUT__SITE_NAME . '_label'] = "Имя сайта с «Клиентской базой»";
$MESS[Options::INPUT__SITE_NAME . '_help']  = 'Укажите вместе с https://, напр. https://mysite.ru';
$MESS[Options::INPUT__LOG_ENABLED . '_label']  = 'Логирование включено';
$MESS[Options::INPUT__LOG_ENABLED . '_help']   = 'Лог будет вестись в файлах <b>note.log</b> (текущий размер #note-file-size#) и <b>error.log</b> (текущий размер #error-file-size#) в папке /upload/rover.amocrm/log/';
$MESS[Options::INPUT__LOG_ENABLED . '_disabled_help']   = 'Папка /upload/rover.amocrm/log/ недоступна для записи';
