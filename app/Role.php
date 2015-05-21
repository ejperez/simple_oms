<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    public function users()
    {
        return $this->hasMany('SimpleOMS\User', 'role_id', 'id');
    }

}
