<?php 
if(!empty($_FILES)){
    //$file = $_FILES['image']; →array(5){["name"] => "画像パス",["type"] => "image/jpeg",["tmp_name"] => "サーバー側にある一時的に保存されている場所"}
    //var_dumpで$file,$_FILESの中身を要確認する
    $upload_path = 'images/'.$file['name'];

    //move_uploaded_file ファイルを移動する $file['tmp_name']から$upload_pathへ
    $rst = move_uploaded_file($file['tmp_name'],$upload_path);

    if($rst){//$rstに値が入っている場合（true）
        $img_path = $upload_path;
    }else{//false
        $err_msg = '画像をアップできません。';
    }
}else{//$_FILESが空の場合
    $err_msg = '画像を選択してください';
}
?>