<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Image;
use League\Fractal\TransformerAbstract;

class UserImageTransformer extends TransformerAbstract
{
    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Image  $image
     * @return array
     */
    public function transform(Image $image = null)
    {
        if (! $image) {
            return [];
        }

        $media = $image->getFirstMedia('profile_image');

        if (! $media) {
            return [
                'id' => $image->id,
            ];
        }

        return [
            'id' => $image->id,
            'file_name' => $media->file_name,
            'thumb_src' => $media->getUrl('thumb'),
            'webp_src' => (string) ($media->hasGeneratedConversion('webp')) ? $media->getUrl('webp') : '',
            'src' => $media->getUrl(),
            'mime_type' => $media->mime_type,
            'srcset' => $media->getSrcSet(),
        ];
    }
}
