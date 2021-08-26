<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Models\SmartBlock;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class SmartBlockGetter
{
    /**
     * Return a representation of an image
     *
     * @param RevisionMeta $meta
     * @param boolean $publishing
     * @return mixed
     */
    public static function get(RevisionMeta $meta, $publishing = false)
    {
        if(empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id
            ];
        }

        $smartBlock = SmartBlock::whereIn('id', (array) $meta->meta_value)->first();

        if(! $smartBlock) {
            return null;
        }

        if($publishing) {
            return $smartBlock->id;
        }

        return [
            'id' => $smartBlock->id,
            'name' => $smartBlock->name,
            'content' => $smartBlock->getFieldContent()
        ];
    }
}
