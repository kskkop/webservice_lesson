<footer id="footer">
    Copyright <a href="http://webukatu.com/">ウェブカツ!!WEBサービス部"</a>.ALL Rights Reserved.
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
    $(function() {
        var $ftr = $('#footer');
        if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
            $ftr.attr({
                'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
            });
        }
        //メッセージ表示
        let $jsShowMsg = $('#js-show-msg'); //DOMを格納する変数には$をつける
        let msg = $jsShowMsg.text();
        if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
            $jsShowMsg.slideToggle('show'); //slideToggle 要素をつけ外し
            setTimeout(function() {
                $jsShowMsg.slideToggle('slow');
            }, 5000); //5秒後にslideToggle(外し)
        }


        //画像ライブプレビュー
        let $dropArea = $('.area-drop');
        let $fileInput = $('.input-file');
        $dropArea.on('click', function(e) {
            e.stopPropagation();
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
            let file = this.files[0],           //2.files配列にファイルが入っている
                $img = $(this).siblings('.prev-img'),//3. jQueryのsiblingsメソッドで兄弟のimgを取得
                fileReader = new FileReader();   //4.ファイルを読み込むFileReaderオブジェクト

            //5. 読み込みが完了した際のイベントパンドラ。imgのsrcにデータをセット
            fileReader.onload = function(event){
                //読み込んだデータをimgに設定
                $img.attr('src',event.target.result).show();
            };

            //6.画像読み込み
            fileReader.readAsDataURL($file);
        });

        //テキストエリアカウント
        let $countUp = $('#js-count'),
            $countView =$('#js-count-view');
        $countUp.on('keyup',function(e){
            $countView.html($(this).val().length);
        });
    });
</script>
</body>

</html>