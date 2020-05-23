<?php
 
error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告する
ini_set('display_errors','On'); //画面にエラーを表示させるか
 
//post送信されていた場合
if(!empty($_POST)){
 
  //本来は最初にバリデーションを行うが、今回は省略
  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  //DBへの接続準備
  $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
          // SQL実行失敗時に例外をスロー
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          // デフォルトフェッチモードを連想配列形式に設定
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
          // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
      );
 
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
 
  //SQL文（クエリー作成）SELECT * （米印＊にすると全てのカラムを取得する）FROM (検索したいテーブル名) WHERE (検索したい条件) AND(追加する条件)
  //usersカラムのusersテーブルのemail,pass両方はあっているかどうかという検索
  $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email AND pass = :pass');
 
  //プレースホルダに値をセットし、SQL文を実行
  $stmt->execute(array(':email' => $email, ':pass' => $pass));
 
  $result = 0;

  //検索した結果を取り出す 変数->fetchメソッドを実行する fetch(引数) 
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
  //結果が($resultが)０でない場合
  if(!empty($result)){
 
    //SESSION（セッション）を使うにsession_start()メソッドを呼び出す sessionを使う準備 必ず実行
    session_start();
 
    //SESSION['login']に値を代入 $_SESSION 配列の形式になっている 配列の中にloginというキーを作る その中にtrueを入れている
    //webサーバー内にSESSIONという領域に値を保存できる
    $_SESSION['login'] = true;
 
    //マイページへ遷移
    header("Location:mypage.php"); //headerメソッドは、このメソッドを実行する前にechoなど画面出力処理を行っているとエラーになる。
 
  }
}
 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ログイン</h1>
    <form action="" method="post"><!--actionとは送信先空の場合自分自身（index.php）消しても自分自身-->

    <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">

    <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">

    <input type="submit" value="送信">

    </form>
    <a href="mypage.php">マイページへ</a>
</body>
</html>

