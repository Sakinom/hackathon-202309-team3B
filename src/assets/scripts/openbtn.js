$(".openbtn").click(function () {
    $(this).toggleClass('active');
    $("#g-nav").toggleClass('panelactive');
    //ナビゲーションにpanelactiveクラスを付与

});
$("#g-nav a").click(function () {
    //ナビゲーションのリンクがクリックされたら
    $(".openbtn").removeClass('active');
    //ボタンの activeクラスを除去し
    $("#g-nav").removeClass('panelactive');
    //ナビゲーションのpanelactiveクラスも除去
});