<?php

namespace App\Client\Package;

use Illuminate\Database\Eloquent\Model;

class SoldPackages extends Model
{
  protected $guarded = ['id'];

  public function Client()
  {
    return $this->belongsTo('App\Client\Client','clientId');
  }

  public function Seller()
  {
    return $this->belongsTo('App\Employee','saleBy');
  }

  public function Extensions()
  {
    return $this->hasMany('App\Client\Package\SoldPackageExtension', 'soldPackageId');
  }
  public function Benefits()
  {
    return $this->hasMany('App\Client\Package\SoldPackageBenefits', 'soldPackageId');
  }

}
