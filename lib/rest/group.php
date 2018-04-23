<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 30.12.2017
 * Time: 23:05
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Rest;

use Rover\CB\Rest;
use Rover\CB\Helper\Filter;
/**
 * Class User
 *
 * @package Rover\CB\Rest
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Group extends Rest
{
    const URL__LIST = '/api/group/get_list/';

    /**
     * @param array $filter
     * @return array|mixed
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getList(array $filter = array())
    {
        $groups = $this->requestPost(self::URL__LIST);

        return Filter::run($groups, $filter);
    }
}