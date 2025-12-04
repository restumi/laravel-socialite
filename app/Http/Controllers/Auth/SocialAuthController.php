<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try{
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e){
            Log::info(['GOOGLE ERRRORORRRRR' => $e]);
            return redirect('/login')->withErrors(['social' => 'gagal login dengan google']);
        }

        $user = User::where('google_id', $googleUser->id)->first();

        if(!$user){
            $user = User::where('email', $googleUser->email)->first();

            if(!$user){
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(rand(1000000000, 9999999999))
                ]);
            } else {
                $user->update(['google_id' => $googleUser->id]);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try{
            $facebookUser = Socialite::driver('facebook')->user();
        }catch(Exception $e){
            Log::info(['FACEBOOK ERRRORORRRRR' => $e]);
            return redirect('/login')->withErrors(['social' => 'gagal login dengan facebook']);
        }

        $user = User::where('facebook_id', $facebookUser->id)->first();

        if(!$user){
            $user = User::where('email', $facebookUser->email)->first();

            if(!$user){
                $user = User::create([
                    'name' => $facebookUser->name,
                    'email' => $facebookUser->email,
                    'facebook_id' => $facebookUser->id,
                    'password' => Hash::make(rand(10000000000, 9999999999))
                ]);
            } else {
                $user->update(['facebook_id' => $facebookUser->id]);
            }
        }

        Auth::login($user);
        return redirect()->intended('/dashboard');
    }
}
