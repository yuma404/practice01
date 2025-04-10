<?php
session_start();
require('function.php');

//セッションにidとnameが保存されている場合　MySQLに接続しURLから取得したid同じidの内容を削除
//セッションにidとnameが保存されていない場合　ログイン画面へ
if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    //URLからidが取得できなかった場合　一覧画面へ
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if(!$id){
        header('Location: view.php');
        exit();
    }

    $dsn = 'mysql:dbname=test_db;host=run-php-db;';
    $user = 'test';
    $password = 'test';

    try {
        $db = new PDO($dsn, $user, $password);
        $sth = $db->query("DELETE FROM todo WHERE id = '{$id}'");
    } catch (PDOException $e) {
        print('Error:'.$e->getMessage());
        exit;
    }
}else{
    header('Location: login.php');
    exit();
}

header('Location: view.php');
exit();
?>