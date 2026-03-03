<?php
require_once __DIR__ . '/../../includes/config.php';
$pageTitle = '水道事業';
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>水道事業</h1>
    <p>水道関連業務の総合管理システム</p>
</div>

<div class="card-grid">
    <div class="card">
        <div class="card-icon">📋</div>
        <h2>行き先表</h2>
        <p>社員の行き先と帰社予定時刻を管理します。</p>
        <a href="/modules/water_service/destination.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">🔧</div>
        <h2>工事受付表</h2>
        <p>工事の受付情報と進捗状況を管理します。</p>
        <a href="/modules/water_service/construction_orders.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">🏗️</div>
        <h2>解体工事受付</h2>
        <p>解体工事の受付と進捗を管理します。</p>
        <a href="/modules/water_service/demolition.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">💧</div>
        <h2>水抜き箇所</h2>
        <p>水抜き作業が必要な箇所を管理します。</p>
        <a href="/modules/water_service/water_drainage.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">📅</div>
        <h2>残工事＆各種工事予定</h2>
        <p>残工事と今後の工事予定を管理します。</p>
        <a href="/modules/water_service/remaining_work.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">📖</div>
        <h2>マニュアル</h2>
        <p>作業マニュアルやガイドラインを管理します。</p>
        <a href="/modules/water_service/manuals.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">📷</div>
        <h2>工事写真アーカイブ</h2>
        <p>工事現場の写真を日付・場所別に保存します。</p>
        <a href="/modules/water_service/photo_archive.php" class="btn">開く</a>
    </div>
    
    <div class="card">
        <div class="card-icon">📝</div>
        <h2>カレンダー型情報メモ管理</h2>
        <p>日付別に情報やメモを管理します。</p>
        <a href="/modules/water_service/calendar_memos.php" class="btn">開く</a>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
