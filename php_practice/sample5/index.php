<?php
error_reporting(E_ALL);//全てのエラーを報告する
ini_set('display_errors','On');//画面にエラーを表示させるか

//1.ファイルが送信されていた場合
if(!empty($_FILES)){//$_FILESという変数 フォームからファイルが送信されると$_FILESという変数に格納される

    //A.フォームから送られたファイルを受信
    $file = $_FILES['image'];//formからname="imageを受信"

    //B.メッセージ表示用と画像表示用の変数を用意
    $msg = '';
    $img_path = '';//画像表示パス

    //C.画像アップロードプログラム（外部のphpファイル）を読み込む
    include('upload.php');

}var_dump($file);

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
    <p><?php if(!empty($msg)) echo $msg; ?></p>

    <h1>画像アップロード</h1>

    <form method="post" enctype="multipart/form-data"><!--enctype 画像などのファイルをフォームから送信する場合使う-->

        <input type="file" name="image"><!--ファイルを送信するフォーム-->

        <input type="submit" value="アップロード">

    </form>
    <?php if(!empty($img_path)){ ?>
        <div class="img_area">
            <p>アップロードした画像</p>
            <img src="<?php echo $img_path;?>" alt="">
        </div>
    <?php } ?>
</body>
</html>