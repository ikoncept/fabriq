<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Illuminate\Support\Collection;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class ButtonsGetter
{
    /**
     * Return a representation of an image.
     *
     * @param  RevisionMeta  $meta
     * @param  bool  $publishing
     * @return mixed
     */
    public static function get(RevisionMeta $meta, $publishing = false)
    {
        if (empty($meta->toArray())) {
            return [
                'meta_id' => $meta->id,
            ];
        }
        $value = new Collection($meta->meta_value);

        $buttons = $value->map(function ($button) {
            $tempMeta = RevisionMeta::make([
                'meta_value' => $button,
            ]);
            $button = ButtonGetter::get($tempMeta);

            return $button;
        });

        return $buttons;
    }
}
