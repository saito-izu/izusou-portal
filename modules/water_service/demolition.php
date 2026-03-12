<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '解体工事受付 - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $order_number = 'D' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $data = [
        'order_number' => $order_number,
        'customer_name' => $_POST['customer_name'],
        'property_address' => $_POST['property_address'],
        'phone' => $_POST['phone'],
        'scheduled_date' => $_POST['scheduled_date'],
        'demolition_type' => $_POST['demolition_type'],
        'status' => $_POST['status'],
        'assigned_staff' => $_POST['assigned_staff'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('demolition_orders', $data)) {
        $success_message = '解体工事受付を登録しました。受付番号: ' . $order_number;
    }
}

if (isset($_GET['delete'])) {
    $db->delete('demolition_orders', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '解体工事受付を削除しました。';
}

$orders = $db->fetchAll("SELECT * FROM demolition_orders ORDER BY created_at DESC");
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>解体工事受付</h1>
    <p>解体工事の受付と進捗管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規解体工事受付</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="customer_name">顧客名 *</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>
        
        <div class="form-group">
            <label for="property_address">物件住所 *</label>
            <input type="text" id="property_address" name="property_address" required>
        </div>
        
        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="tel" id="phone" name="phone">
        </div>
        
        <div class="form-group">
            <label for="demolition_type">解体種別</label>
            <select id="demolition_type" name="demolition_type">
                <option value="建物全体">建物全体</option>
                <option value="部分解体">部分解体</option>
                <option value="内装解体">内装解体</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="scheduled_date">解体予定日</label>
            <input type="date" id="scheduled_date" name="scheduled_date">
        </div>
        
        <div class="form-group">
            <label for="status">状況 *</label>
            <select id="status" name="status" required>
                <option value="受付">受付</option>
                <option value="見積中">見積中</option>
                <option value="契約済">契約済</option>
                <option value="作業中">作業中</option>
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
    <h2>解体工事受付一覧</h2>
    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>受付番号</th>
                    <th>顧客名</th>
                    <th>物件住所</th>
                    <th>解体種別</th>
                    <th>予定日</th>
                    <th>状況</th>
                    <th>担当者</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['property_address']); ?></td>
                        <td><?php echo htmlspecialchars($order['demolition_type'] ?? '-'); ?></td>
                        <td><?php echo $order['scheduled_date'] ?? '-'; ?></td>
                        <td>
                            <?php
                            $statusClass = $order['status'] === '完了' ? 'badge-success' : 
                                          ($order['status'] === '作業中' ? 'badge-warning' : 'badge-info');
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
        <p>登録されている解体工事受付はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
