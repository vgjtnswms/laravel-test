<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Крестики-Нолики | Вход</title>
        <link rel="icon" type="image/ico" sizes="16x16" href="/favicon.ico">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite('resources/css/app.css')
    </head>
    <body class="bg-gray-100">
      <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Крестики-Нолики
                    </h1>
                    <form class="space-y-4 md:space-y-6" method="POST" action="/login">
                        @csrf
                        <div>
                            <label for="login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ваш логин</label>
                            <input type="text" name="login" id="login" maxlength="24" value="@if (!empty($login)) {{$login}} @endif" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="nagibator3000">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class=" text-sm">Сейчас онлайн: {{$onlineUserCount}}</div>
                                <div class="flex items-center h-5 ml-3"></div>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="w-full text-gray-900 bg-white hover:bg-gray-100 border border-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center justify-center">
                            <svg class="w-[14px] h-[14px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 15">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 7.5h11m0 0L8 3.786M12 7.5l-4 3.714M12 1h3c.53 0 1.04.196 1.414.544.375.348.586.82.586 1.313v9.286c0 .492-.21.965-.586 1.313A2.081 2.081 0 0 1 15 14h-3"/>
                            </svg>
                            <span class="ml-2 mb-0.5 h-5">Войти в игру</span>
                        </button>
                        @if (!empty($messages))
                        <p class="text-sm font-light text-red-500">
                            @foreach ($messages as $message)
                                {{$message}}<br>
                            @endforeach
                        </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
      </section>
    </body>
</html>
