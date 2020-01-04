<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'memo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    public  $timestamps = false;
}