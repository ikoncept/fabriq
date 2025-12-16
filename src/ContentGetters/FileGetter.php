<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Fabriq;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class FileGetter
{
    /**
     * Return a representation of an image.
     *
     * @param  bool  $publishing
     * @return mixed
     */
    public static function get(RevisionMeta $meta, $publishing = false)
    {
        if (empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id,
            ];
        }

        $keyName = Fabriq::getModelClass('file')->getKeyName();
        $file = Fabriq::getModelClass('file')
            ->where('id', $meta->meta_value[$keyName] ?? $meta->meta_value[0])
            ->first();

        if (! $file) {
            return null;
        }
        if ($publishing) {
            return [$file->id];
        }
        $media = $file->getFirstMedia('files');

        return [
            'id' => $file->id,
            'uuid' => $media->uuid,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'thumb_src' => ($media->hasGeneratedConversion('file_thumb')) ? $media->getUrl('file_thumb') : '',
            'extension' => pathinfo($media->file_name, PATHINFO_EXTENSION),
            'src' => $media->getUrl(),
            'readable_name' => $file->readable_name,
            'caption' => $file->caption,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'updated_at' => $file->updated_at,
            'created_at' => $file->created_at,
        ];
    }
}
