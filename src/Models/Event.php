<?php

namespace Ikoncept\Fabriq\Models;

use Carbon\Carbon;
use Ikoncept\Fabriq\Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nTerm;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;

class Event extends Model
{
    use HasFactory, HasTranslatedRevisions;

    protected $fillable = ['start', 'end', 'start_time', 'end_time', 'date', 'title', 'daily_interval'];

    protected $dates = ['start', 'end'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'event';


    /**
     * Create a new factory
     */
    protected static function newFactory() : EventFactory
    {
        return EventFactory::new();
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions() : RevisionOptions
    {
        return RevisionOptions::create()
            ->registerDefaultTemplate('event-item');
    }

    /**
     * Set the date attribute
     *
     * @param array|null $value
     * @return void
     */
    public function setDateAttribute($value) : void
    {
        $this->attributes['start'] = Carbon::parse($value['start']);
        $this->attributes['end'] = Carbon::parse($value['end']);
    }

    /**
     * Get the title for the event
     *
     * @return string|null
     */
    public function getTitleAttribute()
    {
        $delimiter = $this->getDelimiter();

        return $this->translateByKey('events'. $delimiter . $this->id . $delimiter . $delimiter .'title', 'sv');
    }

    /**
     * Scope events between dates
     *
     * @param Builder $query
     * @param string ...$dates
     * @return Builder
     */
    public function scopeDateRange(Builder $query, ...$dates) : Builder
    {
        $startDate = Carbon::parse($dates[0]);
        $endDate = Carbon::parse($dates[1]);

        return $query->where('start', '>=', $startDate)
            ->orWhereNotNull('daily_interval');
    }
}
