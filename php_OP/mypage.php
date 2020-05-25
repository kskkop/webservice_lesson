<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

session_start();
//if(empty($_SESSION['login'])) header("location:login.php")

if(!empty($_FILES)){
    // フォームに送られてきたfile
    $file = $_FILES['image'];//formから送られてきたname = imageを受信

    $img_path = '';//送信されたimgのパス 変数空で用意しておく
    $err_msg = '';//エラーメッセージを変数空で用意しておく

    include('upload.php');
 }
 //var_dump($file['name']);
 //var_dump($_FILES);
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
    <section>
    <h1>マイページ</h1>

    <?php if(!empty($_SESSION['login'])){?><!--$_SESSION['login']がtrueでない場合-->

        <!--SESSIONから新規登録、ログインしたときの名前を取得-->
        <p>こんにちは！<?php echo htmlspecialchars($_SESSION['NAME'],ENT_QUOTES); ?>さん！</p>

        <form method="post" enctype="multipart/form-data"><!--enctype 画像などのファイルをフォームから送信するときに使う-->

            <input type="file" name="image"><!--ファイルを送信するフォーム-->

            <input type="submit" value="アップロード">

        </form>
            <?php if(!empty($img_path)){ ?><!--imgが空でない場合-->
                <div class="image">
                    <img src="<?php echo $img_path;?>" alt=""><!--echo でimgのパスを img src = ""に入れる-->
                    <p><?php if(!empty($err_msg)) {echo $err_msg;}?></p><!--err_msgが空でなかった場合画像がアップロードされなかった場合-->
                </div>
            <?php }?>
    <?php }else{?><!--$_SESSIONに値が入っていない場合-->
        <p>ログインできません</p>

    <?php } ?>
    </section>
</body>
</html>