<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>伊豆総株式会社</h1>
                <p>社内ポータルサイト</p>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
            <nav>
                <ul id="mainMenu">
                    <li><a href="/index.php">ホーム</a></li>
                    <li><a href="/modules/real_estate/index.php">不動産事業</a></li>
                    <li><a href="/modules/villa_management/index.php">別荘管理事業</a></li>
                    <li><a href="/modules/water_service/index.php">水道事業</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
