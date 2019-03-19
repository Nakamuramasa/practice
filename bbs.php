<?php
require_once("util.php");
//1ページに表示されるコメント数の設定
$num = 10;
$page = 0;
if(isset($_GET['page']) && $_GET['page'] > 0){
  $page = intval($_GET['page'])-1;
}
//データベースの設定
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

  $sql = "SELECT * FROM bbs ORDER BY date DESC LIMIT :page, :num";
  $stmt = $pdo->prepare($sql);
  $page *= $num;

  $stmt->bindParam(':page', $page, PDO::PARAM_INT);
  $stmt->bindParam(':num', $num, PDO::PARAM_INT);

  $stmt->execute();
}catch(Exception $e){
  echo '<span class="error">エラーがありました。</span><br>';
  echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>掲示板</title>
  </head>
  <body>
    <h1>掲示板</h1>
    <p><a href="logout.php">ログアウト</a></p>
    <form method="POST" action="write.php">
        <p>名前:<input type="text" name="name"></P>
        <p>タイトル:<input type="text" name="title"></p>
        <textarea name="body"></textarea>
        <p>削除パスワード(数字4桁):<input type="text" name="pass"></p>
        <p><input type="submit" value="書き込む"></p>
    </form>
    <hr />
  <?php
    while($row = $stmt->fetch()):
      $title = $row['title'] ? $row['title'] : ' (無題) ';
  ?>
    <p>名前：<?php echo $row['name'] ?></p>
    <p>タイトル：<?php echo $title ?></p>
    <p><?php echo nl2br($row['body'], false) ?></p>
    <p><?php echo $row['date'] ?></p>

    <form action="delete.php" method="post">
      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
      削除パスワード：<input type="password" name="pass">
      <input type="submit" value="削除">
    </form>

  <?php
    endwhile;
//ページ数を表示する
    try{
      $sql = "SELECT COUNT(*) FROM bbs";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    }catch(Exception $e){
      echo '<span class="error">エラーがありました。</span><br>';
      echo $e->getMessage();
    }
    //コメントの件数を取得する
    $comments = $stmt->fetchColumn();
    //ページ数を計算する
    $max_page = ceil($comments / $num);
    echo '<p>';
    for($i = 1; $i <= $max_page; $i++){
      echo '<a href="bbs.php?page=' . $i . '">' . $i . '</a>&nbsp;';
    }
    echo '</p>'
    ?>
  </body>
  </html>
