<?php

namespace Ikoncept\Fabriq\Services;

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
    protected $fileType;

    public function generateResponsiveImagesViaLambda(Media $media): void
    {
        $this->fileType = config('fabriq.enable_webp') ? 'webp' : $media->extension;
        $temporaryDirectory = TemporaryDirectory::create();

        $baseImage = app(Filesystem::class)->copyFromMediaLibrary(
            $media,
            $temporaryDirectory->path(Str::random(16).'.'.$media->extension)
        );

        $media = $this->cleanResponsiveImages($media);

        $filesPayload = $this->buildPayload($this->widthCalculator->calculateWidthsFromFile($baseImage), $media);
        $extension = $this->fileType;
        $random = Str::random(16);
        $tempS3path = 'crunch/' . $random . '.' . $extension;
        Storage::disk('s3')->put($tempS3path, file_get_contents($baseImage));

        $payload = [
            'file' => $tempS3path,
            'bucket' => 'fabriq-cms',
            'outputs' => $filesPayload->toArray(),
            'deleteSourceFile' => true
        ];

        $response = LambdaService::call('media-cruncher', $payload);


        $responsiveImagePath = $this->fileNamer->temporaryFileName($media, $extension);

        foreach ($response['files'] as $file) {
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

        $this->generateTinyJpg($media, $baseImage, 'media_library_original', $temporaryDirectory);

        $temporaryDirectory->delete();
    }

    protected function buildPayload(Collection $widths, $media) : Collection
    {
        $mediaPathGenerator = new MediaPathGenerator();
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
