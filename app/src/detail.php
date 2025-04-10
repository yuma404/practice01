<?php
session_start();
require('function.php');

//セッションにidとnameが保存されている場合　MySQLに接続しURLから取得したidと同じidの内容を取得
//セッションにidとnameが保存されていない場合　ログイン画面へ
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    //URLからidが取得できなかった場合　一覧画面へ
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if(!$id){
        header('Location: view.php');
        exit();
    }

    $name = $_SESSION['name'];
    $todo = [];
    $dsn = 'mysql:dbname=test_db;host=run-php-db;';
    $user = 'test';
    $password = 'test';
    
    try {
        $db = new PDO($dsn, $user, $password);
        $sth = $db->query("SELECT title, main_text FROM todo WHERE id = '{$id}'");
        $todo = $sth->fetch();
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
    <title>ToDoリスト詳細</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>ToDoリスト詳細</h1>
        <div class="text-end">
            <p class="fs-4"><?php echo h($name) ?> <button class="btn btn-secondary" onclick="location.href='logout.php'">ログアウト</button></p>
        </div>
        <hr>
        <?php if($todo != ''): ?>
            <p class="fs-4"><?php echo h($todo['title']); ?></p>
            <div class="p-2 border border-dark-subtle border-3 rounded-3">
                <p><?php echo nh($todo['main_text']); ?></p>
            </div>
        <?php else: ?>
            <p>詳細が見つかりません。削除された可能性があります。</p>
        <?php endif; ?>
        <br>
        <a href="view.php">一覧へ戻る</a> |
        <button class="btn btn-warning" onclick="location.href='edit.php?id=<?php echo h($id); ?>'">編集</button> |
        <button class="btn btn-danger" onclick="location.href='delete.php?id=<?php echo h($id); ?>'">削除</button>
    </div>
</body>
</html>