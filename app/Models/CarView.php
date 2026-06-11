<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarView extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['car_id'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
