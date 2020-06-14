<?php 
//共通変数・ 関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//========================
//画面処理
//========================
//DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);
debug('ユーザーID'.$_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData,true));

// post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報'.print_r($_POST,true));
    debug('var_dump'.$_POST['username']);

    //変数にユーザー情報を代入
    $username = $_POST['username'];
    $tel = (!empty($_POST['tel'])) ? $_POST['tel'] : 0;//三項演算子 [条件式] ? [真の場合] : [偽の場合];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;//後続のバリデーションに引っかかるため、からで送信されてきたら0を入れる
    $addr = $_POST['addr'];
    $age = (!empty($_POST['age'])) ? $_POST['age'] : 0;
    $email = $_POST['email'];

    //DBの情報と入力情報が異なる場合にバリデーションを行う
    if($dbFormData['username'] !== $username){
        //名前の最大文字数チェック
        validMaxLen($username,'username');
    }
    if((int)$dbFormData['tel'] !== $tel){
        //電話番号の形式チェック
        validTel($tel,'tel');
    }
    if($dbFormData['addr'] !== 'addr'){
        //住所の最大文字数チェック
        validMaxLen($addr,'addr');
    }
    if((int)$dbFormData['zip'] !== $zip){
        //DBデータをint型にキャスト（型変換）して変換
        //比較する === !==は厳密に比較するので数字の１と数値の１は別物と判断
        //DBから取得したデータは全て文字列型なのでint型にキャスト
        //郵便番号形式チェック
        validZip($zip,'zip');
    }
    if((int)$dbFormData['age'] !== $age){
        //年齢の半角数字チェック
        validNumber($age,'age');
        //年齢の最大文字数チェック
        validAge($age,'age');
    }
    if($dbFormData['email'] !== $email){
        //最大文字数チェック
        validMaxLen($email,'email');
        if(empty($err_msg['email'])){
            //emailの重複チェック
            validEmailDup($email,'email');
        }
        //emailの形式チェック
        validEmail($email,'email');
        //emailの未入力チェック
        validRequired($email,'email');
    }
    if(empty($err_msg)){
        debug('バリデーションOKです');

        //例外処理
        try{
            //DBへ接続
            $dbh = dbConnect();
            debug('データベース接続');
            //SQL文作成
            $sql = 'UPDATE users SET username = :u_name,tel = :tel,zip = :zip,addr = :addr,age = :age,email = :email WHERE id = :u_id';//, tel = :tel, zip = :zip, addr = :addr, age = :age, email = :email 
            debug('sql文作成');
            $data = array(':u_name' => $username,':tel' => $tel,':zip' => $zip,':addr' => $addr,':age' => $age,':email' => $email,':u_id' => $dbFormData['id']);//, ':tel' => $tel, ':zip' => $zip, ':addr' => $addr, ':age' => $age, ':email' => $email, 
            debug('$dataに情報を入れる');
            debug(':u_nameの中身'.$data[':u_name']);
            debug('$dbFormDataの中身'.$dbFormData['age']);
            debug('$ageの中身'.$age);
    
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            //クエリ成功の場合
            if($data){
                //debug('クエリ成功。');
                debug('マイページへ遷移します。');
                header("Location:mypage.php");//マイページへ
            /*}else{
                debug('クエリに失敗しました。');
                $err_msg['common'] = MSG08;*/
            }
        }catch(Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common']= MSG07;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'プロフィール編集';
require("head.php");
?>
<body class="page-profEdit page-2colum page-logined">
    <!--メニュー-->
    <?php
    require('header.php');
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">
        <h1 class="page-title">プロフィール編集</h1>
        <!--Main-->
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                        名前
                        <input type="text" name="username" value="<?php echo getFormData('username');//フォーム入力保持 ?>">
                    </label>

                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['username'])) echo $err_msg['username'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
                        TEL<span style="font-size:12px;margin-left:5px;">※ハイフンなしでご入力ください</span>
                        <input type="text" name="tel" value="<?php if(!empty(getFormData('tel'))){echo getFormData('tel');}?>">
                    </label>

                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['tel'])) echo $err_msg['tel'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['zip'])) echo 'err'; ?>">
                        郵便番号<span style="font-size:12px;margin-left:5px;">※ハイフンなしでご入力ください</span>
                        <input type="text" name="zip" value="<?php if(!empty(getFormData('zip'))){echo getFormData('zip');} ?>">
                    </label>

                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['zip'])) echo $err_msg['zip'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['addr'])) echo 'err';?>">
                        住所
                        <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
                    </label>

                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['addr'])) echo $err_msg['addr'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
                        年齢
                        <input type="number" name="age" value="<?php if(!empty(getFormData('age'))) echo getFormData('age'); ?>">
                    </label>

                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['age'])) echo $err_msg['age'];
                        ?>
                    </div>

                    <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                        email
                        <input type="text" name="email" value="<?php echo getFormData('email');?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>

                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="変更する">
                    </div>
                </form>
            </div>
        </section>

        <!--サイドバー-->
        <?php
        require('sidebar_mypage.php');
        ?>
    </div>
    <!--footer-->
    <?php
    require('footer.php');
    
    /*
    お世話になっております

プロフィール編集画面はメールアドレスのみ入力されている状態でも送信できる仕様だと思うのですが
名前のみ入力して送信すると電話番号と年齢が未入力なのに$err_msgでてしまいました。
（なぜ電話番号と年齢だけエラーになるのか理解できていません...）

そこで次のように変更すると未入力で送信しても
$err_msgはなくなりました。この処理で問題ないでしょうか？

if(!empty($_POST['tel']) && $dbFormData['tel'] !== $tel){
        //電話番号の形式チェック
        validTel($tel,'tel');
    }
if(!empty($_POST['age']) && $dbFormData['age'] !== $age){
        //年齢の最大文字数チェック
        validMaxLen($age,'age');
        //年齢の半角数字チェック
        validNumber($age,'age');
    }

上記のコードを追記し、さらになんですが、
全て入力した状態で送信すると正しく更新されるんですが、

仮に名前だけ入力した場合 、
「POST送信はされているがデータベースへは更新されない」という現象がおきました。
試しにphpmyadminで直接sql文を書いてみると正しく名前だけ更新することができました。
debug('')で$data[':u_name']の中身やどこまで処理が進んでいるか確認していたんですが
問題なく送信した名前は入っているし、クエリ成功もしていました。

いろいろと悩んだのですが
どうも
「ageに値が入っていない状態だと更新されない」ということがわかりました。
そこでageに最初から値を入れておけばいいのでは？と思いageのデフォルト値を0に変更したら
名前だけ入力しても更新することができました。

これだとユーザー登録した直後は0歳のままですし
実際これで解決と言っていいのでしょうか？

ちょっと自分の中でモヤモヤしてしまっていて...
回答よろしくお願いします。
    
*/
?>