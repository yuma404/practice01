<?php
session_start();
require('function.php');

//セッションに保存されている場合　$formで受け取り
if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: register.php');
}

//リクエストがPOSTメソッドだった場合　MySQLに接続し内容を登録
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$dsn = 'mysql:dbname=test_db;host=run-php-db;';
	$user = 'test';
	$password = 'test';

	try {
		$db = new PDO($dsn, $user, $password);
		$password = password_hash($form['password'], PASSWORD_DEFAULT);
		$sth = $db->query("INSERT INTO users (user_name, user_password) VALUES ('{$form['name']}', '{$password}')");
	} catch (PDOException $e) {
		print('Error:'.$e->getMessage());
		exit;
	}
	
	//セッションの内容を削除し登録完了画面へ
	unset($_SESSION['form']);
	header('Location: completed.php');
	exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録画面</title>
	<link rel="stylesheet" href="bootstrap-5.3.0-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container-sm">
		<h1>会員登録</h1>
		<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
		<form action="" method="post">
			<dl>
				<dt>■ユーザー名</dt>
				<dd><?php echo h($form['name']); ?></dd>
				<dt>■パスワード</dt>
				<dd>
					<?php echo h($form['password']); ?>
					(本来はpasswordは表示しない)
				</dd>
			</dl>
			<a href="register.php?action=rewrite">←書き直す</a> | 
			<input class="btn btn-primary" type="submit" value="登録する" />
		</form>
	</div>
</body>

</html>