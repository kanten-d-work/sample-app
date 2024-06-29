<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>
            イベントトップ
        </title>
    </head>
    <body>
        <main>
            <h1>
                イベントトップ
            </h1>
            <table>
                <tr>
                    <td>ユーザID</td>
                    <td>{{ $user_id }}</td>
                </tr>

                <tr>
                    <td>現在日時</td>
                    <td>{{ $now }}</td>
                </tr>
                <tr>
                    <td>最終アクセス日時</td>
                    <td>{{ $event_status->last_access_at }}</td>
                </tr>
                <tr>
                    <td>現在の工場レベル</td>
                    <td>{{ $factory_status->level }}</td>
                    <td>
                        @if (!$is_factory_level_max)
                            @if ($is_factory_levelup)
                                <form action="/event" method="POST">
                                    @csrf
                                    <input type="submit" value="工場レベルアップ">
                                </form>
                            @else
                                次のレベルまで{{ $factory_status->need_count - $item_status->count }}個
                            @endif
                        @else
                            <font color="red">最大レベル</font>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>1分毎の生産数</td>
                    <td>{{ $factory_status->product_count }}</td>
                </tr>
                <tr>
                    <td>アイテム所持数</td>
                    <td>{{ $item_status->count }}個@if ($add_count > 0) <font color="green">(+{{ $add_count }})</font> @endif</td>
                </tr>
            </table>
        </main>
    </body>
</html>