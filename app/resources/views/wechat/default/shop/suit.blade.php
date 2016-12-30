@extends('wechat.default.master')

@section('title', '商品详情')

@push('links')
<link href="/common/css/wechat/default/shopping.css" rel="stylesheet">
<style>
a {
    color: #333;
}

.container {
    margin-bottom: 30px;
}

/******************************套装信息**********************************/
.suit {
    position: relative;
    z-index: 1;
    height: 90px;
    margin: 15px -5px;
    background: #fff;
}

.suit img {
    float: left;
    width: 80px;
    height: 80px;
    margin: 5px;
}

.suit .title-group {
    float: left;
    max-width: 40%;
    padding: 10px 0 0 5px;
}

.title {
    overflow: hidden;
    margin-bottom: 10px;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.suit .sub-title {
    color: #999;
    font-size: 14px;
    margin-top: 15px;
}

.price-bar {
    margin: 10px 8px 0 0;
    color: orange;
    font-size: 18px;
}

.price-bar .fa-btc {
    position: relative;
    top: -1px;
    font-size: 16px;
}

.suit .price i {
    margin-right: 1px;
    font-size: 16px;
}

.item-cart-bar {
    position: absolute;
    right: 47px;
    top: 50px; 
}

.share{
    position: absolute;
    right: 12px;
    top: 50px;
    font-size: 17px;
    border-left: 1px solid #dfdfdf;
    padding-left: 10px;
    color: {{ $theme->colors['THEME'] or '#0092DB' }};
}

/*******************************子商品信息***********************************/
.commodity-group {
    position: relative;
    margin-bottom: 15px;
}

.commodity-group .tree {
    position: absolute;
    z-index: 0;
    top: -33px;
    left: -5px;
    width: 2px;
    height: 100%;
    border-radius: 0 0 0 4px;
    background: #ccc;
}

.commodity-group .tree-branch {
    position: relative;
    top: 37px;
    left: -4px;
    width: 12px;
    height: 1px;
    background: #ccc;
}

.commodity-group .title-group {
    float: left;
    max-width: 50%;
    padding: 10px 0 0 5px;
}

.specification {
    margin: -3px 8px 0 0;
    text-align: right;
}

.specification .count {
    color: #999;
}

.commodity {
    height: 70px;
    margin: 0 -5px 6px 8px;
    background: #fff;
}

.commodity img {
    float: left;
    width: 60px;
    height: 60px;
    margin: 5px;
}

.commodity .sub-title {
    color: #999;
    font-size: 12px;
}

.commodity .money,
.total-money {
    color: #999;
    font-weight: bold;
}

.commodity .money i,
.total-money i {
    margin-right: 1px;
    font-size: 13px;
}

/*************************************购物车Bar******************************************/
.cart-bar .order-submit,
.item-cart-bar .multi-add
{
    background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
}

.item-cart-bar .subtract,
.item-cart-bar .add {
    color: {{ $theme->colors['BUTTON2'] or '#0092DB' }};
}

.cart-list-wrapper .cart-header>h3 {
    border-color: {{ $theme->colors['THEME'] or '#0092DB' }};
}


.selected[data-specification-id] .subtract,
.selected[data-specification-id] .quantity {
    display: inline-block;
}


</style>
@endpush

@section('content')
<div class="container item-page" data-item-id={{$item['id']}} data-specification-id={{$item['specifications'][0]->id}}>
    <div class="suit">
        <img src="{{$item['image'][0]?$item['image'][0]:'/common/imgs/noimg.png'}}">
        <div class="title-group">
            <div class="item-title">{{$item['name']}}</div>
            <div class="sub-title">{{$item['summary']}}</div>
        </div>
        <div class="price-bar pull-right" >
            @if($item['price'])
            <span class="price">
                <i class="fa fa-cny" aria-hidden="true"></i>
                <span class="value">{{$item['price']}}</span> 
            </span>
            @endif
        
            @if ($item['price'] && $item['bonus_require'])
            <span class="price-and-bonus">
                +
            </span>
            @endif
            
            @if($item['bonus_require'])
            <span class="bonus-require">
                <i class="fa fa-btc" aria-hidden="true"></i>
                <span class="value">{{$item['bonus_require']}}</span>
            </span>
            @endif
        </div>
        <div class="item-cart-bar" data-specification-id={{$item['specifications'][0]->id}}>
            <i class="subtract fa fa-minus"></i>
            <span class="quantity"></span>
            <i class="add fa fa-plus " ></i>
        </div>
        <div class="share" id="share">
            <i class="fa fa-share-alt"></i>
        </div>
    </div>
    <div class="commodity-group">
        <div class="tree"></div>
        <?php $total_price = 0;?>
        @foreach($item['suit'] as $commodity)
            <div>
                <div class="tree-branch"></div>
                <a href="/wechat/mall/item/{{$commodity['commoditySpecifications']['commodity']['id']}}">
                    <div class="commodity">
                        <img src="{{$commodity['commoditySpecifications']['commodity']['image'][0]?$commodity['commoditySpecifications']['commodity']['image'][0]:'/common/imgs/noimg.png'}}">
                        <div class="title-group">
                            <div class="title">{{$commodity['commoditySpecifications']['commodity']['name']}}</div>
                            @if($commodity['commoditySpecifications']['name'])<div class="sub-title">规格: {{$commodity['commoditySpecifications']['name']}}</div>@endif
                        </div>
                        <div class="specification pull-right">
                            <div class="money"><i class="fa fa-cny" aria-hidden="true"></i>{{$commodity['commoditySpecifications']['price']}}</div>
                            <div class="count">x{{$commodity['count']}}</div>
                        </div>
                    </div>
                </a>
                <?php $total_price += $commodity['commoditySpecifications']['price'] * $commodity['count'];?>
            </div>
        @endforeach
    </div>
    <span class="pull-right total-money">合计: <i class="fa fa-cny" aria-hidden="true"></i>{{sprintf('%.2f',$total_price)}}</span>
    
    <!--  购物车  -->
    @include($theme->getViewPath('shop.cart_bar'))
</div>
@endsection

@section('scripts')
    @parent
    <script src="/common/js/cart.js"></script>
    <script>
        $(function(){

            var cartData = <?php echo $cartData ? $cartData : '[]'?>;

            var mall_settings = {
                menuitems: [
                    { type: 'shop',  title: '积分商城', shopId: '3423432423'},
                    { type: 'split'},
                    { type: 'orders', title: '订单管理'},
                    { type: 'member', title: '会员中心'},
                ]
            };
            
            $.cart = new Cart(cartData);

            $('#share').click(function(){
                swal({
                    title:'',
                    text: "<img src='{{$qrCode}}' style='width:200px;height:200px'><br><span>使用微信扫一扫或者将此二维码分享给你的朋友们</span>",
                    html:true,
                    allowOutsideClick:true,
                });
            });


        })
        
    </script>
@endsection