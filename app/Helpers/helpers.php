<?php namespace SimpleOMS\Helpers;

use Hashids\Hashids;
use Config;

final class Helpers{

    /***
     * Encodes an integer
     * @param $value
     * @return mixed
     */
    public static function hash($value)
    {
        if (is_numeric($value)){
            $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
            return $hashids->encode($value);
        } else {
            return false;
        }
    }

    /***
     * Decodes a hash value
     * @param $value
     * @return bool
     */
    public static function unhash($value)
    {
        if (is_null($value)){
            return false;
        } else {

            $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
            return $hashids->decode($value);
        }
    }
}

