<?php

namespace App\Traits;

use App\Jobs\ModelCountActivityJob;
use App\Models\Views;

trait ActivityCountable
{
    private static $retrievedEventTriggered = false;
    public function views()
    {
        return $this->morphMany(Views::class, 'viewable');
    }

    protected static function bootActivityCountable()
    {

        // Event for created
        static::created(function ($model) {
            static::handleActivityCountEvent($model, 'Record Created.');
        });

        // Event for updated
        static::updated(function ($model) {
            static::handleActivityCountEvent($model, 'Record Updated.');
        });

        // Event for deleted
        static::deleted(function ($model) {
            static::handleActivityCountEvent($model, 'Record Deleted.');
        });

        // Event for retrieved (read)
        static::retrieved(function ($model) {
            if (!self::$retrievedEventTriggered) {
                static::$retrievedEventTriggered = true;
                static::handleActivityCountEvent($model, 'Record Retrieved.');
            }
        });
    }

    protected static function handleActivityCountEvent($model, $activity)
    {
        if (config('app_settings.queue_start')) {
            dispatch(new ModelCountActivityJob($model, $activity, auth()->id()));
        } else {
            modelCount($model, $activity);
        }
    }
}
