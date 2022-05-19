<?php

namespace Ikoncept\Fabriq\Services;

use GuzzleHttp\Promise\Utils;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImage;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImageGenerator as SpatieResponsiveImageGenerator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class ResponsiveImageGenerator extends SpatieResponsiveImageGenerator
{

    protected string $fileType;

    public function generateResponsiveImagesViaLambda(Media $media): void
    {
        $this->fileType = config('fabriq.enable_webp') ? 'webp' : $media->extension;

        $media = $this->cleanResponsiveImages($media);
        $filesPayload = $this->buildPayload(
            $this->widthCalculator->calculateWidths($media->size,
            $media->getCustomProperty('width'),
            $media->getCustomProperty('height')),
            $media
        );

        $extension = $this->fileType;
        $responsiveImagePath = $this->fileNamer->temporaryFileName($media, $extension);

        $promises = $filesPayload->map(function($file) use ($media) {
            $payload = [
                'file' => $media->getPath(),
                'bucket' => 'fabriq-cms',
                'outputs' => collect([])->push($file)->toArray(),
                'deleteSourceFile' => false
            ];
            return LambdaService::callAsync('media-cruncher', $payload);
        });

        $response = Utils::unwrap($promises);
        $responses = collect($response)->map(function($response) {
            return json_decode($response->get('Payload')->getContents(), true);
        });


        foreach ($responses as $response) {
            $file = $response['files'][0];
            $fileName = $this->addPropertiesToFileName(
                $responsiveImagePath,
                'media_library_original',
                $file['info']['width'],
                $file['info']['height'],
                $extension
            );

            ResponsiveImage::register($media, $fileName, 'media_library_original');
            Storage::disk('s3')->move($file['path'], pathinfo($file['path'], PATHINFO_DIRNAME) .'/' . $fileName);
        }

        $temporaryDirectory = TemporaryDirectory::create();

        $baseImage = app(Filesystem::class)->copyFromMediaLibrary(
            $media,
            $temporaryDirectory->path(Str::random(16).'.'.$media->extension)
        );

        $this->generateTinyJpg($media, $baseImage, 'media_library_original', $temporaryDirectory);

        $temporaryDirectory->delete();
    }

    protected function buildPayload(Collection $widths, Media $media) : Collection
    {
        $generatorClass = config('fabriq.media-library.path_generator');
        $mediaPathGenerator = new $generatorClass();
        $responsiveImagePath = $this->fileNamer->temporaryFileName($media, $this->fileType);
        return $widths->map(function ($width) use ($media, $responsiveImagePath, $mediaPathGenerator) {
            return [
                'format' => $this->fileType,
                'path' => $mediaPathGenerator->getPathForResponsiveImages($media) . $width . '_' . $responsiveImagePath,
                'maxWidth' => $width,
                'acl' => 'public-read'
            ];
        });
    }
}
