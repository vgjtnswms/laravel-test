<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use App\Models\User;
use App\Models\Game;

class LoginController extends Controller
{
    /**
     * TODO: Сделать вывод ошибок при подключении к игре через сессии
     * 
     * Вход и создание игровой сессии
     * @param \Illuminate\Http\Request $request
     */
    public function login(Request $request): Redirector|RedirectResponse|View|Factory
    {   
        if (Auth::check()) {
            return $this->successLogin($request);
        }

        if ($request->isMethod('POST')) {
            $login = $request->get('login');
            // Валидация данных и проверка на возможность войти в игру (максимум 2 игрока одновременно)
            $messages = $this->checkDataState($login);
            if (!empty($messages)) {
                return view('login')->with([
                    'onlineUserCount' => Game::getOnlineUserCount(),
                    'login' => $login,
                    'messages' => $messages
                ]);
            }
            // Проверка существования пользователя (если пользователя нет, создаем)
            $user = User::where('login', $login)->take(1)->first();
            if (!$user) {
                $user = User::create(['login' => $login]);
            }
            if (!$user) {
                return view('login')->with([
                    'onlineUserCount' => Game::getOnlineUserCount(),
                    'login' => $login,
                    'messages' => ['Произошла техническая ошибка. Попробуйте позже.']
                ]);
            }
            // Аутентифицируемся и подключаемся к игре (или создаем новую игру)
            $authResult = Auth::loginUsingId($user->getId());
            $startResult = Game::start($user->getId());
            if ($authResult && $startResult) {
                return $this->successLogin($request);
            }
            // Вывод ошибки, если не получилось создать игру или аутентифицироваться
            return view('login')->with([
                'onlineUserCount' => Game::getOnlineUserCount(),
                'login' => $login,
                'messages' => ['Произошла техническая ошибка. Попробуйте позже.']
            ]);
        }
        
        return view('login')->with([
            'onlineUserCount' => Game::getOnlineUserCount()
        ]);
    }

    /**
     * Выход и завершение игровой сессии
     * @param \Illuminate\Http\Request $request
     */
    public function logout(Request $request): Redirector|RedirectResponse
    {
        Game::finish();
        Auth::logout();
        return redirect('/login');
    }

    /**
     * Проверка состояния данных перед созданием игровой сессии.
     */
    private function checkDataState(string|null $login): array
    {
        $messages = [];
        if (empty($login)) {
            $messages[] = 'Логин не может быть пустым.';
        }
        if (!empty($login) && mb_strlen($login) < 3) {
            $messages[] = 'Логин должен содержать не менее 3 символов.';
        }
        if (!empty($login) && mb_strlen($login) > 24) {
            $messages[] = 'Логин не должен превышать 24 символа в длину.';
        }
        if (!Game::canStart()) {
            $messages[] = 'Вы не можете войти в игру, так как в ней не может быть одновременно более двух человек.';
        }
        if (!empty($login) && Game::isLockedLogin($login)) {
            $messages[] = 'Данный логин занят.';
        }
        return $messages;
    }

    /**
     * Действия на успешный вход в игру.
     */
    private function successLogin(Request $request): Redirector|RedirectResponse
    {
        return redirect('/');
    }

}
