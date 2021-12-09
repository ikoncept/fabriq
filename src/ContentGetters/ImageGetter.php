<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Ikoncept\Fabriq\ContentGetters\BaseGetter;

class ImageGetter extends BaseGetter implements GetterInterface
{
    /**
     * @return array|null
     */
    public static function get(RevisionMeta $meta, bool $publishing = false)
    {
        if(empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id
            ];
        }

        $image = Fabriq::getModelClass('image')
            ->whereIn('id', (array) $meta->meta_value);

         $image = self::getObjectOnce(self::getHash($image), $image);

        if(! $image) {
            return null;
        }
        if($publishing) {
            return [$image->id];
        }


        /** @var Image $image **/
        $media = $image->getFirstMedia('images');

        return [
            'id' => $image->id,
            'file_name' => $media->file_name,
            'src' => $media->getUrl(),
            'thumb_src' => $media->getUrl('thumb'),
            'webp_src' => (string) ($media->hasGeneratedConversion('webp')) ? $media->getUrl('webp') : '',
            'srcset' => ($media->getSrcSet()) ? $media->getSrcset() : null,
            'alt_text' => (string) $image->alt_text,
            'caption' => $image->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'width' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null,
            'height' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null,
            'custom_crop' => (bool) $image->custom_crop,
            'x_position' => (string) $image->x_position,
            'y_position' => (string) $image->y_position,
            'meta_id' => $meta->id
        ];
    }

}
