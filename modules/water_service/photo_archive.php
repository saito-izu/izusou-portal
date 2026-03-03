<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = '工事写真アーカイブ - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'project_name' => $_POST['project_name'],
        'photo_date' => $_POST['photo_date'],
        'location' => $_POST['location'],
        'photo_path' => $_POST['photo_path'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'uploaded_by' => $_POST['uploaded_by']
    ];
    
    if ($db->insert('construction_photos', $data)) {
        $success_message = '工事写真を登録しました。';
    }
}

if (isset($_GET['delete'])) {
    $db->delete('construction_photos', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = '工事写真を削除しました。';
}

// フィルタリング
$filter_category = $_GET['category'] ?? '';
$filter_date = $_GET['date'] ?? '';

$sql = "SELECT * FROM construction_photos WHERE 1=1";
$params = [];

if ($filter_category) {
    $sql .= " AND category = ?";
    $params[] = $filter_category;
}

if ($filter_date) {
    $sql .= " AND photo_date = ?";
    $params[] = $filter_date;
}

$sql .= " ORDER BY photo_date DESC, created_at DESC";
$photos = $db->fetchAll($sql, $params);

include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>工事写真アーカイブ</h1>
    <p>工事現場の写真管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>写真登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="project_name">プロジェクト名 *</label>
            <input type="text" id="project_name" name="project_name" required>
        </div>
        
        <div class="form-group">
            <label for="photo_date">撮影日 *</label>
            <input type="date" id="photo_date" name="photo_date" required 
                   value="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label for="location">撮影場所</label>
            <input type="text" id="location" name="location">
        </div>
        
        <div class="form-group">
            <label for="photo_path">写真パス（URL） *</label>
            <input type="text" id="photo_path" name="photo_path" required
                   placeholder="例: /photos/project001/photo01.jpg">
        </div>
        
        <div class="form-group">
            <label for="description">説明</label>
            <textarea id="description" name="description"></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">カテゴリ</label>
            <select id="category" name="category">
                <option value="">選択してください</option>
                <option value="着工前">着工前</option>
                <option value="施工中">施工中</option>
                <option value="完成">完成</option>
                <option value="検査">検査</option>
                <option value="その他">その他</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="uploaded_by">登録者</label>
            <input type="text" id="uploaded_by" name="uploaded_by">
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>フィルター</h2>
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <select name="category" id="filter_category">
                <option value="">全カテゴリ</option>
                <option value="着工前" <?php echo $filter_category === '着工前' ? 'selected' : ''; ?>>着工前</option>
                <option value="施工中" <?php echo $filter_category === '施工中' ? 'selected' : ''; ?>>施工中</option>
                <option value="完成" <?php echo $filter_category === '完成' ? 'selected' : ''; ?>>完成</option>
                <option value="検査" <?php echo $filter_category === '検査' ? 'selected' : ''; ?>>検査</option>
                <option value="その他" <?php echo $filter_category === 'その他' ? 'selected' : ''; ?>>その他</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <input type="date" name="date" id="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
        </div>
        <button type="submit" class="btn">フィルター</button>
        <a href="photo_archive.php" class="btn btn-secondary">クリア</a>
    </form>
</div>

<div class="table-container">
    <h2>写真一覧</h2>
    <?php if (count($photos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>プロジェクト名</th>
                    <th>撮影日</th>
                    <th>場所</th>
                    <th>カテゴリ</th>
                    <th>説明</th>
                    <th>登録者</th>
                    <th>写真</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($photos as $photo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($photo['project_name']); ?></td>
                        <td><?php echo $photo['photo_date']; ?></td>
                        <td><?php echo htmlspecialchars($photo['location'] ?? '-'); ?></td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo htmlspecialchars($photo['category'] ?? '-'); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($photo['description'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($photo['uploaded_by'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($photo['photo_path']); ?>" 
                               target="_blank" style="color: var(--primary-color);">
                                📷 表示
                            </a>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $photo['id']; ?>&category=<?php echo urlencode($filter_category); ?>&date=<?php echo urlencode($filter_date); ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている工事写真はありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
