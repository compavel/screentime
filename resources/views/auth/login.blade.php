<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-white">Welcome Back</h2>
        <p class="text-gray-500 text-sm mt-1">Login to your account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                   class="w-full bg-gray-800/50 border border-gray-700/50 text-white text-sm rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none placeholder-gray-600">
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block text-xs font-medium text-gray-400 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-gray-800/50 border border-gray-700/50 text-white text-sm rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none placeholder-gray-600">
            @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-600 bg-gray-800 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                <span class="text-xs text-gray-500">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg px-4 py-2.5 mt-6 transition">
            Log in
        </button>

        <p class="text-center text-xs text-gray-500 mt-6">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 transition">Register</a>
        </p>
    </form>
</x-guest-layout>
