<?php

use Illuminate\Database\Eloquent\Model as Yash;

class User extends Yash
{
    public $modal;
    protected $fillable = [
        'fname',
        'lname',
        'username',
        'email',
        'contact',
        'member',
        'password',
    ];
}
