<?php
session_start();
require('function.php');

//セッションにidとnameが保存されている場合　下記更新の処理へ
//セッションにidとnameが保存されていない場合　ログイン画面へ
if(isset($_SESSION['id']) && isset($_SESSION['name'])){

    $name = $_SESSION['name'];

    //URLからidが取得できなかった場合　一覧画面へ
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if(!$id){
        header('Location: view.php');
        exit();
    }

    //リクエストがPOSTメソッドだった場合
    //リクエストがPOSTメソッドではなかった場合　MySQLに接続しURLから取得したid同じidの内容を取得
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

        //$errorが空だった場合　MySQLに接続しURLから取得したid同じidの内容を更新
        if (empty($error)) {
            $dsn = 'mysql:dbname=test_db;host=run-php-db;';
            $user = 'test';
            $password = 'test';
    
            try {
                $db = new PDO($dsn, $user, $password);
                $sth = $db->query("UPDATE todo SET title = '{$form['title']}', main_text = '{$form['main_text']}' WHERE id = '{$id}'");
            } catch (PDOException $e) {
                print('Error:'.$e->getMessage());
                exit;
            }

            //セッションの内容を削除し一覧画面へ
            unset($_SESSION['form']);
            header('Location: view.php');
            exit();
        }
    } else {
        $dsn = 'mysql:dbname=test_db;host=run-php-db;';
        $user = 'test';
        $password = 'test';
        try {
            $db = new PDO($dsn, $user, $password);
            $sth = $db->query("SELECT title, main_text FROM todo WHERE id = '{$id}'");
            $form = $sth->fetch();
        } catch (PDOException $e) {
            print('Error:'.$e->getMessage());
            exit;
        }
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
    <title>ToDoリスト編集</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>ToDoリスト編集</h1>
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
            <a href="detail.php?id=<?php echo h($id); ?>">←戻る</a> |
            <input class="btn btn-primary" type="submit" value="保存する"/>
        </form>
    </div>
</body>
</html>