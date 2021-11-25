<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 12/30/2020
 * Time: 5:12 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Service;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Context;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Text\Encoding;
use Rover\CB\Options;

/**
 * Class Tools
 *
 * @package Rover\AmoCRM\Service
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Tools
{
    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getGUID()
    {
        if (function_exists('com_create_guid'))
            return com_create_guid();

        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = //chr(123)// "{"
            /*.*/substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        //.chr(125);// "}"
        return $uuid;
    }

    /**
     * @param        $input
     * @param string $separator
     * @param bool   $strToUpper
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function fromCamelCase($input, string $separator = '-', bool $strToUpper = false): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];
        foreach ($ret as &$match)
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);

        $result = implode($separator, $ret);
        if ($strToUpper)
            $result = strtoupper($result);

        return $result;
    }

    /**
     * @param        $string
     * @param string $separator
     * @param false  $capitalizeFirstCharacter
     * @return string|string[]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function toCamelCase($string, string $separator = '-', bool $capitalizeFirstCharacter = false)
    {

        $str = str_replace($separator, '', ucwords($string, $separator));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * @param $className
     * @return string
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getShortClassName($className): string
    {
        $className = trim($className);
        if (!strlen($className))
            throw new ArgumentNullException('className');

        return basename(str_replace('\\', '/', $className));
    }

    /**
     * @param $text
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function prepareText($text): string
    {
        return strip_tags(preg_replace('/<[\/]*br[^>]*>/Usi', "\r\n", trim($text)));
    }

    /**
     * @param array $array
     * @param array $filterKeys
     * @param false $fixEncoding
     * @param bool  $stringOnly
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function filterKeys(array $array, array $filterKeys = [], bool $fixEncoding = false, bool $stringOnly = true): array
    {
        $result     = [];
        $filterKeys = array_map('mb_strtoupper', $filterKeys);
        if (empty($filterKeys))
            return $array;

        foreach ($array as $key => $value)
        {
            if ($stringOnly && !is_scalar($value)) continue;

            $key = trim($key);
            if (!mb_strlen($key))
                continue;

            if (count($filterKeys) && !in_array(mb_strtoupper($key), $filterKeys))
                continue;

            if (is_string($value))
            {
                if ($fixEncoding)
                {
                    $encoding = mb_detect_encoding($value, LANG_CHARSET);
                    if ($encoding != LANG_CHARSET)
                        $value = Encoding::convertEncoding($value, $encoding, LANG_CHARSET);
                }

                $value = trim($value);
                if (!mb_strlen($value))
                    continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}