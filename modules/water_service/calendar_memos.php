<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'カレンダー型情報メモ管理 - 水道事業';
$db = Database::getInstance();

// 今月の情報取得
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$current_date = sprintf('%04d-%02d-01', $year, $month);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $data = [
        'memo_date' => $_POST['memo_date'],
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'category' => $_POST['category'],
        'priority' => $_POST['priority'],
        'created_by' => $_POST['created_by']
    ];
    
    if ($db->insert('calendar_memos', $data)) {
        $success_message = 'メモを登録しました。';
    }
}

if (isset($_GET['delete'])) {
    $db->delete('calendar_memos', 'id = :id', ['id' => (int)$_GET['delete']]);
    $success_message = 'メモを削除しました。';
}

// 今月のメモを取得
$first_day = date('Y-m-01', strtotime($current_date));
$last_day = date('Y-m-t', strtotime($current_date));
$memos = $db->fetchAll(
    "SELECT * FROM calendar_memos WHERE memo_date BETWEEN ? AND ? ORDER BY memo_date, priority DESC",
    [$first_day, $last_day]
);

// 日付別にグループ化
$memos_by_date = [];
foreach ($memos as $memo) {
    $memos_by_date[$memo['memo_date']][] = $memo;
}

// カレンダー生成用の変数
$first_day_of_month = new DateTime($first_day);
$last_day_of_month = new DateTime($last_day);
$days_in_month = (int)$last_day_of_month->format('d');
$start_day_of_week = (int)$first_day_of_month->format('w'); // 0=日曜日

include __DIR__ . '/../../includes/header.php';
?>

<style>
.calendar-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.calendar-day-header {
    padding: 0.5rem;
    text-align: center;
    font-weight: bold;
    background: var(--primary-color);
    color: white;
    border-radius: 5px;
}

.calendar-day {
    padding: 0.5rem;
    border: 2px solid #ddd;
    border-radius: 5px;
    min-height: 100px;
    cursor: pointer;
    transition: all 0.3s;
}

.calendar-day:hover {
    background: var(--light-color);
    border-color: var(--primary-color);
}

.calendar-day.has-memo {
    background: #fff3cd;
    border-color: var(--accent-color);
}

.calendar-day-number {
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.calendar-memo-item {
    font-size: 0.85rem;
    padding: 0.2rem 0.4rem;
    margin-bottom: 0.3rem;
    background: white;
    border-radius: 3px;
    border-left: 3px solid var(--primary-color);
}

.calendar-memo-item.priority-high {
    border-left-color: var(--warning-color);
}

.calendar-memo-item.priority-urgent {
    border-left-color: #e74c3c;
    font-weight: bold;
}
</style>

<div class="page-header">
    <h1>カレンダー型情報メモ管理</h1>
    <p>日付別の情報とメモの管理</p>
    <a href="/modules/water_service/index.php" class="btn btn-secondary">水道事業トップに戻る</a>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="calendar-container">
    <div class="calendar-controls">
        <a href="?year=<?php echo date('Y', strtotime($current_date . ' -1 month')); ?>&month=<?php echo date('m', strtotime($current_date . ' -1 month')); ?>" class="btn">
            ← 前月
        </a>
        <h2><?php echo date('Y年 n月', strtotime($current_date)); ?></h2>
        <a href="?year=<?php echo date('Y', strtotime($current_date . ' +1 month')); ?>&month=<?php echo date('m', strtotime($current_date . ' +1 month')); ?>" class="btn">
            次月 →
        </a>
    </div>
    
    <div class="calendar-grid">
        <div class="calendar-day-header">日</div>
        <div class="calendar-day-header">月</div>
        <div class="calendar-day-header">火</div>
        <div class="calendar-day-header">水</div>
        <div class="calendar-day-header">木</div>
        <div class="calendar-day-header">金</div>
        <div class="calendar-day-header">土</div>
        
        <?php for ($i = 0; $i < $start_day_of_week; $i++): ?>
            <div class="calendar-day"></div>
        <?php endfor; ?>
        
        <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
            <?php
                $date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $has_memo = isset($memos_by_date[$date_str]);
            ?>
            <div class="calendar-day <?php echo $has_memo ? 'has-memo' : ''; ?>" 
                 onclick="document.getElementById('memo_date').value='<?php echo $date_str; ?>'; document.getElementById('memo_date').focus();">
                <div class="calendar-day-number"><?php echo $day; ?></div>
                <?php if ($has_memo): ?>
                    <?php foreach ($memos_by_date[$date_str] as $memo): ?>
                        <div class="calendar-memo-item priority-<?php echo $memo['priority']; ?>">
                            <?php echo htmlspecialchars($memo['title']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>

<div class="form-container">
    <h2>メモ登録</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="memo_date">日付 *</label>
            <input type="date" id="memo_date" name="memo_date" required 
                   value="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label for="title">タイトル *</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content">内容</label>
            <textarea id="content" name="content" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">カテゴリ</label>
            <select id="category" name="category">
                <option value="">選択してください</option>
                <option value="会議">会議</option>
                <option value="工事">工事</option>
                <option value="点検">点検</option>
                <option value="イベント">イベント</option>
                <option value="その他">その他</option>
            </select>
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
            <label for="created_by">作成者</label>
            <input type="text" id="created_by" name="created_by">
        </div>
        
        <button type="submit" class="btn">登録する</button>
    </form>
</div>

<div class="table-container" style="margin-top: 2rem;">
    <h2>今月のメモ一覧</h2>
    <?php if (count($memos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>タイトル</th>
                    <th>内容</th>
                    <th>カテゴリ</th>
                    <th>優先度</th>
                    <th>作成者</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($memos as $memo): ?>
                    <tr>
                        <td><?php echo $memo['memo_date']; ?></td>
                        <td><?php echo htmlspecialchars($memo['title']); ?></td>
                        <td><?php echo htmlspecialchars($memo['content'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($memo['category'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $priorityMap = ['normal' => '通常', 'high' => '高', 'urgent' => '緊急'];
                            $priorityClass = $memo['priority'] === 'urgent' ? 'badge-warning' : 
                                           ($memo['priority'] === 'high' ? 'badge-info' : 'badge-default');
                            ?>
                            <span class="badge <?php echo $priorityClass; ?>">
                                <?php echo $priorityMap[$memo['priority']] ?? $memo['priority']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($memo['created_by'] ?? '-'); ?></td>
                        <td>
                            <a href="?delete=<?php echo $memo['id']; ?>&year=<?php echo $year; ?>&month=<?php echo $month; ?>" 
                               class="btn btn-warning" 
                               onclick="return confirm('本当に削除しますか？')">削除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>今月のメモはありません。</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
