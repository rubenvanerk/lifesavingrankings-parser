<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\IndividualResultSplit
 *
 * @property int $id
 * @property string $time
 * @property int $distance
 * @property int $individual_result_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit whereIndividualResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\IndividualResultSplit whereTime($value)
 */
class IndividualResultSplit extends Model
{
    protected $connection = 'rankings';
    protected $table = 'rankings_individualresultsplit';
    public $timestamps = false;
    protected $fillable = ['time', 'distance', 'individual_result_id'];
}
