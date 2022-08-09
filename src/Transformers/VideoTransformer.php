<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Video;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class VideoTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
        'tags',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Video  $video
     * @return array
     */
    public function transform(Video $video)
    {
        $media = $video->getFirstMedia('videos');
        if (! $media) {
            return [
                'id' => $video->id,
            ];
        }

        return [
            'id' => $video->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'c_name' => $media->name.'.'.Str::afterLast($media->file_name, '.'),
            'extension' => Str::afterLast($media->file_name, '.'),
            'file_name' => $media->file_name,
            'thumb_src' => $media->getUrl('thumb'),
            'poster_src' => $media->getUrl('poster'),
            'src' => $media->getUrl(),
            'alt_text' => $video->alt_text,
            'caption' => $video->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            // 'width' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null,
            // 'height' => ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null,
            'updated_at' => $video->updated_at,
            'created_at' => $video->created_at,
        ];
    }

    public function includeTags(Video $video) : Collection
    {
        return $this->collection($video->tags, new TagTransformer);
    }
}
