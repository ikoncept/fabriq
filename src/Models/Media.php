<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Fabriq;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaLibraryMedia;

class Media extends MediaLibraryMedia
{
    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'media';

    public function image()
    {
        return $this->belongsTo(Fabriq::getFqnModel('image'));
    }
}
