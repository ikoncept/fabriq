<?php

namespace Ikoncept\Fabriq\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaLibraryMedia;

class Media extends MediaLibraryMedia
{
    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'media';
}
