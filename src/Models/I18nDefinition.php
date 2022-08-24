<?php

namespace Ikoncept\Fabriq\Models;

use Infab\TranslatableRevisions\Models\I18nDefinition as BaseI18nDefinition;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class I18nDefinition extends BaseI18nDefinition implements HasMedia
{
    use InteractsWithMedia;

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'i18n_definition';
}
