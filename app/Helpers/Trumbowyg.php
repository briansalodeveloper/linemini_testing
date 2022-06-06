<?php

namespace App\Helpers;

use App\Helpers\Upload;

class Trumbowyg
{
    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * Trumbowyg::moveTemporaryFiles($htmlText, $path, $disk)
     * will only update image source if current image source is from storage/app/public folder of the application
     *
     * @param String $htmlText
     * @param String $path
     * @param String $disk
     */
    public static function moveTemporaryFiles($htmlText, $path, $disk, $deletePrev = false, $isDeleteAutomatically = false, $retainName = false)
    {
        $rtn = '';

        $index = 0;
        $ctrIndex = 0;
        $txtArray = explode('<img', $htmlText);
        $updatedImages = [];

        foreach ($txtArray as $ind => &$tx) {
            if ($ind != 0) {
                $txPreExcess = '';
                $txSource = '';
                $txPostExcess = '';
                $indSourceStartWithOpeningQuote = strpos($tx, 'src="');

                if ($indSourceStartWithOpeningQuote !== false) {
                    $indSourceStartWithOpeningQuote += 5;

                    $indClosingImgTag = strpos($tx, '>');
                    $cntClosingQuote = 1;
                    $txFromSource = explode('src="', $tx)[1];
                    $urlLength = strlen(explode('"', $txFromSource)[0]);

                    $txPreExcess = substr($tx, 0, $indSourceStartWithOpeningQuote);
                    $txSource = substr($tx, $indSourceStartWithOpeningQuote, $urlLength);
                    $txPostExcess = substr($tx, (strlen($txPreExcess) + strlen($txSource)));
                }

                if (Upload::isValidUrlString($txSource)) {
                    if (strpos($txSource, env('APP_URL')) !== false) {
                        $filename = null;

                        if ($retainName) {
                            $filename = Upload::getBaseName($txSource);
                        }

                        $saved = Upload::saveFromUrl($txSource, $path, $filename, null, $disk);
                        if ($saved) {
                            $tx = $txPreExcess . $saved . $txPostExcess;
                            $updatedImages[] = $txSource;
                            if ($isDeleteAutomatically) {
                                Upload::removeFromUrl($txSource, Upload::DISK_PUBLIC);
                            }
                        }
                    } elseif (strpos($txSource, 'amazonaws.com/') !== false && strpos($txSource, $path) === false) {
                        $filename = null;

                        if ($retainName) {
                            $filename = Upload::getBaseName($txSource);
                        }

                        $saved = Upload::saveFromUrl($txSource, $path, $filename, null, $disk);
                        if ($saved) {
                            $tx = $txPreExcess . $saved . $txPostExcess;
                            if ($deletePrev) {
                                $updatedImages[] = $txSource;
                                if ($isDeleteAutomatically) {
                                    Upload::removeFromUrl($txSource, $disk);
                                }
                            }
                        }
                    }
                }
            }
        }

        $rtn = implode('<img', $txtArray);
        $rtn = [
            'html' => $rtn,
            'updatedImages' => $updatedImages
        ];

        return $rtn;
    }
}
