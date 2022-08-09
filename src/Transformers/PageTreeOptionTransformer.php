<?php

namespace Ikoncept\Fabriq\Transformers;

use Kalnoy\Nestedset\Collection;
use League\Fractal\TransformerAbstract;

class PageTreeOptionTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * Carry variable.
     *
     * @var array
     */
    protected $carry = [];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Collection  $tree
     * @return array
     */
    public function transform(Collection $tree) : array
    {
        $this->walktree($tree);

        return $this->carry;
    }

    /**
     * Walk page tree.
     *
     * @param Collection $tree
     * @param string $prefix
     * @return Collection
     */
    protected function walktree($tree, $prefix = '-')
    {
        return $tree->transform(function ($item) use ($prefix) {
            $item->prefixed_name = $prefix.' '.$item->name;
            $item->depth = strlen($prefix);
            $this->carry[] = $item;
            if ($item->children->count()) {
                $item->children = $this->walktree($item->children, $prefix.'-');
            }

            return $item;
        });
    }
}
