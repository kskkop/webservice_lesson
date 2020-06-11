<?php

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証

//==============================
//画面処理
//==============================
//DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザーデータ'.print_r($userData,true));

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります.');
    debug('POST情報'.$_POST);

    //変数にユーザー情報を代入
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    //未入力チェック
    validRequired($pass_old,'pass_old');
    validRequired($pass_new,'pass_new');
    validRequired($pass_new_re,'pass_new_re');

    if(empty($err_msg)){
        debug('未入力チェック');

        //古いパスワードのチェック
        validPass($pass_old,'pass_old');
        //新しいパスワードのチェック
        validPass($pass_new,'pass_new');

        //古いパスワードとDBパスワードを照合 (DB に入っているデータと同じであれば、半角英数字チェックや最大文字チェックは行わなくて良い)
        if(!password_verify($pass_old,$userData['password'])){
            $err_msg['pass_old'] = MSG14;
        }
        //新しいパスワードと古いパスワードが同じかどうかチェック
        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG15;
        }

        //パスワードとパスワード再入力が合っているかどうかチェック(ログイン画面で最大、最小チェックもしていたが、パスワードの方でチェックしているので実は必要ない)
        validMatch($pass_new,$pass_re,'pass_new_re');

        if(empty($err_msg)){
            debug('バリデーションOK');

            //例外処理
            try{
                //DBへ接続
                $dbh = dbConnect();
                //SQL文作成
                $sql = 'UPDATE users SET password = pass WHERE id = :id';
                $data = array(':id' => $_SESSION['user_id'],':pass' => password_hash($pass_new,PASSWORD_DEFAULT));
                //クエリ実行
                $stmt = queryPost($dbh,$sql,$data);

                //クエリ成功の場合
                if($stmt){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC01;

                    //メール送信
                    $username = ($userData['username']) ? $userData['username']:'名無し';
                    $from = 'keisuke96125@gmail.com';
                    $to = $userData['email'];
                    $subject = 'パスワード変更通知 | WEBUKATUMARKET';
                    //EOTはEndOfFileの略。ABCでもなんでもいい。先頭の<<<後の文字列と合わせること。最後のEOTの前後に空白など何も入れてはいけない
                    //EOT内の半角空白も全てそのまま半角空白として扱われるのでインデントはしないこと
                    $comment = <<<EOT
{$username} さん
パスワードが変更されました。

//////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL  http://webukatu.com/
E-mail info@webukatu.com
////////////////////////////////////////
EOT;
                    sendMail($from,$to,$subject,$comment);

                    header("Location:mypage.php");//マイページへ
                }else{
                    debug('クエリに失敗しました');
                    $err_msg['common'] = MSG07;
                }
            }catch(Exception $e){
                error_log('エラー発生:'. $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}

?>
<?php
$siteTitle = 'パスワード変更';
require('head.php');
?>

<body class="page-passEdit page-2colum page-logined">
    <style>
    .form{
        margin-top: 50px;
    }
    </style>

    <!--メニュー-->
    <?php
    require('header.php');
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width"></div>
    <h1 class="page-title">パスワード変更</h1>
    <!--Main-->
</body>