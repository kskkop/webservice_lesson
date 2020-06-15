<?php
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品出品登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//=============================
//画面処理
//=============================
//GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'], $p_id) : '';
//新規登録画面から編集画面か判別用フラグ
$edit_flg = (!empty($dbFormData)) ? false : true;
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();
debug('商品ID：' . $p_id);
debug('フォーム用DBデータ：' . print_r($dbFormData, true));
debug('カテゴリーデータ:' . print_r($dbFormData, true));

//パラメータ改竄チェック
//============================
// GETパラメータはあるが改竄されている(URLをいじくった)場合,正しい商品データが取れないのでマイページへ遷移させる
if (!empty($p_id) && empty($dbFormData)) {
    debug('GETパラメータの商品IDが違います。マイページへ遷移します.');
    header("Location:mypage.php"); //マイページへ
}

//POST送信時処理
//===========================
if (!empty($_POST)) {
    debug('POST送信があります');
    debug('POST情報:' . print_r($_POST, true));
    debug('FILE情報：' . print_r($_FILES, true));

    //変数にユーザー情報を代入
    $name = $_POST['name'];
    $category = $_POST['category_id'];
    $price = (!empty($_POST['price'])) ? $_POST['price'] : 0; //0や空文字の場合は０を入れる。デフォルトのフォームには０が入っている
    $comment = $_POST['comment'];
    //画像をアップロードし、パスを格納
    $pic = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';
    //画像をPOSTしてない（登録していない）がすでにDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
    $pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic3;
    //if(!empty($_FILES['pic2']['name'])){
    //$pic1=$uploadImg($_FILES['pic1'],'pic1')
    //}else{
    //$pec1='';
    //}

    //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
    if (empty($dbFormData)) {
        //未入力チェック
        validRequired($name, 'name');
        //最大文字数チェック
        validMaxLen($name, 'name');
        //セレクトチェックボックスチェック
        validSelect($category, 'category_id');
        //最大文字数チェック
        validMaxLen($comment, 'comment', 500);
        //未入力チェック
        validRequired($price, 'price');
        //半角数字チェック
        validNumber($price, 'price');
    } else {
        if ($dbFormData['name'] !== $name) {
            //未入力チェック
            validRequired($name, 'name');
            //最大文字数チェック
            validMaxLen($name, 'name');
        }
        if ($dbFormData['category_id'] !== $category) {
            //セレクトボックスチェック
            validSelect($category, 'category_id');
        }
        if ($dbFormData['comment'] !== $comment) {
            //最大文字数チェック
            validMaxLen($comment, 'comment', 500);
        }
        if ($dbFormData['price'] !== $price) {
            //未入力チェック
            validRequired($price, 'price');
            //半角数字チェック
            validNumber($price, 'price');
        }
    }
    if (empty($err_msg)) {
        debug('バリデーションOKです。');

        //例外処理
        try {
            //DBへ接続
            $dbh = dbConnect();
            //SQL文作成
            //編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
            if ($edit_flg) {
                debug('DB更新です');
                $sql = 'UPDATE product SET :name, category_id = :category, price = :price, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE user_id = :u_id AND id = :p_id';
                $data = array(':name' => $name, ':category' => $category, ':price' => $price, ':comment' => $comment, ':pic1' => $pic1, 'pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['u_id'], ':p_id' => $p_id);
            } else {
                debug('新規登録です。');
                $sql = 'INSERT INTO product (name, category, price, comment, pic1, pic2, pic3, user_id, create_date) VALUES (:name, :category, :price, :comment, :pic1, :pic2, :pic3, :u_id, :date)';
                $data = array(':name' => $name, ':category' => $category, ':price' => $price, ':comment' => $comment, ':pic1' => $pic1, 'pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['u_id'], ':date' => date('Y-m-d H:i:s'));
            }
            debug('SQL:' . $sql);
            debug('流し込みデータ：' . print_r($data, true));
            //クエリ実行
            $stmt = queryPost($dbh, $sql, $data);

            //クエリ実行の場合
            if ($stmt) {
                $_SESSION['msg_success'] = SUC04;
                debug('マイページへ遷移します。');
                header("Location:mypage.php"); //マイページへf
            }
        } catch (Exception $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = (!$edit_flg) ? '商品出品登録' : '商品編集';
require('head.php');
?>

<body class="page-profEdit page-2colum page-logined">

    <!--メニュー-->
    <?php
    require('header.php');
    ?>
    <!--メインコンテンツ-->
    <div id="contents" class="site-width">
        <h1 class="page-title"><?php echo (!$edit_flg) ? '商品を出品する' : '商品を編集する'; ?></h1>
        <!--Main-->
        <section id="main">
            <div class="form-container">

                <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box; ">
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>

                    <label class="<?php if (!empty($err_msg['name'])) echo 'err'; ?>">
                        商品名<span class="label-require">必須</span>
                        <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if (!empty($err_msg['name'])) echo $err_msg['name'];
                        ?>
                    </div>

                    <label class="<?php if (!empty($err_msg['category_id'])) echo 'err'; ?>">
                        カテゴリ<span class="label-require">必須</span>
                        <select name="category_id" id="">
                            <option value="0" <?php if (getFormData('category_id') === 0) {
                                                    echo 'selected';
                                                } ?>>選択してください</option>
                            <?php
                            foreach ($dbCategoryData as $key => $val) {
                            ?>
                                <option value="<?php echo $val['id'] ?>">
                                    <?php if (getFormData('category_id') == $val['id']) {
                                        echo 'selected';
                                    } ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </label>
                </form>
            </div>
        </section>
    </div>
</body>