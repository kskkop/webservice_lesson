<?php

//================================
// ログイン認証・自動ログアウト
//================================
// ログインしている場合
if( !empty($_SESSION['login_date']) ){//login_dateがあればログインしてる $_SESSION['login_date'] = time();
  debug('ログイン済みユーザーです。');

  // ログインしているが最終ログイン日時＋有効期限 < 現在日時を超えていた場合
  if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){//time()現在日時を取得
    debug('ログイン有効期限オーバーです。');

    // セッションを削除（ログアウトする）
    session_destroy();
    // ログインページへ
    header("Location:login.php");
  }else{
    debug('ログイン有効期限以内です。');
    //最終ログイン日時を現在日時に更新
    $_SESSION['login_date'] = time();
    //今表示しているファイルがlogin.phpだった場合
    //$_SERVER['PHP_SELF']はドメインのパスを返すため
    //basename関数を使うことでファイル名だけ取り出せる
    //無限ループ防止
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイページへ遷移します。');
      header("Location:mypage.php"); //マイページへ
    }

  }

}else{
  debug('未ログインユーザーです。');
  //現在表示しているページがlogin.phpでない場合のみログインページへ遷移（無限ループ防止）
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header("Location:login.php");//ログインページへ
  }
}