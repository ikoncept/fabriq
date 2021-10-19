<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Infab\Core\Traits\ApiControllerTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DownloadsController extends ApiController
{

    use ApiControllerTrait;

    const DOWNLOADABLE_TYPES = [
        'images' => 'Ikoncept\Fabriq\Models\Image',
        'files' => 'Ikoncept\Fabriq\Models\File',
        'videos' => 'Ikoncept\Fabriq\Models\Video'
    ];

    public function index(Request $request) : BinaryFileResponse
    {
        $type = self::DOWNLOADABLE_TYPES[$request->type];
        $files = $type::whereIn('id', $request->items)->get();

        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');
        $filename = 'fabriq-cms-export' . '-' . now()->format('Y-m-d-his') . '.zip';

        $zip->open((string) $tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files->each(function ($item) use ($zip, $request) {
            $mediaFile = $item->getFirstMedia($request->type);
            $disk = $item->getFirstMedia($request->type)->disk;
            if($disk === 's3') {
                $url = Storage::disk($disk)->url($mediaFile->getPath());
                $file = file_get_contents($url);
                $zip->addFromString($item->media[0]->file_name, (string) $file);
            } else {
                $zip->addFile($item->getFirstMedia($request->type)->getPath(), $item->media[0]->file_name);
            }
        });

        $zip->close();
        $path = Storage::putFileAs('downloads', (string) $tempFile, $filename);
        unlink((string)$tempFile);

        return response()->download(storage_path('app/' . $path), $filename)
            ->deleteFileAfterSend();
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @param integer $id
     * @return mixed BinaryFileResponse | StreamedResponse
     */
    public function show(Request $request, int $id) : mixed
    {
        $type = self::DOWNLOADABLE_TYPES[$request->type];
        $item = $type::where('id', $id)->firstOrFail();

        $mediaFile = $item->getFirstMedia($request->type);
        $disk = $mediaFile->disk;
        $headers = [
            'X-Filename' => $mediaFile->file_name
        ];

        if($disk === 'public') {
            return response()->download($mediaFile->getPath(), $mediaFile->file_name, $headers);
        }

        return Storage::disk($disk)->download($mediaFile->getPath(), $mediaFile->file_name, $headers);
    }

}
