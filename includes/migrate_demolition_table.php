<?php
// 解体工事受付テーブルのマイグレーションスクリプト
require_once __DIR__ . '/config.php';

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 既存テーブルのバックアップ
    $db->exec("DROP TABLE IF EXISTS demolition_orders_backup");
    $db->exec("CREATE TABLE demolition_orders_backup AS SELECT * FROM demolition_orders");
    
    // 既存テーブルを削除
    $db->exec("DROP TABLE IF EXISTS demolition_orders");
    
    // 新しいスキーマでテーブルを再作成
    $db->exec("CREATE TABLE demolition_orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE NOT NULL,
        chome_ban TEXT,
        owner_name TEXT,
        construction_company TEXT,
        water_company TEXT,
        pipe_diameter TEXT,
        water_application_date DATE,
        payment_date DATE,
        amount DECIMAL(15,2),
        neighborhood_association TEXT,
        construction_date DATE,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "マイグレーション完了: demolition_ordersテーブルを新しいスキーマで再作成しました。\n";
    echo "古いデータは demolition_orders_backup テーブルに保存されています。\n";

} catch (PDOException $e) {
    die('マイグレーションエラー: ' . $e->getMessage() . "\n");
}
