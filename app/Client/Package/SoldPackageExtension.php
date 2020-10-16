<?php

namespace App\Client\Package;

use Illuminate\Database\Eloquent\Model;

class SoldPackageExtension extends Model
{
  protected $guarded = ['id'];

  public function Client()
  {
    return $this->belongsTo('App\Client\Client');
  }

  public function Package()
  {
    return $this->belongsTo('App\Client\Package\SoldPackage');
  }
}
