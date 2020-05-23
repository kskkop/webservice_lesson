<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <?php 

    $answer = '仕事よりも君が大事さ。仕事はもう嫌で嫌で嫌で。。嫌で嫌で嫌で。。ほんと嫌なんだ。';
    echo '彼女：仕事と私どっちが大事なのよ！<br>';
    echo '自分：'.$answer.'<br>';

    if($answer === '仕事に決まってるだろ！<br>'){
        echo '彼女：あたたたたたたたっーーー！！<br>';
        echo '自分：ひでぶっ！！';
    }else if($answer === '仕事よりも君が大事さ。仕事はもう嫌で嫌で嫌で。。嫌で嫌で嫌で。。ほんと嫌なんだ。'){
        echo '彼女：仕事してこいやーーーーーー！！<br>';
        echo '自分：ひでぶっ！！';
    }
    ?>
</body>
</html>