<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndividualResultSplit extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_individualresultsplit';
    public $timestamps = false;
    protected $fillable = ['time', 'distance', 'individual_result_id'];
}
