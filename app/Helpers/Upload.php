<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Traits\UploadTrait;

class Upload
{
    use UploadTrait;

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const DISK_LOCAL = 'local';
    const DISK_PUBLIC = 'public';
    const DISK_S3 = 's3';

    const RETURN_TYPE_PATHURL = 'url';
    const RETURN_TYPE_PATHSERVER = 'server';
    const RETURN_TYPE_NAME = 'name';

    const TEMP_DIR_DATE_FORMAT = 'Y-m-d';

    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * Upload::save($file, $path, $filename, $extension, $disk, $rtnType)
     * Upload file
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $name = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::save($file, $path, $name);
     *
     * @param Illuminate\Http\File $file
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function save($file, string $path = null, string $filename = null, string $extension = null, string $disk = null, string $rtnType = null)
    {
        $rtn = false;

        if (!empty($file)) {
            try {
                self::tmpResourcesDump();

                if (is_null($path)) {
                    $path = static::getConstPath('default');
                }

                if (is_null($extension)) {
                    if ($file instanceof File) {
                        $extension = $file->extension();
                    } else {
                        $extension = $file->getClientOriginalExtension();
                    }
                }

                if (is_null($disk)) {
                    $disk = config('const.upload.disk.default');
                }

                if (is_null($rtnType)) {
                    $rtnType = self::RETURN_TYPE_PATHURL;
                }

                if ($extension == 'csv' && empty($filename)) {
                    $filename = static::randomHashName() . '.' . $extension;
                }

                $saved = false;

                if (empty($filename)) {
                    $saved = Storage::disk($disk)->put($path, $file);
                } else {
                    $filename = str_replace(' ', '_', Upload::getBaseName($filename));
                    $saved = Storage::disk($disk)->putFileAs($path, $file, $filename, 'public');
                }

                if ($saved) {
                    $rtn = Storage::disk($disk)->url($saved);
                    $rtn = urldecode($rtn);

                    if ($rtnType == self::RETURN_TYPE_PATHSERVER) {
                        $rtn = Storage::disk($disk)->path($saved);
                    } elseif ($rtnType == self::RETURN_TYPE_NAME) {
                        $rtn = Upload::getBaseName($saved);
                    }
                }
            } catch (\Aws\S3\Exception\S3Exception $e) {
                \L0g::error('S3Exception: ' . $e->getMessage());
                \SlackLog::error('S3Exception: ' . $e->getMessage());
            } catch (\Exception $e) {
                \L0g::error('Exception: ' . $e->getMessage());
                \SlackLog::error('Exception: ' . $e->getMessage());
            } catch (\Error $e) {
                \L0g::error($e->getMessage());
                \SlackLog::error($e->getMessage());
            }
        }

        return $rtn;
    }

    /**
     * Upload::saveFromUrl($url, $path, $filename, $extension, $disk, $rtnType)
     * save file from url to specific application disk (local, public, s3)
     *
     * @param String $url
     * @param String $path
     * @param String $filename
     * @param String $extension
     * @param String $disk
     * @param String $rtnType
     * @return Bool/String $rtn - false/url path/path
     */
    public static function saveFromUrl(string $url, string $path = null, string $filename = null, string $extension = null, string $disk = null, string $rtnType = null)
    {
        $rtn = false;

        if (!static::isValidUrlString($url)) {
            \Log::error('Invalid url from string "' . $url . '"');
        } else {
            if (Upload::isUrlOwnedS3($url)) {
                $curPath = explode('amazonaws.com/', $url)[1];
                $file = Storage::disk(self::DISK_S3)->path($curPath);

                try {
                    if (Storage::disk('s3')->has($file)) {
                        $contents = file_get_contents($url);
                    }

                    $file = '/tmp/' . Upload::getBaseName($url);
                    file_put_contents($file, $contents);
                    $file = new UploadedFile($file, Upload::getBaseName($url));
                } catch (\Exception $e) {
                    \L0g::error('Exception: ' . $e->getMessage());
                    \SlackLog::error('Exception: ' . $e->getMessage());
                } catch (\Error $e) {
                    \L0g::error($e->getMessage());
                    \SlackLog::error($e->getMessage());
                }

                $rtn = self::saveS3($file, $path, $filename, $extension, $rtnType);
            } elseif (strpos($url, 'storage/') !== false) {
                $curPath = explode('storage/', $url)[1];
                $file = Storage::disk(self::DISK_PUBLIC)->path($curPath);

                if (file_exists($file)) {
                    $file = new File($file);
                    $rtn = self::save($file, $path, $filename, $extension, $disk, $rtnType);
                }
            } else {
                // TODO: for external url saving (other than application files url & s3 bucket url)
            }
        }

        return $rtn;
    }

    /**
     * Upload::saveS3($file, $path, $filename, $extension, $rtnType)
     * Call the Upload::save() method with given disk "s3"
     * Upload file to aws s3 bucket
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $name = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::saveS3($file, $path, $name);
     *
     * @param Illuminate\Http\File $file
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function saveS3($file, string $path = null, string $filename = null, string $extension = null, string $rtnType = null)
    {
        return self::save($file, $path, $filename, $extension, self::DISK_S3, $rtnType);
    }

    /**
     * Upload::saveTemp($file, $path, $filename, $extension, $disk, $rtnType)
     * Call the Upload::save() method with random name saving
     * Upload file to temporary folder
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $saved = Upload::saveTemp($file, $path);
     *
     * @param Illuminate\Http\File $file
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function saveTemp($file, string $path = null, string $filename = null, string $extension = null, string $disk = null, string $rtnType = null)
    {
        $rtn = false;

        if (is_null($path)) {
            $path = static::getConstPath('tmp.default', Carbon::now()->format(self::TEMP_DIR_DATE_FORMAT) . '/' . Carbon::now()->timestamp);
        }

        if (is_null($disk)) {
            $disk = config('const.upload.disk.tmp.default');
        }

        $saved = self::save($file, $path, $filename, $extension, $disk, $rtnType);

        if (!empty($saved)) {
            $rtn = $saved;
        }

        return $rtn;
    }

    /**
     * Upload::saveImage($image, $path, $filename, $extension, $disk, $rtnType)
     * Call the Upload::save() method
     * Upload image
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $name = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::saveImage($file, $path, $name);
     *
     * @param Illuminate\Http\File $image
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function saveImage($image, string $path = null, string $filename = null, string $extension = null, string $disk = null, string $rtnType = null)
    {
        $rtn = false;
        $imageFile = null;

        try {
            if (is_null($path)) {
                $path = static::getConstPath('image');
            }

            if (is_null($extension)) {
                $extension = config('const.upload.extension.image');
            }

            if (is_null($disk)) {
                $disk = config('const.upload.disk.image');
            }

            $dirSave = Storage::disk($disk)->path($path);
            $image = \Image::make($image)->encode($extension);
            $imageFile = new File($image->basePath());
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error('Error: ' . $e->getMessage());
            \SlackLog::error('Error: ' . $e->getMessage());
        }

        if (!empty($imageFile)) {
            $rtn = self::save($imageFile, $path, $filename, $extension, $disk, $rtnType);
        }

        return $rtn;
    }

    /**
     * Upload::saveImageS3($image, $path, $filename, $extension, $rtnType)
     * Call the Upload::saveImage() method with given disk "s3"
     * Upload image to aws s3 bucket
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $name = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::saveImageS3($file, $path, $name);
     *
     * @param Illuminate\Http\File $image
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function saveImageS3($image, string $path = null, string $filename = null, string $extension = null, string $rtnType = null)
    {
        return self::saveImage($image, $path, $filename, $extension, self::DISK_S3, $rtnType);
    }

    /**
     * Upload::saveImageTemp($image, $path, $filename, $extension, $disk, $rtnType)
     * Call the Upload::save() method with random name saving
     * Upload image to temporary folder
     * sample method call:
     *      $file = $request->image;
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $name = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::saveImageTemp($file, $path, $name);
     *
     * @param Illuminate\Http\File $image
     * @param String $path - destination path to be save
     * @param String $filename - file name to be save
     * @param String $extension - file extenstion to be save
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be save
     * @param App\Helpers\Upload::RETURN_TYPE_/String $rtnType - return type either URL/Server Path/File name : default is URL
     * @return Bool/String $rtn - return false or path/url
     */
    public static function saveImageTemp($image, string $path = null, string $filename = null, string $extension = null, string $disk = null, string $rtnType = null)
    {
        $rtn = false;

        if (is_null($path)) {
            $path = static::getConstPath('tmp.image', Carbon::now()->format(self::TEMP_DIR_DATE_FORMAT) . '/' . Carbon::now()->timestamp);
        }

        if (is_null($disk)) {
            $disk = config('const.upload.disk.tmp.image');
        }

        $saved = self::saveImage($image, $path, $filename, $extension, $disk, $rtnType);
        if (!empty($saved)) {
            $rtn = $saved;
        }

        return $rtn;
    }

    /**
     * Upload::remove($fullPath, $disk)
     * Remove file from storage
     * sample method call:
     *      $path = ?;
     *      $saved = Upload::remove($path);
     *
     * @param String $fullPath - destination path to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function remove(string $fullPath, string $disk = null): bool
    {
        $rtn = false;

        try {
            if (is_null($disk)) {
                $disk = config('const.upload.disk.default');
            }

            if ($disk === self::DISK_S3) {
                if (static::isValidUrlString($fullPath)) {
                    if (strpos($fullPath, 'amazonaws.com/') === false) {
                        \L0g::error('Invalid s3 url from string "' . $fullPath . '". Not refrering to amazonaws.com.');
                        \SlackLog::error('Invalid s3 url from string "' . $fullPath . '". Not refrering to amazonaws.com.');
                        return $rtn;
                    } else {
                        $fullPath = explode('amazonaws.com/', $fullPath)[1];
                    }
                }
            } else {
                if (static::isValidUrlString($fullPath)) {
                    if (strpos($fullPath, 'storage/') === false) {
                        \L0g::error('Invalid url from string "' . $fullPath . '". Not refrering to storage path.');
                        \SlackLog::error('Invalid url from string "' . $fullPath . '". Not refrering to storage path.');
                        return $rtn;
                    } else {
                        $fullPath = explode('storage/', $fullPath)[1];
                    }
                } elseif (strpos($fullPath, storage_path()) !== false) {
                    if ($disk == self::DISK_LOCAL && strpos($fullPath, storage_path() . '/app/') !== false) {
                        $fullPath = explode('storage/app/', $fullPath)[1];
                    } elseif ($disk == self::DISK_PUBLIC && strpos($fullPath, storage_path() . '/app/public/') !== false) {
                        $fullPath = explode('storage/app/public/', $fullPath)[1];
                    } else {
                        $fullPath = explode('storage/', $fullPath)[1];
                    }
                }
            }

            $rtn = Storage::disk($disk)->delete($fullPath);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            \L0g::error('S3Exception: ' . $e->getMessage());
            \SlackLog::error('S3Exception: ' . $e->getMessage());
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error('Error: ' . $e->getMessage());
            \SlackLog::error('Error: ' . $e->getMessage());
        }

        return $rtn;
    }

    /**
     * Upload::removeFromUrl($url, $disk)
     * Call the Upload::remove() method
     * Remove file from storage specific parameter for url only
     * sample method call:
     *      $url = ?;
     *      $saved = Upload::removeFromUrl($url);
     *
     * @param String $url - destination url to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeFromUrl(string $url, $disk = null): bool
    {
        $rtn = false;

        if ($disk == self::DISK_LOCAL) {
            \L0g::error('Invalid disk type "local" when removing via URL');
            \SlackLog::error('Invalid disk type "local" when removing via URL');
        } elseif (!static::isValidUrlString($url)) {
            \L0g::error('Invalid url from string "' . $url . '"');
            \SlackLog::error('Invalid url from string "' . $url . '"');
        } else {
            $rtn = self::remove($url, $disk);
        }

        return $rtn;
    }

    /**
     * Upload::removeS3($path)
     * Call the Upload::remove() method with given disk "s3"
     * Remove file from aws s3 bucket
     * sample method call:
     *      $path = ?;
     *      $saved = Upload::removeS3($path);
     *
     * @param String $path - destination path or url to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeS3(string $path): bool
    {
        return self::remove($path, self::DISK_S3);
    }

    /**
     * Upload::removeFile($filename, $path, $disk)
     * Call the Upload::remove() method
     * Remove file from storage with filename, path as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $filename = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeFile($filename, $path);
     *
     * @param String $filename - filename to be remove
     * @param String $path - destination path to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeFile(string $filename, string $path = null, string $disk = null): bool
    {
        if (is_null($path)) {
            $path = static::getConstPath('default');
        }

        $fullPath = $path . '/' . $filename;
        return self::remove($fullPath, $disk);
    }

    /**
     * Upload::removeFileS3($filename, $path)
     * Call the Upload::removeFile() method
     * Remove file from s3 bucket with filename, path as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $filename = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeFileS3($filename, $path);
     *
     * @param String $filename - filename to be remove
     * @param String $path - destination path to be save
     * @return Bool $rtn - return true/false
     */
    public static function removeFileS3(string $filename, string $path = null): bool
    {
        return self::removeFile($filename, $path, self::DISK_S3);
    }

    /**
     * Upload::removeFileTemp($filename, $path)
     * Call the Upload::removeFile() method
     * Remove file from storage temporary folder with filename, path and disk as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $filename = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeFileTemp($filename, $path);
     *
     * @param String $filename - filename to be remove
     * @param String $path - destination path to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeFileTemp(string $filename, string $path = null, string $disk = null): bool
    {
        if (is_null($path)) {
            $path = static::getConstPath('tmp.default', Carbon::now()->format(self::TEMP_DIR_DATE_FORMAT) . '/' . Carbon::now()->timestamp);
        }

        if (is_null($disk)) {
            $disk = config('const.upload.disk.tmp.default');
        }

        return self::removeFile($filename, $path, $disk);
    }

    /**
     * Upload::removeImage($image, $path, $disk)
     * Call the Upload::removeFile() method
     * Remove image from storage with filename, path and disk as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $image = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeImage($image, $path);
     *
     * @param String $image - image to be remove
     * @param String $path - destination path to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeImage(string $image, string $path = null, string $disk = null): bool
    {
        if (is_null($path)) {
            $path = static::getConstPath('image');
        }

        if (is_null($disk)) {
            $disk = config('const.upload.disk.image');
        }

        return self::removeFile($image, $path, $disk);
    }

    /**
     * Upload::removeImageS3($image, $path)
     * Call the Upload::removeImage() method with disk type "s3" given
     * Remove image from s3 bucket with filename, path as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $image = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeImageS3($image, $path);
     *
     * @param String $image - image to be remove
     * @param String $path - destination path to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeImageS3(string $image, string $path = null): bool
    {
        return self::removeImage($image, $path, self::DISK_S3);
    }

    /**
     * Upload::removeImageTemp($image, $path, $disk)
     * Call the Upload::removeImage() method
     * Remove image from storage temporary folder with filename, path and disk as arguments
     * sample method call:
     *      $path = Upload::getCustomPath('noticeThumbnail', 1);
     *      $image = Upload::getCustomName('noticeThumbnail');
     *      $saved = Upload::removeImageTemp($image, $path);
     *
     * @param String $image - image to be remove
     * @param String $path - destination path to be remove
     * @param App\Helpers\Upload::DISK_/String $disk - disk type to be remove
     * @return Bool $rtn - return true/false
     */
    public static function removeImageTemp(string $image, string $path = null, string $disk = null): bool
    {
        if (is_null($path)) {
            $path = static::getConstPath('tmp.image', Carbon::now()->format(self::TEMP_DIR_DATE_FORMAT) . '/' . Carbon::now()->timestamp);
        }

        if (is_null($disk)) {
            $disk = config('const.upload.disk.tmp.image');
        }

        return self::removeFile($image, $path, $disk);
    }

    /*======================================================================
     * PRIVATE STATIC METHODS
     *======================================================================*/

    /**
     * Upload::tmpResourcesDump($disk)
     * Dump all temporary folder which is 2 days old and above
     *
     * @param App\Helpers\Upload::DISK_/String $disk - disk type of the folder
     * @return void
     */
    private static function tmpResourcesDump($disk = null)
    {
        if (is_null($disk)) {
            $disk = self::DISK_PUBLIC;
        }

        $dirs = Storage::disk($disk)->directories('tmp');

        // check to see if more than 2 temporary directory exist means:
        // (today will not be deleted, yesterday will not be deleted, then before yesterday will be deleted)
        if (count($dirs) > 2) {
            $validDir = [
                Carbon::now()->format(self::TEMP_DIR_DATE_FORMAT),
                Carbon::now()->addDays(-1)->format(self::TEMP_DIR_DATE_FORMAT)
            ];
    
            foreach ($dirs as $dir) {
                if (!in_array(explode('tmp/', $dir)[1], $validDir)) {
                    \Log::info('Temporary storage folder "' . $dir . '" is deleted. Note: folders 2 days old and above will be deleted.');
                    Storage::disk($disk)->deleteDirectory($dir);
                }
            }
        }
    }
}
