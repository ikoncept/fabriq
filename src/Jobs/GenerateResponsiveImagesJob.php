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
    use Queueable;
    use SerializesModels;

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
                $this->setMediaProcessingStatus(false);

                return true;
            }
            $responsiveImageGenerator->generateResponsiveImages($this->media);
            $this->setMediaProcessingStatus(false);

            return true;
        } catch (Exception $exception) {
            $this->setMediaProcessingStatus(true);
            $this->fail($exception);
        }

        return true;
    }

    protected function setMediaProcessingStatus(bool $failed): void
    {
        $this->media->setCustomProperty('processing', false);
        $this->media->setCustomProperty('processing_failed', $failed);
        $this->media->save();
        MediaFinishedProcessing::dispatch($this->media);
    }
}
