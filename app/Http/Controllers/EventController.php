<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Item;
use App\Models\Factory;
use App\Models\Event;
use App\Models\FactoryMst;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    private $_user_id;
    private $_first_access;

    const FIRST_LEVEL = 1;  // 最初の工場レベル
    const NEED_ITEM_ID = 1; // レベルアップに必要なアイテムID

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 現在認証しているユーザーIDを取得
        $this->_user_id = Auth::id();
        // 初回アクセスかどうか(フロント側で何らかの表示を切り替える際に使用する)
        $this->_first_access = false;
        // イベント用データが無ければ初回アクセスとして各種ステータス情報を作成、アイテムを付与する。
        if (!Event::where('id', '=', $this->_user_id)->exists()) {
            $this->_first_access = true;
            $this->_create_data();
        }
    }

    /**
     * イベントトップ
     */
    public function index(): View
    {
        // ステータス取得
        $status_list = $this->_get_status();

        return view('event/index', [
            'is_first_access' => $this->_first_access,                      // 初回アクセスかどうか
            'event_status' => $status_list['event_status'],                 // イベントステータス
            'item_status' => $status_list['item_status'],                   // 所持している工場レベルアップ用アイテム情報
            'factory_status' => $status_list['factory_status'],             // 工場ステータス
            'now' => now()->format('Y-m-d H:i:s'),                          // 現在日時
            'user_id' => $this->_user_id,                                   // ユーザID
            'add_count' => $status_list['add_count'],                       // 加算されたアイテム数
            'is_factory_levelup' => $status_list['is_factory_levelup'],     // 工場レベルアップ可能かどうか
            'is_factory_level_max' => $status_list['is_factory_level_max'], // 工場レベルが最大かどうか
        ]);
    }

    /**
     * 工場レベルアップ
     */
    public function factory_levelup(): RedirectResponse
    {
        // 工場ステータス取得
        $factory_status = User::find($this->_user_id)->factories;
        // 工場マスタデータ取得
        $factory_mst_data = FactoryMst::where('level', $factory_status['level'])->first();
        // 所持している精算アイテム情報を取得
        $item_status = User::find($this->_user_id)->items()->where('item_id', self::NEED_ITEM_ID)->first();
        // 次のレベルアップに必要なアイテム数が0、もしくは必要数に達していない場合はここでリダイレクトさせる
        if ($factory_mst_data['need_count'] == 0 || $item_status['count'] < $factory_mst_data['need_count']) {
            return redirect('/event');
        }
        // 各ステータス更新
        DB::transaction(function () use($factory_status, $item_status, $factory_mst_data) {
            // 工場レベルをアップ
            $factory_status->level = $factory_status->level + 1;
            $factory_status->save();
            // レベルアップ用アイテムを消費
            $item_status->count = $item_status->count - $factory_mst_data['need_count'];
            $item_status->save();
        });
        // リダイレクト
        return redirect('/event');
    }

    /**
     * 各種データ作成
     * 初回アクセス時のみ呼び出される
     */
    private function _create_data()
    {
        // イベントステータス登録
        $event = new Event;
        $event->id = $this->_user_id;
        $event->last_access_at = now()->format('Y-m-d H:i:s');
        $event->save();
        // 工場ステータス登録
        $factory = new Factory;
        $factory->id = $this->_user_id;
        $factory->level = self::FIRST_LEVEL;
        $factory->save();
        // レベルアップ用アイテム付与
        $item = new Item;
        $item->id = $this->_user_id;
        $item->item_id = self::NEED_ITEM_ID;
        $item->count = 10;
        $item->save();
    }

    /**
     * 各ステータス取得
     */
    private function _get_status(): array
    {
        // 各ステータスとレベルアップ用アイテムデータを取得
        $event_status = User::find($this->_user_id)->events;
        // 工場ステータス
        $factory_status = User::find($this->_user_id)->factories;
        // 工場マスタデータ取得
        $factory_mst_data = FactoryMst::where('level', $factory_status['level'])->first();
        $factory_status['need_count'] = $factory_mst_data['need_count'];
        $factory_status['product_count'] = $factory_mst_data['product_count'];
        // 所持アイテム
        $item_status = User::find($this->_user_id)->items()->where('item_id', self::NEED_ITEM_ID)->first();
        // 現在日時と最終アクセス日の差分から増やすアイテム数を算出
        $last_access_at = $event_status['last_access_at'];
        $product_count = $factory_status['product_count'];
        $now = now()->format('Y-m-d H:i:s');
        $add_count = floor(((strtotime($now) - strtotime($last_access_at))) / 60) * $product_count;

        // 更新
        if ($add_count > 0) {
            DB::transaction(function () use($event_status, $item_status, $now, $add_count) {
                // 最終アクセス日時を更新
                $event_status->last_access_at = $now;
                $event_status->save();
                // アイテム数を加算する
                $item_status->count = $item_status->count + $add_count;
                $item_status->save();
            });
        }
        // レベルアップできるかどうか(所持アイテム数 > レベルアップに必要な個数)
        $is_factory_levelup = ($item_status->count >= $factory_status['need_count']) ? true : false;
        // 工場レベルが最大化どうか
        $is_factory_level_max = ($factory_status['need_count'] == 0) ? true : false;
        return [
            'event_status' => $event_status,
            'factory_status' => $factory_status,
            'item_status' => $item_status,
            'add_count' => $add_count,
            'is_factory_levelup' => $is_factory_levelup,
            'is_factory_level_max' => $is_factory_level_max,
        ];
    }
}