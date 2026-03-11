<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '水抜き箇所 - 水道事業';
$db = Database::getInstance();

// 編集対象のデータを取得
$edit_location = null;
if (isset($_GET['edit'])) {
    $edit_location = $db->fetchOne("SELECT * FROM water_drainage_locations WHERE id = ?", [(int)$_GET['edit']]);
}

// データの追加処理
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

// データの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
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
    
    if ($db->update('water_drainage_locations', $data, 'id = :id', ['id' => $id])) {
        $success_message = '水抜き箇所を更新しました。';
        header('Location: water_drainage.php?updated=1');
        exit;
    } else {
        $error_message = '更新に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $db->delete('water_drainage_locations', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '水抜き箇所を削除しました。';
}

if (isset($_GET['updated'])) {
    $success_message = '水抜き箇所を更新しました。';
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

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo $edit_location ? '水抜き箇所編集' : '新規水抜き箇所登録'; ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_location ? 'update' : 'add'; ?>">
        <?php if ($edit_location): ?>
            <input type="hidden" name="id" value="<?php echo $edit_location['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="location_name">場所名 *</label>
            <input type="text" id="location_name" name="location_name" required
                   value="<?php echo htmlspecialchars($edit_location['location_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="address">住所 *</label>
            <input type="text" id="address" name="address" required
                   value="<?php echo htmlspecialchars($edit_location['address'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="property_owner">物件所有者</label>
            <input type="text" id="property_owner" name="property_owner"
                   value="<?php echo htmlspecialchars($edit_location['property_owner'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="contact_info">連絡先</label>
            <input type="text" id="contact_info" name="contact_info"
                   value="<?php echo htmlspecialchars($edit_location['contact_info'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="drainage_type">水抜き種別</label>
            <select id="drainage_type" name="drainage_type">
                <option value="ドレン" <?php echo ($edit_location['drainage_type'] ?? '') === 'ドレン' ? 'selected' : ''; ?>>ドレン</option>
                <option value="水栓" <?php echo ($edit_location['drainage_type'] ?? '') === '水栓' ? 'selected' : ''; ?>>水栓</option>
                <option value="取出し" <?php echo ($edit_location['drainage_type'] ?? '') === '取出し' ? 'selected' : ''; ?>>取出し</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="last_drainage_date">前回水抜き日</label>
            <input type="date" id="last_drainage_date" name="last_drainage_date"
                   value="<?php echo htmlspecialchars($edit_location['last_drainage_date'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="next_scheduled_date">次回予定日</label>
            <input type="date" id="next_scheduled_date" name="next_scheduled_date"
                   value="<?php echo htmlspecialchars($edit_location['next_scheduled_date'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="status">状況 *</label>
            <select id="status" name="status" required>
                <option value="濁り" <?php echo ($edit_location['status'] ?? '') === '濁り' ? 'selected' : ''; ?>>濁り</option>
                <option value="砂" <?php echo ($edit_location['status'] ?? '') === '砂' ? 'selected' : ''; ?>>砂</option>
                <option value="赤水" <?php echo ($edit_location['status'] ?? '') === '赤水' ? 'selected' : ''; ?>>赤水</option>
                <option value="塩素うすい" <?php echo ($edit_location['status'] ?? '') === '塩素うすい' ? 'selected' : ''; ?>>塩素うすい</option>
                <option value="温かい" <?php echo ($edit_location['status'] ?? '') === '温かい' ? 'selected' : ''; ?>>温かい</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"><?php echo htmlspecialchars($edit_location['notes'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="btn"><?php echo $edit_location ? '更新する' : '登録する'; ?></button>
        <?php if ($edit_location): ?>
            <a href="water_drainage.php" class="btn btn-secondary">キャンセル</a>
        <?php endif; ?>
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
                    <th>状況</th>
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
                            <span class="badge badge-info">
                                <?php echo htmlspecialchars($location['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="?edit=<?php echo $location['id']; ?>" class="btn">編集</a>
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
