<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
