<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '解体工事受付 - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $order_number = 'D' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $data = [
        'order_number' => $order_number,
        'chome_ban' => $_POST['chome_ban'] ?? '',
        'owner_name' => $_POST['owner_name'] ?? '',
        'construction_company' => $_POST['construction_company'] ?? '',
        'water_company' => $_POST['water_company'] ?? '',
        'pipe_diameter' => $_POST['pipe_diameter'] ?? '',
        'water_application_date' => $_POST['water_application_date'] ?? null,
        'payment_date' => $_POST['payment_date'] ?? null,
        'amount' => $_POST['amount'] ?? null,
        'neighborhood_association' => $_POST['neighborhood_association'] ?? '',
        'construction_date' => $_POST['construction_date'] ?? null,
        'notes' => $_POST['notes'] ?? ''
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
        
        <div class="form-row">
            <div class="form-group">
                <label for="chome_ban">丁目番</label>
                <input type="text" id="chome_ban" name="chome_ban" placeholder="例: 1丁目2-3">
            </div>
            
            <div class="form-group">
                <label for="owner_name">施主</label>
                <input type="text" id="owner_name" name="owner_name">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="construction_company">建築業者</label>
                <input type="text" id="construction_company" name="construction_company">
            </div>
            
            <div class="form-group">
                <label for="water_company">水道業者</label>
                <input type="text" id="water_company" name="water_company">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="pipe_diameter">口径</label>
                <select id="pipe_diameter" name="pipe_diameter">
                    <option value="">選択してください</option>
                    <option value="13mm">13mm</option>
                    <option value="20mm">20mm</option>
                    <option value="25mm">25mm</option>
                    <option value="30mm">30mm</option>
                    <option value="40mm">40mm</option>
                    <option value="50mm">50mm</option>
                    <option value="その他">その他</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="water_application_date">給水申込書日付</label>
                <input type="date" id="water_application_date" name="water_application_date">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="payment_date">入金日</label>
                <input type="date" id="payment_date" name="payment_date">
            </div>
            
            <div class="form-group">
                <label for="amount">金額</label>
                <input type="number" id="amount" name="amount" step="0.01" placeholder="例: 150000">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="neighborhood_association">自治会</label>
                <input type="text" id="neighborhood_association" name="neighborhood_association">
            </div>
            
            <div class="form-group">
                <label for="construction_date">工事日</label>
                <input type="date" id="construction_date" name="construction_date">
            </div>
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes" placeholder="その他の情報や特記事項"></textarea>
        </div>
        
        <button type="submit" class="btn">受付登録</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>解体工事受付一覧</h2>
    <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>受付番号</th>
                        <th>丁目番</th>
                        <th>施主</th>
                        <th>建築業者</th>
                        <th>水道業者</th>
                        <th>口径</th>
                        <th>給水申込書日付</th>
                        <th>入金日</th>
                        <th>金額</th>
                        <th>自治会</th>
                        <th>工事日</th>
                        <th>備考</th>
                        <th class="action-cell">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td data-label="受付番号"><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td data-label="丁目番"><?php echo htmlspecialchars($order['chome_ban'] ?? '-'); ?></td>
                            <td data-label="施主"><?php echo htmlspecialchars($order['owner_name'] ?? '-'); ?></td>
                            <td data-label="建築業者"><?php echo htmlspecialchars($order['construction_company'] ?? '-'); ?></td>
                            <td data-label="水道業者"><?php echo htmlspecialchars($order['water_company'] ?? '-'); ?></td>
                            <td data-label="口径"><?php echo htmlspecialchars($order['pipe_diameter'] ?? '-'); ?></td>
                            <td data-label="給水申込書日付"><?php echo $order['water_application_date'] ?? '-'; ?></td>
                            <td data-label="入金日"><?php echo $order['payment_date'] ?? '-'; ?></td>
                            <td data-label="金額">
                                <?php 
                                if (isset($order['amount']) && $order['amount'] !== null) {
                                    echo '¥' . number_format($order['amount']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td data-label="自治会"><?php echo htmlspecialchars($order['neighborhood_association'] ?? '-'); ?></td>
                            <td data-label="工事日"><?php echo $order['construction_date'] ?? '-'; ?></td>
                            <td data-label="備考" class="notes-cell"><?php echo htmlspecialchars($order['notes'] ?? '-'); ?></td>
                            <td data-label="操作" class="action-cell">
                                <a href="?delete=<?php echo $order['id']; ?>" 
                                   class="btn btn-warning btn-sm" 
                                   onclick="return confirm('本当に削除しますか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>登録されている解体工事受付はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
