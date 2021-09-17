@extends('vendor.fabriq.layouts.auth')

@section('content')
    <div class="w-full mx-4 md:mx-auto md:max-w-md">
        @include('vendor.fabriq._partials.logo')
        <div class="flex flex-wrap justify-center p-8 bg-white border rounded-lg shadow-lg">
            <div class="w-full">
                <div class="flex flex-col break-words">
                    <form class="w-full" method="POST" action="{{ route('login') }}">
                        @csrf
                        @if (session('status'))
                            <div class="px-3 py-4 mb-4 text-sm text-green-700 bg-green-100 rounded" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('permissionError'))
                            <div class="px-3 py-4 mb-4 text-sm text-red-700 bg-red-100 rounded" role="alert">
                                {{ session('permissionError') }}
                            </div>
                        @endif

                        <div class="mb-6">
                            <label for="email" class="sr-only">
                                {{ __('E-Mail Address') }}:
                            </label>
                            <input type="email" class="fabriq-input w-full  @error('email') border-red-500 @enderror"
                                name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required
                                autocomplete="email" autofocus>

                            @error('email')
                                <p class="mt-2 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mb-6 ">
                            <label for="password" class="sr-only">
                                {{ __('Password') }}:
                            </label>

                            <input placeholder="{{ __('Password') }}" id="password" type="password"
                                class="fabriq-input w-full @error('password') border-red-500 @enderror" name="password"
                                required>

                            @error('password')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex mb-2">
                            <input type="hidden" name="remember" id="remember" value="true">
                        </div>
                        <div class="flex flex-wrap items-center">
                            <button type="submit" class="w-full py-4 antialiased leading-none fabriq-btn btn-royal">
                                {{ __('Login') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="mt-2 ml-auto text-sm text-gray-500 no-underline whitespace-no-wrap transition-colors duration-150 hover:text-gray-600"
                                    href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif

                            {{-- @if (Route::has('register'))
                                <p class="w-full mt-8 -mb-4 text-xs text-center text-gray-100">
                                    {{ __("Don't have an account?") }}
                                    <a class="text-blue-500 no-underline hover:text-blue-700"
                                        href="{{ route('register') }}">
                                        {{ __('Register') }}
                                    </a>
                                </p>
                            @endif --}}
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endsection
