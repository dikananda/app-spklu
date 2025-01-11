<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPKLU Map Bali</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="font-[sans-serif]">
    <div class="min-h-screen flex flex-col items-center justify-center py-6 px-4">
        <div class="grid md:grid-cols-2 items-center gap-6 max-w-6xl w-full">
            <div class="border border-gray-300 rounded-lg p-6 max-w-md shadow-[0_2px_22px_-4px_rgba(93,96,127,0.2)] max-md:mx-auto">
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div class="mb-8">
                        <h3 class="text-gray-800 text-3xl font-bold">SPKLU MAP Bali - Admin</h3>
                        <p class="text-gray-500 text-sm mt-4 leading-relaxed">Masuk untuk mengelola marker yang ada.</p>
                    </div>

                    <div>
                        <label for="email" class="text-gray-800 text-sm mb-2 block">Email</label>
                        <div class="relative flex items-center">
                            <input id="email" name="email" type="email" required class="w-full text-sm text-gray-800 border border-gray-300 pl-4 pr-10 py-3 rounded-lg outline-blue-600" placeholder="Enter email" value="{{ old('email') }}" autofocus autocomplete="username" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="text-gray-800 text-sm mb-2 block">Password</label>
                        <div class="relative flex items-center">
                            <input id="password" name="password" type="password" required class="w-full text-sm text-gray-800 border border-gray-300 pl-4 pr-10 py-3 rounded-lg outline-blue-600" placeholder="Enter password" autocomplete="current-password" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 shrink-0 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                            <label for="remember_me" class="ml-3 block text-sm text-gray-800">
                                Remember me
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-sm">
                                <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-semibold">
                                    Forgot your password?
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="!mt-8">
                        <button type="submit" class="w-full shadow-xl py-2.5 px-4 text-sm tracking-wide rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>
            <div class="max-md:mt-8">
                <img src="https://readymadeui.com/login-image.webp" class="w-full aspect-[71/50] max-md:w-4/5 mx-auto block object-cover" alt="Dining Experience" />
            </div>
        </div>
    </div>
</body>
</html>