<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Http\JsonResponse;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageSourceSetController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Get src set data
     *
     * @param integer $id
     * @return JsonResponse
     */
    public function show(int $id) : JsonResponse
    {
        $image = Image::findOrFail($id);

        /** @var Media $media */
        $media = $image->media->first();

        $conversion = '';
        $attributeString = '';
        $loadingAttributeValue = '';
        $width = ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->width() : null;
        $height = ($media->responsiveImages()->files->first()) ? $media->responsiveImages()->files->first()->height() : null;

        $srcset = view('_partials.srcset', compact(
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
                'alt_text' => $image->alt_text
            ]
        ]);
    }
}
