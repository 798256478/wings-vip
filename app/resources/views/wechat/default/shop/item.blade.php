@extends('wechat.default.master')
@inject('theme', 'App\Services\ThemeService')
@section('title', '商品详情')

@push('links')
<link rel="stylesheet" href="/components/unslider/dist/css/unslider.css">
<link rel="stylesheet" href="/components/unslider/dist/css/unslider-dots.css">
<link href="/common/css/wechat/default/shopping.css" rel="stylesheet">
<style>
    /*******************************主题颜色******************************************/
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

    .price{
        color: #FF9933;
    }

    .bonus-require{
        color: #339900;
    }

    .item-page .home-link{
        position: absolute;
        top: 30px;
        left: 30px;
        color: #444;
        opacity: 0.7;
    }

    .item-page .share{
        position: absolute;
        right: 15px;
        top:15px;
        padding-left: 15px;
        border-left: 1px solid #dfdfdf;
        color: #888;
        font-size: 20px;
    }

    .item-page .home-link .fa-chevron-left {
        color:#fff;
    }
    /*.details-content {
        width:100%;
    }*/
    .details-content img{
        max-width:320px !important;
        width:320px !important;
        width:expression(document.body.clientWidth>320?"320px":"auto") !important;
        overflow:hidden !important;
        height:auto !important;
    }
</style>
@endpush

@section('content')
<div class="container item-page" data-item-id={{$item['id']}} 
    @if($item['is_single_specification'])
        data-specification-id={{$item['specifications'][0]->id}}
    @else
        data-multi-specification
    @endif
>
    <div class="row">
        <div class="item-images">
            <ul>
                @foreach($item['image'] as $image)
		      	<li>
		        	<img src="{{$image or '/upload/noimg.png'}}">
		      	</li>
		      	@endforeach
            </ul>
        </div>
        <a class="home-link" href="javascript:" onclick="self.location=document.referrer;">
            <i class="fa fa-chevron-circle-left fa-3x" aria-hidden="true"></i>
        </a>
    </div>
    <div class="group info">
        <span class="item-title">{{$item['name']}}</span>
{{--        <h4 class="sub-title">{{$item['']['sub_title']}}</h4>--}}
        <div class="share" id="share">
            <i class="fa fa-share-alt"></i>
        </div>

        @if($item['is_single_specification'])
            <div class="price-bar" >
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
        @endif
    </div>
    {{--<img src="{{$qrCode}}" alt="">--}}
    @if (!$item['is_single_specification'])
    <div class="group">
        <h3>规格</h3>
        @include($theme->getViewPath('shop.specifications_control'))
    </div>
    @endif
    <div class="group">
        <h3>商品详情</h3>
        <div class="details-content">
            {!!$item->detail!!}
        </div>
    </div>
    @include($theme->getViewPath('shop.cart_bar'))
</div>
@endsection

@section('scripts')
    @parent
    <script src="/common/js/cart.js"></script>
    <script src="/components/unslider/dist/js/unslider-min.js"></script>
    <script>
        $(function(){
            $('.item-images').unslider({
                autoplay: true,
                infinite: true,
                arrows: false,
                nav:true,
                delay:5000,
                speed:1000,
            });

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