<?php

use Illuminate\Database\Eloquent\Model as Yash;

class Msg extends Yash
{
    protected $table = 'msg';
    protected $fillable = [
        'name',
        'email',
        'msg',
    ];
}
