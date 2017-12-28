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
 * Class DataLine
 *
 * @package Rover\CB\Rest
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class DataLine extends Data
{
    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $fields
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function create($tableId, $cals = false, array $fields = array())
    {
        $fields = $this->addKey($fields, 'line');

        return parent::create($tableId, $cals, $fields);
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
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function read($tableId, $cals = false, array $fields = array(), array $filter = array(), array $sort = array(), $start = 0, $limit = 0)
    {
        $fields = $this->addKey($fields, 'line');
        $filter = $this->addKey($filter, 'line');
        $sort   = $this->addKey($sort, 'line');

        return parent::read($tableId, $cals, $fields, $filter, $sort, $start, $limit);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $fields
     * @param array $filter
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function update($tableId, $cals = false, array $fields = array(), array $filter = array())
    {
        $fields = $this->addKey($fields, 'line');
        $filter = $this->addKey($filter, 'line');

        return parent::update($tableId, $cals, $fields, $filter);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $filter
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function delete($tableId, $cals = false, array $filter = array())
    {
        $filter = $this->addKey($filter, 'line');

        return parent::delete($tableId, $cals, $filter);
    }
}