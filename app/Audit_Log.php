<?php namespace SimpleOMS;

use Illuminate\Database\Eloquent\Model;

class Audit_Log extends Model {

	protected $table = 'audit_logs';

    protected $fillable = ['user_id', 'activity', 'data'];

    public function user()
    {
        return $this->hasOne('SimpleOMS\User', 'user_id', 'id');
    }
}
