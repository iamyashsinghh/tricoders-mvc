<?php

use Illuminate\Database\Eloquent\Model as Yash;

class ABook extends Yash
{
    protected $table = 'assigned_books';
    protected $fillable = [
        'users_id',
        'book_id',
        'id',
    ];
}
