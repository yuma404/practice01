<?php
session_start();
require('function.php');

$error = [];
$user_name = '';
$user_password = '';

//リクエストがPOSTメソッドだった場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //各入力の受け取り
    $user_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $user_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    //ユーザー名が空だった場合　$errorのnameにblankを代入
    if ($user_name === '') {
        $error['name'] = 'blank';
    }

    //パスワードが空だった場合　$errorのpasswordにblankを代入
    if ($user_password === '') {
        $error['password'] = 'blank';
    }
    
    //ユーザー名とパスワードが空ではない場合、MySQLに接続
    if ($user_name != '' && $user_password != '') {
        $dsn = 'mysql:dbname=test_db;host=run-php-db;';
        $user = 'test';
        $password = 'test';

        try {
            $db = new PDO($dsn, $user, $password);
            $sth = $db->query("SELECT id, user_name, user_password  FROM users WHERE user_name = '{$user_name}' LIMIT 1");
            $user = $sth->fetch();
            //ユーザー名で取得した情報が空ではない場合
            if ($user != '') {
                //パスワードが一致した場合、セッションにidとnameを保存し一覧画面へ
                if (password_verify($user_password, $user['user_password'])) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['name'] = $user['user_name'];
                    header('Location: view.php');
                    exit();
                } else {
                    $error['login'] = 'failed';
                }
            } else {
                $error['login'] = 'failed';
            }
            
        } catch (PDOException $e) {
            print('Error:'.$e->getMessage());
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-sm">
        <h1>ログイン画面</h1>
        <p>ユーザー名とパスワードを入力してログインしてください。</p>
        <form action="" method="post">
            <dl>
                <dt>■ユーザー名</dt>
                <dd>
                    <input type="text" name="name" size="35" maxlength="20" value="<?php echo h($user_name); ?>"/>
                    <?php if (isset($error['name']) && $error['name'] === 'blank'): ?>
                    <p class="text-danger">* ユーザー名を入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>■パスワード</dt>
                <dd>
                    <input type="password" name="password" size="35" maxlength="20" value="<?php echo h($user_password); ?>"/>
                    <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
                    <p class="text-danger">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['login']) && $error['login'] === 'failed'): ?>
                    <p class="text-danger">* ログインに失敗しました。正しくご記入ください。</p>
                    <?php endif; ?>
                </dd>
            </dl>
            <input class="btn btn-primary" type="submit" value="ログインする"/>
        </form>
        <p><a href="register.php">新規登録はこちらから</a></p>
    </div>
</body>
</html>