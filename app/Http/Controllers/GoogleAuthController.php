<?php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name'      => $googleUser->name,
                'google_id' => $googleUser->id,
                'avatar'    => $googleUser->avatar,
            ]);

            Auth::login($user);

            return redirect('/produk');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }
}