<?php
session_start();
require('function.php');

//check.phpの書き直しから遷移した場合、セッションの内容を$formに代入
if(isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',
        'password' => ''
    ];
}

$error = [];

//リクエストがPOSTメソッドだった場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //各入力の受け取り
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    //ユーザー名が空だった場合　$errorのnameにblankを代入
    //空でない場合　MySQLに接続し、同じユーザー名が使用されているか確認
    if ($form['name'] === '') {
        $error['name'] = 'blank';
    } else {
        $dsn = 'mysql:dbname=test_db;host=run-php-db;';
        $user = 'test';
        $password = 'test';

        try {
            $db = new PDO($dsn, $user, $password);
            $sth = $db->query("SELECT COUNT(*) FROM users WHERE user_name = '{$form['name']}'");
            $cnt = $sth->fetch();
            //受け取ったユーザー名が既に使用されている場合　$errorにduplicateを代入
            if ($cnt['COUNT(*)'] > 0) {
                $error['name'] = 'duplicate';
            }
        } catch (PDOException $e) {
            print('Error:'.$e->getMessage());
            exit;
        }
    }

    //パスワードが空だった場合　$errorのpasswordにblankを代入
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    } else if (strlen($form['password']) < 4) {
        $error['password'] = 'length';
    }
    
    //$errorが空だった場合　セッションにユーザー名とパスワードを保存し会員登録画面へ
    if (empty($error)) {
        $_SESSION['form'] = $form;
        header('Location: check.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録画面</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>登録画面</h1>
        <p>入力した内容を確認して、「登録する」ボタンをクリックしてください</p>
        <form action="" method="post">
            <dl>
                <dt>■ユーザー名</dt>
                <dd>
                    <input type="text" name="name" size="35" maxlength="20" pattern=".*\S.*" value="<?php echo h($form['name']); ?>"/>
                    <?php if (isset($error['name']) && $error['name'] === 'blank'): ?>
                        <p class="text-danger">* ユーザー名を入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['name']) && $error['name'] === 'duplicate'): ?>
                        <p class="text-danger">* 既に使用されています</p>
                    <?php endif; ?>
                </dd>
                <dt>■パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="20" pattern=".*\S.*" value="<?php echo h($form['password']); ?>"/>
                    <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
                    <p class="text-danger">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['password']) && $error['password'] === 'length'): ?>
                    <p class="text-danger">* パスワードは４文字以上で入力してください</p>
                    <?php endif; ?>
                </dd>
            </dl>
            <a href="login.php">←戻る</a> | 
            <input class="btn btn-primary" type="submit" value="登録する" />
        </form>
    </div>
</body>
</html>