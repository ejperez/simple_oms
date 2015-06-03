<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

    public static function getValue($key){
        return self::find($key)->value;
	}
}
