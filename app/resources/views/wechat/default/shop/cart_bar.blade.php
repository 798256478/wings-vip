<div class="main-mask" style="display:none"></div>
<div class="cart-mask" style="display:none"></div>
<div class="cart-bar">

    <i class="menu-icon fa fa-bars" aria-hidden="true"></i>
    <span class="split">|</span>
    <span class="cart-click-zone">
        <i class="cart-icon fa fa-shopping-cart" aria-hidden="true"></i>
        <span class="total-price null-value">
            <i class="fa fa-cny" aria-hidden="true"></i>
            <span class="value">0</span>
        </span>
        <span class="total-price-and-bonus">
            +
        </span>
        <span class="total-bonus-require null-value">
            <i class="fa fa-btc" aria-hidden="true"></i>
            <span class="value">0</span>
        </span>
    </span>
    <div class="order-submit">去结算</div>

    <div class="shop-menu" style="display:none">
        <!--<div class="home">商城首页</div>
        <div class="split"></div>-->
        @foreach ($shops as $item)
            <div class="section">
                <a href="/wechat/mall/shop/{{$item->id}}">{{$item->title}}</a> 
            </div>
        @endforeach
        <div class="split"></div>
        <div class="section">
            <a href="/wechat/order">我的订单</a> 
        </div>
        <div class="section">
            <a href="/wechat">会员中心</a>
        </div>
    </div>
    <div class="cart-list-wrapper" style="display:none">
        <div class="cart-header">
            <h3>购物车</h3>
            <span class="clear-btn">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
                清空
            </span>
        </div>
        <ul class="cart-list">
            
        </ul>
    </div>
</div>