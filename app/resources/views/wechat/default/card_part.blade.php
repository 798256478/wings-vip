<style>
    .card{
        background : url(/common/imgs/card-backgroud.jpg);
        background-size:100% 100%;
    }
</style>
<div class="card-wrapper row viewport-flip">
    <div class="card flip {{ $card_settings['color'] or '' }}">
        <div class="top clearfix">
            <div class="left">
                <img class="icon img-circle" src="{{ $card_settings['logo'] or '' }}"></img>
            </div>
            <div class="right">
                <strong class="title">{{ $card_settings['title'] or 'VIP Card' }}</strong>
                <small class="subtitle">{{ $card_settings['sub_title'] or ''}}</small>
                <strong class="code"><small> No.</small> {{ $card_info->card_code}}</strong>
            </div>
        </div>
    </div>
    <div class="card flip out back {{ $card_settings['color'] or '' }}" style="display: none">
        <div class="top clearfix">
            <div class="left">
                <img class="icon img-circle" src="{{ $card_info->headimgurl or '' }}"></img>
            </div>
            <div class="right">
                <strong class="name">
                        {{ $card_info->name ? $card_info->name :  ($card_info->nickname ? $card_info->nickname : '匿名') }}
                </strong>
                <strong class="phone"><small> Phone.</small>
                    {{ $card_info->mobile or '000 0000 0000' }}
                </strong>
            </div>
        </div>
        <div class="bottom clearfix">
            <fieldset class="property">
                <a href="/wechat/balance">
                    <label>Coin</label>
                    <span>{{ $card_info->balance or 0}}</span>
                </a>
            </fieldset>
            <fieldset class="property">
                <a href="/wechat/Bonus_redemption">
                    <label>Point</label>
                    <span>{{ $card_info->bonus or 0 }}</span>
                </a>
            </fieldset>
            <fieldset class="property">
                <a href="/wechat/description">
                    <label>Level</label>
                    <span>{{ $card_info->levelStr or '暂无' }}</span>
                </a>
            </fieldset>
        </div>
    </div>
</div>
<script>
$(function(){
    // 在前面显示的元素，隐藏在后面的元素
    $.eleBack = null;
    $.eleFront = null;
    // 纸牌元素们
    $.eleList = $(".card");

    // 确定前面与后面元素
    $.funBackOrFront = function() {
        $.eleList.each(function() {
            if ($(this).hasClass("out")) {
                $.eleBack = $(this);
            } else {
                $.eleFront = $(this);
            }
        });
    };
    $.funBackOrFront();

    $(".card-wrapper").bind("click", function() {
        // 切换的顺序如下
        // 1. 当前在前显示的元素翻转90度隐藏, 动画时间225毫秒
        // 2. 结束后，之前显示在后面的元素逆向90度翻转显示在前
        // 3. 完成翻面效果
        $.eleFront.addClass("out").removeClass("in");
        setTimeout(function() {
            $.eleFront.hide();
            $.eleBack.show();
            $.eleBack.addClass("in").removeClass("out");
            // 重新确定正反元素
            $.funBackOrFront();
        }, 325);
        return false;
    });
});
</script>