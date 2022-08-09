<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Illuminate\Database\Eloquent\Builder;
use Infab\TranslatableRevisions\Models\RevisionMeta;

interface GetterInterface
{
    /**
     * Get transformed data.
     *
     * @return array|null
     */
    public static function get(RevisionMeta $meta, bool $isPublishing);
}
