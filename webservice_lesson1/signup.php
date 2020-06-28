<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「新規登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();//画面表示時などのsessionなどのログを出す

//post送信されていた場合
if(!empty($_POST)){
    debug('post送信');
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
        debug('未入力チェックOK');
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
        validMaxLen($pass_re,'pass_re');
        //パスワード再入力の最小文字数チェック
        validMinLen($pass_re,'pass_re');

        if(empty($err_msg)){
            debug('文字数、形式、emailOK');
            //パスワードとパスワード再入力が合っているかどうかチェック
            validMatch($pass,$pass_re,'pass_re');

            if(empty($err_msg)){
                debug('バリデーションOK');
                debug('例外処理へ');
                //例外処理
                try{
                    //DBへ接続するときは例外処理を行う
                    $dbh = dbConnect();
                    //SQL文作成
                    $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
                    $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                    ':login_time' => date('Y-m-d H:i:s'),
                    ':create_date' => date('Y-m-d H:i:s'));
                    //クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    //クエリ成功の場合
                    if($stmt){
                        //debug('クエリ成功');
                        $sesLimit = 60*60;
                        //最終ログイン日時を現在日時に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //ユーザーIDを格納
                        //$_SESSION['user_id']は$result['id']
                        //lastInsertID 直前にINSERTしたレコードのIDを取得できる
                        $_SESSION['user_id'] = $dbh->lastInsertId();

                        debug('$dbhの中身'.print_r($dbh));
                        debug('$dbh->の中身'.print_r($dbh->lastInsertId()));
                        debug('セッション変数の中身:'.print_r($_SESSION,true));
                        header("Location:mypage.php");//マイページへ
                    /*}else{
                        error_log('クエリに失敗しました。');
                        $err_msg['common'] = MSG07;*/
                    }
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
            <h1><a href="index.php">WEBUKATU MARKET</a></h1>
            <nav id="top-nav">
                <ul>
                    <li><a href="signup.php" class="btn btn-primary">ユーザー登録</a></li>
                    <li><a href="login.php">ログイン</a></li>
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