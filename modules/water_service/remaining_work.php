<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '残工事＆各種工事予定 - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'project_name' => $_POST['project_name'],
        'location' => $_POST['location'],
        'construction_type' => $_POST['construction_type'],
        'scheduled_date' => $_POST['scheduled_date'],
        'assigned_staff' => $_POST['assigned_staff'],
        'priority' => $_POST['priority'],
        'progress_status' => $_POST['progress_status'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('remaining_construction', $data)) {
        $success_message = '工事予定を登録しました。';
    }
}

if (isset($_GET['delete'])) {
    $db->delete('remaining_construction', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '工事予定を削除しました。';
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

<div class="form-container">
    <h2>新規工事予定登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="project_name">プロジェクト名 *</label>
            <input type="text" id="project_name" name="project_name" required>
        </div>
        
        <div class="form-group">
            <label for="location">場所 *</label>
            <input type="text" id="location" name="location" required>
        </div>
        
        <div class="form-group">
            <label for="construction_type">工事種別 *</label>
            <select id="construction_type" name="construction_type" required>
                <option value="">選択してください</option>
                <option value="新規工事">新規工事</option>
                <option value="残工事">残工事</option>
                <option value="修繕工事">修繕工事</option>
                <option value="定期メンテナンス">定期メンテナンス</option>
                <option value="緊急対応">緊急対応</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="scheduled_date">予定日</label>
            <input type="date" id="scheduled_date" name="scheduled_date">
        </div>
        
        <div class="form-group">
            <label for="assigned_staff">担当者</label>
            <input type="text" id="assigned_staff" name="assigned_staff">
        </div>
        
        <div class="form-group">
            <label for="priority">優先度 *</label>
            <select id="priority" name="priority" required>
                <option value="normal">通常</option>
                <option value="high">高</option>
                <option value="urgent">緊急</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="progress_status">進捗状態 *</label>
            <select id="progress_status" name="progress_status" required>
                <option value="未着手">未着手</option>
                <option value="準備中">準備中</option>
                <option value="進行中">進行中</option>
                <option value="完了">完了</option>
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
    <h2>工事予定一覧</h2>
    <?php if (count($projects) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>プロジェクト名</th>
                    <th>場所</th>
                    <th>工事種別</th>
                    <th>予定日</th>
                    <th>担当者</th>
                    <th>優先度</th>
                    <th>進捗状態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['location']); ?></td>
                        <td><?php echo htmlspecialchars($project['construction_type']); ?></td>
                        <td><?php echo $project['scheduled_date'] ?? '-'; ?></td>
                        <td><?php echo htmlspecialchars($project['assigned_staff'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $priorityMap = ['normal' => '通常', 'high' => '高', 'urgent' => '緊急'];
                            $priorityClass = $project['priority'] === 'urgent' ? 'badge-warning' : 
                                           ($project['priority'] === 'high' ? 'badge-info' : 'badge-default');
                            ?>
                            <span class="badge <?php echo $priorityClass; ?>">
                                <?php echo $priorityMap[$project['priority']] ?? $project['priority']; ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusClass = $project['progress_status'] === '完了' ? 'badge-success' : 
                                          ($project['progress_status'] === '進行中' ? 'badge-info' : 'badge-default');
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($project['progress_status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $project['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている工事予定はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
