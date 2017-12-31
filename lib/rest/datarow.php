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


class DataRow extends Data
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
        $fields = $this->addKey($fields, 'row');

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
    public function read($tableId, $cals = false, array $fields = array(), array $filter = array(), array $sort = array(), $start = 0, $limit = 500)
    {
        $fields = $this->addKey($fields, 'row');
        $filter = $this->addKey($filter, 'row');
        $sort   = $this->addKey($sort, 'row');

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
        $fields = $this->addKey($fields, 'row');
        $filter = $this->addKey($filter, 'row');

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
        $filter = $this->addKey($filter, 'row');

        return parent::delete($tableId, $cals, $filter);
    }
}