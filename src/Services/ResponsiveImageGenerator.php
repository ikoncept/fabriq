<?php

namespace Ikoncept\Fabriq\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\ResponsiveImages\Events\ResponsiveImagesGenerated;
use Spatie\MediaLibrary\ResponsiveImages\Exceptions\InvalidTinyJpg;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImage;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImageGenerator as SpatieResponsiveImageGenerator;
use Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;
use Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\WidthCalculator;
use Spatie\MediaLibrary\Support\File;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;
use Spatie\MediaLibrary\Support\ImageFactory;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class ResponsiveImageGenerator extends SpatieResponsiveImageGenerator
{

    protected function callMediaCruncher(
        string $baseImage,
        int $targetWidth,
        int $conversionQuality,
        string $responsiveImagePath,
        string $extension
    ) : Response
    {
        try {
            $file = (string) file_get_contents($baseImage);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('fabriq.remote_image_processing_api_key')
            ])
            ->attach(
                'attachment', $file, 'image.' . $extension
            )
            ->post(config('fabriq.remote_image_processing_url') . '/api/media', [
                'width' => $targetWidth,
                'quality' => $conversionQuality,
                'responsiveImagePath' => $responsiveImagePath,
            ]);

            return $response;
        } catch (\Throwable $e) {
            throw new FileNotFoundException($e->getMessage());
        }

    }

    public function generateResponsiveImage(
        Media $media,
        string $baseImage,
        string $conversionName,
        int $targetWidth,
        BaseTemporaryDirectory $temporaryDirectory,
        int $conversionQuality = self::DEFAULT_CONVERSION_QUALITY
    ): void {
        $extension = $this->fileNamer->extensionFromBaseImage($baseImage);
        $responsiveImagePath = $this->fileNamer->temporaryFileName($media, $extension);

        $tempDestination = $temporaryDirectory->path($responsiveImagePath);

        if(config('fabriq.enable_remote_image_processing')) {
            $mediaResponse = $this->callMediaCruncher($baseImage, $targetWidth, $conversionQuality, $responsiveImagePath, $extension);
            // $file = file_get_contents($mediaResponse->json()['media_path']);
            file_put_contents($tempDestination, $mediaResponse->body());
        } else {
            ImageFactory::load($baseImage)
                ->optimize()
                ->width($targetWidth)
                ->quality($conversionQuality)
                ->save($tempDestination);
        }

        $responsiveImageHeight = ImageFactory::load($tempDestination)->getHeight();

        // Users can customize the name like they want, but we expect the last part in a certain format
        $fileName = $this->addPropertiesToFileName(
            $responsiveImagePath,
            $conversionName,
            $targetWidth,
            $responsiveImageHeight,
            $extension
        );

        $responsiveImagePath = $temporaryDirectory->path($fileName);

        rename($tempDestination, $responsiveImagePath);

        $this->filesystem->copyToMediaLibrary($responsiveImagePath, $media, 'responsiveImages');

        ResponsiveImage::register($media, $fileName, $conversionName);
    }
}
