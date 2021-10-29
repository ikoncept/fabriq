<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Image;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;

class ImageTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'tags'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Image  $image
     * @return array
     */
    public function transform(Image $image)
    {
        $media = $image->getFirstMedia('images');
        if(! $media) {
            return [
                'id' => $image->id
            ];
        }

        return [
            'id' => $image->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'c_name' => $media->name . '.' . Str::afterLast($media->file_name, '.'),
            'extension' => Str::afterLast($media->file_name, '.'),
            'file_name' => $media->file_name,
            'thumb_src' => $media->getUrl('thumb'),
            'webp_src' => (string) ($media->hasGeneratedConversion('webp')) ? $media->getUrl('webp') : '',
            'src' => $media->getUrl(),
            'srcset' => $media->getSrcSet(),
            'alt_text' => $image->alt_text,
            'caption' => $image->caption,
            'mime_type' => $media->mime_type,
            'custom_crop' => (bool) $image->custom_crop,
            'x_position' => (string) $image->x_position,
            'y_position' => (string) $image->y_position,
            'size' => $media->size,
            'width' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null,
            'height' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null,
            'updated_at' => $image->updated_at,
            'created_at' => $image->created_at,
        ];
    }

    public function includeTags(Image $image) : Collection
    {
        return $this->collection($image->tags, new TagTransformer);
    }
}
