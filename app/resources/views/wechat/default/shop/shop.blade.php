@extends('wechat.default.master')
@inject('theme', 'App\Services\ThemeService')

@section('title', '会员商城－'. $shop->title)

@push('links')
{!! $theme->loadWechatCss('shopping') !!}
@if(count($shop->recommended_items) > 0)
    <link rel="stylesheet" href="/components/unslider/dist/css/unslider.css">
    <link rel="stylesheet" href="/components/unslider/dist/css/unslider-dots.css">
@endif
<style type="text/css">
    .container{
        position: relative;
    }

    /*******************************主题颜色******************************************/
    .cart-bar .order-submit,
    .item-cart-bar .multi-add
    {
        background-color: {{ $theme->colors['BUTTON1'] or '#0092DB' }};
    }

    .item-cart-bar .subtract,
    .item-cart-bar .add {
        color: {{ $theme->colors['BUTTON2'] or '#0092DB' }};
    }

    .cart-list-wrapper .cart-header>h3 {
        border-color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .price{
        color: #FF9933;
    }

    .bonus-require{
        color: #339900;
    }
    
    .content-none{
        color: #777;
        text-align: center;
        margin-top: 40px;
    }

    .content-none .icon{
        height: 50px;
        width: 50px;
        border-radius: 25px;
        margin: 10px auto;
        padding-top:7px;
        background-color: #eee;
        color: #fff;
        font-size: 30px;
    }

    .item-page .item-images img {
        width:100%;
        height:50vw;
    }


</style>
@endpush

@section('content')
<div class="container">
    @if($shop != null )
        @if(count($shop->recommended_items) > 0)
            <div class="row item-page">
                <div class="item-images">
                    <ul>
                        @foreach($shop->recommended_items as $item)
                            <li>
                                @if(isset($item['url']) && $item['url'])
                                    <a href="{{$item['url']}}">
                                        <img src="{{$item['image'] or '/upload/noimg.png'}}">
                                    </a>
                                @else
                                    <a href="/wechat/mall/item/{{$item['id']}}">
                                        <img src="{{strlen($item['image']) > 0 ? $item['image'] : '/upload/noimg.png'}}">
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @foreach ($shop->categories as $category)
            @include($theme->getViewPath('shop.showcase'))
        @endforeach
        @include($theme->getViewPath('shop.cart_bar'))
    @else
        <div class="content-none">
            <div class="icon">
                <span class="glyphicon glyphicon-piggy-bank"></span>
            </div>

            商城没有开启~
        </div>
    @endif
</div>
@endsection

@section('scripts')
    @parent
    <script src="/common/js/cart.js"></script>
    @if(count($shop->recommended_items) > 0)
        <script src="/components/unslider/dist/js/unslider-min.js"></script>
    @endif
    <script>
        
        $(function(){
            @if(count($shop->recommended_items) > 0)
                $('.item-images').unslider({
                    autoplay: true,
                    infinite: true,
                    arrows: false,
                    nav:true,
                    delay:5000,
                    speed:1000,
                });
            @endif
            var cartData = <?php echo $cartData ? $cartData: '[]'?>;
            if(cartData.suitId){
                cartData = [];
            }

            var mall_settings = {
                menuitems: [
                    { type: 'shop',  title: '积分商城', shopId: '3423432423'},
                    { type: 'split'},
                    { type: 'orders', title: '订单管理'},
                    { type: 'member', title: '会员中心'},
                ]
            };
            
            $.cart = new Cart(cartData);

        });
    </script>
@endsection