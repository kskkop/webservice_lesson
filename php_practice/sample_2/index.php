<?php
error_reporting(E_ALL);//E_STRICTレベル以外のエラーを報告する
ini_set('display_errors','On');//画面にエラーを表示させるか

//POST送信されていた場合　フォームに送信されると$_POSTに情報が入る　連想配列の形式で入っている
if(!empty($_POST)){//!empty　空でない場合　$_POST　最初からある変数更新された情報が入っている　POSTに値が入ってる場合バリデーションのチェックを行う
    //エラーメッセージを格納
    define('MSG01','入力必須です');//define→定数を定義 define('定数名','定数の中の値')
    define('MSG02','emailの形式で入力してください');
    define('MSG03','パスワード（再入力が合っていません）');
    define('MSG04','半角英数字で入力してください');
    define('MSG05','6文字以上で入力してください');

//配列$err_msgを用意
$err_msg = array();//配列を定義
//array()配列

//フォームが入力されていない場合
if(empty($_POST['email'])){//連想配列をemptyで空かどうか調べる
//フォームに送信されると$_POSTに情報が入る　連想配列の形式で入っている
//キーはhtmlのname属性の値をキーとして情報を取り出せる
    $err_msg['email'] = MSG01;

}
if(empty($_POST['pass'])){

    $err_msg['pass'] = MSG01;

}
if(empty($_POST['pass_retype'])){

    $err_msg['pass_retype'] = MSG01;

}

if(empty($err_msg)){//連想配列が空だった場合したのバリデーション　必須項目で何も問題なかった場合

    //変数にユーザー情報を代入する
    $email = htmlspecialchars($_POST['email'],ENT_QUOTES);//サニタイズ htmlspecialchars は、フォームから送られてきた値や、データベースから取り出した値をブラウザ上に表示する際に使用します。主に、悪意のあるコードの埋め込みを防ぐ目的で使われます。(エスケープと呼ばれます)
    $pass = htmlspecialchars($_POST['pass'],ENT_QUOTES);//https://web-dev.xyz/php-htmlspecialchars/
    $pass_re = htmlspecialchars($_POST['pass_retype'],ENT_QUOTES);
    //emailの形式でない場合　preg_match("チェックしたい正規表現",チェックしたい値)
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email)){
        $err_msg['email'] = MSG02;
    }
    //パスワードが一致してない場合
    if($pass !== $pass_re){
        $err_msg['pass_retype'] = MSG03;
    }
    if(empty($err_msg)){

        //パスワードと再入力が半角英数字じゃない場合
        if(!preg_match("/^[a-zA-Z0-9]+$/",$pass)){
            $err_msg['pass'] = MSG04;
        }elseif(mb_strlen($pass) < 6){//mb_strlen()値が何文字かどうかチェック
            //パスワード再入力が6文字未満の場合
            $err_msg['pass'] = MSG05;
        }
        if(empty($err_msg)){
            //DBへの接続準備
            //サーバーの名前,データベースの名前;サーバー先（自分のパソコン);データベースの文字コード
            $dsn = 'mysql:dbname=php_sample01_;host=localhost;charset=utf8'; 
            $user = 'root';//ユーザーネーム
            $password = 'root';//パスワード
            $option = array(
                //SQL実行時に例外をスロー
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                //デフォルトフェッチモードを連想配列形式に設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                //バッファードクエリを使う(一度に結果セットを全て取得し、サーバー負荷を軽減)
                //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );
            //PDOオブジェクト生成（PDO→DBへ接続する為のもの）
            $dbh = new PDO($dsn,$user,$password,$options);

            //SQL文（クエリー作成）
            //$dbhというオブジェクトの中にはprepareというメソッドがある オブジェクト名->メソッド名()
            //INSERT INTO テーブルに情報を保存する 登録する情報のカラムを指定する VALUES() 
            $stmt = $dbh->prepare('INSERT INTO users (email,pass,login_time)VALUE(:email,:pass,:login_time)');

            //プレースホルダーに値をセットし、SQL文を実行 データベースへ情報を渡す
            //->execute その中にあるsqlを実行することができる 配列array()VALUEの中に情報をいれる        
                $stmt->execute(array(
                ':email'=> $email,
                ':pass' => $pass,
                'login_time' => date('Y-m-d H:i:s')
                ));

        } header("Location:mypage.php");//エラーメッセージが空の場合マイページへ
        }
    }
}//空の場合そのまま読み込まれる⇩
//var_dump($_POST);
//var_dump($err_msg);

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
    <h1>ユーザー登録(２周目)</h1>
    <form action="" method="post">
        <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></span><!--$email['email']に値が入っている場合$err_msg['email']を表示する-->
        <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>"><!--入力された値を保持する-->

        <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?></span><!--$email['pass']に値が入っている場合$err_msg['pass']を表示する-->
        <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['email'])) echo $_POST['pass'];?>"><!--入力された値を保持する-->

        <span class="err_msg"><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype'];?></span><!--$email['pass_retype']に値が入っている場合$err_msg['pass_retype']を表示する-->
        <input type="password" name="pass_retype" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>"><!--入力された値を保持する-->

        <input type="submit" value="送信">
    </form>
    <a href="mypage.php">マイページへ</a>
</body>
</html>