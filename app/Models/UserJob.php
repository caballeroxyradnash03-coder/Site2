<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserJob extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // The instructor-specified table name for user jobs.
    protected $table = 'tbluserjob';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'jobid';

    /**
     * Indicates if the model should be timestamped.
     * Set to false if your table does not have created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;
}

