<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\File;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class FileGetter
{
    /**
     * Return a representation of an image.
     *
     * @param RevisionMeta $meta
     * @param bool $publishing
     * @return mixed
     */
    public static function get(RevisionMeta $meta, $publishing = false)
    {
        if (empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id,
            ];
        }

        $file = Fabriq::getModelClass('file')
            ->whereIn('id', (array) $meta->meta_value)->first();

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
