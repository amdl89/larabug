<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Fortify::registerView(function ()
        {
            return inertia('Auth/Register');
        });

        Fortify::loginView(function ()
        {
            return inertia('Auth/Login');
        });

        Fortify::confirmPasswordView(function ()
        {
            return inertia('Auth/Password/Confirm');
        });

        Fortify::requestPasswordResetLinkView(function ()
        {
            return inertia('Auth/Password/Request');
        });

        Fortify::resetPasswordView(function (Request $request)
        {
            return inertia('Auth/Password/Reset', [
                'token' => $request->route('token'),
                'email' => $request->email,
            ]);
        });

        $this->app->instance(
            LoginResponse::class,
            new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    return redirect()->intended(
                        RouteServiceProvider::defaultRedirect(
                            $request->user()
                        )
                    );
                }
            }
        );

        Fortify::authenticateUsing(function (Request $request)
        {
            $user = User::query()
                ->where('email', $request->email)
                ->orWhere('username', $request->email)
                ->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            )
            {
                return $user;
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request)
        {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request)
        {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
