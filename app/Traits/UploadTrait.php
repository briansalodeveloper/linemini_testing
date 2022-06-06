<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * self::getConstPath($constPath, $id, $filename, $isCustomPath)
     * get custom const designated path
     *
     * @param String $constPath - from config const.upload.custom.path value
     * @param Int/String $id - folder id
     * @param String/Null $filename - return directory with filename
     * @param Bool $isCustomPath - check to use a custom path and not concatinate the "const.upload.path."
     * @return String $rtn - const path
     */
    public static function getConstPath(string $constPath, $id = null, string $filename = null, bool $isCustomPath = false)
    {
        $rtn = '';
        
        if ($isCustomPath) {
            $path = config($constPath);
        } else {
            $path = config('const.upload.path.' . $constPath);
        }

        if (!empty($path)) {
            if (!is_array($path)) {
                $path = [$path];
            }

            $rtn = $path[0];

            if (!empty($id)) {
                $rtn .= '/' . $id;
            }

            if (count($path) > 1) {
                $rtn .= '/' . $path[1];
            }

            if (!empty($filename)) {
                $rtn .= '/' . $filename;
            }
        }

        return $rtn;
    }

    /**
     * self::getFullCustomPath($customPath, $customName)
     * return combined value of self::getCustomPath() + self::getCustomName()
     *
     * @param String $customPath
     * @param String $customName
     * @param Int/String $id
     * @return String
     */
    public static function getFullCustomPath(string $customPath, string $customName, $id)
    {
        return self::getCustomPath($customPath, $id) . '/' . self::getCustomName($customName);
    }

    /**
     * self::getCustomPath($constPath, $id, $filename)
     * get custom const designated path
     *
     * @param String $constPath - from config const.upload.custom.path value
     * @param Int/String $id - folder id
     * @param String/Null $filename - return directory with filename
     * @return String $rtn - const path
     */
    public static function getCustomPath(string $constPath, $id = null, string $filename = null): string
    {
        $constPath = 'const.upload.custom.path.' . $constPath;
        $rtn = self::getConstPath($constPath, $id, $filename, true);

        return $rtn;
    }

    /**
     * self::getCustomName($constName)
     * get custom const designated name
     *
     * @param String $constName - from config const.upload.custom.name value
     * @return String $rtn - const name
     */
    public static function getCustomName(string $constName): string
    {
        $rtn = '';
        
        $name = config('const.upload.custom.name.' . $constName);

        if (!empty($name)) {
            $rtn = $name;
        }

        return $rtn;
    }

    /**
     * self::getBasePath($urlPath)
     * get the base path of the file string path/url
     * can only be use by files with in the application or in s3 bucket
     *
     * @param String $urlPath
     * @return String basepath
     */
    public static function getBasePath(string $urlPath): string
    {
        $rtn = '';

        if (!empty($urlPath)) {
            if (self::isUrlPublic($urlPath)) {
                $rtn = explode('storage/', $urlPath)[1];
            } elseif (self::isUrlOwnedS3($urlPath)) {
                $rtn = explode('amazonaws.com/', $urlPath)[1];
            }
        }

        return $rtn;
    }

    /**
     * self::getBaseName($urlPath)
     * get the base name of the file string path/url
     * can only be use by files with in the application or in s3 bucket
     *
     * @param String $urlPath
     * @return String basepath
     */
    public static function getBaseName(string $urlPath): string
    {
        return explode('/', $urlPath)[count(explode('/', $urlPath)) - 1];
    }

    /**
     * self::isValidUrlString($url)
     * return if string given is a url or not
     *
     * @param String $url - url text
     * @return Bool true/false
     */
    public static function isValidUrlString(string $url): bool
    {
        return (strpos($url, 'http:') !== false || strpos($url, 'https:') !== false) && substr($url, 0, 4) === 'http';
    }

    /**
     * self::randomHashName($length)
     * return random hash name
     *
     * @param Int $length
     * @return String $rtn
     */
    public static function randomHashName(int $length = 40): string
    {
        $rtn = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
    
        for ($i = 0; $i < $length; $i++) {
            $rtn .= $keys[array_rand($keys)];
        }
    
        return $rtn;
    }

    /**
     * self::isUrlPattern($url, $needle)
     * check url string if consist of needle
     *
     * @param String $url
     * @param String/Array $needle
     * @return Bool true/false
     */
    public static function isUrlPattern($url, $needle)
    {
        $rtn = true;
        
        if (!is_array($needle)) {
            $needle = [$needle];
        }

        foreach ($needle as $stick) {
            $rtn = strpos($url, $stick) !== false;
            if (!$rtn) {
                break;
            }
        }

        return $rtn;
    }

    /**
     * self::isUrlPublic($url)
     * check if url is from application storage folder
     *
     * @param String $url
     * @return Bool true/false
     */
    public static function isUrlPublic(string $url): bool
    {
        return self::isUrlPattern($url, env('APP_URL'));
    }

    /**
     * self::isUrlS3($url)
     * check if url is from s3 bucket
     *
     * @param String $url
     * @return Bool true/false
     */
    public static function isUrlS3(string $url): bool
    {
        return self::isUrlPattern($url, 'amazonaws.com/');
    }

    /**
     * self::isUrlOwnedS3($url)
     * check if url is from own applicatino s3 bucket
     *
     * @param String $url
     * @return Bool true/false
     */
    public static function isUrlOwnedS3(string $url): bool
    {
        return self::isUrlS3($url) && self::isUrlPattern($url, strtolower(env('AWS_BUCKET')));
    }

    /**
     * self::exist($url)
     * check if file url exist in the application storage folder or in s3 bucket
     *
     * @param String $url
     * @return Bool true/false
     */
    public static function exist(string $url): bool
    {
        return self::existInPublic($url) || self::existInS3($url);
    }

    /**
     * self::existInPublic($urlPath)
     * check if file url/path exist in the application storage folder
     *
     * @param String $urlPath
     * @return Bool true/false
     */
    public static function existInPublic(string $urlPath): bool
    {
        $rtn = false;

        if (!empty($urlPath)) {
            if (self::isUrlPublic($urlPath)) {
                $path = self::getBasePath($urlPath);
                $rtn = Storage::disk(self::DISK_PUBLIC)->exists($path);
            } else {
                $rtn = Storage::disk(self::DISK_PUBLIC)->exists($urlPath);
            }
        }

        return $rtn;
    }

    /**
     * self::existInS3($urlPath)
     * check if file url/path exist in the s3 bucket
     *
     * @param String $urlPath
     * @return Bool true/false
     */
    public static function existInS3(string $urlPath): bool
    {
        $rtn = false;

        if (!empty($urlPath)) {
            if (self::isUrlOwnedS3($urlPath)) {
                $path = self::getBasePath($urlPath);
                $rtn = Storage::disk(self::DISK_S3)->exists($path);
            } else {
                $rtn = Storage::disk(self::DISK_S3)->exists($urlPath);
            }
        }

        return $rtn;
    }
}
