<?php
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php_log');

//エラーメッセージを定数に格納
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワードの(再入力)が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以内で入力してください');
define('MSG06','255文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください');
define('MSG08','そのEmailは既に登録されています');
define('EMAIL','/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/');
define('HANKAKU','/^[a-zA-Z0-9]+$/');

//配列$err_msgを用意
$err_msg = array();


//バリデーション関数 未入力チェック
function validRequired($str,$key){//$str,$keyはローカル関数
    if(empty($str)){//$str(フォームに入力された値)が空の場合
        global $err_msg;//global 関数内から外部のglobal変数を使う
        $err_msg[$key] = MSG01;//$key('email','pass'など)
    }
}
function validEmail($str,$key){//Email形式チェック
    if(!preg_match(EMAIL,$str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
//バリデーション Email重複チェック
function validEmailDup($email){
    global $err_msg;
    //例外処理
    try{
        //DBへ接続
        $dbh = dbConnect();
        //SQL文実行
        $sql = 'SELECT count(*) FROM users WHERE email = :email';
        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで一つだけ取り出して判定します
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch(Exception $e){
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
function validMatch($str1,$str2,$key){//同値チェック
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
function validMinLen($str, $key,$min = 6){//パスワード最小文字数チェック
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
function validMaxLen($str, $key, $max = 255){//パスワード最大文字数チェック 255文字はデータベースへ接続する前のチェック
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
function validHalf($str,$key){//半角チェック
    if(!preg_match(HANKAKU,$str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//DB接続関数
function dbConnect(){
    //DBへ接続準備
    $dsn = 'mysql:dbname=freemarket;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $option =array(
        //
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //デフォルトフェッチモードを連想配列に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //PDOオブジェクト生成(DB接続)
    $dbh = new PDO($dsn,$user,$password,$option);
    return $dbh;
}
//SQL実行関数
function queryPost($dbh,$sql,$data){
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダーに値をセットし、SQL文を実行
    $stmt->execute($data);
    return $stmt;
}
//post送信されていた場合
if(!empty($_POST)){
    
    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');

    //バリデーションエラーがない場合（未入力チェックエラーがない場合）
    if(empty($err_msg)){

        //emailの形式チェック
        validEmail($email,'email');
        //emailの最大文字数チェック
        validMaxLen($email,'email');
        //emailの重複チェック
         validEmailDup($email);
         
        //パスワードが半角英数字かどうか
        validHalf($pass,'pass');
        //パスワードの最大文字数チェック
        validMaxLen($pass,'pass');
        //パスワードの最小文字数チェック
        validMinLen($pass,'pass');

        //パスワード再入力の最大文字数チェック
        validMaxLen($pass,'pass_re');
        //パスワード再入力の最小文字数チェック
        validMinLen($pass,'pass_re');

        if(empty($err_msg)){

            //パスワードとパスワード再入力が合っているかどうかチェック
            validMatch($pass,$pass_re,'pass_re');

            if(empty($err_msg)){

                //例外処理
                try{
                    //DBへ接続するときは例外処理を行う
                    $dbh = dbConnect();
                    //SQL文作成
                    $sql = 'INSERT INTO users (email,password,login_time,create_date)
                    VALUES (:email,:pass,:login_time,:create_date)';
                    $data = array(':email' => $email, ':pass' => password_hash($pass,PASSWORD_DEFAULT),
                    ':login_time' => date('Y-m-d H:i:s'),
                    ':create_date' => date('Y-m-d H:i:s'));
                    //クエリ実行
                    queryPost($dbh,$sql,$data);

                    header("Location:mypage.html");
                }catch(Exception $e) {
                    error_log('エラー発生:' . $e->getMessage());//ログにエラーメッセージ
                    $err_msg['common'] = MSG07;//画面にもエラーメッセージ
                }

            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザー登録 | WEBUKARU MARKET</title>
    <link rel="stylesheet" href="style.css">
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
    <body class="page-signup page-1colum">

    <!--メニュー-->
    <header>
        <div class="site-width">
            <h1><a href="index.html">WEBUKATU MARKET</a></h1>
            <nav id="top-nav">
                <ul>
                    <li><a href="signup.html" class="btn btn-primary">ユーザー登録</a></li>
                    <li><a href="login.html">ログイン</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

    <!--Main-->
    <section id="main">
        <div class="form-container">
            <form action="" method="post" class="form">
                <h2 class="title">ユーザー登録</h2>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
                Email
                <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                パスワード<span style="font-size:12px">※英数字6文字以上</span>
                <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                </label>
                <div class="area-msg">
                    <?php 
                    if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                パスワード再入力
                <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']?>">
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
                ?>
            </div>
            <div class="btn-container">
                <input type="submit" class="btn btn-mind" value="登録する">
            </div>
            </form>
        </div>

    </section>
    </div>
                <!--footer-->
    <footer id="footer">
        Copyright <a href="http://webukatu.com">ウェブカツ!!WEBサービス部</a>.ALL Rights Reserved.
    </footer>

        <script src="js/vendor/jquery-2.2.2.min.js"></script>
        <script>
        $(function(){
            var $ftr = $('#footer');
            if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
                $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
            }
        });
        </script>

    </body>
</body>
</html>