<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Infab\TranslatableRevisions\Models\I18nDefinition as BaseI18nDefinition;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class I18nDefinition extends BaseI18nDefinition implements HasMedia
{
    use InteractsWithMedia;
}
