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

    const CHOICES = [//PHP配列 ['名前'  => '内容','名前'  => '内容',]
        'WORK' => '仕事に決まってるだろ！',
        'YOUR' => '仕事よりも君が大事さ。仕事はもう嫌で嫌で嫌で。。嫌で嫌で嫌で。。ほんと嫌なんだ。',
        'HENTAI' => 'ぐ・・ぐへ・・ぐへへぇ・・・き・・きみが大事さぁぁ・・・ぐへへぇ。'
    ];
    $answer = null;

    switch(mt_rand(0,2)){
        case 0:
            $answer = CHOICES['WORK'];
        break;
        case 1:
            $answer = CHOICES['YOUR'];
        break;
        case 2;
            $answer = CHOICES['HENTAI'];
    }

    echo '仕事と私どっちを取るのよ!<br>';
    echo '自分：'.$answer.'<br>';

    if($answer === CHOICES['WORK']){
        echo '彼女：あたたたたたたたっーーー！！<br>';
    }
    else if($answer === CHOICES['YOUR']){
        echo '彼女：仕事してこいやーーーーーー！！<br>';
    }
    else if($answer === CHOICES['HENTAI']){
        echo '彼女：ひぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃ！！<br>';
    }
    echo '自分：ひでぶっ！！';
    
    /*
    $rand = mt_rand(0,2);
    $answerRand = $answer[$rand];

    echo '彼女：仕事と私どっちを取るのよ！<br>';
    echo '自分：'.$answer[$rand].'<br>';

    if($answer[$rand] === '仕事に決まってるだろ！'){
        echo '彼女：あたたたたたたたっーーー！！<br>';
        echo '自分：ひでぶっ！！';
    }else if($answer[$rand] === '仕事よりも君が大事さ。仕事はもう嫌で嫌で嫌で。。嫌で嫌で嫌で。。ほんと嫌なんだ。'){
        echo '彼女：仕事してこいやーーーーーー！！<br>';
        echo '自分：ひでぶっ！！';
    }else if($answer[$rand] === 'ぐ・・ぐへ・・ぐへへぇ・・・き・・きみが大事さぁぁ・・・ぐへへぇ。'){
        echo '彼女：ひぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃぃ！！<br>';
        echo 'ひでぶっ！！';
    }
    
    /*
    ?>
</body>
</html>