<?php

use Illuminate\Database\Eloquent\Model as Yash;

class Nav_data extends Yash
{
    protected $table = 'nav_data';
    protected $fillable = [
        'name',
        'link',
        'icon',
        'dropdown',
        'dropdown_data',
    ];
}
