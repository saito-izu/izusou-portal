<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '工事受付表 - 水道事業';
$db = Database::getInstance();

// データの追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // 受付番号の生成
    $order_number = 'K' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $data = [
        'order_number' => $order_number,
        'customer_name' => $_POST['customer_name'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'construction_type' => $_POST['construction_type'],
        'scheduled_date' => $_POST['scheduled_date'],
        'status' => $_POST['status'],
        'assigned_staff' => $_POST['assigned_staff'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('construction_orders', $data)) {
        $success_message = '工事受付を登録しました。受付番号: ' . $order_number;
    } else {
        $error_message = '登録に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->delete('construction_orders', 'id = :id', ['id' => $id])) {
        $success_message = '工事受付を削除しました。';
    } else {
        $error_message = '削除に失敗しました。';
    }
}

// 工事リストの取得
$orders = $db->fetchAll("SELECT * FROM construction_orders ORDER BY created_at DESC");

include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>工事受付表</h1>
    <p>工事の受付と進捗管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規工事受付</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="customer_name">顧客名 *</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>
        
        <div class="form-group">
            <label for="address">住所 *</label>
            <input type="text" id="address" name="address" required>
        </div>
        
        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="tel" id="phone" name="phone">
        </div>
        
        <div class="form-group">
            <label for="construction_type">工事種別 *</label>
            <select id="construction_type" name="construction_type" required>
                <option value="">選択してください</option>
                <option value="水道管工事">水道管工事</option>
                <option value="給水設備工事">給水設備工事</option>
                <option value="排水設備工事">排水設備工事</option>
                <option value="修繕工事">修繕工事</option>
                <option value="その他">その他</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="scheduled_date">工事予定日</label>
            <input type="date" id="scheduled_date" name="scheduled_date">
        </div>
        
        <div class="form-group">
            <label for="status">状態 *</label>
            <select id="status" name="status" required>
                <option value="受付">受付</option>
                <option value="見積中">見積中</option>
                <option value="契約済">契約済</option>
                <option value="施工中">施工中</option>
                <option value="完了">完了</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="assigned_staff">担当者</label>
            <input type="text" id="assigned_staff" name="assigned_staff">
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        
        <button type="submit" class="btn">受付登録</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>工事受付一覧</h2>
    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>受付番号</th>
                    <th>顧客名</th>
                    <th>住所</th>
                    <th>工事種別</th>
                    <th>工事予定日</th>
                    <th>状態</th>
                    <th>担当者</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td><?php echo htmlspecialchars($order['construction_type']); ?></td>
                        <td><?php echo $order['scheduled_date'] ?? '-'; ?></td>
                        <td>
                            <?php
                            $statusClass = 'badge-default';
                            if ($order['status'] === '受付') $statusClass = 'badge-info';
                            elseif ($order['status'] === '施工中') $statusClass = 'badge-warning';
                            elseif ($order['status'] === '完了') $statusClass = 'badge-success';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($order['assigned_staff'] ?? '-'); ?></td>
                        <td>
                            <a href="?delete=<?php echo $order['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている工事受付はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
