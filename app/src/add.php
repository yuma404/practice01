<?php
session_start();
require('function.php');

//セッションにidとnameが保存されている場合　それぞれ$idと$nameに代入
//セッションにidとnameが保存されていない場合　ログイン画面へ
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
}else{
    header('Location: login.php');
    exit();
}

$form = [
    'title' => '',
    'main_text' => ''
];

$error = [];

//リクエストがPOSTメソッドだった場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //各入力の受け取り
    $form['title'] = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $form['main_text'] = filter_input(INPUT_POST, 'main_text');

    //タイトルが空だった場合　$errorのtitleにblankを代入（今回は重複チェックなし）
    if ($form['title'] === '') {
        $error['title'] = 'blank';
    }

    //内容が空だった場合　$errorのmain_textにblankを代入
    if ($form['main_text'] === '') {
        $error['main_text'] = 'blank';
    }

    //$errorが空だった場合　MySQLに接続し入力内容を登録
    if (empty($error)) {
        $dsn = 'mysql:dbname=test_db;host=run-php-db;';
	    $user = 'test';
	    $password = 'test';

	    try {
            $db = new PDO($dsn, $user, $password);
            $sth = $db->query("INSERT INTO todo (user_id, title, main_text) VALUES ('{$id}', '{$form['title']}', '{$form['main_text']}')");
        } catch (PDOException $e) {
            print('Error:'.$e->getMessage());
            exit;
        }

        //セッションの内容を削除し一覧画面へ
        unset($_SESSION['form']);
        header('Location: view.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト追加</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>ToDoリスト追加</h1>
        <div class="text-end">
            <p class="fs-4"><?php echo h($name) ?> <button class="btn btn-secondary" onclick="location.href='logout.php'">ログアウト</button></p>
        </div>
        <hr>
        <form action="" method="post">
                <dl>
                    <dt>タイトル</dt>
                    <dd>
                        <input type="text" name="title" size="35" maxlength="20" pattern=".*\S.*" value="<?php echo h($form['title']); ?>"/>
                        <?php if (isset($error['title']) && $error['title'] === 'blank'): ?>
                        <p class="text-danger">* タイトルを入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>内容</dt>
                    <dd>
                        <textarea name="main_text" cols="40" rows="5" wrap="hard"><?php echo h($form['main_text']); ?></textarea>
                        <?php if (isset($error['main_text']) && $error['main_text'] === 'blank'): ?>
                        <p class="text-danger">* 内容を入力してください</p>
                        <?php endif; ?>
                    </dd>
                </dl>
                <a href="view.php">一覧へ戻る</a> |
                <input class="btn btn-primary" type="submit" value="追加する"/>
            </form>
    </div>
</body>
</html>