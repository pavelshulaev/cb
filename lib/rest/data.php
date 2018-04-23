<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 28.12.2017
 * Time: 18:46
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Rest;

use Bitrix\Main\ArgumentNullException;
use Rover\CB\Rest;

/**
 * Class Row
 *
 * @package Rover\CB\Rest
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Data extends Rest
{
    const URL__CREATE   = '/api/data/create/';
    const URL__READ     = '/api/data/read/';
    const URL__UPDATE   = '/api/data/update/';
    const URL__DELETE   = '/api/data/delete/';
    const URL__FILES    = '/api/data/files/';
    /**
     * @param       $tableId
     * @param array $data
     * @param bool  $cals
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function create($tableId, $cals = true, array $data = array())
    {
        $tableId = intval($tableId);
        if (!$tableId)
            throw new ArgumentNullException('tableId');

        $data = array(
            'table_id'  => $tableId,
            'cals'      => $cals,
            'data'      => $data
        );

        return $this->requestPost(self::URL__CREATE, $data);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $fields
     * @param array $filter
     * @param array $sort
     * @param int   $start
     * @param int   $limit
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function read($tableId, $cals = true, array $fields = array(), array $filter = array(), array $sort = array(), $start = 0, $limit = 500)
    {
        $tableId = intval($tableId);
        if (!$tableId)
            throw new ArgumentNullException('tableId');

        $data = array(
            'table_id'  => $tableId,
            'cals'      => $cals,
            'fields'    => $fields,
            'filter'    => $filter,
            'sort'      => $sort,
            'start'     => $start,
            'limit'     => $limit
        );

        return $this->requestPost(self::URL__READ, $data);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $data
     * @param array $filter
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function update($tableId, $cals = true, array $data = array(), array $filter = array())
    {
        $tableId = intval($tableId);
        if (!$tableId)
            throw new ArgumentNullException('tableId');

        $data = array(
            'table_id'  => $tableId,
            'cals'      => $cals,
            'data'      => $data,
            'filter'    => $filter
        );

        return $this->requestPost(self::URL__UPDATE, $data);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $filter
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function delete($tableId, $cals = true, array $filter = array())
    {
        $tableId = intval($tableId);
        if (!$tableId)
            throw new ArgumentNullException('tableId');

        $data = array(
            'table_id'  => $tableId,
            'cals'      => $cals,
            'filter'    => $filter
        );

        return $this->requestPost(self::URL__DELETE, $data);
    }

    /**
     * @param $fields
     * @param $key
     * @return array
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function addKey($fields, $key)
    {
        $key = trim($key);
        if (!strlen($key))
            throw new ArgumentNullException('key');

        return array($key => $fields);
    }

    /**
     * @param $tableId
     * @param $fileFieldId
     * @param $lineId
     * @param $fileName
     * @return mixed
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getFiles($tableId, $fileFieldId, $lineId, $fileName)
    {
        $tableId = intval($tableId);
        if (!$tableId)
            throw new ArgumentNullException('tableId');

        $data = array(
            'table_id'      => $tableId,
            'file_field_id' => $fileFieldId,
            'line_id'       => $lineId,
            'file_name'     => $fileName,
        );

        return $this->requestPost(self::URL__FILES, $data);
    }
}