<?php

namespace Ikoncept\Fabriq\Models;

use Carbon\Carbon;
use Ikoncept\Fabriq\ContentGetters\ImageGetter;
use Ikoncept\Fabriq\Database\Factories\ArticleFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;

class Article extends Model
{
    use HasFactory, HasTranslatedRevisions, BroadcastsEvents;

    public const RELATIONSHIPS = ['template', 'template.fields', 'slugs'];

    protected $guarded = ['content'];

    protected $dates = ['publishes_at', 'unpublishes_at'];

    public array $templateSluggable = ['title'];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'article';

    /**
     * Create a new factory.
     */
    protected static function newFactory() : ArticleFactory
    {
        return ArticleFactory::new();
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions() : RevisionOptions
    {
        return RevisionOptions::create()
            ->registerDefaultTemplate('article')
            ->registerSpecialTypes(['image'])
            ->registerGetters([
                'image' => 'getImages',
            ]);
    }

    /**
     * Relation for slugs.
     *
     * @return MorphMany
     */
    public function slugs() : MorphMany
    {
        return $this->morphMany(Fabriq::getFqnModel('slug'), 'model');
    }

    /**
     * Getter for images.
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getImages(RevisionMeta $meta)
    {
        return ImageGetter::get($meta, $this->isPublishing);
    }

    public function getIsPublishedAttribute() : bool
    {
        if (! isset($this->attributes['publishes_at'])) {
            return false;
        }

        if ($this->attributes['has_unpublished_time'] && now()->isAfter(Carbon::parse($this->attributes['unpublishes_at']))) {
            return false;
        }

        if (now()->isAfter(Carbon::parse($this->attributes['publishes_at']))) {
            return true;
        }

        return false;
    }

    protected function getTimeZone() : string
    {
        $header = request()->header('X-TIMEZONE', 'Europe/Stockholm');
        if (is_array($header)) {
            return 'Europe/Stockholm';
        }

        return $header;
    }

    /**
     * Set publishes at attribute.
     *
     * @param string|null $value
     * @return void
     */
    public function setPublishesAtAttribute($value) : void
    {
        ($value) ? $this->attributes['publishes_at'] = Carbon::parse($value, $this->getTimeZone())->shiftTimezone('UTC')->toDateTimeString() : $this->attributes['publishes_at'] = null;
    }

    /**
     * Set publishes at attribute.
     *
     * @param string|null $value
     * @return void
     */
    public function setUnPublishesAtAttribute($value) : void
    {
        ($value) ? $this->attributes['unpublishes_at'] = Carbon::parse($value, $this->getTimeZone())->shiftTimezone('UTC')->toDateTimeString() : $this->attributes['unpublishes_at'] = null;
    }

    /**
     * Search for articles.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search) : Builder
    {
        return $query->whereLike(['name', 'publishes_at'], $search);
    }

    /**
     * Scope for published articles.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query) : Builder
    {
        return $query->where('publishes_at', '<=', now())
            ->where(function (Builder $query) {
                $query->where('unpublishes_at', '>=', now())
                    ->orWhereNull('unpublishes_at');
            })
            ->orderBy('publishes_at', 'DESC');
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @param  string  $event
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($event)
    {
        $prefix = config('broadcasting.connections.pusher.key');

        return [new Channel($prefix.'-article'), new Channel('article.'.$this->id)];
    }
}
