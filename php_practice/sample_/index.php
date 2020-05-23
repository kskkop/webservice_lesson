<?php
//E_STRICTレベル以外のエラーを報告する
//画面にエラーを表示させるか

//POST送信されていた場合　フォームに送信されると$_POSTに情報が入る　連想配列の形式で入っている
//!empty　空でない場合　$_POST　最初からある変数更新された情報が入っている　POSTに値が入ってる場合バリデーションのチェックを行う
    //エラーメッセージを格納
    //define→定数を定義 define('定数名','定数の中の値')
    

//配列$err_msgを用意
//配列を定義
//array()配列

//フォームが入力されていない場合
//連想配列をemptyで空かどうか調べる
//フォームに送信されると$_POSTに情報が入る　連想配列の形式で入っている
//キーはhtmlのname属性の値をキーとして情報を取り出せる



//連想配列が空だった場合したのバリデーション　必須項目で何も問題なかった場合

    //変数にユーザー情報を代入する
    //サニタイズ

    //emailの形式でない場合　preg_match("チェックしたい正規表現",チェックしたい値)
    //パスワードが一致してない場合

        //パスワードと再入力が半角英数字じゃない場合
        //mb_strlen()値が何文字かどうかチェック
            //パスワード再入力が6文字未満の場合


            //DBへの接続準備
            //サーバーの名前,データベースの名前;サーバー先（自分のパソコン);データベースの文字コード
            //ユーザーネーム
            //パスワード
            //オプション

                //SQL実行時に例外をスロー

                //デフォルトフェッチモードを連想配列形式に設定

                //バッファードクエリを使う(一度に結果セットを全て取得し、サーバー負荷を軽減)

                //SELECTで得た結果に対してもrowCountメソッドを使えるようにする


            //PDOオブジェクト生成（PDO→DBへ接続する為のもの）

            //SQL文（クエリー作成）
            //$dbhというオブジェクトの中にはprepareというメソッドがある オブジェクト名->メソッド名()
            //INSERT INTO テーブルに情報を保存する 登録する情報のカラムを指定する VALUES() 

            //プレースホルダーに値をセットし、SQL文を実行 データベースへ情報を渡す
            //->execute その中にあるsqlを実行することができる 配列array()VALUEの中に情報をいれる



            //エラーメッセージが空の場合マイページへ


//空の場合そのまま読み込まれる⇩
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