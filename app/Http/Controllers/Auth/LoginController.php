<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Login
    public function showLoginForm(){
      $pageConfigs = [
          'bodyClass' => "bg-full-screen-image",
          'blankPage' => true
      ];

      return view('/auth/login', [
          'pageConfigs' => $pageConfigs
      ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

  /**
   * The user has been authenticated.
   *
   * @return mixed
   */
  protected function authenticated(Request $request, $user)
  {
    Auth::logoutOtherDevices(request('password'));
    if(Auth::user()->login_revoked){
      Session::flash('message', '');
      $request->session()->invalidate();
      return Redirect::route('login',['revoked'])->with(['message'=>'Login Revoked']);
    }
    $ipData = Http::get('http://api.ipstack.com/'. request()->getClientIp() .'?access_key=4326e660ef68ae733ba724dace0ff95c')->json();
    $agent = new \Jenssegers\Agent\Agent();
    $platform = $agent->platform();
    $browser = $agent->browser();
    Auth::user()->LoginLog()->create([
      'ip' => request()->getClientIp(),
      'time'=> Carbon::now(),
      'browser'=> $browser . ' - '.$agent->version($browser),
      'platform'=> $platform . ' - '.$agent->version($platform),
      'device'=> $agent->deviceType(),
      'location'=> $ipData['city'].', '.$ipData['country_name']. ', '.$ipData['zip'],
      'long_lat'=> $ipData['longitude'].', '.$ipData['latitude'],
    ]);
  }
}
