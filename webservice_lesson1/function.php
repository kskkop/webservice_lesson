<?php
//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;//実際にネット上にあげるときはfalseを入れておく
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ:'.$str);
  }
}
//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();//sessionIDを書き換える

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){//画面表示時のsessionなどの情報をログに吐き出す
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){//$_SESSION['login_date'] = time(); ログインした時間を入れている login.phpで定義
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );//$_SESSION['login_limit'] login.phpで定義
  }
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','255文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');//必ずメールアドレスまたはパスワードが違うとする
define('MSG10', '電話番号の形式が違います');
define('MSG11','郵便番号の形式が違います');
define('MSG12','半角数字で入力してください。');
define('MSG13','年齢は99歳までです');
define('MSG14','古いパスワードが違います');
define('MSG15','古いパスワードと同じです');
define('MSG16','文字で入力してください');
define('MSG17','正しくありません');
define('MSG18','有効期限が切れています');
define('SUC01','パスワードを変更しました');
define('SUC02','プロフィールを変更しました');
define('SUC03','メールを送信しました');
define('SUC04','登録しました。');
//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if($str === ''){//金額フォームなどを考えると数値の0はOKにし、空文字はダメにする
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
//バリデーション関数（Email形式チェック）
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try{
    //例外処理
    $dbh = dbConnect();
    //SQL文作成
    //~かつdelete_flg = 0としないと退会した後また新規登録するときにemail重複でエラーとなってしまう
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shift関数は配列の先頭を切り出す関数です。クエリ結果は配列形式で入っています。array_shiftで一つ目だけ取り出して判定します。
    if(!empty(array_shift($result))){
      debug('emailが重複しています。');
      $err_msg['email'] = MSG08;
    }
  }catch (Exception $e){
    error_log('エラー発生' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//電話番号
function validTel($str,$key){
  if(!preg_match("/^(0{1}\d{9,10})$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
//郵便番号
function validZip($str, $key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}
//年齢は二桁まで
function validAge($str,$key){
  if(mb_strlen($str) > 2){
    global $err_msg;
    $err_msg[$key] = MSG13;
  }
}
//半角数字
function validNumber($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
//固定長チェック
function validLength($str,$key,$len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len . MSG16;
  }
}
function validPass($str,$key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str,$key);
  //最小文字数チェック
  validMinLen($str,$key);
}
//selectboxチェック
function validSelect($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG17;
  }
}
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//================================
// データベース
//================================
//DB接続関数
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=freemarket;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    //SQL実行時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    //デフォルトフェッチモードを連想配列に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
    //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}
/*//SQL実行関数
function queryPost($dbh,$sql,$data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダーに値をセットし、SQL文を実行
  $stmt->execute($data);
  return $stmt;
}*/
function queryPost($dbh,$sql,$data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダーに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    $err_msg['common'] = MSG07;
    return 0;
  }
    debug('クエリ成功。');
    return $stmt;
}
function getUser($u_id){
  debug('ユーザー情報を取得します。');

  try{
    $dbh = dbConnect();
    //DBへ接続
    //SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    //クエリ実行
     $stmt = queryPost($dbh,$sql,$data);
     /*
     if($stmt){
       debug('getUserクエリ成功。');
     }else{
       debug('クエリ失敗しました。');
     }
     */
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch(Exception $e) {
    error_log('エラー発生' . $e->getMessage());
  }
  //クエリ結果のデータを返却
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getProduct($u_id,$p_id){
  debug('商品情報を取得します。');
  debug('ユーザーID:'.$u_id);
  debug('商品ID:'.$p_id);
  //例外処理
  try{
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    
    if($stmt){
      //クエリ結果のデータを1レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
  }
}
function getCategory(){
  debug('カテゴリー情報を取得します。');
  //例外処理
  try{
    //DBへ接続
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      //クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch (Exception $e){
    error_log('エラー発生'.$e->getMessage());
  }
}
//==================================
//メール送信
//==================================
function sendMail($from,$to,$subject,$comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    //文字化けしないように設定(お決まりパターン)
    mb_language("Japanese");//現在使っている言語を設定する
    mb_internal_encoding("UTF-8");//内部の日本語をどうエンコーディング (機械がわかる言葉へ変換するかを設定)

    //メールを送信(送信結果はtrueかfalseで返ってくる)
    $result = mb_send_mail($to,$subject,$comment,"From: ".$from);
    //送信結果を判定
    if($result){
      debug('メールを送信しました');
    }else{
      debug('エラー発生 メールの送信に失敗しました。');
    }
  }
}
//==================================
//その他
//==================================
//フォーム入力保持
function getFormData($str){
  global $dbFormData;
  //ユーザーデータがある場合
  if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      //POSTにデータがある場合
      if(isset($_POST[$str])){//金額や郵便番号などのフォームで数字や数値の0が入っている場合もあるのでissetを使うこと
       return $_POST[$str];
      }else{
        //ない場合(フォームにエラーがある＝POSTされているはずなので,まずあり得ないが)DBの情報を表示
       return $dbFormData[$str];
      }
    }else{
      //POSTにデータがあり、DBの情報と違う場合(このフォームも変更していてエラーはないが他のフォームで引っかかっている状態)
      if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
       return $_POST[$str];
      }else{//そもそも変更していない
       return $dbFormData[$str];
      }
    }
  }else{//ユーザーデータがない場合
    if(isset($_POST[$str])){//issetは0が入っているとtrue空の配列もtrue
     return $_POST[$str];
    }
  }
}
//sessionを一回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

//認証キーを生成
function makeRandKey($length = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i = 0; $i < $length; ++$i){
    $str .= $chars[mt_rand(0,61)];//結合代入演算子('.=')ランダムの数字をどんどん足していく
  }
  return $str;
}

//画像処理
function uploadImg($file,$key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));

  if(isset($file['error']) && is_int($file['error'])){//is_int数値かどうか$file['error']は数値
    try{
      //バリデーション
      //$file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
      //「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
      switch($file['error']){
        case UPLOAD_ERR_OK: //OK
        break;
        case UPLOAD_ERR_NO_FILE:   //ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません');//DB接続できなかった場合やファイルアップロードできなかった場合など
                                                                //コードを実行されて初めてエラーになるかどうかわかるもの
                                                                //成功になるものもあるし、エラーになるものもある
        case UPLOAD_ERR_INI_SIZE:   // php.ini定義の最大サイズが超過した場合メモリや画像のサイズ
        case UPLOAD_ERR_FORM_SIZE:  //フォーム定義の最大サイズ超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default://その他の場合
          throw new RuntimeException('その他のエラーが発生しました');
      }
      //$file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを事前でチェックする
      //exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){//第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
                                                                             //in_array 配列に値があるかどうかチェックする
                                                                             //$typeがgif,jpeg,pngかどうか見ている
        throw new RuntimeException('画像形式が未対応です');
      }

      //ファイルデータからsha--1ハッシュをとってファイル名を決定し、ファイルを保存する
      //ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまうと同じファイル名がアップロードされる可能性があり、
      //DBにパスを保存した場合、どっちの画の像パスなのか判断つかなくなってしまう
      //image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);//image_type_to__extension() 拡張子をつける

      if(!move_uploaded_file($file['tmp_name'],$path)){//ファイルを移動する
        throw new RuntimeException('ファイル保存時にエラー発生しました。');
      }
      //保存したファイルパスのパーミッション(権限)を変更する
      chmod($path,0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    }catch(RuntimeException $e){//エラーになった場合ここで捕まえる
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}
?>