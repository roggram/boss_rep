<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model {
    protected $table = 'message';
    protected $dates = ['created_at', 'updated_at'];

    public function getAttribute($key) {
        return parent::getAttribute(Str::snake($key));
    }
    public function setAttribute($key, $value){
        return parent::setAttribute(Str::snake($key), $value);
    }
}