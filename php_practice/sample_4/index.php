<?php
error_reporting(E_ALL);//E_STRICT以外の全てのエラーを取得する
ini_set('display_errors','On');//画面にエラーを表示させるか

//POST送信されているかどうか
if(!empty($_POST)){
    $to = $_POST['email'];
    $subject = $_POST['subject'];
    $comment = $_POST['comment'];
    //メッセージ表示用変数
    $msg = '';

    //メール送信プログラムを読み込む
    include('mail.php');
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
    <p><?if(!empty($msg))echo $msg;?></p>

    <h1>お問い合わせ</h1>
    <form method="post">
        <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>"><!--送信された後も値を保持する-->

        <input type="text" name="subject" placeholder="件名" value="<?php if(!empty($_POST['subject'])) echo $_POST['subject']; ?>">

        <textarea name="comment" placeholder="内容"><?php if(!empty($_POST['comment'])) echo $_POST['comment']; ?></textarea>

        <input type="submit" value="送信">
    </form>
</body>
</html>