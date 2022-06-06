<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple messages links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    'request' => [
        'invalid' => '無効なリクエスト',
    ],
    'success' => [
        'read' => ':nameの取得が完了しました。',
        'create' => ':nameの作成が完了しました。',
        'update' => ':nameの更新が完了しました。',
        'delete' => ':nameの削除が完了しました。',
    ],
    'failed' => [
        'read' => ':nameの取得に失敗しました。',
        'create' => ':nameの作成に失敗しました。',
        'update' => ':nameの更新に失敗しました。',
        'delete' => ':nameを削除できませんでした。',
        'login' => 'アカウント及びパスワードが一致しません。',
    ],
    'custom' => [
        'fileSize' => [
            'kb' => ':nameには:kb KB以下のファイルを指定してください。',
            'mb' => ':nameには:mb MB以下のファイルを指定してください。'
        ],
        'imageNotExist' => '画像が存在しません',
        'csvNotExist' => 'CSVが存在しません ',
        'specifyAoOrUb' => '利用事業・所属事業所で指定する場合は選択してください。',
        'specifyAoOrUbOrStore' =>  'お店・利用事業・所属事業所で指定する場合は選択してください。',
        'weWillSendWithFollowingContents' => '下記の内容で送信を行います。 よろしいですか?',
    ],
];
