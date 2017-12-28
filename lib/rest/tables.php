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

        $result = array();
        foreach ($tables['data'] as $tableId => $tableData)
        {
            $tableData['id'] = $tableId;

            foreach ($filter as $filterField => $filterValue)
            {
                if (!isset($tableData[$filterField]))
                    continue(2);

                if ($tableData[$filterField] != $filterValue)
                    continue(2);
            }

            $result[$tableId] = $tableData;
        }

        $tables['data'] = $result;
        $tables['count']= count($result);

        return $tables;
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