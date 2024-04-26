<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Block;
use Ikoncept\Fabriq\Models\Page;
use Illuminate\Http\JsonResponse;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class PublishPageController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Publish a page revision.
     */
    public function store(int $pageId): JsonResponse
    {
        $page = Fabriq::getFqnModel('page')::withoutEvents(function () use ($pageId) {
            $page = Fabriq::getFqnModel('page')::with('blocks')->whereId($pageId)->firstOrFail();
            $page->publish($page->revision);

            foreach ($page->blocks->groupBy('revision')->values()[0] as $block) {

                // Replicate the old draft block
                $newBlock = $block->replicate();
                $newBlock->save();

                // Up the revision
                $block->revision = $page->revision;
                $block->save();

                // replicate the block to
                // $block->block_type_id = $block->block_type_id;
                // $block->locale = $block->locale;
                // $block->content = $block->content;
                // $block->hidden = $block->hidden ?? false;
                // $block->save();
            }
            // Delete old blocks
            $blocks = Block::where('revision', $page->revision - 2)->get()
                ->where('page_id', $page->id)
                ->each(function ($item) {
                    $item->delete();
                });

            return $page;
        });

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'));
    }
}
