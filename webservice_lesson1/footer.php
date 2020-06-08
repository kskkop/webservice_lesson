<footer id="footer">
    Copyright <a href="http://webukatu.com/">ウェブカツ!!WEBサービス部"</a>.ALL Rights Reserved.
</footer>

<script src="js/vendor/jquery-2.2.2.min.js"></script>
<script>
$(function(){
    var $ftr = $('#footer');
    if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
    }
});
</script>
</body>
</html>