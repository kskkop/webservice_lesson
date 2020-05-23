<?php
error_reporting(E_ALL);//E_STRICTレベル以外のエラーを報告する 何かエラーがあった時エラーメっセージ　E_ALLは全てのエラー
ini_set('display_errors','On');//画面にエラーを表示させるか　

//1.post送信されていた場合その中の処理をする
if(!empty($_POST)){//emptyメソッド変数に対して空かどうかをみている　！なのでPOSTに変数が入ってる場合バリデーションのチェックを行う

    //エラーメッセージを格納
    define('MSG01','入力必須です');//define→定数を定義　define('定数名','定数の中の値')
    define('MSG02','Emailの形式で入力してください');
    define('MSG03','パスワード（再入力）が合っていません');
    define('MSG04','半角英数字のみご利用いただけます');
    define('MSG05','６文字以上で入力してください');

    //配列$err_msgを用意
    $err_msg = array();//配列を定義 ページを読み込んだ初回は定義されない↓HTMLを読み込む
    //array()配列

    //2.フォームが入力されていない場合
    if(empty($_POST['email'])){//連想配列をemptyで調べる

        $err_msg['email'] = MSG01;//連想配列

    }
    if(empty($_POST['pass'])){

        $err_msg['pass'] = MSG01;

    }
    if(empty($_POST['pass_retype'])){

        $err_msg['pass_retype'] = MSG01;

    }

    if(empty($err_msg)){//連想配列が空だった場合したのバリデーションチェックをする

        //変数にユーザー情報を代入する
        $email = htmlspecialchars($_POST['email'],ENT_QUOTES);//サニタイズ
        $pass = htmlspecialchars($_POST['pass'],ENT_QUOTES);
        $pass_re = htmlspecialchars($_POST['pass_retype'],ENT_QUOTES);
    
        //3.emailの形式でない場合　preg_matchメソッド→ルールに沿っているかどうかを確認する　preg_match("ルール",チェック対象にしたい値)
        if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email)){
            $err_msg['email'] = MSG02;
        }
        //4パスワードと再入力が合っていない場合
        if($pass !== $pass_re){
            $err_msg['pass_retype'] = MSG03;
        }
        if(empty($err_msg)){

            //5.パスワードとパスワード再入力が半角英数字でない場合
            if(!preg_match("/^[a-zA-Z0-9]+$/",$pass)){//preg_matchメソッド→ルールに沿っているかどうかを確認する　preg_match("ルール",チェック対象にしたい値)
                $err_msg['pass'] = MSG04;
            }elseif(mb_strlen($pass) < 6){//mb_strlen()→値が何文字かどうかをチェックする　(mb_strlen($pass) < 6)　６文字未満の場合
                //パスワードとパスワード再入力が６文字以上でない場合
                $err_msg['pass'] = MSG05;
            }
        }
        if(empty($err_msg)){

            //DBへの接続準備 myAdminに接続する為のコード
            $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';//データベースの名前 localhost→自分のパソコン
            $user = 'root';
            $password = 'root';
            $options = array(
                //SQL実行失敗時に例外をスロー
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                //デフォルトフェッチモードを連想配列形式に設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                //バックファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
                //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );
            //PDOオブジェクト生成（DBへ接続）
            $dbh = new PDO($dsn, $user, $password, $options);

            //SQL文(クエリー作成) $dbhというオブジェクトにはprepareというメソッドがある メソッドを呼び出すには->メソッド名で呼び出す
            //テーブルの中に情報を保存したいのでINSERT INTO users() というテーブルに保存する
            //登録する情報のカラムを指定する VALUES() 
            //placeholder (:名前,:名前,:名前) 後々から情報を当てはめていく（虫食い状態にする）セキュリティ上placeholderをつかう
            $stmt = $dbh->prepare('INSERT INTO users (email,pass,login_time) VALUES (:email,:pass,:login_time)');

            //プレースホルダに値をセットし、SQL文を実行 データベースへ情報を渡します
            //->execute その中にあるsqlを実行することができる 配列array()VALUEの中に情報をいれる
            //date('Y-m-d H:i:s') 年月日時分秒
            $stmt->execute(array(':email' => $email, ':pass' => $pass, ':login_time' => date('Y-m-d H:i:s')));

            //headerメソッド
            header("location:mypage.php");//マイページへ
        }
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
    <h1>ユーザー登録</h1>
    <form action="" method="post"><!--actionとは送信先　空の場合自分自身（index.php）消しても自分自身-->

        <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span><!--$err_msg(配列)の中のemailの中に値が入ってるかどうか→入っていた場合err_msg['email']を表示させる　if文がないとない箱を表示させようとするのでエラーメッセージ-->
        <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']?>"><!--value=""入力された値を保持する-->

        <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
        <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>"><!--value=""入力された値を保持する-->

        <span class="err_msg"><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span>
        <input type="password" name="pass_retype" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>"><!--value=""入力された値を保持する-->

        <input type="submit" value="送信">
    </form>
    <a href="mypage.php">マイページへ</a>
</body>
</html>

