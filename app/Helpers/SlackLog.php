<?php

namespace App\Helpers;

class SlackLog
{
    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * SlackLog::info($message)
     * send slack log information message
     *
     * @param String $message
     * @return void
     */
    public static function info($message)
    {
        if (!empty(config('slackLog.enable')) && !empty(config('slackLog.webhookUrl'))) {
            \Log::channel('slack')->info($message);
        } else {
            \L0g::error('Slack Log is not working properly.', [
                'slackLog.enable' => config('slackLog.enable'),
                'slackLog.webhookUrl' => config('slackLog.webhookUrl'),
            ]);
        }
    }

    /**
     * SlackLog::error($message)
     * send slack log error message
     *
     * @param String $message
     * @return void
     */
    public static function error($message)
    {
        if (!empty(config('slackLog.enable')) && !empty(config('slackLog.webhookUrl'))) {
            \Log::channel('slack')->error($message);
        } else {
            \L0g::error('Slack Log is not working properly.', [
                'slackLog.enable' => config('slackLog.enable'),
                'slackLog.webhookUrl' => config('slackLog.webhookUrl'),
            ]);
        }
    }
}
