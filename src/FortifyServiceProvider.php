<?php

namespace Ikoncept\Fabriq;

use Ikoncept\Fabriq\Actions\Fortify\CreateNewUser;
use Ikoncept\Fabriq\Actions\Fortify\ResetUserPassword;
use Ikoncept\Fabriq\Actions\Fortify\UpdateUserPassword;
use Ikoncept\Fabriq\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
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
        Fortify::loginView(function () {
            /** @var view-string $viewString **/
            $viewString = 'vendor.fabriq.auth.login';
            return view($viewString);
        });

        Fortify::requestPasswordResetLinkView(function () {
            /** @var view-string $viewString **/
            $viewString = 'vendor.fabriq.auth.forgot-password';
            return view($viewString);
        });

        Fortify::resetPasswordView(function ($request) {
            /** @var view-string $viewString **/
            $viewString = 'vendor.fabriq.auth.reset-password';
            return view($viewString, ['request' => $request]);
        });

        // Fortify::verifyEmailView(function($request) {
        //     return view('auth.reset-password', ['request' => $request]);
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $user = config('fabriq.models.user')::where('email', $request->email)->first();

            // Cookie::queue('x-bajs', json_encode(['admin', 'apa']), 2000, '.fabriq-cms.test', false, false);
            // 'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
            if ($user &&
                Hash::check($request->password, $user->password)) {
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
    }
}
