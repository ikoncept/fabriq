<?php

namespace Ikoncept\Fabriq\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return config('fabriq.bucket_prefix').'/'.$media->uuid.'/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'c/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive-images/';
    }
}
