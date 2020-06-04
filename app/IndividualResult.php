<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndividualResult extends Model
{
    protected $table = 'rankings_individualresult';
    public $timestamps = false;

    public function splits()
    {
        return $this->hasMany(IndividualResultSplit::class, 'individual_result_id');
    }
}
