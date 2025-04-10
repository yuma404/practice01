<?php
session_start();
require('function.php');

//セッションにidとnameが保存されている場合　MySQLに接続し登録済みのToDoを取得
//セッションにidとnameが保存されていない場合　ログイン画面へ
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
    $todos = [];
    $dsn = 'mysql:dbname=test_db;host=run-php-db;';
    $user = 'test';
    $password = 'test';

    try {
        $db = new PDO($dsn, $user, $password);
        $sth = $db->query("SELECT id, title FROM todo WHERE user_id = '{$id}'");
        $todos = $sth->fetchALL();
    } catch (PDOException $e) {
        print('Error:'.$e->getMessage());
        exit;
    }
}else{
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>ToDoリスト一覧</h1>
        <div class="text-end">
            <p class="fs-4"><?php echo h($name) ?> <button class="btn btn-secondary" onclick="location.href='logout.php'">ログアウト</button></p>
        </div>
        <hr>
        <p><button class="btn btn-primary" onclick="location.href='add.php'">＋新規追加</button></p>
        <ul>
        <?php 
        if($todos != ''): 
            for($i = 0; $i < count($todos); $i++):
        ?>
        <li>
            <a class="fs-4" href="detail.php?id=<?php echo h($todos[$i]['id']); ?>"><?php echo $todos[$i]['title']; ?></a>
        </li>
        <?php 
            endfor;
        endif;
        ?>
        </ul>
    </div>
</body>
</html>