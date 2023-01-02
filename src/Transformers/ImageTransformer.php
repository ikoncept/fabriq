<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'tags',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Image  $image
     * @return array
     */
    public function transform(Image $image)
    {
        $media = $image->getFirstMedia('images');
        if (! $media) {
            return [
                'id' => $image->id,
            ];
        }

        return [
            'id' => $image->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'c_name' => $media->name.'.'.Str::afterLast($media->file_name, '.'),
            'extension' => Str::afterLast($media->file_name, '.'),
            'file_name' => $media->file_name,
            'thumb_src' => $media->getUrl('thumb'),
            'webp_src' => (string) ($media->hasGeneratedConversion('webp')) ? $media->getUrl('webp') : '',
            'src' => $media->getUrl(),
            'srcset' => $media->getSrcSet(),
            'responsive' => $media->toHtml(),
            'alt_text' => $image->alt_text,
            'caption' => $image->caption,
            'mime_type' => $media->mime_type,
            'custom_crop' => (bool) $image->custom_crop,
            'x_position' => (string) $image->x_position,
            'y_position' => (string) $image->y_position,
            'size' => $media->size,
            'width' => $this->getWidth($media),
            'height' => $this->getHeight($media),
            'processing' => (bool) $media->getCustomProperty('processing'),
            'processing_failed' => (bool) $media->getCustomProperty('processing_failed'),
            'updated_at' => $image->updated_at,
            'created_at' => $image->created_at,
        ];
    }

    public function includeTags(Image $image): Collection
    {
        return $this->collection($image->tags, Fabriq::getTransformerFor('tag'));
    }

    /**
     * Get width.
     *
     * @param  Media  $media
     * @return mixed
     */
    protected function getWidth(Media $media)
    {
        if ($media->getCustomProperty('width')) {
            return $media->getCustomProperty('width');
        }
        if ($media->responsiveImages()->files->first()) {
            return $media->responsiveImages()->files->first()->width();
        }

        return null;
    }

    /**
     * Get height.
     *
     * @param  Media  $media
     * @return mixed
     */
    protected function getHeight(Media $media)
    {
        if ($media->getCustomProperty('height')) {
            return $media->getCustomProperty('height');
        }
        if ($media->responsiveImages()->files->first()) {
            return $media->responsiveImages()->files->first()->height();
        }

        return null;
    }
}
