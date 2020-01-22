# Модуль для Битрикс «Клиентская база api»
Модуль предоставляет простые классы и методы для взаимодействия с [Клиентской базой](https://clientbase.ru) через её апи.

Модуль доступен на [Маркетплейсе Битрикса](https://marketplace.1c-bitrix.ru/solutions/rover.cb/).

## Настройка модуля
После установки на сайт, настройка производится в административной части: «Настройки» — «Настройки модулей» — «Клиентская база api».

На странице настроек необходимо ввести:

* имя сайта, на котором установлена Клиентская база (с http/https). Если КБ установлена на в корневую папку, то имя сайта следует ввести вместе с той папкой, в которую она устновлена;
* логин пользователя Клиентской базы, от лица и с правами которого будет осуществляться подключение;
* api-ключ этого пользвателя.

> Для пользователя, под которым вы ходите подключиться, в настройках КБ должна стоять галочка "<b>Включить API</b>".

Если все данные верны, то после сохранения настроек появится надпись `[есть подключение]`. После этого можно обращаться к Клиентской базе с помощью классов и методов модуля.

## Использование модуля
Модуль реализует все возможности, доступные в апи Клиентской базы на апрель 2018 года. 

Взаимодействие с Клиентской базой возможно как с помощью классов сущностей, так и с помощью основного класса `\Rover\CB\Rest`. Классы сущностей являются обёрткой над `\Rover\CB\Rest` и реализуют более удобные методы для доступа к конкретным сущностям КБ.

Классы сущностей расположены в пространстве имён `\Rover\CB\Rest\`. Их методы практически идентичны аналогичным методам апи КБ.

## Пример
Получение списка пользователей с помощью класса `Rover\CB\Rest\User`:

    use Bitrix\Main\Loader;
    use Rover\CB\Rest\User;

    if (Loader::includeModule('rover.cb')) {
    
        try{
    
            $users = User::getInstance()->getList();
    
            echo '<pre>';
            print_r($users);
            echo '</pre>';
    
        } catch (\Exception $e){
            ShowError($e->getMessage());
        }
    
    } else {
        ShowError("Не удалось подключить модуль «Клиентская база api»");
    }
    
Результат будет примерно таким:

    Array
    (
        [data] => Array
            (
                [1] => Array
                    (
                        [id] => 1
                        [fio] => ...
                        [group_id] => 1
                        [arc] => 0
                    )
    
                [10] => Array
                    (
                        [id] => 10
                        [fio] => ...
                        [group_id] => 1
                        [arc] => 0
                    )
    
                ...
                
            )
    
        [count] => 5
        [code] => 0
        [message] => Done
    )
    
Вместо `Rover\CB\Rest\User::getInstance()->getList()` можно использовать `\Rover\CB\Rest::getInstance()->requestPost('/api/user/get_list/')`. Вообще, с помощью класса `\Rover\CB\Rest` можно обратиться ко всем методам апи Клисентской базы, в т.ч. и к тем, которые могут быть еще не реализованы в классах сущностей.

## Доступные классы

<b>`\Rover\CB\Rest`</b> — Основной универсальный класс.

* `static getInstance()` — авторизуется в КБ и получает экземпляр класса `\Rover\CB\Rest`;
* `requestPost($url, array $data = array())` — post-запрос к КБ
* `requestGet($url, array $data = array())` — get-запрос к КБ
* `request($method, $url, array $data = array())` — запрос к КБ. Метод определяется переменной `$method`

### Классы сущностей
Классы сущностей реализуют все методы `\Rover\CB\Rest`, за тем исключением, что `static getInstance()` возвращает экземпляр вызвавшего её класса.

Методы классов сущностей принимают те же параметры и в том же порядке, что и соответствующин методы апи КБ, кроме параметра `access_id`: он не нужен. Структура возвращаемых данных идентична структуре в КБ. Более детальную информацию можно прочесть в [справке по апи КБ](https://clientbase.ru/help/for_admin_16/api/).

<b>`\Rover\CB\Rest\Data`</b> — Работа с данными в таблицах.

* `create($tableId, $cals = true, array $data = array())` — [/api/data/create](https://clientbase.ru/help/for_admin_16/api/data/create/);
* `read($tableId, $cals = true, array $fields = array(), array $filter = array(), array $sort = array(), $start = 0, $limit = 500)` — [/api/data/read](https://clientbase.ru/help/for_admin_16/api/data/read/);
* `update($tableId, $cals = true, array $data = array(), array $filter = array())` — [/api/data/update](https://clientbase.ru/help/for_admin_16/api/data/update/);
* `delete($tableId, $cals = true, array $filter = array())` — [/api/data/delete](https://clientbase.ru/help/for_admin_16/api/data/delete/);
* `files($tableId, $fileFieldId, $lineId, $fileName)` — [/api/data/files](https://clientbase.ru/help/for_admin_16/api/rabota_s_fajlami/poluchenie_fajlov/).


<b>`\Rover\CB\Rest\DataLine`</b>, <b>`\Rover\CB\Rest\DataRow`</b> - вспомогательные классы для `\Rover\CB\Rest\Data`. Реализуют те же методы за тем отличием, что в массивах данных, фильтра и сортировки не надо указывать ключи `line` и `row` соотвественно.


<b>`\Rover\CB\Rest\Group`</b> — Работа с группами.

* `getList(array $filter = array())` * — [/api/group/get_list](https://clientbase.ru/help/for_admin_16/api/group/get_list/).


<b>`\Rover\CB\Rest\Table`</b> — Работа с таблицами.

* `getList(array $filter = array())` * — [/api/table/get_list](https://clientbase.ru/help/for_admin_16/api/table/get_list/);
* `getPerms($id)` — [/api/table/get_perms](https://clientbase.ru/help/for_admin_16/api/table/get_perms/);
* `getInfo($id)` — [/api/table/info](https://clientbase.ru/help/for_admin_16/api/table/info/).


<b>`\Rover\CB\Rest\User`</b> — Работа с пользователями.

* `getList(array $filter = array())` * — [/api/user/get_list](https://clientbase.ru/help/for_admin_16/api/user/get_list/)

\* в массиве `$filter` можно передать данные для фильтрации результата в формате `array('filterKey' => 'filterValue')`. Например: `array('id' => 22)`, тогда в результате останется только запись с id=22. Фильтр работает только по точному соответствию. Других операций не предусмотренно. 

## Требования	
* php 5.3 или выше;
* модуль php CURL;
* [«Конструктор административной части» (rover.fadmin)](https://github.com/pavelshulaev/fadmin) 1.6.9 или выше.

## Контакты
По всем вопросам вы можете связаться со мной по email: rover.webdev@gmail.com, либо через форму на сайте https://rover-it.me.