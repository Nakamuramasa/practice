<?php 
require_once("util.php");
$gobackURL = "bbs.php";

//エラー処理
$errors = [];
if(!preg_match("/^[0-9]{4}$/", $_POST['pass'])){
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

//データベースに接続
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
  $sql = "DELETE FROM bbs WHERE id=:id AND pass=:pass";
  $stmt = $pdo->prepare($sql);

  $stmt->bindParam('id',$_POST["id"], PDO::PARAM_INT);
  $stmt->bindParam(':pass', $_POST["pass"], PDO::PARAM_STR);

  $stmt->execute();

}catch(Exception $e){
    echo "エラー：" . $e->getMessage();
}
header("Location: $gobackURL");
exit();
?>
