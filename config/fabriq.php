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


    /**
     * Model mapping
     */
    'models' => [
        'article' => \Ikoncept\Fabriq\Models\Article::class,
        'blockType' => \Ikoncept\Fabriq\Models\BlockType::class,
        'comment' => \Ikoncept\Fabriq\Models\Comment::class,
        'contact' => \Ikoncept\Fabriq\Models\Contact::class,
        'event' => \Ikoncept\Fabriq\Models\Event::class,
        'file' => \Ikoncept\Fabriq\Models\File::class,
        'i18nDefinition' => \Ikoncept\Fabriq\Models\I18nDefinition::class,
        'image' => \Ikoncept\Fabriq\Models\Image::class,
        'locale' => \Ikoncept\Fabriq\Models\Locale::class,
        'media' => \Ikoncept\Fabriq\Models\Media::class,
        'menu' => \Ikoncept\Fabriq\Models\Menu::class,
        'menuItem' => \Ikoncept\Fabriq\Models\MenuItem::class,
        'notification' => \Ikoncept\Fabriq\Models\Notification::class,
        'page' => \Ikoncept\Fabriq\Models\Page::class,
        'role' => \Ikoncept\Fabriq\Models\Role::class,
        'slug' => \Ikoncept\Fabriq\Models\Slug::class,
        'smartBlock' => \Ikoncept\Fabriq\Models\SmartBlock::class,
        'tag' => \Spatie\Tags\Tag::class,
        'user' => \App\Models\User::class,
        'video' => \Ikoncept\Fabriq\Models\Video::class,
    ],

    'media-library' => [
        'max_file_size' => 1024 * 1024 * 500, // 500 MB,
        'jobs' => [
            'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
            'generate_responsive_images' => \Ikoncept\Fabriq\Jobs\GenerateResponsiveImagesJob::class
        ],
        'path_generator' => \Ikoncept\Fabriq\Services\MediaPathGenerator::class,
        'remote' => [
            'extra_headers' => [
                'CacheControl' => 'max-age=604800',
                'ACL' => 'public-read',
            ],
        ]
    ],

    'fortify' => [
        'features' => [
            \Laravel\Fortify\Features::resetPasswords(),
            \Laravel\Fortify\Features::updateProfileInformation(),
            \Laravel\Fortify\Features::updatePasswords()
        ]
    ]
];
