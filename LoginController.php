<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use Auth;

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

    protected $provider = 'google';

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    # /home
    protected $redirectTo = '/app_roier';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver($this->provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        // $user = Socialite::driver('google')->user();
        // $authUser = $this->findOrCreateUser($user, $provider);
        // Auth::login($authUser, true);
        // return redirect($this->redirectTo);
        // //dd($user);
        // $user->token;

        // OAuth Two Providers
        //$token = $user->token;
        //$refreshToken = $user->refreshToken; // not always provided
        //$expiresIn = $user->expiresIn;

        try {
            $user = Socialite::driver($this->provider)->stateless()->user();
        } catch (\Exception $e) {
            throw $e;
        }
        // only allow people with @company.com to login
        // if(explode("@", $user->email)[1] !== 'company.com'){
        //     return redirect()->to('/');
        // }
        // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->provider        = $this->provider;
            $newUser->provider_id     = $user->id;
            $newUser->save();
            auth()->login($newUser, true);
        }
        return redirect()->to('/app_roier');
    }

    // public function findOrCreateUser($user, $provider)
    // {
    //     $authUser = User::where('provider_id', $user->id)->first();
    //     if ($authUser){
    //         return $authUser;
    //     }
    //     return User::create([
    //         'name' =>$user->name,
    //         'email' =>$user->email,
    //         'provider' => strtoupper($provider),
    //         'provider_id' => $user->id
    //     ]);

    // }
}
