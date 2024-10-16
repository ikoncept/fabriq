<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Exception;
use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Image;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class ImageGetter extends BaseGetter implements GetterInterface
{
    /**
     * @return array|null
     */
    public static function get(RevisionMeta $meta, bool $publishing = false)
    {
        if (empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id,
            ];
        }

        if ($meta->meta_value === null) {
            return null;
        }

        try {
            $keyName = Fabriq::getModelClass('image')->getKeyName();
            $id = $meta->meta_value[$keyName];
        } catch (Exception $e) {
            $id = $meta->meta_value[0];
        }

        $image = Fabriq::getModelClass('image')
            ->where('id', $id)
            ->first();

        if (! $image) {
            return null;
        }
        if ($publishing) {
            return [$image->id];
        }

        /** @var Image $image * */
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
            'responsive' => $media->toHtml(),
            'x_position' => (string) $image->x_position,
            'y_position' => (string) $image->y_position,
            'meta_id' => $meta->id,
        ];
    }
}
