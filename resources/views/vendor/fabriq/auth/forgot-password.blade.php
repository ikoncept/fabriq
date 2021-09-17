@extends('vendor.fabriq.layouts.auth')

@section('content')
    <div class="w-full mx-4 md:mx-auto md:max-w-md">
        @include('vendor.fabriq._partials.logo')
        <div class="flex flex-wrap justify-center p-8 bg-white border rounded-lg shadow-lg">
            @if (session('status'))
                <div class="w-full px-3 py-4 mb-4 text-sm text-green-700 bg-green-100 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col w-full break-words">
                <div class="mb-2 text-xl font-semibold text-gray-600 sr-only">
                    {{ __('Reset Password') }}
                </div>
                <form class="w-full" method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-2">
                        <label for="email" class="sr-only">
                            {{ __('E-Mail Address') }}:
                        </label>

                        <input id="email" placeholder="{{ __('E-Mail Address') }}" type="email"
                            class="fabriq-input w-full @error('email') border border-red-500 @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <p class="mt-2 mb-4 text-xs italic text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap">
                        <button type="submit"
                            class="w-full py-3 mt-4 fabriq-btn btn-gold">
                            {{ __('Send Password Reset Link') }}
                        </button>

                        <p class="w-full mt-8 -mb-4 text-xs text-center text-grey-dark">
                            <a class="text-gray-400 no-underline hover:text-gray-500" href="{{ route('login') }}">
                                {{ __('Back to login') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
