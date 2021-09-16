<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Transformers\DummyTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use NamespacedDummyModel;

class ModelCountController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Model map
     *
     * @var array
     */
    protected $modelMap = [
        'pages' => \Ikoncept\Fabriq\Models\Page::class,
        'images' => \Ikoncept\Fabriq\Models\Image::class,
        'files' => \Ikoncept\Fabriq\Models\File::class,
        'articles' => \Ikoncept\Fabriq\Models\Article::class,
    ];

    public function show(string $modelType) : JsonResponse
    {
        if(! array_key_exists($modelType, $this->modelMap)) {
            return $this->errorWrongArgs('This model type can\'t be counted ('.$modelType.')');
        }

        $count = (new $this->modelMap[$modelType])
            ->without('media')
            ->get()->count();

        return $this->respondWithArray([
            'data' => [
                'count' => $count
            ]
        ]);
    }
}
