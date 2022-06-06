<?php

return [
    'pageBreadCrumbs' => [
        // SAMPLE:
        // 'sample.route' => [
        //     'title' => 'Sample',
        //     'routes' => [
        //         'words.Home' => 'login',
        //         'words.Column' => 'dashboard.index',
        //         'words.Content' => ['notice.index', ['noticeType' => 'deal']],
        //         'words.Login,<i class="fa fa-bullhorn"></i>' => ['notice.index', ['noticeType' => 'deal']],
        //     ]
        // ],
    ],
    'routeMenuGroup' => [
        'notice' => [
            'notice.index',
            'notice.create',
            'notice.edit',
        ],
        'recipe' => [
            'recipe.index',
            'recipe.create',
            'recipe.edit',
        ],
        'productInformation' => [
            'productInformation.index',
            'productInformation.create',
            'productInformation.edit',
        ],
        'column' => [
            'column.index',
            'column.create',
            'column.edit',
        ],
        'message' => [
            'message.index',
            'message.create',
            'message.edit',
        ],
    ],
    'upload' => [
        'disk' => [
            'default' => 'public',
            'image' => 'public',
            'csv' => 'public',
            'tmp' => [
                'default' => 'public',
                'image' => 'public',
                'csv' => 'public',
            ],
        ],
        'path' => [
            'default' => 'default',
            'image' => 'images',
            'csv' => 'csv',
            'tmp' => [
                'default' => ['tmp', 'default'],
                'image' => ['tmp', 'images'],
                'csv' => ['tmp', 'csv'],
            ],
        ],
        'extension' => [
            'image' => 'png',
            'csv' => 'csv',
        ],
        'custom' => [
            // NOTE: please follow folder structure:
            //  notice/[id]/thumbnail
            //  notice/[id]/csv
            'path' => [
                'noticeThumbnail' => 'M_ContentPlan',
                'noticeImageContent' => 'M_ContentPlan',

                'recipeThumbnail' => 'M_ContentPlan',
                'recipeImageContent' => 'M_ContentPlan',
                
                'productInformationThumbnail' => 'M_ContentPlan',
                'productInformationImageContent' => 'M_ContentPlan',
                
                'columnThumbnail' => 'M_ContentPlan',
                'columnImageContent' => 'M_ContentPlan',

                'messageThumbnail' => 'M_Message',
                'messageImageContent' => 'M_Message',
            ],
            'name' => [
                // 'noticeThumbnail' => 'thumbnail.png',
            ],
        ],
    ]
];
