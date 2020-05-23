<?php 
error_reporting(E_ALL);//E_STRICTレベル以外のエラーを報告する
ini_set('display_errors','On');//画面にエラーを表示させるか

//セッションに値が入っているかどうかチェックしたい
session_start();

//ログインしてなければ、login画面へ戻す
if(empty($_SESSION['login']))header("Location:login.php");

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!--$_SESSION['login']にtrueを入れていた-->
<?php if(!empty($_SESSION['login'])){?>
    <h1>マイページ</h1>
    <section>
        <p>
            あなたのemailはです。<br>
            あなたのpassはです
        </p>
        <a href="index.php">ユーザー登録画面へ</a>
    </section>
<?php }else{?>

<p>ログインしてないと見れません</p>

<?php } ?>
</body>
</html>