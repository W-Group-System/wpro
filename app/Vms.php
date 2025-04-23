<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vms extends Model
{
    protected $connection = 'mysql_vms';
    protected $table = 'vms';

}
