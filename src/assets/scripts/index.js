//初回のみモーダルをすぐ出す判定。flagがモーダル表示のstart_open後に代入される
var access = $.cookie('access')
if(!access){
    flag = true;
    $.cookie('access', false);
}else{
    flag = false	
}

//モーダル表示
$(".modal-open").modaal({
start_open:flag, // ページロード時に表示するか
overlay_close:true,//モーダル背景クリック時に閉じるか
before_open:function(){// モーダルが開く前に行う動作
    $('html').css('overflow-y','hidden');/*縦スクロールバーを出さない*/
},
after_close:function(){// モーダルが閉じた後に行う動作
    $('html').css('overflow-y','scroll');/*縦スクロールバーを出す*/
}
});

// レンジバナー
document.addEventListener('DOMContentLoaded', function() {
    const rangeItems = document.querySelectorAll('.range_slider');
    for(let i = 0; i < rangeItems.length; i++) {
        const rangeItem = rangeItems[i];
        const rangeItemInput = rangeItem.querySelector('input[type="range"]');
        const min = parseInt(rangeItemInput.getAttribute('min'));
        rangeItem.querySelector('.range_slider_min').innerText = min;
        const max = parseInt(rangeItemInput.getAttribute('max'));
        rangeItem.querySelector('.range_slider_max').innerText = max;
        const rangeItemCurrent = rangeItem.querySelector('.range_slider_input_current span');
        matchCurrent();

        rangeItemInput.addEventListener('input', function() {
            matchCurrent();
        }, false);

        function matchCurrent() {
            const current = parseInt(rangeItemInput.value);
            const ratio = ((current - min) / (max - min)) * 100;
            rangeItemCurrent.innerText = current;
            rangeItemCurrent.style.left = ratio + '%';
        }
    }
}, false);

