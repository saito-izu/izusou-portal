<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '別荘管理事業';
$db = Database::getInstance();

// データの追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'villa_name' => $_POST['villa_name'],
        'address' => $_POST['address'],
        'owner_name' => $_POST['owner_name'],
        'contact_info' => $_POST['contact_info'],
        'contract_start_date' => $_POST['contract_start_date'],
        'contract_end_date' => $_POST['contract_end_date'],
        'management_status' => $_POST['management_status'],
        'last_inspection_date' => $_POST['last_inspection_date'],
        'next_inspection_date' => $_POST['next_inspection_date'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('villa_management', $data)) {
        $success_message = '別荘情報を追加しました。';
    } else {
        $error_message = '追加に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->delete('villa_management', 'id = :id', ['id' => $id])) {
        $success_message = '別荘情報を削除しました。';
    } else {
        $error_message = '削除に失敗しました。';
    }
}

// 別荘リストの取得
$villas = $db->fetchAll("SELECT * FROM villa_management ORDER BY created_at DESC");

include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>別荘管理事業</h1>
    <p>別荘の管理状況と点検スケジュール</p>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規別荘登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="villa_name">別荘名 *</label>
            <input type="text" id="villa_name" name="villa_name" required>
        </div>
        
        <div class="form-group">
            <label for="address">所在地 *</label>
            <input type="text" id="address" name="address" required>
        </div>
        
        <div class="form-group">
            <label for="owner_name">オーナー名 *</label>
            <input type="text" id="owner_name" name="owner_name" required>
        </div>
        
        <div class="form-group">
            <label for="contact_info">連絡先</label>
            <input type="text" id="contact_info" name="contact_info">
        </div>
        
        <div class="form-group">
            <label for="contract_start_date">契約開始日</label>
            <input type="date" id="contract_start_date" name="contract_start_date">
        </div>
        
        <div class="form-group">
            <label for="contract_end_date">契約終了日</label>
            <input type="date" id="contract_end_date" name="contract_end_date">
        </div>
        
        <div class="form-group">
            <label for="management_status">管理状態 *</label>
            <select id="management_status" name="management_status" required>
                <option value="active">管理中</option>
                <option value="paused">一時停止</option>
                <option value="terminated">終了</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="last_inspection_date">前回点検日</label>
            <input type="date" id="last_inspection_date" name="last_inspection_date">
        </div>
        
        <div class="form-group">
            <label for="next_inspection_date">次回点検予定日</label>
            <input type="date" id="next_inspection_date" name="next_inspection_date">
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>別荘一覧</h2>
    <?php if (count($villas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>別荘名</th>
                    <th>所在地</th>
                    <th>オーナー</th>
                    <th>連絡先</th>
                    <th>管理状態</th>
                    <th>前回点検</th>
                    <th>次回点検</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($villas as $villa): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($villa['villa_name']); ?></td>
                        <td><?php echo htmlspecialchars($villa['address']); ?></td>
                        <td><?php echo htmlspecialchars($villa['owner_name']); ?></td>
                        <td><?php echo htmlspecialchars($villa['contact_info'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $statusClass = 'badge-default';
                            $statusText = $villa['management_status'];
                            if ($villa['management_status'] === 'active') {
                                $statusClass = 'badge-success';
                                $statusText = '管理中';
                            } elseif ($villa['management_status'] === 'paused') {
                                $statusClass = 'badge-warning';
                                $statusText = '一時停止';
                            } elseif ($villa['management_status'] === 'terminated') {
                                $statusClass = 'badge-default';
                                $statusText = '終了';
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </td>
                        <td><?php echo $villa['last_inspection_date'] ?? '-'; ?></td>
                        <td><?php echo $villa['next_inspection_date'] ?? '-'; ?></td>
                        <td>
                            <a href="?delete=<?php echo $villa['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている別荘はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
