<?php

namespace Ikoncept\Fabriq\Models;

use Karabin\TranslatableRevisions\Models\I18nDefinition as BaseI18nDefinition;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class I18nDefinition extends BaseI18nDefinition implements HasMedia
{
    use InteractsWithMedia;
}
