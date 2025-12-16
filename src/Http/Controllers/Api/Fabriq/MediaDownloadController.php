<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaDownloadController extends ApiController
{
    use ApiControllerTrait;

    public function show(Request $request, string $uuid): BinaryFileResponse
    {
        $mediaFile = Fabriq::getModelClass('media')
            ->whereUuid($uuid)->firstOrFail();

        $name = $mediaFile->file_name;

        $disk = $mediaFile->disk;
        $headers = [
            'X-FILENAME' => $name,
        ];

        if ($disk === 'public') {
            return response()->download($mediaFile->getPath(), $name, $headers);
        }

        return Storage::disk($disk)->download($mediaFile->getPath(), $name, $headers);
    }
}
