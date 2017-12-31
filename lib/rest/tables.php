<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 28.12.2017
 * Time: 19:55
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Rest;

use Bitrix\Main\ArgumentNullException;
use Rover\CB\Rest;

/**
 * Class Table
 *
 * @package Rover\CB\Rest
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Tables extends Rest
{
    const URL__LIST     = '/api/table/get_list/';
    const URL__PERMS    = '/api/table/get_perms/';
    const URL__INFO     = '/api/table/info/';

    /**
     * @param array $filter
     * @return mixed
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getList(array $filter = array())
    {
        $tables = $this->requestPost(self::URL__LIST);
        if (empty($filter))
            return $tables;

        return $this->filter($tables, $filter);
    }

    /**
     * @param $id
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getPerms($id)
    {
        $id = intval($id);
        if (!$id)
            throw new ArgumentNullException('id');

        $data = array(
            'id' => $id
        );

        return $this->requestPost(self::URL__PERMS, $data);
    }

    /**
     * @param $id
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getInfo($id)
    {
        $id = intval($id);
        if (!$id)
            throw new ArgumentNullException('id');

        $data = array(
            'id' => $id
        );

        return $this->requestPost(self::URL__INFO, $data);
    }
}