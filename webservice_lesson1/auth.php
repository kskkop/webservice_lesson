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
    debug('マイページへ遷移します。');
    header("Location:mypage.html"); //マイページへ
  }

}else{
  debug('未ログインユーザーです。');
}