<?php

namespace App\Models\Traits;

use \Ramsey\Uuid\Uuid as RamsyUuid;

trait Uuid {
    public static function boot() {
        parent::boot();
        static::creating(function($obj) {
            $obj->id = RamsyUuid::uuid4()->toString();
        });
    }
}

