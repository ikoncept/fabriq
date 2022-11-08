<?php

namespace Ikoncept\Fabriq\Actions;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nLocale;

class ClonePage
{
    public function __invoke(Model $root, Model $sourcePage, string $pageName = ''): Model
    {
        $page = Fabriq::getModelClass('page');
        $page->name = ($pageName) ? $pageName : 'Kopia av '.$sourcePage->name;
        $page->template_id = $sourcePage->template_id;
        $page->revision = $sourcePage->revision;
        $page->published_version = $sourcePage->published_version;
        $page->parent_id = $root->id;
        $page->locked = $sourcePage->locked;
        $page->updated_by = auth()->user()->id ?? null;
        $page->save();

        $locales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        foreach ($locales as $locale) {
            $content = $sourcePage->getFieldContent($sourcePage->revision, $locale->iso_code);
            $page->updateContent($content->toArray(), $locale->iso_code);
        }

        return $page;
    }
}
