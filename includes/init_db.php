<?php
// データベース初期化スクリプト
require_once __DIR__ . '/../includes/config.php';

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 行き先表テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS destination_board (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        employee_name TEXT NOT NULL,
        destination TEXT NOT NULL,
        departure_time DATETIME,
        return_time DATETIME,
        contact_info TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 工事受付表テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS construction_orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE NOT NULL,
        customer_name TEXT NOT NULL,
        address TEXT NOT NULL,
        phone TEXT,
        construction_type TEXT NOT NULL,
        scheduled_date DATE,
        status TEXT DEFAULT '受付',
        assigned_staff TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 解体工事受付テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS demolition_orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT UNIQUE NOT NULL,
        customer_name TEXT NOT NULL,
        property_address TEXT NOT NULL,
        phone TEXT,
        scheduled_date DATE,
        demolition_type TEXT,
        status TEXT DEFAULT '受付',
        assigned_staff TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 水抜き箇所テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS water_drainage_locations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        location_name TEXT NOT NULL,
        address TEXT NOT NULL,
        property_owner TEXT,
        contact_info TEXT,
        drainage_type TEXT,
        last_drainage_date DATE,
        next_scheduled_date DATE,
        status TEXT DEFAULT 'pending',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 残工事＆各種工事予定テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS remaining_construction (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        project_name TEXT NOT NULL,
        location TEXT NOT NULL,
        construction_type TEXT NOT NULL,
        scheduled_date DATE,
        assigned_staff TEXT,
        priority TEXT DEFAULT 'normal',
        progress_status TEXT DEFAULT '未着手',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // マニュアルテーブル
    $db->exec("CREATE TABLE IF NOT EXISTS manuals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        category TEXT NOT NULL,
        file_path TEXT,
        description TEXT,
        version TEXT,
        created_by TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 工事写真アーカイブテーブル
    $db->exec("CREATE TABLE IF NOT EXISTS construction_photos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        project_name TEXT NOT NULL,
        photo_date DATE NOT NULL,
        location TEXT,
        photo_path TEXT NOT NULL,
        description TEXT,
        category TEXT,
        uploaded_by TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // カレンダー型情報メモ管理テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS calendar_memos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        memo_date DATE NOT NULL,
        title TEXT NOT NULL,
        content TEXT,
        category TEXT,
        priority TEXT DEFAULT 'normal',
        created_by TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 不動産事業テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS real_estate_properties (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        property_name TEXT NOT NULL,
        address TEXT NOT NULL,
        property_type TEXT NOT NULL,
        status TEXT DEFAULT '販売中',
        price DECIMAL(15,2),
        area DECIMAL(10,2),
        contact_person TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 別荘管理事業テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS villa_management (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        villa_name TEXT NOT NULL,
        address TEXT NOT NULL,
        owner_name TEXT NOT NULL,
        contact_info TEXT,
        contract_start_date DATE,
        contract_end_date DATE,
        management_status TEXT DEFAULT 'active',
        last_inspection_date DATE,
        next_inspection_date DATE,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    echo "データベースの初期化が完了しました。\n";
    echo "データベースファイル: " . DB_PATH . "\n";

} catch (PDOException $e) {
    die('データベース初期化エラー: ' . $e->getMessage() . "\n");
}
