<?php
error_reporting(E_ALL);
ini_set('display','On');

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

    if(empty($_POST['name'])){//連想配列をemptyで空かどうか調べる

        $err_msg['name'] = MSG01;

    }
    if(empty($_POST['email'])){

        $err_msg['email'] = MSG01;

    }
    if(empty($_POST['pass'])){

        $err_msg['pass'] = MSG01;

    }
    if(empty($_POST['pass_re'])){
        
        $err_msg['pass_re'] = MSG01;

    }
    if(empty($err_msg)){//全ての項目が入力されている場合
        //htmlspecialchars は、フォームから送られてきた値や、データベースから取り出した値をブラウザ上に表示する際に使用します。主に、悪意のあるコードの埋め込みを防ぐ目的で使われます。(エスケープと呼ばれます)
        $name = htmlspecialchars($_POST['name'],ENT_QUOTES);//サニタイズ
        $email = htmlspecialchars($_POST['email'],ENT_QUOTES);
        $pass = htmlspecialchars($_POST['pass'],ENT_QUOTES);
        $pass_re = htmlspecialchars($_POST['pass_re'],ENT_QUOTES);

        if(mb_strlen($name) >= 20){//$nameが20文字以上の場合

            $err_msg['name'] = MSG02;

        }
        if(!preg_match(EMAIL_VALID,$email)){//$emailがemailの形式でない場合

            $err_msg['email'] = MSG03;

        }
        if($pass !== $pass_re){//パスワードと再入力があっていない場合

            $err_msg['pass_re'] = MSG04;

        }
        if(empty($err_msg)){//この段階でエラーメッセージがない場合
            
            if(!preg_match(HANKAKU,$pass)){//パスワードが半角の形式でない場合

                $err_msg['pass'] = MSG05;

            }elseif(mb_strlen($pass) < 6){//パスワードが6文字以下の場合

                $err_msg['pass'] = MSG06;

            }
        }
        if(empty($err_msg)){//エラーメッセージが空の場合（バリデーションクリア）
            //DBへの接続準備 phpMyAdminに接続する為コード
            $dsn = 'mysql:dbname=php_op;host=localhost;charset=utf8';
            $user = 'root';
            $password = 'root';
            $option = array(
                //SQL実行時に例外をスロー
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                //デフォルトフェッチモードを連想配列型式に設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                //バックファーどクエリを使う（一度に結果セットを全てを取得し、サーバー負荷を軽減）
                //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );
            //PDOオブジェクト生成（DBへ接続）PHP Data Objects
            $dbh = new PDO($dsn,$user,$password,$options);

            //SQL文（クエリー作成） $dbh というオブジェクトにはprepareというメソッドがある ->メソッド呼び出し
            //INSERT INTO users() usersというテーブルに情報を保存する
            //VALUES() 登録する情報のカラムを指定する 
            //placeholder (:名前,:名前,:名前) 後々情報を(カラムに)当てはめていく（虫食い状態）
            $stmt = $dbh->prepare('INSERT INTO users(name,email,pass,login_time) VALUES(:name,:email,:pass,:login_time)');

            //プレースホルダーに値をセット SQL文を実行 データベースへ情報を渡す
            //->execute その中にあるSQLを実行することができる('INSERT INTO users(name,email,pass,login_time) VALUES(:name,:email,:pass,:login_time)');
            //配列array()VALUESのなかに情報をいれる
            //date('Y-m-d H:i:s')年月日時分秒
            $stmt->execute(array(':name' => $name,':email' => $email, ':pass' => $pass,'login_time' => date('Y-m-d H:i:s')));

            //session（セッション）を使うにはsession_start() sessionを使う準備 必ず実行
            session_start();

            //$_SESSIONは配列の形式 
            //mypageでは$_SESSION['login']がtrueの場合ログインした画面を表示する
            $_SESSION['login'] = true;
            //$_SESSION['NAME']に$nameを代入する ログインしたユーザーの名前
            $_SESSION['NAME'] = $name;
            
            //マイページへ
            header("location:mypage.php");
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
        
        <h1>新規登録</h1>

            
        <form method="post">
            <span class="err_msg"><?php if(!empty($err_msg['name'])) echo $err_msg['name'];?></span>
            <input type="text" name="name" placeholder="お名前" value="<?php if(!empty($_POST['name'])) echo $_POST['name'];?>">

            <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></span>
            <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">

            <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']?></span>
            <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">

            <span class="err_msg"><?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']?></span>
            <input type="password" name="pass_re" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">

            <input type="submit" value="登録">

            <a href="login.php">ログインはこちら</a>

        </form>

    </section>
</body>
</html>