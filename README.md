# 伊豆総株式会社 ポータルサイト

社内の情報共有と業務管理を効率化するためのポータルサイトです。

## 主な機能

### 1. 不動産事業
- 物件情報の登録・管理
- 販売状況の追跡
- 顧客情報の管理

### 2. 別荘管理事業
- 別荘の管理状況確認
- 契約情報の管理
- 点検スケジュールの管理

### 3. 水道事業（8つのサブ機能）
1. **行き先表** - 社員の行き先と帰社予定時刻の管理
2. **工事受付表** - 工事の受付と進捗管理
3. **解体工事受付** - 解体工事の受付と進捗管理
4. **水抜き箇所** - 水抜き作業が必要な箇所の管理
5. **残工事＆各種工事予定** - 今後の工事予定と残工事の管理
6. **マニュアル** - 作業マニュアルとガイドラインの管理
7. **工事写真アーカイブ** - 工事現場の写真管理
8. **カレンダー型情報メモ管理** - 日付別の情報とメモの管理

## 技術スタック

- **バックエンド**: PHP 8.2
- **データベース**: SQLite3
- **フロントエンド**: HTML5, CSS3, JavaScript
- **デザイン**: レスポンシブデザイン対応

## インストール

### 1. データベースの初期化

```bash
php includes/init_db.php
```

### 2. PHPサーバーの起動

```bash
php -S localhost:8000
```

ブラウザで `http://localhost:8000` にアクセスしてください。

## ディレクトリ構造

```
webapp/
├── assets/
│   ├── css/
│   │   └── style.css          # スタイルシート
│   ├── js/                     # JavaScriptファイル
│   └── images/                 # 画像ファイル
├── includes/
│   ├── config.php             # 設定ファイル
│   ├── db.php                 # データベースクラス
│   ├── header.php             # 共通ヘッダー
│   ├── footer.php             # 共通フッター
│   └── init_db.php            # データベース初期化スクリプト
├── modules/
│   ├── real_estate/           # 不動産事業モジュール
│   ├── villa_management/      # 別荘管理事業モジュール
│   └── water_service/         # 水道事業モジュール
│       ├── index.php          # 水道事業トップページ
│       ├── destination.php    # 行き先表
│       ├── construction_orders.php  # 工事受付表
│       ├── demolition.php     # 解体工事受付
│       ├── water_drainage.php # 水抜き箇所
│       ├── remaining_work.php # 残工事＆各種工事予定
│       ├── manuals.php        # マニュアル
│       ├── photo_archive.php  # 工事写真アーカイブ
│       └── calendar_memos.php # カレンダー型情報メモ管理
├── data/
│   └── izuso_portal.db        # SQLiteデータベース
├── index.php                  # トップページ
├── .htaccess                  # Apache設定ファイル
└── README.md                  # このファイル
```

## 主な特徴

### レスポンシブデザイン
- モバイル、タブレット、デスクトップに対応
- タッチデバイスでの操作に最適化

### スタイリッシュなUI
- モダンなカードベースのデザイン
- アニメーション効果による快適なユーザー体験
- 直感的なナビゲーション

### データ管理
- SQLiteによる軽量で高速なデータベース
- CRUD操作（作成、読み取り、更新、削除）の完全サポート
- データの検索・フィルタリング機能

### セキュリティ
- SQLインジェクション対策（プリペアドステートメント使用）
- XSS対策（HTMLエスケープ処理）
- データベースファイルへの直接アクセス制限

## ブラウザ対応

- Google Chrome（推奨）
- Mozilla Firefox
- Microsoft Edge
- Safari

## カスタマイズ

### カラーテーマの変更

`assets/css/style.css` の `:root` セクションで色を変更できます：

```css
:root {
    --primary-color: #1a5490;
    --secondary-color: #2c7ab8;
    --accent-color: #f39c12;
    /* ... */
}
```

### データベースのバックアップ

```bash
cp data/izuso_portal.db data/backup_$(date +%Y%m%d).db
```

## ライセンス

© 2024 伊豆総株式会社 All Rights Reserved.
