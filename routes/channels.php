<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel(config('fabriq.ws_prefix') . '.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel(config('fabriq.ws_prefix') . '.presence.*.*.*', function ($user) {
    $imageProps = [];

    if($user->image) {
        $media = $user->image->getFirstMedia('profile_image');
        $imageProps = [
            'image' => [
                'data' => [
                    'src' => (string) ($media->hasGeneratedConversion('webp')) ? $media->getUrl('webp') : $media->getUrl()
                ]
            ]
        ];
    }

    $data = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'image' => [
            'data' => []
        ],
        'timestamp' => round(microtime(true) * 1000)
    ];

    return array_merge($data, $imageProps);
});
