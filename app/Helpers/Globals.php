<?php

namespace App\Helpers;

use App\Helpers\Upload;
use App\Models\ContentPlan;
use App\Models\Message;

class Globals
{
    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * Globals::implode()
     * return a concatenated array with a set of character string combination
     *
     * @param Array $array
     * @param String $delimeter
     * @param String/Null $pre - prefix to be added every loop
     * @return String $rtn
     */
    public static function implode($array, $delimeter, $pre = null)
    {
        $rtn = '';
        
        foreach ($array as $ind => $ar) {
            if ($ind != 0) {
                $rtn .= $delimeter;
            }
            
            if (!empty($pre)) {
                $rtn .= $pre;
            }
            
            $rtn .= $ar;
        }

        return $rtn;
    }

    /**
     * Globals::mContentPlan()
     * return a model class (ContentPlan)
     *
     * @return ContentPlan
     */
    public static function mContentPlan()
    {
        return ContentPlan::class;
    }

    /**
     * Globals::mMessage()
     * return a model class (Message)
     *
     * @return Message
     */
    public static function mMessage()
    {
        return Message::class;
    }

    /**
     * Globals::hUpload()
     * return a helper class (Upload)
     *
     * @return Upload
     */
    public static function hUpload()
    {
        return Upload::class;
    }
}
