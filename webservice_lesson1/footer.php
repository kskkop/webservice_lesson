<footer id="footer">
    Copyright <a href="http://webukatu.com/">ウェブカツ!!WEBサービス部"</a>.ALL Rights Reserved.
</footer>

<script src="js/vendor/jquery-3.4.1.min.js"></script>
<script>
    $(function() {
        var $ftr = $('#footer');
        if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
            $ftr.attr({
                'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
            });
        }
        //メッセージ表示
        var $jsShowMsg = $('#js-show-msg'); //DOMを格納する変数には$をつける
        var msg = $jsShowMsg.text();
        if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
            $jsShowMsg.slideToggle('slow'); //slideToggle 要素をつけ外し
            setTimeout(function() {$jsShowMsg.slideToggle('slow');}, 5000); //5秒後にslideToggle(外し)
        }


        //画像ライブプレビュー
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');
        $dropArea.on('dragover', function(e) {//第一引数にイベント名
            e.stopPropagation();//
            e.preventDefault();
            $(this).css('border','3px #ccc dashed');

        });
        
        $dropArea.on('dragleave',function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border','none');
        });
        $fileInput.on('change',function(e){
            $dropArea.css('border','none');
            var file = this.files[0],           //2.files配列にファイルが入っている this ドラッグ＆ドロップしたもの
                $img = $(this).siblings('.prev-img'),//3. jQueryのsiblingsメソッドで兄弟のimgを取得
                fileReader = new FileReader();   //4.ファイルを読み込むFileReaderオブジェクト

            //5. 読み込みが完了した際のイベントパンドラ。imgのsrcにデータをセット
            fileReader.onload = function(event){//
                //読み込んだデータをimgに設定
                $img.attr('src',event.target.result).show();//src属性に画像を設定している
            };

            //6.画像読み込み
            fileReader.readAsDataURL(file);//readAsDataURL(); 画像ファイル自体をDataURLというものに変換している
                                           //DataURLは画像自体を通常imgタグのsrc属性にはサーバーに置いてある画像ファイルのパスを指定することで
                                           //読み込まれて表示されているがData URLを使えば画像ファイル自体を文字列にしてしまい、そのままsrc属性にセットして表示できる
                                           //DataURLとは画像を文字列として扱えるものでimgタグのsrcに画像のパスを入れるかわりに
                                           //画像自体を文字列にして入れてしまうことで表示させるもの
        });

        //テキストエリアカウント
        var $countUp = $('#js-count'),
            $countView =$('#js-count-view');
        $countUp.on('keyup',function(e){
            $countView.html($(this).val().length);
        });

        //画像切替
        var $switchImgSubs = $('.js-switch-img-sub'),
            $switchImgMain = $('#js-switch-img-main');
        $switchImgSubs.on('click',function(e){
            $switchImgMain.attr('src',$(this).attr('src'));//クリックされたものをメインにする
        });
    });
</script>
</body>

</html>