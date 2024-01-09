<?php

class Contact extends Controller
{
    public function index($name = '', $name2 = '')
    {
        echo "$name <br/>";
        echo $name2;
    }

    public function phone()
    {
        echo 'hello World';
    }
}
