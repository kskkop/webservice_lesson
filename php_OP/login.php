<?php

if(!empty($_POST)){//フォームに送信されて$_POSTに値が入っている場合

    define('MSG01','入力必須です');//define('定数名','定数の値')
    define('MSG02','20文字以内で入力してください');
    define('MSG03','emailの形式ではありません');
    define('MSG04','パスワード(再入力)が合っていません');
    define('MSG05','パスワードは半角英数字で入力してください');
    define('MSG06','パスワードは6文字以上にしてください');
    define('EMAIL_VALID',"/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/");
    define('HANKAKU',"/^[a-zA-Z0-9]+$/");

    $err_msg = array();//配列を定義 連想配列

    if(empty($_POST['email'])){

        $err_msg['email'] = MSG01;

    }
    if(empty($_POST['pass'])){

        $err_msg['pass'] = MSG01;

    }
    if(empty($err_msg)){
        //htmlspecialcharsを関数化
        function h($s){
            return htmlspecialchars($s,ENT_QUOTES);
        }
        $email = h($_POST['email']);//htmlspecialchars(エンティティ化対象文字列,フラグ)ENT_QUOTES→'文字列'と"文字列"を共に変換する。
        $pass = h($_POST['pass']);

        if(!preg_match(EMAIL_VALID,$email)){//preg_match(チェックしたい形式,その形式に合っているかチェックしたい値)合っていればtrue

            $err_msg['email'] = MSG03;

        }
        if(empty($err_msg)){
            
            if(!preg_match(HANKAKU,$pass)){//preg_match(チェックしたい形式,その形式に合っているかチェックしたい値)合っていればtrue

                $err_msg['pass'] = MSG05;

            }elseif(mb_strlen($pass) < 6){

                $err_msg['pass'] = MSG06;

            }
        }
        if(empty($err_msg)){

            $dsn = 'mysql:dbname=php_op;host=localhost;charset=utf8';
            $user = 'root';
            $password = 'root';
            $option = array(

                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );

            $dbh = new PDO($dsn,$user,$password,$option);

            $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email AND pass = :pass');

            $stmt->execute(array(':email' => $email,':pass' => $pass));

            $result = 0;

            $result = $stmt->fetch(PDO::FETCH_ASSOC);//検索した値を取り出す usersの情報が入っている
            if(empty($result)){
                header("location:mypage.php");
            }
            if(!empty($result)){//$resultが0でない場合($email,$passが一致している場合)
                session_start();

                //webサーバー内にないSESSIONに値を保持する
                //SESSION['login']に値が入っている場合mypage.phpに遷移する
                $_SESSION['login'] = true;//sessionのloginにtrueを代入する
                $_SESSION['NAME'] = $result['name'];//ログインしたユーザーの名前を取得
                header("location:mypage.php");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <section>
        
        <h1>ログイン</h1>

            
        <form method="post">

            <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></span><!--$err_msg['email']が空でない場合 エラーメッセージ-->
            <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">

            <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']?></span>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">

            <input type="submit" value="ログイン">

        </form>

    </section>
</body>
</html>