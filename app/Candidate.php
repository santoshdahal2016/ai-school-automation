<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class Candidate extends Model implements ToModel
{

    public $timestamps = false;
    protected $table = 'candidates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Name', 'Email_Address', 'Scholarship','amount','currency','category','status'
    ];

    /**
     * @param array $row
     *
     * @return Model|Model[]|null
     */
    public function model(array $row)
    {
        return new Candidate([
            'Name' => $row[1], 'Email_Address' => $row[2],
            'Scholarship' => $row[3],'amount' => $row[4],
            'currency' => $row[5],  'category' => $row[6],  'status' => $row[7]
        ]);
    }
}
