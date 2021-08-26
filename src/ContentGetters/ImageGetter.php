<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Models\Image;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class ImageGetter
{
    /**
     * Return a representation of an image
     *
     * @param RevisionMeta $meta
     * @param boolean $publishing
     * @return mixed
     */
    public static function get(RevisionMeta $meta, $publishing = false)
    {
        if(empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id
            ];
        }

        $image = Image::whereIn('id', (array) $meta->meta_value)->first();
        if(! $image) {
            return null;
        }
        if($publishing) {
            return [$image->id];
        }
        $media = $image->getFirstMedia('images');

        return [
            'id' => $image->id,
            'file_name' => $media->file_name,
            'src' => $media->getUrl(),
            'thumb_src' => $media->getUrl('thumb'),
            'srcset' => $media->getSrcSet(),
            'alt_text' => $image->alt_text,
            'caption' => $image->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'width' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null,
            'height' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null,
            'meta_id' => $meta->id
        ];
    }
}
