<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\File;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
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
     * @param  File  $file
     * @return array
     */
    public function transform(File $file)
    {
        $media = $file->getFirstMedia('files');
        if (! $media) {
            return [
                'id' => $file->id,
            ];
        }

        return [
            'id' => $file->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'c_name' => $media->name.'.'.Str::afterLast($media->file_name, '.'),
            'extension' => Str::afterLast($media->file_name, '.'),
            'file_name' => $media->file_name,
            'thumb_src' => ($media->hasGeneratedConversion('file_thumb')) ? $media->getUrl('file_thumb') : '',
            'src' => $media->getUrl(),
            'readable_name' => $file->readable_name,
            'caption' => $file->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'updated_at' => $file->updated_at,
            'created_at' => $file->created_at,
        ];
    }

    public function includeTags(File $file): Collection
    {
        return $this->collection($file->tags, new TagTransformer);
    }
}
