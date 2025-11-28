<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Infab\TranslatableRevisions\Models\RevisionMeta;

class MediaGetter extends GetterInterface
{
    public function get(RevisionMeta $meta)
    {
        if ($meta->meta_value['type'] === 'image') {
            $localMeta = new RevisionMeta;
            $localMeta->meta_value = $meta->meta_value['image'];

            $imageData = ImageGetter::get($localMeta);

            return array_merge($meta->meta_value, [
                'image' => $imageData,
            ]);
        }

        if ($meta->meta_value['type'] === 'video') {
            $localMeta = new RevisionMeta;
            $localMeta->meta_value = $meta->meta_value['video'];

            $videoData = VideoGetter::get($localMeta);

            return array_merge($meta->meta_value, [
                'video' => $videoData,
            ]);
        }

        return [
            'type' => 'image',
            'fullscreen' => true,
            'image' => null,
            'video' => null,
        ];
    }
}
