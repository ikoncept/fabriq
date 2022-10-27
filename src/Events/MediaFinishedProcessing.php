<?php

namespace Ikoncept\Fabriq\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaFinishedProcessing implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var mixed
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $media
     * @return void
     */
    public function __construct($media)
    {
        $this->media = $media;
    }

    public function broadcastAs(): string
    {
        return 'media-finished-processing';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $prefix = config('fabriq.ws_prefix');

        return [new Channel($prefix.'.media'), new Channel($prefix.'.media.'.$this->media->model_id)];
    }

    public function broadcastWith(): array
    {
        return [
            'image_id' => $this->media->model_id,
            'media_id' => $this->media->id,
            'processing' => (bool) $this->media->getCustomProperty('processing'),
            'processing_failed' => (bool) $this->media->getCustomProperty('processing_failed'),
        ];
    }
}
