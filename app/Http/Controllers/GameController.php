<?php

namespace App\Http\Controllers;

use App\Events\GameMoveEvent;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request $request
     */
    public function game(Request $request)
    {
        $user = User::authorizedUser();
        if (!$user) {
            // TODO: добавить обработку ошибки
        }

        $game = Game::game($user);
        if (!$game) {
            Auth::logout();
            return redirect('/login');
        }

        $opponent = null;
        if ($user->getId() == $game->getHostUserId() && is_numeric($game->getOpponentUserId())) {
            $opponent = User::find($game->getOpponentUserId());
        }
        if ($user->getId() == $game->getOpponentUserId()) {
            $opponent = User::find($game->getHostUserId());
        }

        $gameMove = $game->getMove($user); // кто ходит
        $gameSide = $game->getSide($user); // крестиками или ноликами

        // event(new GameMoveEvent('Game Move Event'));

        return view('game')->with([
            "user" => $user,
            "opponent" => $opponent,
            "game" => $game,
            "gameMove" => $gameMove,
            "gameSide" => $gameSide,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function gameMove(Request $request)
    {
        $user = User::authorizedUser();
        if (!$user) {
            return response()->json(['result' => false]);
        }

        $cellNumber = (int) $request->get('cell-number');
        if (!$cellNumber || !in_array($cellNumber, [1,2,3,4,5,6,7,8,9])) {
            return response()->json(['result' => false]);
        }

        $result = Game::move($user, $cellNumber);
        if (!$result) {
            return response()->json(['result' => false]);
        }

        $game = Game::game($user);
        if (!$game) {
            return response()->json(['result' => false]);
        }

        return response()->json([
            "game-status" => $game->status,
            'result' => true,
        ]);
    }
}
