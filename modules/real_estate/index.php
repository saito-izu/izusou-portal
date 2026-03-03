<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '不動産事業';
$db = Database::getInstance();

// データの追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'property_name' => $_POST['property_name'],
        'address' => $_POST['address'],
        'property_type' => $_POST['property_type'],
        'status' => $_POST['status'],
        'price' => $_POST['price'],
        'area' => $_POST['area'],
        'contact_person' => $_POST['contact_person'],
        'notes' => $_POST['notes']
    ];
    
    if ($db->insert('real_estate_properties', $data)) {
        $success_message = '物件情報を追加しました。';
    } else {
        $error_message = '追加に失敗しました。';
    }
}

// データの削除処理
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($db->delete('real_estate_properties', 'id = :id', ['id' => $id])) {
        $success_message = '物件情報を削除しました。';
    } else {
        $error_message = '削除に失敗しました。';
    }
}

// 物件リストの取得
$properties = $db->fetchAll("SELECT * FROM real_estate_properties ORDER BY created_at DESC");

include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>不動産事業</h1>
    <p>物件情報の管理</p>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規物件登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="property_name">物件名 *</label>
            <input type="text" id="property_name" name="property_name" required>
        </div>
        
        <div class="form-group">
            <label for="address">所在地 *</label>
            <input type="text" id="address" name="address" required>
        </div>
        
        <div class="form-group">
            <label for="property_type">物件種別 *</label>
            <select id="property_type" name="property_type" required>
                <option value="">選択してください</option>
                <option value="土地">土地</option>
                <option value="戸建て">戸建て</option>
                <option value="マンション">マンション</option>
                <option value="アパート">アパート</option>
                <option value="別荘">別荘</option>
                <option value="その他">その他</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="status">状態 *</label>
            <select id="status" name="status" required>
                <option value="販売中">販売中</option>
                <option value="契約済">契約済</option>
                <option value="保留中">保留中</option>
                <option value="成約">成約</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="price">価格（円）</label>
            <input type="number" id="price" name="price" step="0.01">
        </div>
        
        <div class="form-group">
            <label for="area">面積（㎡）</label>
            <input type="number" id="area" name="area" step="0.01">
        </div>
        
        <div class="form-group">
            <label for="contact_person">担当者</label>
            <input type="text" id="contact_person" name="contact_person">
        </div>
        
        <div class="form-group">
            <label for="notes">備考</label>
            <textarea id="notes" name="notes"></textarea>
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>物件一覧</h2>
    <?php if (count($properties) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>物件名</th>
                    <th>所在地</th>
                    <th>種別</th>
                    <th>状態</th>
                    <th>価格（円）</th>
                    <th>面積（㎡）</th>
                    <th>担当者</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($property['property_name']); ?></td>
                        <td><?php echo htmlspecialchars($property['address']); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td>
                            <?php
                            $statusClass = 'badge-default';
                            if ($property['status'] === '販売中') $statusClass = 'badge-info';
                            elseif ($property['status'] === '契約済') $statusClass = 'badge-warning';
                            elseif ($property['status'] === '成約') $statusClass = 'badge-success';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($property['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $property['price'] ? number_format($property['price']) : '-'; ?></td>
                        <td><?php echo $property['area'] ? number_format($property['area'], 2) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($property['contact_person'] ?? '-'); ?></td>
                        <td>
                            <a href="?delete=<?php echo $property['id']; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている物件はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
