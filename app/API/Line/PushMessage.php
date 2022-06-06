<?php

namespace App\API\Line;

use GuzzleHttp\Client;

class PushMessage
{
    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const PATTERN = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
    const MAX_SEND_USER_COUNT = 500;

    /*======================================================================
     * STATIC METHODS
     *======================================================================*/

    /**
     * PushMessage::send($lineids, $message)
     *
     * @param Array/String $lineIds - will accept array or string value
     * @param String $sendMessage
     * @return Bool/GuzzleHttp\Psr7\Response $rtn
     */
    public static function send($lineIds, $sendMessage, $image = null, $imagePreview = null)
    {
        $rtn = false;

        try {
            if (!is_array($lineIds)) {
                $lineIds = [$lineIds];
            }

            $params = [
                'to' => $lineIds,
                'messages' => [],
            ];

            if (!empty($image) && !empty($imagePreview)) {
                $params['messages'][] = [
                    "type" => "image",
                    "originalContentUrl" => $image,
                    "previewImageUrl" => $imagePreview
                ];
            }

            $params['messages'][] = [
                'type' => 'text',
                'text' => $sendMessage,
            ];

            $uuid = self::uuidV4FactoryGenerate();
            $params = json_encode($params, JSON_UNESCAPED_UNICODE);
    
            $client = new Client();
            $rtn = $client->request('POST', 'https://api.line.me/v2/bot/message/multicast', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . config('line.messaging.accessToken'),
                    'Accept'        => 'application/json',
                    'X-Line-Retry-Key' => $uuid
                ],
                'body' => $params
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \L0g::error('Guzzle ClientException: ' . $e->getMessage());
            \SlackLog::error('Guzzle ClientException: ' . $e->getMessage());
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Guzzle ClientException: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error('Guzzle ClientException: ' . $e->getMessage());
        }

        return $rtn;
    }

    /**
     * UUID 生成
     *
     * @return String
     * @throws \Exception
     */
    public static function uuidV4FactoryGenerate(): string
    {
        $chars = str_split(self::PATTERN);

        foreach ($chars as $i => $char) {
            if ($char === 'x') {
                $chars[$i] = dechex(random_int(0, 15));
            } elseif ($char === 'y') {
                $chars[$i] = dechex(random_int(8, 11));
            }
        }

        return implode('', $chars);
    }
}
