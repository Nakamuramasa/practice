<?php
require_once("util.php");
$gobackURL = "bbs.php";

$name = $_POST['name'];
$body = $_POST['body'];
$title = $_POST['title'];
$pass = $_POST['pass'];

//エラー処理
$errors = [];
if(!isset($name) || ($name ==="")){
  $errors[] = "名前を入力してください";
}
if(!isset($body) || ($body==="")){
  $errors[] = "本文を入力してください";
}
if(!preg_match("/^[0-9]{4}$/", $pass)){
  $errors[] = "パスワードを4桁の数字で入力してください";
}
if(count($errors) > 0){
  echo '<ol class="error">';
  foreach($errors as $value){
    echo "<li>, $value, </li>";
  }
  echo "<hr>";
  echo "<a href=", $gobackURL, ">戻る</a>";
  exit();
}

setcookie('name', $name, time() + 60 * 60 * 24 * 30);

$user = 'testuser';
$password = 'testpass';
$dbName = 'practice';
$host = '127.0.0.1';
$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

//データベースに接続
try{
  $pdo = new PDO($dsn, $user , $password);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "INSERT INTO bbs (name, title, body, date, pass)
          VALUES (:name, :title, :body, now(), :pass)";
  $stmt = $pdo->prepare($sql);

  $stmt->bindParam(':name',$name, PDO::PARAM_STR);
  $stmt->bindParam(':title', $title, PDO::PARAM_STR);
  $stmt->bindParam(':body', $body, PDO::PARAM_STR);
  $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);

  $stmt->execute();

  header("Location: $gobackURL");
  exit();
}catch(Exception $e){
  die('エラー:' . $e->getMessage());
}
 ?>
