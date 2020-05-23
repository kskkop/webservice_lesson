<?php
$str = '';
$arr = array('か','ず','き','ち');
$num = 10;
if($num<20){
    $str = 'ウェブカツ';
}
for($i = 0;$i < 2;$i++){
    $str = $str.$arr[$i];
}
function combine($str){
    $str = 'ジェーエス';
    return $str.'ぴーえいちぴー';
}
echo combine($str);
echo $str;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
</body>
</html>