<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Illuminate\Database\Eloquent\Builder;
use Infab\TranslatableRevisions\Models\RevisionMeta;

interface GetterInterface
{
    public static function get(RevisionMeta $meta, bool $isPublishing) : array|null;
}
