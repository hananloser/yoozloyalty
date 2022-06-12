<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UUids
{

    /**
    * Boot function from Laravel.
    */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            }
        });
    }

    /**
    * Get the value indicating whether the IDs are incrementing.
    *
    * @return bool
    */
    public function getIncrementing()
    {
        return false;
    }

    /**
    * Get the auto-incrementing key type.
    *
    * @return string
    */
    public function getKeyType()
    {
        return 'string';
    }

}
