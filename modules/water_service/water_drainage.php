<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '水抜き箇所 - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'location_name' => $_POST['location_name'],
        'address' => $_POST['address'],
        'property_owner' => $_POST['property_owner'],
        'contact_info' => $_POST['contact_info'],
        'drainage_type' => $_POST['drainage_type'],
        'last_drainage_date' => $_POST['last_drainage_date'],
        'next_scheduled_date' => $_POST['next_scheduled_date'],
        'status' => $_POST['status'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('water_drainage_locations', $data)) {
        $success_message = '水抜き箇所を登録しました。';
    }
}

if (isset($_GET['delete'])) {
    $db->delete('water_drainage_locations', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '水抜き箇所を削除しました。';
}

$locations = $db->fetchAll("SELECT * FROM water_drainage_locations ORDER BY next_scheduled_date ASC");
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>水抜き箇所</h1>
    <p>水抜き作業が必要な箇所の管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規水抜き箇所登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="location_name">場所名 *</label>
            <input type="text" id="location_name" name="location_name" required>
        </div>
        
        <div class="form-group">
            <label for="address">住所 *</label>
            <input type="text" id="address" name="address" required>
        </div>
        
        <div class="form-group">
            <label for="property_owner">物件所有者</label>
            <input type="text" id="property_owner" name="property_owner">
        </div>
        
        <div class="form-group">
            <label for="contact_info">連絡先</label>
            <input type="text" id="contact_info" name="contact_info">
        </div>
        
        <div class="form-group">
            <label for="drainage_type">水抜き種別</label>
            <select id="drainage_type" name="drainage_type">
                <option value="定期水抜き">定期水抜き</option>
                <option value="季節水抜き">季節水抜き（冬季）</option>
                <option value="緊急水抜き">緊急水抜き</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="last_drainage_date">前回水抜き日</label>
            <input type="date" id="last_drainage_date" name="last_drainage_date">
        </div>
        
        <div class="form-group">
            <label for="next_scheduled_date">次回予定日</label>
            <input type="date" id="next_scheduled_date" name="next_scheduled_date">
        </div>
        
        <div class="form-group">
            <label for="status">状態 *</label>
            <select id="status" name="status" required>
                <option value="pending">未実施</option>
                <option value="scheduled">予定あり</option>
                <option value="completed">完了</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>水抜き箇所一覧</h2>
    <?php if (count($locations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>場所名</th>
                    <th>住所</th>
                    <th>所有者</th>
                    <th>種別</th>
                    <th>前回実施日</th>
                    <th>次回予定日</th>
                    <th>状態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $location): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($location['location_name']); ?></td>
                        <td><?php echo htmlspecialchars($location['address']); ?></td>
                        <td><?php echo htmlspecialchars($location['property_owner'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($location['drainage_type'] ?? '-'); ?></td>
                        <td><?php echo $location['last_drainage_date'] ?? '-'; ?></td>
                        <td><?php echo $location['next_scheduled_date'] ?? '-'; ?></td>
                        <td>
                            <?php
                            $statusMap = ['pending' => '未実施', 'scheduled' => '予定あり', 'completed' => '完了'];
                            $statusClass = $location['status'] === 'completed' ? 'badge-success' : 
                                          ($location['status'] === 'scheduled' ? 'badge-info' : 'badge-warning');
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo $statusMap[$location['status']] ?? $location['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $location['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている水抜き箇所はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
