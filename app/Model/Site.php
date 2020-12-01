<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public $timestamps = false;
    //

    public function getHost(){
        $s = str_replace(['http://', 'https://'], ['', ''], $this->url);
        $a = explode('/', $s);
        return $a[0];
    }
}
