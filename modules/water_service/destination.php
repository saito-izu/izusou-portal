<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '行き先表 - 水道事業';
$db = Database::getInstance();

// データの追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'employee_name' => $_POST['employee_name'],
        'destination' => $_POST['destination'],
        'departure_time' => $_POST['departure_time'],
        'return_time' => $_POST['return_time'],
        'contact_info' => $_POST['contact_info'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('destination_board', $data)) {
        $success_message = '行き先情報を追加しました。';
    } else {
        $error_message = '追加に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->delete('destination_board', 'id = :id', ['id' => $id])) {
        $success_message = '行き先情報を削除しました。';
    } else {
        $error_message = '削除に失敗しました。';
    }
}

// 本日の行き先情報を取得
$today = date('Y-m-d');
$destinations = $db->fetchAll("SELECT * FROM destination_board WHERE DATE(departure_time) = ? ORDER BY departure_time", [$today]);

include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>行き先表</h1>
    <p>社員の行き先と帰社予定時刻</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>行き先登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="employee_name">氏名 *</label>
            <input type="text" id="employee_name" name="employee_name" required>
        </div>
        
        <div class="form-group">
            <label for="destination">行き先 *</label>
            <input type="text" id="destination" name="destination" required>
        </div>
        
        <div class="form-group">
            <label for="departure_time">出発時刻 *</label>
            <input type="datetime-local" id="departure_time" name="departure_time" required 
                   value="<?php echo date('Y-m-d\TH:i'); ?>">
        </div>
        
        <div class="form-group">
            <label for="return_time">帰社予定時刻</label>
            <input type="datetime-local" id="return_time" name="return_time">
        </div>
        
        <div class="form-group">
            <label for="contact_info">連絡先</label>
            <input type="text" id="contact_info" name="contact_info">
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>本日の行き先（<?php echo date('Y年m月d日'); ?>）</h2>
    <?php if (count($destinations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>氏名</th>
                    <th>行き先</th>
                    <th>出発時刻</th>
                    <th>帰社予定</th>
                    <th>連絡先</th>
                    <th>備考</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($destinations as $dest): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dest['employee_name']); ?></td>
                        <td><?php echo htmlspecialchars($dest['destination']); ?></td>
                        <td><?php echo date('H:i', strtotime($dest['departure_time'])); ?></td>
                        <td><?php echo $dest['return_time'] ? date('H:i', strtotime($dest['return_time'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($dest['contact_info'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($dest['notes'] ?? '-'); ?></td>
                        <td>
                            <a href="?delete=<?php echo $dest['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>本日の行き先情報はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
