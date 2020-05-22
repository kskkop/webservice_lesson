<?php 
if(!empty($_FILES)){
    //$file = $_FILES['image']; →array(5){["name"] => "画像パス",["type"] => "image/jpeg",["tmp_name"] => "サーバー側にある一時的に保存されている場所"}
    //var_dumpで$file,$_FILESの中身を要確認
    $upload_path = 'images/'.$file['name'];

    $rst = move_uploaded_file($file['tmp_name'],$upload_path);

    if($rst){
        $img_path = $upload_path;
    }else{
        $err_msg = '画像をアップできません。';
    }
}else{
    $err_msg = '画像を選択してください';
}
?>