<?php

namespace Ikoncept\Fabriq\Actions;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nLocale;

class ClonePage
{
    public function __invoke(Model $root, Model $pageToClone, string $pageName = ''): Model
    {
        $page = Fabriq::getModelClass('page');
        $page->name = $pageName ?? 'Kopia av '.$pageToClone->name;
        $page->template_id = $pageToClone->template_id;
        $page->revision = $pageToClone->revision;
        $page->published_version = $pageToClone->published_version;
        $page->parent_id = $root->id;
        $page->updated_by = auth()->user()->id ?? null;
        $page->save();

        $locales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        foreach ($locales as $locale) {
            $content = $pageToClone->getFieldContent($pageToClone->revision, $locale->iso_code);
            $page->updateContent($content->toArray(), $locale->iso_code);
        }

        return $page;
    }
}
