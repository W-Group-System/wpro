<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class HmoAttachment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = "hmo_attachments";
    protected $fillable = ['hmo_id', 'path'];
}
