<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '残工事＆各種工事予定 - 水道事業';
$db = Database::getInstance();

// 編集対象のデータを取得
$edit_project = null;
if (isset($_GET['edit'])) {
    $edit_project = $db->fetchOne("SELECT * FROM remaining_construction WHERE id = ?", [(int)$_GET['edit']]);
}

// データの追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'project_name' => $_POST['location_detail'], // 場所（具体的な場所名）
        'location' => $_POST['district_number'], // 丁目番
        'construction_type' => $_POST['construction_content'], // 工事内容
        'scheduled_date' => $_POST['work_date'], // 日付
        'assigned_staff' => $_POST['assigned_staff'], // 担当者
        'priority' => 'normal', // デフォルト値
        'progress_status' => '未着手', // デフォルト値
        'notes' => $_POST['notes'] // 備考
    ];
    
    if ($db->insert('remaining_construction', $data)) {
        $success_message = '工事予定を登録しました。';
    }
}

// データの更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $data = [
        'project_name' => $_POST['location_detail'], // 場所（具体的な場所名）
        'location' => $_POST['district_number'], // 丁目番
        'construction_type' => $_POST['construction_content'], // 工事内容
        'scheduled_date' => $_POST['work_date'], // 日付
        'assigned_staff' => $_POST['assigned_staff'], // 担当者
        'priority' => 'normal', // デフォルト値
        'progress_status' => '未着手', // デフォルト値
        'notes' => $_POST['notes'] // 備考
    ];
    
    if ($db->update('remaining_construction', $data, 'id = :id', ['id' => $id])) {
        $success_message = '工事予定を更新しました。';
        header('Location: remaining_work.php?updated=1');
        exit;
    } else {
        $error_message = '更新に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $db->delete('remaining_construction', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '工事予定を削除しました。';
}

if (isset($_GET['updated'])) {
    $success_message = '工事予定を更新しました。';
}

$projects = $db->fetchAll("SELECT * FROM remaining_construction ORDER BY scheduled_date ASC, priority DESC");
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>残工事＆各種工事予定</h1>
    <p>今後の工事予定と残工事の管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2><?php echo $edit_project ? '工事予定編集' : '新規工事予定登録'; ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $edit_project ? 'update' : 'add'; ?>">
        <?php if ($edit_project): ?>
            <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="work_date">日付 *</label>
                <input type="date" id="work_date" name="work_date" required
                       value="<?php echo htmlspecialchars($edit_project['scheduled_date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="district_number">丁目番 *</label>
                <input type="text" id="district_number" name="district_number" required
                       placeholder="例: 1丁目23番地"
                       value="<?php echo htmlspecialchars($edit_project['location'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="construction_content">工事内容 *</label>
            <input type="text" id="construction_content" name="construction_content" required
                   placeholder="例: 給水管布設替工事"
                   value="<?php echo htmlspecialchars($edit_project['construction_type'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="location_detail">場所 *</label>
            <input type="text" id="location_detail" name="location_detail" required
                   placeholder="例: 山田様宅前道路"
                   value="<?php echo htmlspecialchars($edit_project['project_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="assigned_staff">担当者</label>
            <input type="text" id="assigned_staff" name="assigned_staff"
                   placeholder="例: 田中、鈴木"
                   value="<?php echo htmlspecialchars($edit_project['assigned_staff'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes" rows="4" 
                      placeholder="その他特記事項があれば記入してください"><?php echo htmlspecialchars($edit_project['notes'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo $edit_project ? '更新する' : '登録する'; ?></button>
            <?php if ($edit_project): ?>
                <a href="remaining_work.php" class="btn btn-secondary">キャンセル</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>工事予定一覧</h2>
    <?php if (count($projects) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>丁目番</th>
                        <th>工事内容</th>
                        <th>場所</th>
                        <th>担当者</th>
                        <th>備考</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td data-label="日付"><?php echo $project['scheduled_date'] ?? '-'; ?></td>
                            <td data-label="丁目番"><?php echo htmlspecialchars($project['location']); ?></td>
                            <td data-label="工事内容"><?php echo htmlspecialchars($project['construction_type']); ?></td>
                            <td data-label="場所"><?php echo htmlspecialchars($project['project_name']); ?></td>
                            <td data-label="担当者"><?php echo htmlspecialchars($project['assigned_staff'] ?? '-'); ?></td>
                            <td data-label="備考" class="notes-cell"><?php echo htmlspecialchars($project['notes'] ?? '-'); ?></td>
                            <td data-label="操作" class="action-cell">
                                <a href="?edit=<?php echo $project['id']; ?>" class="btn btn-sm">編集</a>
                                <a href="?delete=<?php echo $project['id']; ?>" 
                                   class="btn btn-sm btn-warning" 
                                   onclick="return confirm('本当に削除しますか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>登録されている工事予定はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
