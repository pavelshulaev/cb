<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 23.04.2018
 * Time: 17:17
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Helper;

/**
 * Class Filter
 *
 * @package Rover\CB\Helper
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Filter
{
    /**
     * @param array $data
     * @param array $filter
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function run(array $data, array $filter = array())
    {
        if (empty($filter))
            return $data;

        $result = array();

        foreach ($data['data'] as $id => $row)
        {
            $row['id'] = $id;

            foreach ($filter as $filterField => $filterValue)
            {
                if (!isset($row[$filterField]))
                    continue(2);

                if ($row[$filterField] != $filterValue)
                    continue(2);
            }

            unset($row['id']);

            $result[$id] = $row;
        }

        $data['data'] = $result;
        $data['count']= count($result);

        return $data;
    }
}