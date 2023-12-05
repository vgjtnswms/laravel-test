<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Крестики-Нолики</title>
        <link rel="icon" type="image/ico" sizes="16x16" href="/favicon.ico">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
            // Enable pusher logging - don't include this in production
            // Pusher.logToConsole = true;
            var pusher = new Pusher('ea32f060b868783b38dc', {
              cluster: 'ap2'
            });
            var channel = pusher.subscribe('channel-game-move');
            channel.bind('event-game-move', function(data) {
              alert(JSON.stringify(data));
            });
        </script>
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="bg-gray-100 h-screen w-full text-center" style="min-height: 600px; min-width: 400px;" data-game-session="<?= $game->getId() ?>" data-game-move="<?= $gameMove ?>" data-game-side="<?= $gameSide ?>">
        <nav class="bg-white border-gray-200 dark:bg-gray-900" style="position: absolute; width: 100%;">
            <div class="{{-- max-w-screen-xl  --}}flex flex-wrap items-center justify-between mx-auto p-4">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white mb-1 cursor-pointer">Крестики-Нолики</span>
                <div class="flex items-center md:order-2">
                    @if (!empty($opponent))
                    <span class="hidden md:inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900">Соперник:</span>
                    <button {{-- style="min-width: 150px; text-align: left;"  --}} type="button" data-dropdown-toggle="language-dropdown-menu1" class="hidden md:inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900 dark:text-white rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                        @if ($gameSide == 0)
                        <svg class="w-5 h-5 mr-2 rounded-full" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="42px" height="42px" viewBox="0 0 42 42" xml:space="preserve"><path fill-rule="evenodd" d="M21.002,26.588l10.357,10.604c1.039,1.072,1.715,1.083,2.773,0l2.078-2.128 c1.018-1.042,1.087-1.726,0-2.839L25.245,21L36.211,9.775c1.027-1.055,1.047-1.767,0-2.84l-2.078-2.127 c-1.078-1.104-1.744-1.053-2.773,0L21.002,15.412L10.645,4.809c-1.029-1.053-1.695-1.104-2.773,0L5.794,6.936 c-1.048,1.073-1.029,1.785,0,2.84L16.759,21L5.794,32.225c-1.087,1.113-1.029,1.797,0,2.839l2.077,2.128 c1.049,1.083,1.725,1.072,2.773,0L21.002,26.588z"/></svg>
                        @endif
                        @if ($gameSide == 1)
                        <svg class="w-5 h-5 mr-2 rounded-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="533.333px" height="533.333px" viewBox="0 0 533.333 533.333" style="enable-background:new 0 0 533.333 533.333;" xml:space="preserve"><g><path d="M266.667,0C119.391,0,0,119.391,0,266.667c0,147.275,119.391,266.666,266.667,266.666 c147.275,0,266.667-119.391,266.667-266.666C533.333,119.391,413.942,0,266.667,0z M266.667,466.667c-110.458,0-200-89.543-200-200 c0-110.458,89.542-200,200-200c110.457,0,200,89.543,200,200C466.667,377.124,377.124,466.667,266.667,466.667z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                        @endif
                        <span>{{$opponent->login}}</span>
                    </button>
                      <!-- Dropdown -->
                    <div class="text-left z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700" id="language-dropdown-menu1">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Логин соперника:</span>
                            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">{{$opponent->login}}</span>
                        </div>
                        @if ($gameSide == 0)
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Соперник играет за:</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400 inline-flex items-center">
                                <svg class="w-3.5 h-3.5 mr-2 rounded-full" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="42px" height="42px" viewBox="0 0 42 42" xml:space="preserve"><path fill-rule="evenodd" d="M21.002,26.588l10.357,10.604c1.039,1.072,1.715,1.083,2.773,0l2.078-2.128 c1.018-1.042,1.087-1.726,0-2.839L25.245,21L36.211,9.775c1.027-1.055,1.047-1.767,0-2.84l-2.078-2.127 c-1.078-1.104-1.744-1.053-2.773,0L21.002,15.412L10.645,4.809c-1.029-1.053-1.695-1.104-2.773,0L5.794,6.936 c-1.048,1.073-1.029,1.785,0,2.84L16.759,21L5.794,32.225c-1.087,1.113-1.029,1.797,0,2.839l2.077,2.128 c1.049,1.083,1.725,1.072,2.773,0L21.002,26.588z"/></svg>
                                <span class="mb-0.5">Крестики</span>
                            </span>
                        </div>
                        @endif
                        @if ($gameSide == 1)
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Соперник играет за:</span>
                            <svg class="w-3.5 h-3.5 mr-2 rounded-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="533.333px" height="533.333px" viewBox="0 0 533.333 533.333" style="enable-background:new 0 0 533.333 533.333;" xml:space="preserve"><g><path d="M266.667,0C119.391,0,0,119.391,0,266.667c0,147.275,119.391,266.666,266.667,266.666 c147.275,0,266.667-119.391,266.667-266.666C533.333,119.391,413.942,0,266.667,0z M266.667,466.667c-110.458,0-200-89.543-200-200 c0-110.458,89.542-200,200-200c110.457,0,200,89.543,200,200C466.667,377.124,377.124,466.667,266.667,466.667z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">Нолики</span>
                        </div>
                        @endif
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Статистика:</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Победы: 0</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Поражения: 0</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Ничья: 0</span>
                        </div>
                    </div>
                    @endif
                    <span class="inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900"></span>
                    <span class="inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900">Вы:</span>
                    <button type="button" data-dropdown-toggle="language-dropdown-menu2" class="inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900 dark:text-white rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                        @if ($gameSide == 1)
                        {{-- Крестики --}}
                        <svg class="w-5 h-5 mr-2 rounded-full" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="42px" height="42px" viewBox="0 0 42 42" xml:space="preserve"><path fill-rule="evenodd" d="M21.002,26.588l10.357,10.604c1.039,1.072,1.715,1.083,2.773,0l2.078-2.128 c1.018-1.042,1.087-1.726,0-2.839L25.245,21L36.211,9.775c1.027-1.055,1.047-1.767,0-2.84l-2.078-2.127 c-1.078-1.104-1.744-1.053-2.773,0L21.002,15.412L10.645,4.809c-1.029-1.053-1.695-1.104-2.773,0L5.794,6.936 c-1.048,1.073-1.029,1.785,0,2.84L16.759,21L5.794,32.225c-1.087,1.113-1.029,1.797,0,2.839l2.077,2.128 c1.049,1.083,1.725,1.072,2.773,0L21.002,26.588z"/></svg>
                        @endif
                        @if ($gameSide == 0)
                        {{-- Нолики --}}
                        <svg class="w-5 h-5 mr-2 rounded-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="533.333px" height="533.333px" viewBox="0 0 533.333 533.333" style="enable-background:new 0 0 533.333 533.333;" xml:space="preserve"><g><path d="M266.667,0C119.391,0,0,119.391,0,266.667c0,147.275,119.391,266.666,266.667,266.666 c147.275,0,266.667-119.391,266.667-266.666C533.333,119.391,413.942,0,266.667,0z M266.667,466.667c-110.458,0-200-89.543-200-200 c0-110.458,89.542-200,200-200c110.457,0,200,89.543,200,200C466.667,377.124,377.124,466.667,266.667,466.667z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                        @endif
                        <span>{{$user->login}}</span>
                      </button>
                      <!-- Dropdown -->
                      <div class="text-left z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700" id="language-dropdown-menu2">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Ваш логин:</span>
                            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">{{$user->login}}</span>
                        </div>
                        @if ($gameSide == 0)
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Вы играете за:</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400 inline-flex items-center">
                                <svg class="w-3 h-3 mr-2 rounded-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="533.333px" height="533.333px" viewBox="0 0 533.333 533.333" style="enable-background:new 0 0 533.333 533.333;" xml:space="preserve"><g><path d="M266.667,0C119.391,0,0,119.391,0,266.667c0,147.275,119.391,266.666,266.667,266.666 c147.275,0,266.667-119.391,266.667-266.666C533.333,119.391,413.942,0,266.667,0z M266.667,466.667c-110.458,0-200-89.543-200-200 c0-110.458,89.542-200,200-200c110.457,0,200,89.543,200,200C466.667,377.124,377.124,466.667,266.667,466.667z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                <span class="mb-0.5">Нолики</span>
                            </span>
                        </div>
                        @endif
                        @if ($gameSide == 1)
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Вы играете за:</span>
                            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">
                                <svg class="w-3 h-3 mr-2 rounded-full" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" width="42px" height="42px" viewBox="0 0 42 42" xml:space="preserve"><path fill-rule="evenodd" d="M21.002,26.588l10.357,10.604c1.039,1.072,1.715,1.083,2.773,0l2.078-2.128 c1.018-1.042,1.087-1.726,0-2.839L25.245,21L36.211,9.775c1.027-1.055,1.047-1.767,0-2.84l-2.078-2.127 c-1.078-1.104-1.744-1.053-2.773,0L21.002,15.412L10.645,4.809c-1.029-1.053-1.695-1.104-2.773,0L5.794,6.936 c-1.048,1.073-1.029,1.785,0,2.84L16.759,21L5.794,32.225c-1.087,1.113-1.029,1.797,0,2.839l2.077,2.128 c1.049,1.083,1.725,1.072,2.773,0L21.002,26.588z"/></svg>
                                <span class="mb-0.5">Крестики</span>
                            </span>
                        </div>
                        @endif
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white font-medium">Статистика:</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Победы: 0</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Поражения: 0</span>
                            <span class="block text-sm text-gray-500 truncate dark:text-gray-400">Ничья: 0</span>
                        </div>
                        <ul class="py-2 font-medium" role="none">
                          <li>
                            <a href="/logout" id="link-logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                              <div class="inline-flex items-center">
                                <svg class="w-[12px] h-[12px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h11m0 0-4-4m4 4-4 4m-5 3H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h3"/>
                                </svg>
                                <span class="ml-2 mb-0.5">Выйти из игры</span>
                              </div>
                            </a>
                          </li>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- <div class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400 " style="position: absolute; border: 1px solid #ccc; width: 100%;">
                <svg class="w-2.5 h-2.5 mr-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"/>
                </svg>
            </div> --}}
            <div style="position: absolute; width: 100%; padding: 0px; margin: 0px;">
                @if (empty($opponent))
                <span class="w-full bg-blue-100 hover:bg-blue-200 cursor-pointer text-blue-800 text-xs font-medium inline-flex items-center text-center px-2.5 py-1 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                    <span class="mx-auto inline-flex">
                        <svg aria-hidden="true" class="w-4 h-4 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span>Ожидание подключения соперника</span>
                    </span>
                </span>
                @endif
                @if (!empty($opponent) && $gameMove == 0)
                <span class="w-full bg-blue-100 hover:bg-blue-200 cursor-pointer text-blue-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400 ">
                    <span class="mx-auto inline-flex">
                        <svg aria-hidden="true" class="w-4 h-4 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span>Ожидание завершения хода соперника</span>
                    </span>
                </span>
                @endif
                @if (!empty($opponent) && $gameMove == 1)
                <span class="w-full bg-green-100 hover:bg-green-200 cursor-pointer text-green-800 text-xs font-medium inline-flex items-center px-2.5 py-1 rounded dark:bg-gray-700 dark:text-green-400 border border-green-400 ">
                    <span class="mx-auto inline-flex">
                        <span>Ваш ход</span>
                    </span>
                </span>
                @endif
            </div>
        </nav>
        <main class="conteiner flex items-center justify-center bg-gray-100 w-full h-screen" style="min-height: 600px; min-width: 400px;">
            <div class="grid grid-cols-3 gap-1">
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="1" style="position: relative;"><label id="label1" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(1) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="2" style="position: relative;"><label id="label2" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(2) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="3" style="position: relative;"><label id="label3" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(3) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="4" style="position: relative;"><label id="label4" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(4) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="5" style="position: relative;"><label id="label5" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(5) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="6" style="position: relative;"><label id="label6" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(6) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="7" style="position: relative;"><label id="label7" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(7) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="8" style="position: relative;"><label id="label8" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(8) ?></label></div>
                <div class="cell-action cell bg-white border border-gray-200 p-12 text-4xl hover:shadow rounded-lg text-center cursor-pointer" data-cell-number="9" style="position: relative;"><label id="label9" style="position: absolute; margin: 0px; padding: 0px; top:0px; left:0px;"><?= $game->getCellValue(9) ?></label></div>
            </div>
        </main>
        <footer class="hidden fixed bottom-0 left-0 z-20 w-full p-4 bg-white border-gray-200 md:flex md:items-center md:justify-center md:p-6 dark:bg-gray-800 dark:border-gray-600">
            <div class="w-full items-center justify-between md:flex md:w-auto md:order-1 text-center" id="navbar-user">
                <div class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">PHP v{{ PHP_VERSION }}</div>
                <div class="text-sm text-gray-400 ml-2 mr-2">+</div>
                <div class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">Laravel v{{ Illuminate\Foundation\Application::VERSION }}</div>
                <div class="text-sm text-gray-400 ml-2 mr-2">+</div>
                <div class="text-sm text-gray-600 cursor-pointer hover:text-gray-900" id="jquery-vesion">jQuery</div>
                <div class="text-sm text-gray-400 ml-2 mr-2">+</div>
                <div class="text-sm text-gray-600 cursor-pointer hover:text-gray-900">Tailwind CSS (Flowbite)</div>
            </div>
        </footer>
        <script>
            $(document).ready(function() {
                $("#jquery-vesion").html("JQuery v" + jQuery.fn.jquery);
                $('#link-logout').confirm({
                    title: 'Вы уверены что хотите выйти из игры?',
                    content: 'Если вы покинете игру Вам будет засчитано техническое поражение.',
                    type: 'orange',
                    typeAnimated: true,
                    boxWidth: '500px',
                    useBootstrap: false,
                    draggable: false,
                    buttons: {
                        tryAgain: {
                            text: 'Выйти из игры',
                            btnClass: 'btn-orange',
                            action: function() {
                                window.location.href = "/logout";
                            }
                        },
                        close: {
                            text: 'Остаться',
                            btnClass: 'btn-white',
                            action: function() {}
                        },
                    }
                });
                var canMove = true;
                $('.cell-action').click(function() {
                    if (canMove == false) {
                        return;
                    }
                    // Блокировка остальных кнопок
                    canMove = false;
                    // Отправляем запрос на сервер
                    $.ajax({
                        url: "/game-move",
                        type: "GET",
                        data: {
                            "cell-number": $(this).data('cell-number'),
                        },
                        success: function(response) {
                            // Получения данных для отправки
                            console.log(response);
                            // Отрисовка
                        },
                    });
                    
                    // Вывод данных на экран и передача хода
                    console.log("Cell Number: " + cellNumber);
                });
            });
        </script>
    </body>
</html>