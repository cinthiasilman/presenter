<?php

namespace App\Units\Auth\Http\Controllers\Social;

use Auth;
use Socialite;
use App\Domains\Users\User;

class GithubController extends SocialController
{
    /**
     * Redirect to Github auth provider.
     * @return mixed
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Catch the user information from Github.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('github')->user();
        } catch (Exception $e) {
            return Redirect::to('auth/github');
        }

        $authUser = $this->findOrCreateUser($user);

        Auth::login($authUser, true);

        return view('home::home');
    }

    /**
     * Find an existing user or create one.
     * @param $user
     * @return static
     */
    private function findOrCreateUser($user)
    {
        if ($authUser = User::where('github_id', $user->id)->first()) {
            return $authUser;
        }

        return User::create([
            'name'      => $user->name,
            'email'     => $user->email,
            'github_id' => $user->id,
            'avatar'    => $user->avatar,
        ]);
    }
}