@extends('layouts.auth')

@section('content')
    <div class="w-full mx-4 md:mx-auto md:max-w-md">
        @include('_partials.logo')
        <div class="flex flex-wrap justify-center p-8 bg-white border rounded-lg shadow-lg">
            @if (session('status'))
                <div class="w-full px-3 py-4 mb-4 text-sm text-green-700 bg-green-100 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col w-full break-words">
                <div class="text-xl font-semibold text-gray-100 uppercase sr-only">
                    {{ __('Reset Password') }}
                </div>
                <form class="w-full" method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-4">
                        <label for="password" class="block mb-1 text-sm text-gray-800 fabriq-label">
                            {{ __('E-Mail Address') }}
                        </label>
                        <input type="hidden" name="email" value="{{ Request::get('email') }}">

                        <input placeholder="{{ __('E-Mail Address') }}" id="email" type="email"
                            class="fabriq-input w-full @error('email') border-red-500 @enderror" name="email_dis"
                            value="{{ Request::get('email') }}" autofocus disabled>

                        @error('email')
                            <p class="mt-4 text-xs italic text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex-wrap mb-4">
                        <label for="password" class="mb-1 text-sm text-gray-800 fabriq-label">
                            {{ __('Password') }}
                        </label>

                        <input id="password" type="password"
                            placeholder="Nytt lösenord"
                            class="fabriq-input w-full @error('password') border-red-500 @enderror" name="password"
                            required autocomplete="new-password">

                        @error('password')
                            <p class="mt-2 mb-4 text-xs italic text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password-confirm" class="mb-1 text-sm text-gray-800 fabriq-label">
                            {{ __('Confirm Password') }}
                        </label>

                        <input id="password-confirm" type="password" class="w-full fabriq-input" name="password_confirmation"
                            placeholder="Bekräfta det nya lösenordet"
                            required autocomplete="new-password">
                    </div>

                    <div class="flex flex-wrap">
                        <button type="submit" class="w-full py-3 fabriq-btn btn-gold">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                    <p class="w-full mt-8 -mb-4 text-xs text-center text-grey-dark">
                        <a class="text-gray-400 no-underline hover:text-gray-500" href="{{ route('login') }}">
                            {{ __('Back to login') }}
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection
