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
    // 水抜きチェックボックスの値を結合
    $drainage_conditions = [];
    if (isset($_POST['drainage_turbid'])) $drainage_conditions[] = '濁り';
    if (isset($_POST['drainage_sand'])) $drainage_conditions[] = '砂';
    if (isset($_POST['drainage_red'])) $drainage_conditions[] = '赤水';
    if (isset($_POST['drainage_chlorine'])) $drainage_conditions[] = '塩素うすい';
    if (isset($_POST['drainage_warm'])) $drainage_conditions[] = '温かい';
    
    $data = [
        'location_name' => $_POST['location_name'],
        'address' => $_POST['district_number'],
        'property_owner' => '', // 空文字で保存
        'contact_info' => '', // 空文字で保存
        'drainage_type' => implode(', ', $drainage_conditions), // 水抜き状態を結合
        'last_drainage_date' => $_POST['drainage_date'],
        'next_scheduled_date' => '', // 空文字で保存
        'status' => $_POST['implementation_status'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('water_drainage_locations', $data)) {
        $success_message = '水抜き箇所を登録しました。';
    }
}

// データの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    // 水抜きチェックボックスの値を結合
    $drainage_conditions = [];
    if (isset($_POST['drainage_turbid'])) $drainage_conditions[] = '濁り';
    if (isset($_POST['drainage_sand'])) $drainage_conditions[] = '砂';
    if (isset($_POST['drainage_red'])) $drainage_conditions[] = '赤水';
    if (isset($_POST['drainage_chlorine'])) $drainage_conditions[] = '塩素うすい';
    if (isset($_POST['drainage_warm'])) $drainage_conditions[] = '温かい';
    
    $data = [
        'location_name' => $_POST['location_name'],
        'address' => $_POST['district_number'],
        'property_owner' => '', // 空文字で保存
        'contact_info' => '', // 空文字で保存
        'drainage_type' => implode(', ', $drainage_conditions), // 水抜き状態を結合
        'last_drainage_date' => $_POST['drainage_date'],
        'next_scheduled_date' => '', // 空文字で保存
        'status' => $_POST['implementation_status'],
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

<?php
// 編集時の水抜き状態をチェックボックス用に分解
$drainage_conditions_array = [];
if ($edit_location && isset($edit_location['drainage_type'])) {
    $drainage_conditions_array = array_map('trim', explode(',', $edit_location['drainage_type']));
}
?>

<div class="form-container">
    <h2><?php echo $edit_location ? '水抜き箇所編集' : '新規水抜き箇所登録'; ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_location ? 'update' : 'add'; ?>">
        <?php if ($edit_location): ?>
            <input type="hidden" name="id" value="<?php echo $edit_location['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="drainage_date">日付 *</label>
                <input type="date" id="drainage_date" name="drainage_date" required
                       value="<?php echo htmlspecialchars($edit_location['last_drainage_date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="district_number">丁目番 *</label>
                <input type="text" id="district_number" name="district_number" required
                       placeholder="例: 1丁目23番地"
                       value="<?php echo htmlspecialchars($edit_location['address'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="location_name">場所 *</label>
            <input type="text" id="location_name" name="location_name" required
                   placeholder="例: 山田様宅前"
                   value="<?php echo htmlspecialchars($edit_location['location_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>水抜き</label>
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="drainage_turbid" value="1"
                           <?php echo in_array('濁り', $drainage_conditions_array) ? 'checked' : ''; ?>>
                    <span>濁り</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="drainage_sand" value="1"
                           <?php echo in_array('砂', $drainage_conditions_array) ? 'checked' : ''; ?>>
                    <span>砂</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="drainage_red" value="1"
                           <?php echo in_array('赤水', $drainage_conditions_array) ? 'checked' : ''; ?>>
                    <span>赤水</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="drainage_chlorine" value="1"
                           <?php echo in_array('塩素うすい', $drainage_conditions_array) ? 'checked' : ''; ?>>
                    <span>塩素うすい</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="drainage_warm" value="1"
                           <?php echo in_array('温かい', $drainage_conditions_array) ? 'checked' : ''; ?>>
                    <span>温かい</span>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="implementation_status">実施状況 *</label>
            <select id="implementation_status" name="implementation_status" required>
                <option value="">選択してください</option>
                <option value="実施済み" <?php echo ($edit_location['status'] ?? '') === '実施済み' ? 'selected' : ''; ?>>実施済み</option>
                <option value="実施中" <?php echo ($edit_location['status'] ?? '') === '実施中' ? 'selected' : ''; ?>>実施中</option>
                <option value="未実施" <?php echo ($edit_location['status'] ?? '') === '未実施' ? 'selected' : ''; ?>>未実施</option>
                <option value="要確認" <?php echo ($edit_location['status'] ?? '') === '要確認' ? 'selected' : ''; ?>>要確認</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes" rows="4" placeholder="その他特記事項があれば記入してください"><?php echo htmlspecialchars($edit_location['notes'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo $edit_location ? '更新する' : '登録する'; ?></button>
            <?php if ($edit_location): ?>
                <a href="water_drainage.php" class="btn btn-secondary">キャンセル</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>水抜き箇所一覧</h2>
    <?php if (count($locations) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>丁目番</th>
                        <th>場所</th>
                        <th>水抜き</th>
                        <th>実施状況</th>
                        <th>備考</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                        <tr>
                            <td data-label="日付"><?php echo $location['last_drainage_date'] ?? '-'; ?></td>
                            <td data-label="丁目番"><?php echo htmlspecialchars($location['address']); ?></td>
                            <td data-label="場所"><?php echo htmlspecialchars($location['location_name']); ?></td>
                            <td data-label="水抜き"><?php echo htmlspecialchars($location['drainage_type'] ?? '-'); ?></td>
                            <td data-label="実施状況">
                                <span class="badge badge-<?php 
                                    echo $location['status'] === '実施済み' ? 'success' : 
                                        ($location['status'] === '実施中' ? 'info' : 
                                        ($location['status'] === '未実施' ? 'warning' : 'error')); 
                                ?>">
                                    <?php echo htmlspecialchars($location['status']); ?>
                                </span>
                            </td>
                            <td data-label="備考" class="notes-cell"><?php echo htmlspecialchars($location['notes'] ?? '-'); ?></td>
                            <td data-label="操作" class="action-cell">
                                <a href="?edit=<?php echo $location['id']; ?>" class="btn btn-sm">編集</a>
                                <a href="?delete=<?php echo $location['id']; ?>" 
                                   class="btn btn-sm btn-warning" 
                                   onclick="return confirm('本当に削除しますか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>登録されている水抜き箇所はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
