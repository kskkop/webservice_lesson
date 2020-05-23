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
    const CHOICES = [
        'WORK' => '仕事に決まってるだろ',
        'YOUR' => '仕事よりも君が大事さ。仕事はもう嫌で嫌で嫌で。。嫌で嫌で嫌で。。ほんと嫌なんだ。',
        'HENTAI' => 'ぐ・・ぐふ・・ぐふふ・・・き・・きみが大事さぁぁ・・・ぐふふぅ。'
    ];
    $answer = null;

    switch(mt_rand(0,2)){
        case 0:
            $answer = CHOICES['WORK'];
        break;
        case 1:
            $answer = CHOICES['YOUR'];
        break;
        case 2:
            $answer = CHOICES['HENTAI'];
    }

    echo '彼女：仕事と私どっちを取るのよ';
    echo '自分：'.$answer;

    if($answer === CHOICES['WORK']){
        echo '彼女：あたたたたたたたっーーー！！';
        echo '「自分：ひでぶっ！！」';
    }elseif($answer === CHOICES['YOUR']){
        echo '彼女：仕事してこいやーーーーーー！！';
        echo '「自分：ひでぶっ！！」';
    }elseif($answer === CHOICES['HENTAI']){
        for($i = 0;$i<100;$i++){
            echo '自分：あ・・アイシテルヨ・・ぐふ・ぐふふぅ。';
        }
        echo '彼女：おまわりさぁぁぁぁぁぁぁぁぁぁぁぁぁぁぁぁぁぁっん！';
        echo '警察官：どうしましたか！！！！！？なんだ！お前はぁぁぁぁぁ！！！！！';
        echo '自分：え・・あ・・ぃ・・いや・・・。';
        echo 'かちゃっ';
    }

    ?>
</body>
</html>