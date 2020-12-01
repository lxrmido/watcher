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

    public function getAppUrl($appType){
        switch ($appType) {
            case 'gkml':
                return $this->url . '/gkmlpt/index';
            case 'hdjl':
                if ($this->id == 2) {
                    return 'http://lygl.gd.gov.cn/main';
                }
                if ($this->id == 200001) {
                    return 'http://jyj.gz.gov.cn/hdjlpt';
                }
                if ($this->service_area_id == 754) {
                    return 'http://www.gdjinping.gov.cn/hdjlpt';
                }
                return $this->url . '/hdjlpt';
            case 'yjzj':
                return $this->url . '/hdjlpt/yjzj/api/captcha';
            default:
                return $this->url;
        }
    }
}
