<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Infab\Core\Traits\ApiControllerTrait;

class ModelCountController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Model map
     *
     * @var array
     */
    protected $modelMap = [
        'pages' => 'page',
        'images' => 'image',
        'files' => 'file',
        'articles' => 'article',
    ];

    public function show(string $modelType) : JsonResponse
    {
        if(! array_key_exists($modelType, $this->modelMap)) {
            return $this->errorWrongArgs('This model type can\'t be counted ('.$modelType.')');
        }

        $count =  Fabriq::getModelClass($this->modelMap[$modelType])
            ->without('media')
            ->get()->count();

        return $this->respondWithArray([
            'data' => [
                'count' => $count
            ]
        ]);
    }
}
