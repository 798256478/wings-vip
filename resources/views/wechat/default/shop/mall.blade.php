@extends('wechat.default.master')
@inject('theme', 'App\Services\ThemeService')

@section('title', '会员商城')

@push('links')
@if(isset($config[0]) && count(count($config[0]['carousel']['items']) > 0))
    <link rel="stylesheet" href="/components/Swiper-master/dist/css/swiper.min.css">
@endif
<link href="/common/css/wechat/default/shopping.css" rel="stylesheet">
<style type="text/css">
   
a {
    color: #333;
}

.container {
    position: relative;
    background: #eee;
}

.swiper-slide img {
    width: 100%;
    height: 50vw;
}

.sort {
    background: #fff;
}

.sort ul {
    list-style-type: none;
    padding: 0 10px;
}

.sort ul li {
    float: left;
    margin: 8px 15px 5px;
    text-align: center;
}

.sort ul li .sort-title {
    margin-top: 2px;
    font-size: 12px;
}

.sort .fa-inverse {
    border-radius: 100%;
    background: #888;
}

.sort .no-icon {
    display: inline-block;
    height: 31.72px;
    width: 31.72px;
    margin-bottom: -5px;
    border-radius: 100%;
    background: #888;
}

.sort .fa-stack {
    width: 1.7em;
    height: 1.7em;
    line-height: 1.7em;
}


.sort img {
    width: 31.72px;
    height: 31.72px;
    border-radius: 100%;
}

.showcase {
    position: relative;
    min-height: 80vw;
}

.showcase.row.double .thumbnail .item-img {
    width: 155px;
    height: 155px;
}

.bottom-image {
    height: 80px;
    margin-top: 8px;
    margin-right: -15px;
    margin-left: -15px;
}

.bottom-image img {
    width: 100%;
    height: 100%;
}

.cart-bar .order-submit,
.item-cart-bar .multi-add {
    background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
}

.item-cart-bar .subtract,
.item-cart-bar .add {
    color: {{ $theme->colors['BUTTON2'] or '#0092DB' }};
}

.no-open {
    display: -webkit-box;
    -webkit-box-pack: center;
    -webkit-box-align: center;
    height: 92vh;
}

.no-open p {
    font-size: 30px;
    color: #666;
}

</style>
@endpush

@section('content')
<div class="container">
    @if(isset($config[0]))
    <div>
        
        @if(count($config[0]['carousel']['items']) > 0)
            <div class="row">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($config[0]['carousel']['items'] as $item)
                            <div class="swiper-slide">
                                <a  @if($item['type']=='commodity') 
                                        href="/wechat/mall/item/{{$item['commodityId']}}" 
                                    @elseif($item['type']=='url') 
                                        href="{{$item['url']}}" 
                                    @endif
                                >
                                    <img src="{{$item['image'] or '/common/imgs/noimg.png'}}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        @endif

        @if(count($config[0]['shop']['items']) > 0)
        <div class="row sort">
            <ul>   
                @foreach($config[0]['shop']['items'] as $item)
                    <a href="/wechat/mall/shop/{{$item['_id']}}">
                        <li>
                            @if($item['icon'])
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-lg"></i>
                                    <i class="icon fa fa-stack-1x fa-inverse {{$item['icon']}}"></i>
                                </span>
                            @endif
                            @if($item['image_icon'])
                                <img src="{{$item['image_icon']}}">
                            @endif
                            @if(!$item['icon'] && !$item['image_icon'])
                                <span class="no-icon">
                                </span>
                            @endif
                            <div class="sort-title">{{$item['title']}}</div>
                        </li>
                    </a>
                @endforeach
            </ul> 
        </div>
        @endif

        @foreach ($categories as $category)
            @if($category['items'])
                @include($theme->getViewPath('shop.showcase'))
            @endif
        @endforeach
        
        @if($config[0]['introduce']['url'])
        <div class="bottom-image">
            <img src="{{$config[0]['introduce']['url']}}">
        </div>
        @endif

        @include($theme->getViewPath('shop.cart_bar'))

    </div>
    @else
    
    <div class="no-open">
        <p>商城未开启</p>
    </div>

    @endif

</div>
@endsection

@section('scripts')
    @parent
    <script src="/common/js/cart.js"></script>
    @if(isset($config[0]) && count(count($config[0]['carousel']['items']) > 0))
        <script src="/components/Swiper-master/dist/js/swiper.js"></script>
    @endif
    <script>

        $(function() {

            var mySwiper = new Swiper('.swiper-container',{
                autoplay : 5000,
                speed : 500,
                pagination : '.swiper-pagination',
            })

            var cartData = <?php echo $cartData ? $cartData: '[]'?>;

            if(cartData.suitId){
                cartData = [];
            }
            
            var mall_settings = {
                menuitems: [
                    { type: 'shop',  title: '积分商城', shopId: '3423432423' },
                    { type: 'split'},
                    { type: 'orders', title: '订单管理' },
                    { type: 'member', title: '会员中心' },
                ]
            };
            
            $.cart = new Cart(cartData);

        });

    </script>
@endsection