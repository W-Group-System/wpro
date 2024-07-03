<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadType extends Model
{
  public function user() {
    return $this->belongsTo(User::class, 'uploaded_by');
  }
}
