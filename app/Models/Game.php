<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Game extends Model
{
    use HasFactory;

    const STATUS_NEW = 0;
    const STATUS_VICTORY = 1;
    const STATUS_DEFEAT = 2;
    const STATUS_DRAW = 3;
    const STATUS_UNFINISHED = 4;

    /**
     * @var array
     */
    protected $fillable = [
        'status',
        'host_user_id'
    ];

    /**
     * Определяем количество пользователей онлайн
     */
    static public function getOnlineUserCount(): Int
    {   
        $result = DB::table('games')
            ->select(DB::raw('(count(host_user_id) + count(opponent_user_id)) as online_user_count'))
            ->where('status', '=', self::STATUS_NEW)
            ->take(1)->first();

        if ($result) {
            return (int) $result->online_user_count;
        }

        return 0;
    }

    /**
     * Проверяем по логину является ли пользователь хостом.
     */
    static public function isHost(string $login): bool
    {
        $user = User::getByLogin($login);
        if (!$user) {
            return false;
        }

        $game = self::where('status', '0')
            ->where('host_user_id', $user->getId())
            ->take(1)->first();
        if ($game) {
            return true;
        }

        return false;
    }

    /**
     * Проверяем является ли логин уже используемым в игре
     */
    static public function isLockedLogin(string $login): bool
    {
        $user = User::getByLogin($login);
        if (!$user) {
            return false;
        }

        $games = DB::table('games')
            ->where('status', '=', 0)
            ->where(function (Builder $query) use ($user) {
                $query->where('host_user_id', '=', $user->getId())
                    ->orWhere('opponent_user_id', '=', $user->getId());
            })
           ->take(1)->first();

        if ($games) {
            return true;
        }
        return false;
    }

    /**
     * Проверяем возможность создать игру или записаться в игру, созданную оппонентом.
     */
    static public function canStart(): bool
    {
        if (self::getOnlineUserCount() >= 2) {
            return false;
        }
        return true;
    }

    /**
     * Создаем игру или записываемся в игру, созданную оппонентом.
     */
    static public function start(): bool
    {   
        if (!self::canStart()) {
            return false;
        }
        $user = User::authorizedUser();
        // Находим чужую игру со свободным местом
        $game = self::where('status', '0')->whereNull('opponent_user_id')->take(1)->first();
        if ($game && $user) {
            self::where('id', $game->getId())
                ->update(['opponent_user_id' => $user->getId()]);
            return true;
        } elseif (!$game && $user) {
            // Создаем свою игру
            $game = self::create([
                'status' => self::STATUS_NEW,
                'host_user_id' => $user->getId()
            ]);
            return true;
        }
        return false;
    }

    /**
     * Получаем активную игровую сессию для заданного пользователя
     */
    static public function game(User $user): Game|null
    {
        $game = self::where('status', '=', self::STATUS_NEW)
            ->where('host_user_id', '=', $user->getId())
            ->take(1)->first();

        if ($game) {
            return $game;
        }

        $game = self::where('status', '=', self::STATUS_NEW)
            ->where('opponent_user_id', '=', $user->getId())
            ->take(1)->first();
        return $game;
    }

    /**
     * Закрываем игровую сессию.
     */
    static public function finish(): bool
    {
        $user = User::authorizedUser();
        if (!$user) {
            return false;
        }

        // Получаем состояние игры
        $game = DB::table('games')
            ->where('status', '=', self::STATUS_NEW)
            ->where(function (Builder $query) use ($user) {
                $query->where('host_user_id', '=', $user->getId())
                    ->orWhere('opponent_user_id', '=', $user->getId());
            })
           ->take(1)->first();
        
        // Меняем статус игровой сессии
        if ($game) {
            // if (self::gameState((array) $game) == self::STATUS_UNFINISHED) {
            //     self::where('id', $game->id)
            //         ->update(['status' => self::STATUS_UNFINISHED]);
            //     // TODO: присваивать техническую победу или техническое поражение
            // } elseif (self::gameState((array) $game) == self::STATUS_VICTORY) {
            //     self::where('id', $game->id)
            //         ->update(['status' => self::STATUS_VICTORY]);
            // } elseif (self::gameState((array) $game) == self::STATUS_DEFEAT) {
            //     self::where('id', $game->id)
            //         ->update(['status' => self::STATUS_DEFEAT]);
            // } elseif (self::gameState((array) $game) == self::STATUS_DRAW) {
            //     self::where('id', $game->id)
            //         ->update(['status' => self::STATUS_DRAW]);
            // }
        }
        return true;
    }

    /**
     * Игровой ход
     */
    static public function move(User $user, Int $cellNumber): bool
    {
        $game = self::game($user);
        // Определяем, чем играет пользователь (крестиками или ноликами)
        $side = $game->getSide($user);
        // Сохраняем ход в базу
        self::where('id', $game->getId())
            ->update(['cell'.$cellNumber => $side, 'move' => ($game->move == 0) ? 1 : 0]);
        // Проверяем не закончилась ли игра
        if (self::gameState($game, $user) != self::STATUS_UNFINISHED) {
            return self::finish();
        }
        return true;
    }

    static public function gameState(Game $game, User $user): Int
    {
        $side = $game->getSide($user);

        // Проверка на победу
        if (self::isVictory($game, $side)) {
            return self::STATUS_VICTORY;
        }

        // Проверка на поражение
        if (self::isVictory($game, ($side == 0) ? 1 : 0)) {
            return self::STATUS_DEFEAT;
        }

        // Проверяем есть ли незаполненные клетки
        for ($i=1; $i <= 9; $i++) {
            $cellName = "cell" . $i;
            if (is_null($game->$cellName)) {
                // Игра еще не закончена
                return self::STATUS_UNFINISHED;
            }
        }

        // Ничья
        return self::STATUS_DRAW;
    }

    static public function isVictory(Game $game, Int $side): bool
    {
        if (($game->cell1 == $side && $game->cell2 == $side && $game->cell3 == $side) 
            || ($game->cell4 == $side && $game->cell5 == $side && $game->cell6 == $side)
            || ($game->cell7 == $side && $game->cell8 == $side && $game->cell9 == $side)
            || ($game->cell1 == $side && $game->cell4 == $side && $game->cell7 == $side)
            || ($game->cell2 == $side && $game->cell5 == $side && $game->cell8 == $side)
            || ($game->cell3 == $side && $game->cell6 == $side && $game->cell9 == $side)
            || ($game->cell1 == $side && $game->cell5 == $side && $game->cell9 == $side)
            || ($game->cell3 == $side && $game->cell5 == $side && $game->cell7 == $side))
            return true;
        return false;
    }

    /**
     * Идентификатор игровой сессии
     */
    public function getId(): Int
    {
        return $this->id;
    }

    /**
     * Идентификатор пользователя (игрока) - хоста.
     */
    public function getHostUserId(): Int
    {
        return $this->host_user_id;
    }

    /**
     * Идентификатор пользователя (игрока) - соперника.
     */
    public function getOpponentUserId(): Int|null
    {
        return $this->opponent_user_id;
    }

    /**
     *  Определение очереди игрового хода.
     */
    public function getMove(User $user): Int|null
    {
        if ($user->getId() == $this->getHostUserId()) {
            return $this->move;
        } else {
            return ($this->move == 0) ? 1 : 0;
        }
    }

    /**
     * Определяем крестиками или ноликами играет пользователь.
     * @param User $user
     * @return Int 1 - крестики, 0 - нолики.
     */
    public function getSide(User $user): Int
    {
        if ($user->getId() == $this->getHostUserId()) {
            return $this->side;
        } else {
            return ($this->side == 0) ? 1 : 0;
        }
    }

    public function getCellValue(Int $cellNumber): string
    {   
        $fieldName = "cell".$cellNumber;
        if (is_null($this->$fieldName))
            return "";
        if ($this->$fieldName == 1)
            return "X";
        if ($this->$fieldName == 0)
            return "O";
    }

}
