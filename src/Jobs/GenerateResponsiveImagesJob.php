<?php

namespace Ikoncept\Fabriq\Jobs;

use Exception;
use Ikoncept\Fabriq\Events\MediaFinishedProcessing;
use Ikoncept\Fabriq\Services\ResponsiveImageGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GenerateResponsiveImagesJob implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;
    use Queueable;

    protected Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function handle(): bool
    {
        /** @var \Ikoncept\Fabriq\Services\ResponsiveImageGenerator $responsiveImageGenerator */
        $responsiveImageGenerator = app(ResponsiveImageGenerator::class);

        try {
            if (config('fabriq.enable_remote_image_processing')) {
                $responsiveImageGenerator->generateResponsiveImagesViaLambda($this->media);

                $this->media->setCustomProperty('processing', false);
                $this->media->setCustomProperty('processing_failed', false);
                $this->media->save();
                MediaFinishedProcessing::dispatch($this->media);

                return true;
            }
            $responsiveImageGenerator->generateResponsiveImages($this->media);

            return true;
        } catch (Exception $exception) {
            $this->media->setCustomProperty('processing', false);
            $this->media->setCustomProperty('processing_failed', true);
            $this->media->save();
            MediaFinishedProcessing::dispatch($this->media);
            $this->fail($exception);
        }

        return true;
    }
}
