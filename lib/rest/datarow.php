<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 28.12.2017
 * Time: 20:27
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Rest;

/**
 * Class DataRow
 *
 * @package Rover\CB\Rest
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class DataRow extends Data
{
    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $row
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function create($tableId, $cals = true, array $row = array())
    {
        $data = $this->addKey($row, 'row');

        return parent::create($tableId, $cals, $data);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $row
     * @param array $filterRow
     * @param array $sort
     * @param int   $start
     * @param int   $limit
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function read($tableId, $cals = true, array $row = array(), array $filterRow = array(), array $sort = array(), $start = 0, $limit = 500)
    {
        $fields = $this->addKey($row, 'row');
        $filter = $this->addKey($filterRow, 'row');
        $sort   = $this->addKey($sort, 'row');

        return parent::read($tableId, $cals, $fields, $filter, $sort, $start, $limit);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $row
     * @param array $filterRow
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function update($tableId, $cals = true, array $row = array(), array $filterRow = array())
    {
        $data   = $this->addKey($row, 'row');
        $filter = $this->addKey($filterRow, 'row');

        return parent::update($tableId, $cals, $data, $filter);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $filterRow
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function delete($tableId, $cals = true, array $filterRow = array())
    {
        $filter = $this->addKey($filterRow, 'row');

        return parent::delete($tableId, $cals, $filter);
    }
}