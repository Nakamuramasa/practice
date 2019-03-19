<?php
session_start();
require_once("util.php");
$gobackURL = "bbs.php";
$loginURL = "login.php";

if(isset($_SESSION['id'])){
  header("Location:$gobackURL");
}elseif(isset($_POST['name']) && isset($_POST['pass'])){
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

    $sql = "SELECT * FROM users WHERE name=:name AND pass=:pass";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':name',$name, PDO::PARAM_STR);
    $stmt->bindParam(':pass', sha1($pass), PDO::PARAM_STR);

    $stmt->execute();

    //ユーザーが存在していた場合
    if($row = $stmt->fetch()){
      $_SESSION['id'] = $row['id'];
      header("Location:$gobackURL");
      exit();
    }else{
      header("Location:$loginURL");
      exit();
    }
  }catch(Exception $e){
    die('エラー:' . $e->getMessage());
  }
}else{
//ログインしていない場合はログインフォームを表示する
 ?>

 <!DOCTYPE html>
 <html lang="ja">
 <head>
   <meta charset="utf-8">
   <title>掲示板練習</title>
   </head>
   <body>
     <h1>掲示板</h1>
     <h2>ログイン</h2>
     <form method="POST" action="login.php">
         <p>ユーザー名:<input type="text" name="name"></P>
         <p>パスワード:<input type="text" name="pass"></p>
     </form>
   </body>
  </html>
<?php } ?>
