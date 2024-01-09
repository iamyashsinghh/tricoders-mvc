<?php

use Illuminate\Database\Eloquent\Model as Yash;

class Books extends Yash
{
    protected $table = 'books';
    protected $fillable = [
        'name',
        'cost',
        'id',
    ];
}
