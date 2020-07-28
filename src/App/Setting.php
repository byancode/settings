<?php

namespace Byancode\Settings\App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    
    protected $table = 'settings';

    protected $fillable = [
        'key', 
        'value'
    ];

    protected $casts = [
        'value' => 'string',
    ];

    function getValueAttribute($value) {
        return \json_decode($value, true) ?? $value;
    }

    function setValueAttribute($value) {
        return \json_encode($value);
    }
}
