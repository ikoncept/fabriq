<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\JsonResponse;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageSourceSetController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Get src set data.
     */
    public function show(int $id): JsonResponse
    {
        $image = Fabriq::getFqnModel('image')::whereId($id)->firstOrFail();

        /** @var Media $media */
        $media = $image->media->first();

        $conversion = '';
        $attributeString = '';
        $loadingAttributeValue = '';
        $width = ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null;
        $height = ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null;

        /** @var view-string $viewString * */
        $viewString = 'vendor.fabriq._partials.srcset';
        $srcset = view($viewString, compact(
            'media',
            'conversion',
            'attributeString',
            'loadingAttributeValue',
            'width',
            'height',
        ))->render();

        return $this->respondWithArray([
            'data' => [
                'html' => $srcset,
                'srcset' => $media->getSrcset(''),
                'onload' => "window.requestAnimationFrame(function(){if(!(size=getBoundingClientRect().width))return;onload=null;sizes=Math.ceil(size/window.innerWidth*100)+'vw';});",
                'sizes' => '1px',
                'src' => $media->getUrl(),
                'width' => $width,
                'height' => $height,
                'alt_text' => $image->alt_text,
            ],
        ]);
    }
}
