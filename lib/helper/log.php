<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 02.06.2017
 * Time: 12:25
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\CB\Helper;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Rover\CB\Options;
use Rover\CB\Service\Dependence;

/**
 * Class Log
 *
 * @package Rover\AmoCRM\Helper
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Log
{

    const FILE__NOTE  = 'note.log';
    const FILE__ERROR = 'error.log';

    /**
     * @param      $message
     * @param null $data
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function addNote($message, $data = null)
    {
        self::add(self::FILE__NOTE, $message, $data);
    }

    /**
     * @param      $message
     * @param null $data
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function addError($message, $data = null)
    {
        self::add(self::FILE__ERROR, $message, $data);
    }

    /**
     * @param      $file
     * @param      $message
     * @param null $data
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function add($file, $message, $data = null)
    {
        if (!Options::isLogEnabled()) {
            return;
        }

        $fullPath = self::getPath($file);
        if (!$fullPath) {
            return;
        }

        self::checkMaxSize($fullPath);

        if (is_array($message)) {
            $message = print_r($message, true);
        }

        if (!empty($data)) {
            if (is_array($data)) {
                $data = print_r($data, true);
            }

            $message .= "\n" . $data;
        }

        file_put_contents($fullPath, date('d.m.Y H:i:s') . ' ' . $message . "\n", FILE_APPEND);
    }

    /**
     * @param $path
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function checkMaxSize($path): void
    {
        $logMaxSize = 1024 * 1024;
        if (!$logMaxSize) {
            return;
        }

        if (filesize($path) > $logMaxSize) {
            file_put_contents($path, '');
        }
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getDir(): string
    {
        return Application::getDocumentRoot() . '/upload/' . Options::MODULE_ID . '/log/';
    }

    /**
     * @param string $fileName
     * @return bool|mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getPath(string $fileName): ?string
    {
        $fileName = trim($fileName);
        if (!$fileName) {
            return null;
        }

        $dir          = self::getDir();
        $dependence   = new Dependence();
        $createResult = $dependence->checkDir($dir)->getResult();
        if (!$createResult) {
            return null;
        }

        return str_replace(['///', '//'], '/', $dir . '/' . $fileName);
    }

    /**
     * @param string $file
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getFileSize(string $file): string
    {
        $path = self::getPath($file);
        if (!$path || !is_file($path)) {
            return 'n/a';
        }

        return self::getStrFileSize(filesize($path));
    }

    /**
     * @param     $size
     * @param int $round
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getStrFileSize($size, int $round = 2)
    {
        $sizes = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
        for ($i = 0; $size > 1024 && $i < count($sizes) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $round) . " " . $sizes[$i];
    }
}