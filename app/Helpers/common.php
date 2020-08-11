<?php
//namespace App\Helpers;

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

