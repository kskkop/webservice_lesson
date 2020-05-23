<?php
//メール送信プログラム

if(!empty($to) && !empty($subject) && !empty($comment)){
    //文字化けしないように設定（お決まりパターン）
    mb_language("Japanese");//現在使っている言語設定する
    mb_internal_encoding("UTF-8");//内部の日本語をどうエンコーディング（機械がわかる言語へ変換）するかを設定

    //メール送信設定（送信結果はtrueかfalseで帰ってくる）
    $from = 'info@webukatu.com';

    $result = mb_send_mail($to,$subject,$comment,"FROM".$from);

    //送信結果を判定
    if($result){
        unset($_POST);//unset(引数)引数の値をクリアする
        $msg = 'メールが送信されました';
    }else{
        $msg = 'メールの送信に失敗しました';
    }
}else{
    $msg = '全て入力必須です';
}
?>