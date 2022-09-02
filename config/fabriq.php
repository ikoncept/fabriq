<?php

use Illuminate\Support\Str;

return [
    /**
     * Modules
     * These are menu items in the sidebar menu
     */
    'modules' => [
        [
            'title' => 'Dashboard',
            'enabled' => env('FABRIQ_ANALYTICS', true),
            'roles' => ['admin'],
            'icon' => 'DashboardIcon',
            'route' => 'home.index',
        ],
        [
            'title' => 'Sidor',
            'enabled' => env('FABRIQ_PAGES', true),
            'roles' => ['admin'],
            'icon' => 'BrowsersIcon',
            'route' => 'pages.index',
        ],
        [
            'title' => 'Smarta block',
            'route' => 'smartBlocks.index',
            'enabled' => env('FABRIQ_SMART_BLOCKS', true),
            'icon' => 'BrushFineIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Nyheter',
            'route' => 'articles.index',
            'enabled' => env('FABRIQ_ARTICLES', false),
            'icon' => 'NewspaperIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Kontakter',
            'route' => 'contacts.index',
            'enabled' => env('FABRIQ_CONTACTS', true),
            'icon' => 'UsersCrownIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Kalender',
            'route' => 'calendar.index',
            'enabled' => env('FABRIQ_EVENTS', false),
            'icon' => 'CalendarIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Användare',
            'route' => 'users.index',
            'enabled' => env('FABRIQ_USERS', true),
            'icon' => 'UsersGearIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Menyer',
            'route' => 'menus.index',
            'enabled' => env('FABRIQ_MENUS', true),
            'icon' => 'ListTreeIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Bilder',
            'route' => 'images.index',
            'enabled' => env('FABRIQ_IMAGES', true),
            'icon' => 'ImagesIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Videos',
            'route' => 'videos.index',
            'enabled' => env('FABRIQ_VIDEOS', true),
            'icon' => 'CameraMovieIcon',
            'roles' => ['admin'],
        ],
        [
            'title' => 'Filer',
            'route' => 'files.index',
            'enabled' => env('FABRIQ_FILES', true),
            'icon' => 'FilesIcon',
            'roles' => ['admin'],
        ],
    ],
    'front_end_domain' => env('FABRIQ_FRONT_END_DOMAIN', 'http://localhost:3000'),
    'bucket_prefix' => env('BUCKET_PREFIX', 'fabriq-dev'),
    'enable_webp' => env('FABRIQ_ENABLE_WEBP', false),
    'enable_remote_image_processing' => env('FABRIQ_REMOTE_IMAGE_PROCESSING', false),
    'remote_image_processing_url' => env('FABRIQ_REMOTE_IMAGE_PROCESSING_URL', 'https://media-cruncher.ikoncept.io'),
    'remote_image_processing_api_key' => env('FABRIQ_REMOTE_IMAGE_PROCESSING_KEY', ''),
    'aws_lambda_access_key' => env('AWS_LAMBDA_ACCESS_KEY_ID'),
    'aws_lambda_secret_key' => env('AWS_LAMBDA_SECRET_ACCESS_KEY'),

    'ws_prefix' => env('IKONCEPT_WS_IDENTIFIER', Str::slug(env('APP_NAME', 'laravel'), '_').'_ws'),

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
    'transformers' => [
        'article' => \Ikoncept\Fabriq\Transformers\ArticleTransformer::class,
        'blockType' => \Ikoncept\Fabriq\Transformers\BlockTypeTransformer::class,
        'comment' => \Ikoncept\Fabriq\Transformers\CommentTransformer::class,
        'contact' => \Ikoncept\Fabriq\Transformers\ContactTransformer::class,
        'event' => \Ikoncept\Fabriq\Transformers\EventTransformer::class,
        'file' => \Ikoncept\Fabriq\Transformers\FileTransformer::class,
        'i18nDefinition' => '',
        'image' => \Ikoncept\Fabriq\Transformers\ImageTransformer::class,
        'locale' => '',
        'media' => '',
        'menu' => \Ikoncept\Fabriq\Transformers\MenuTransformer::class,
        'menuItem' => \Ikoncept\Fabriq\Transformers\MenuItemTransformer::class,
        'notification' => \Ikoncept\Fabriq\Transformers\NotificationTransformer::class,
        'page' => \Ikoncept\Fabriq\Transformers\PageTransformer::class,
        'role' => \Ikoncept\Fabriq\Transformers\RoleTransformer::class,
        'slug' => \Ikoncept\Fabriq\Transformers\SlugTransformer::class,
        'smartBlock' => \Ikoncept\Fabriq\Transformers\SmartBlockTransformer::class,
        'tag' => \Ikoncept\Fabriq\Transformers\TagTransformer::class,
        'user' => \Ikoncept\Fabriq\Transformers\UserTransformer::class,
        'video' => \Ikoncept\Fabriq\Transformers\VideoTransformer::class,
    ],
    'media-library' => [
        'max_file_size' => 1024 * 1024 * 500, // 500 MB,
        'jobs' => [
            'perform_conversions' => Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
            'generate_responsive_images' => \Ikoncept\Fabriq\Jobs\GenerateResponsiveImagesJob::class,
        ],
        'path_generator' => \Ikoncept\Fabriq\Services\MediaPathGenerator::class,
        'remote' => [
            'extra_headers' => [
                'CacheControl' => 'max-age=604800',
                'ACL' => 'public-read',
            ],
        ],
    ],

    'fortify' => [
        'features' => [
            \Laravel\Fortify\Features::resetPasswords(),
            \Laravel\Fortify\Features::updateProfileInformation(),
            \Laravel\Fortify\Features::updatePasswords(),
        ],
    ],
];
