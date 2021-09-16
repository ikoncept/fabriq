<?php

return [
    'modules' => [
        'dashboard' => [
            'title' => 'Dashboard',
            'enabled' => env('FABRIQ_ANALYTICS', false),
            'roles' => ['admin'],
            'route_name' => 'dashboard.index',
        ],
        'pages' => [
            'title' => 'Sidor',
            'enabled' => env('FABRIQ_PAGES', true),
            'roles' => ['admin'],
            'route_name' => 'page.index',
        ],
    ],
    'front_end_domain' => env('FABRIQ_FRONT_END_DOMAIN', 'http://localhost:3000'),
    'bucket_prefix' => env('BUCKET_PREFIX', 'fabriq-dev'),
    'enable_webp' => env('FABRIQ_ENABLE_WEBP', false),
    'enable_remote_image_processing' => env('FABRIQ_REMOTE_IMAGE_PROCESSING', false),
    'remote_image_processing_url' => env('FABRIQ_REMOTE_IMAGE_PROCESSING_URL', 'https://media-cruncher.ikoncept.io'),
    'remote_image_processing_api_key' => env('FABRIQ_REMOTE_IMAGE_PROCESSING_KEY', ''),

    // 'aliases' => [
    //      env('FABRIQ_MORPH_ARTICLE_NAME', 'MorphArticle') => 'Ikoncept\Fabriq\Models\Article',
    //      env('FABRIQ_MORPH_ARTICLE_NAME', 'MorphContact') => 'Ikoncept\Fabriq\Models\Contact',
    // ],


    /**
     * Model mapping
     */
    'modelMap' => [
        'article' => \Ikoncept\Fabriq\Models\Article::class,
        'contact' => \Ikoncept\Fabriq\Models\Contact::class,
        'event' => \Ikoncept\Fabriq\Models\Event::class,
        'page' => \Ikoncept\Fabriq\Models\Page::class,
        'slug' => \Ikoncept\Fabriq\Models\Slug::class,
        'menuItem' => \Ikoncept\Fabriq\Models\MenuItem::class,
        'menu' => \Ikoncept\Fabriq\Models\Menu::class,
        'user' => \Ikoncept\Fabriq\Models\User::class,
    ]
];
