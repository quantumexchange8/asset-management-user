<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BrokerAccount extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'broker_id',
        'broker_login',
        'broker_capital',
        'master_password',
        'status'
    ];

    // Relations
    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class, 'broker_id', 'id');
    }
}
