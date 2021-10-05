<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;

class FileTransformer extends TransformerAbstract
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
     * @param  Model  $file
     * @return array
     */
    public function transform(Model $file)
    {
        $media = $file->getFirstMedia('files');
        if(! $media) {
            return [
                'id' => $file->id
            ];
        }
        return [
            'id' => $file->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'c_name' => $media->name . '.' . Str::afterLast($media->file_name, '.'),
            'extension' => Str::afterLast($media->file_name, '.'),
            'file_name' => $media->file_name,
            'thumb_src' => ($media->hasGeneratedConversion('file_thumb')) ? $media->getUrl('file_thumb')  : '',
            'src' => $media->getUrl(),
            'readable_name' => $file->readable_name,
            'caption' => $file->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'updated_at' => $file->updated_at,
            'created_at' => $file->created_at,
        ];
    }

    public function includeTags(Model $file) : Collection
    {
        return $this->collection($file->tags, new TagTransformer);
    }
}
