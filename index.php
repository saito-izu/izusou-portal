<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'ホーム';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>伊豆総株式会社 ポータルサイトへようこそ</h1>
    <p>社内の情報共有と業務管理を効率化するためのプラットフォームです</p>
</div>

<div class="card-grid">
    <div class="card">
        <div class="card-icon">🏢</div>
        <h2>不動産事業</h2>
        <p>物件管理、販売状況の確認、顧客情報の管理を行います。</p>
        <a href="/modules/real_estate/index.php" class="btn">詳細を見る</a>
    </div>
    
    <div class="card">
        <div class="card-icon">🏡</div>
        <h2>別荘管理事業</h2>
        <p>別荘の管理状況、契約情報、点検スケジュールを確認します。</p>
        <a href="/modules/villa_management/index.php" class="btn">詳細を見る</a>
    </div>
    
    <div class="card">
        <div class="card-icon">💧</div>
        <h2>水道事業</h2>
        <p>工事受付、行き先管理、写真アーカイブなど8つの機能を提供します。</p>
        <a href="/modules/water_service/index.php" class="btn">詳細を見る</a>
    </div>
</div>

<div class="table-container">
    <h2>最近の更新情報</h2>
    <table>
        <thead>
            <tr>
                <th>日時</th>
                <th>カテゴリ</th>
                <th>内容</th>
                <th>担当者</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo date('Y-m-d H:i'); ?></td>
                <td><span class="badge badge-info">システム</span></td>
                <td>ポータルサイトが開設されました</td>
                <td>システム管理者</td>
            </tr>
        </tbody>
    </table>
</div>

<style>
.page-header {
    text-align: center;
}

.card-grid {
    margin-top: 3rem;
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
