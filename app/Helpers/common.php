<?php
//namespace App\Helpers;
use Laravolt\Avatar\Facade as Avatar;
function notifyToast($type,$title,$message){
  session(['notifyToast' => true]);
  session(['notifyTitle' => $title]);
  session(['notifyMessage' => $message]);
  session(['notifyType' => $type]);
}

function forgetNotifyToast(){
  session()->forget('notifyToast');
  session()->forget('notifyTitle');
  session()->forget('notifyMessage');
  session()->forget('notifyTitle');
  return NULL;
}

function inr($number)
{
  $decimal = (string)($number - floor($number));
  $money = floor($number);
  $length = strlen($money);
  $delimiter = '';
  $money = strrev($money);

  for ($i = 0; $i < $length; $i++) {
    if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
      $delimiter .= ',';
    }
    $delimiter .= $money[$i];
  }

  $result = strrev($delimiter);
  $decimal = preg_replace("/0\./i", ".", $decimal);
  $decimal = substr($decimal, 0, 3);

  if ($decimal != '0') {
    $result = $result . $decimal;
  }

  return '₹'.$result;
}

function avatar($text){
  return Avatar::create($text)->toBase64();
}


function readableDate($date){
  return \Carbon\Carbon::parse($date)->format('d M Y h:i:s A');
}

function getVarName(&$var) {
  $tmp = $var; // store the variable value
  $var = '_$_%&33xc$%^*7_r4'; // give the variable a new unique value
  $name = array_search($var, $GLOBALS); // search $GLOBALS for that unique value and return the key(variable)
  $var = $tmp; // restore the variable old value
  return $name;
}

