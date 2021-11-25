<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 11/25/2021
 * Time: 12:04 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use Rover\CB\Admin;
use Rover\CB\Options;

$MESS['rover-cb__connected']           = 'есть подключение';
$MESS['rover-cb__disconnected']        = 'нет подключения';

$MESS[Admin::TAB__MAIN . '_label'] = 'Основные настройки';
$MESS[Admin::TAB__MAIN . '_descr'] = 'Настройка модуля';

$MESS[Options::ENABLED . '_label']          = '<span id="enabled"></span>&nbsp;Интеграция включена';
$MESS[Options::ENABLED . '_hint']           = 'Включение/отключение <u>всей</u> интеграции';
$MESS[Options::LOGIN . '_label']            = '<span id="login"></span>&nbsp;Логин пользователя';
$MESS[Options::LOGIN . '_hint']             = 'Логин вашего пользователя в «Клиентской базе»';
$MESS[Options::API_KEY . '_label']          = '<span id="api_key"></span>&nbsp;Ключ для авторизации в API';
$MESS[Options::API_KEY . '_hint']           = 'Находится в настройках вашего пользователя «Клиентской базы».';
$MESS[Options::SITE . '_label']             = '<span id="site"></span>&nbsp;Имя сайта с «Клиентской базой»';
$MESS[Options::SITE . '_hint']                 = 'Укажите вместе с https://, напр. https://mysite.ru';
$MESS[Options::LOG_ENABLED . '_label']         = '<span id="log_enabled"></span>&nbsp;Логирование включено';
$MESS[Options::LOG_ENABLED . '_hint']          = 'Лог будет вестись в файлах <b>note.log</b> (текущий размер #note-file-size#) и <b>error.log</b> (текущий размер #error-file-size#) в папке /upload/rover.cb/log/';