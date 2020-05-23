<?php
error_reporting(E_ALL);//全てのエラーを報告する
ini_set('display_errors','On');//画面にエラーを表示させるか

//1.post送信されていた場合
if(!empty($_POST)){

    //本来は最初にバリデーションを行うが今回は省略

    //A.変数にユーザー情報を代入
    $to = $_POST['email'];
    $subject = $_POST['subject'];
    $comment = $_POST['comment'];

    //B.メッセージ表示用の変数を用意
    $msg = '';

    //C.メール送信プログラム（外部のphpファイル）を読み込む
    include('mail.php');//include
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
    <p><?php if(!empty($msg = '')) echo $msg; ?></p><!--もしメッセージが空でなかった場合メッセージを表示する-->

    <h1>お問い合わせ</h1>
    <form method="post">
        <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">

        <input type="text" name="subject" placeholder="件名" value="<?php if(!empty($_POST['subject'])) echo $_POST['subject'];?>">

        <textarea name="comment" placeholder="内容" <?php if(!empty($_POST['comment'])) echo $_POST['comment'];?>></textarea><!--textareaはvalue属性をつけられないので直接phpタグで書く 内容が保持される-->
        
        <input type="submit" value="送信">
    </form>
</body>
</html>