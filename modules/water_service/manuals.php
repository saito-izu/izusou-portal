<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'マニュアル - 水道事業';
$db = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'title' => $_POST['title'],
        'category' => $_POST['category'],
        'file_path' => $_POST['file_path'],
        'description' => $_POST['description'],
        'version' => $_POST['version'],
        'created_by' => $_POST['created_by']
    ];
    
    if ($db->insert('manuals', $data)) {
        $success_message = 'マニュアルを登録しました。';
    }
}

if (isset($_GET['delete'])) {
    $db->delete('manuals', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = 'マニュアルを削除しました。';
}

$manuals = $db->fetchAll("SELECT * FROM manuals ORDER BY category, title");
include __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1>マニュアル</h1>
    <p>作業マニュアルとガイドラインの管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>新規マニュアル登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="title">タイトル *</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="category">カテゴリ *</label>
            <select id="category" name="category" required>
                <option value="">選択してください</option>
                <option value="工事作業">工事作業</option>
                <option value="安全管理">安全管理</option>
                <option value="機器操作">機器操作</option>
                <option value="緊急対応">緊急対応</option>
                <option value="点検・メンテナンス">点検・メンテナンス</option>
                <option value="その他">その他</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="file_path">ファイルパス（URL）</label>
            <input type="text" id="file_path" name="file_path" 
                   placeholder="例: /documents/manual001.pdf">
        </div>
        
        <div class="form-group">
            <label for="description">説明</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label for="version">バージョン</label>
            <input type="text" id="version" name="version" placeholder="例: 1.0">
        </div>
        
        <div class="form-group">
            <label for="created_by">作成者</label>
            <input type="text" id="created_by" name="created_by">
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>マニュアル一覧</h2>
    <?php if (count($manuals) > 0): ?>
        <?php
        $categories = [];
        foreach ($manuals as $manual) {
            $categories[$manual['category']][] = $manual;
        }
        ?>
        
        <?php foreach ($categories as $category => $items): ?>
            <h3 style="margin-top: 2rem; color: var(--primary-color);">
                📚 <?php echo htmlspecialchars($category); ?>
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>タイトル</th>
                        <th>説明</th>
                        <th>バージョン</th>
                        <th>作成者</th>
                        <th>登録日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $manual): ?>
                        <tr>
                            <td>
                                <?php if ($manual['file_path']): ?>
                                    <a href="<?php echo htmlspecialchars($manual['file_path']); ?>" 
                                       target="_blank" style="color: var(--primary-color);">
                                        <?php echo htmlspecialchars($manual['title']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($manual['title']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($manual['description'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($manual['version'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($manual['created_by'] ?? '-'); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($manual['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $manual['id']; ?>" 
                                   class="btn btn-warning" 
                                   onclick="return confirm('本当に削除しますか？')">削除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p>登録されているマニュアルはありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
