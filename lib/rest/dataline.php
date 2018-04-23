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
     * @param array $line
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function create($tableId, $cals = true, array $line = array())
    {
        $data = $this->addKey($line, 'line');

        return parent::create($tableId, $cals, $data);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $line
     * @param array $filterLine
     * @param array $sort
     * @param int   $start
     * @param int   $limit
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function read($tableId, $cals = true, array $line = array(), array $filterLine = array(), array $sort = array(), $start = 0, $limit = 500)
    {
        $fields = $this->addKey($line, 'line');
        $filter = $this->addKey($filterLine, 'line');
        $sort   = $this->addKey($sort, 'line');

        return parent::read($tableId, $cals, $fields, $filter, $sort, $start, $limit);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $line
     * @param array $filterLine
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function update($tableId, $cals = true, array $line = array(), array $filterLine = array())
    {
        $data   = $this->addKey($line, 'line');
        $filter = $this->addKey($filterLine, 'line');

        return parent::update($tableId, $cals, $data, $filter);
    }

    /**
     * @param       $tableId
     * @param bool  $cals
     * @param array $filterLine
     * @return mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function delete($tableId, $cals = true, array $filterLine = array())
    {
        $filter = $this->addKey($filterLine, 'line');

        return parent::delete($tableId, $cals, $filter);
    }
}