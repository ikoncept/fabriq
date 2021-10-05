<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
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
     * @param  Model  $image
     * @return array
     */
    public function transform(Model $image)
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
            'src' => $media->getUrl(),
            'srcset' => $media->getSrcSet(),
            'alt_text' => $image->alt_text,
            'caption' => $image->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'width' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null,
            'height' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null,
            'updated_at' => $image->updated_at,
            'created_at' => $image->created_at,
        ];
    }

    public function includeTags(Model $image) : Collection
    {
        return $this->collection($image->tags, new TagTransformer);
    }
}
