<?php
// 伊豆総株式会社ポータルサイト - 設定ファイル
define('DB_PATH', __DIR__ . '/../data/izuso_portal.db');
define('SITE_NAME', '伊豆総株式会社 ポータルサイト');
define('TIMEZONE', 'Asia/Tokyo');

// タイムゾーン設定
date_default_timezone_set(TIMEZONE);

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
