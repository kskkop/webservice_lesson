<?php
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==========================
//ログイン画面処理
//==========================
//post送信されていた場合
if(empty($_POST)){
    debug('POST送信があります。');//ここまで処理が走るとログへ記録

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;//ショートバンドという書き方

    //emailの形式チェック
    validEmail($email,'email');
    //emailの最大文字数チェック
    validMaxLen($email,'email');

    //パスワードの半角英数字チェック
    validHalf($pass,'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass,'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass,'pass');

    //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');

    if(empty($err_msg)){
        debug('バリデーションOKです。');//ここまで処理が走るとログへ記録

        //例外処理
        try{
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            $sql = 'SELECT password,id FROM users WHERE email = :email';
            $data = array(':email'=> $email);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            //クエリ結果の値を取得
            $result = $stmt ->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身:'.print_r($result,true));

            //パスワード照合
            if(!empty($result) && password_verify($pass,array_shift($result))){
                debug('パスワードがマッチしました');
            }
        }
    }
}
?>