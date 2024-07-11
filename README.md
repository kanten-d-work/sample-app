# 開発環境について

* MacBook Pro 13-inch(Apple M1)
    * macOS Sonoma 14.5
* Visual Studio Code
    * バージョン:1.90.2
* docker desktop
    * バージョン:4.31.0
* Google Chrome
    * バージョン:126.0.6478.127
* PHP
    * バージョン:8.3.8
* Laravel
    * バージョン:11.11.1

# 作成した機能について

* イベントページにアクセスした際、最終アクセス日と現在日時との差を"分"に直し、1分ごとにアイテムが加算されていきます。
* 増えたアイテムを消費することで工場をレベルアップさせ、1分毎の生産数を増やすだけのアプリとなっています。

# 変更、追加したディレクトリ・ファイルについて

* sample-app
    * app
        * Http/Controllers
            * EventController.php
        * Models
            * Event.php
            * Factory.php
            * FactoryMst.php
            * Item.php
            * ItemMst.php
    * database
        * migrations
            * 2024_06_26_033414_create_items_table.php
            * 2024_06_26_033424_create_factorys_table.php
            * 2024_06_26_033432_create_events_table.php
        * seeders
            * DatabaseSeeder.php
            * FactoryMstTableSeeder.php
            * ItemMstTableSeeder.php
    * resources/views/event
        * index.blade.php
     
# 確認手順について

1. ユーザ登録をする
    * http://localhost/register
    * Laravel Breezeで作成されたデフォルトの登録画面を使用
2. 登録後、ダッシュボードに記載されている「イベントページへ」のリンクを選択する
    * イベントページURL：http://localhost/event
3. ページを再読込する
    * [最終アクセス日 - 現在日時]が1分以上ある場合に「1分毎の生産数」分だけアイテムが増えるかどうか
4. アイテム所持数が増える
5. 工場レベルアップできる場合はボタンが表示される
6. 工場をレベルアップさせる
    * アイテムの所持数が減るかどうか
    * レベルがアップするかどうか
    * 生産数が変化するか
7. 最大レベル(デフォルトでは5)までアップさせる

※最大レベルまで上げた後に改めて確認する場合は、登録内容を直接修正することで初期状態に戻す

## DBテーブルについて
### usersテーブル
* laravelのデフォルトで作成されるユーザテーブルをそのまま使用

### eventsテーブル
* ユーザのイベントについてのステータスを管理するテーブル
* イベントページへ遷移した際に作成される
~~~
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL COMMENT `ユーザID`,
  `last_access_at` timestamp NOT NULL COMMENT `最終アクセス日`,
  `created_at` timestamp NULL DEFAULT NULL COMMENT `作成日`,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT `更新日`,
  PRIMARY KEY (`id`),
  CONSTRAINT `events_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
~~~

### factoriesテーブル
* ユーザの工場ステータスを管理するテーブル
* イベントページへ遷移した際に作成される
~~~
CREATE TABLE `factories` (
  `id` bigint unsigned NOT NULL COMMENT `ユーザID`,
  `level` int NOT NULL DEFAULT '1' COMMENT `工場レベル`,
  `created_at` timestamp NULL DEFAULT NULL COMMENT `作成日`,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT `更新日`,
  PRIMARY KEY (`id`),
  CONSTRAINT `factories_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
~~~

### itemsテーブル
* ユーザの所持しているアイテムを管理するテーブル
* イベントページへ遷移した際に作成される
~~~
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL COMMENT `ユーザID`,
  `item_id` int NOT NULL COMMENT `アイテムID`,
  `count` int NOT NULL DEFAULT '0' COMMENT `所持数`,
  `created_at` timestamp NULL DEFAULT NULL COMMENT `作成日`,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT `更新日`,
  PRIMARY KEY (`id`,`item_id`),
  CONSTRAINT `items_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
~~~

### factory_mstテーブル
* 工場で生産するアイテム数とレベルアップに必要なアイテム数を管理するマスタテーブル
* レベル(level)は1から始める
* 次のレベルに必要なアイテム数(need_count)は、最大レベルの場合は0を設定する
~~~
CREATE TABLE `factory_mst` (
  `level` int unsigned NOT NULL AUTO_INCREMENT COMMENT `工場レベル`,
  `product_count` int NOT NULL COMMENT `生産数`,
  `need_count` int NOT NULL COMMENT `次のレベルアップに必要なアイテム数`,
  `created_at` timestamp NULL DEFAULT NULL COMMENT `作成日`,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT `更新日`,
  PRIMARY KEY (`level`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
~~~

### item_mstテーブル
* アイテムを管理するマスタテーブル
* 本アプリにおいてはitem_id:1が生産されるアイテム、工場レベルアップに必要なアイテムIDに指定
~~~
CREATE TABLE `item_mst` (
  `item_id` int NOT NULL COMMENT `アイテムID`,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT `アイテム名`,
  `created_at` timestamp NULL DEFAULT NULL COMMENT `作成日`,
  `updated_at` timestamp NULL DEFAULT NULL COMMENT `更新日`,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
~~~

以上
