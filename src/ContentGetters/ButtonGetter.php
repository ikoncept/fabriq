<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Page;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class ButtonGetter
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
        $value = collect($meta->meta_value);

        if(! isset($value['linkType'])) {
            return $value;
        }

        if($value['linkType'] === 'external') {
            $value['path'] = $value['url'];

            return $value;
        }

        if($value['linkType'] === 'internal') {
            if(empty($value['page_id'])) {
                return $value;
            }

            $page = Fabriq::getFqnModel('page')::find($value['page_id']);
            if(! $page) {
                return $value;
            }
            $paths = $page->localizedPaths->flatten();
            unset($value['url']);
            $value['path'] = $paths->first() ?? '/' . $page->slugs->where('locale', app()->getLocale())->first()->slug;

            return $value;
        }

        if($value['linkType'] === 'file') {
            if(! isset($value['file']['id'])) {
                return $value;
            }
            $file = Fabriq::getFqnModel('file')::find($value['file']['id']);
            if(! $file) {
                return $value;
            }
            $media = $file->getFirstMedia('files');
            $value['path'] = $media->getUrl();
            $value['url'] = $value['path'];
            $value['thumb'] = ($media->hasGeneratedConversion('file_thumb')) ? $media->getUrl('file_thumb')  : '';

            return $value;
        }
    }
}
