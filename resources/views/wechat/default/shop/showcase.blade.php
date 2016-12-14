<div class="showcase row 
    @if ($category['layout'] == 'once')
        single
    @elseif ($category['layout'] == 'double')
        double
    @else
        details
    @endif
">
    <div class="header">
        @if(isset($category['icon']))
        <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="icon fa fa-stack-1x fa-inverse {{$category['icon']}}"></i>
        </span>
        @endif
        <span class="title">{{$category['title']}}</span>
        @if (!empty($category['title_en']))
        <span class="title_split"></span>
        <span class="title_en">{{$category['title_en']}}</span>
        @endif
    </div>
    @foreach ($category['items'] as $item)
    <div class="item" data-item-id={{$item['id']}}
        @if($item['is_single_specification'])
            data-specification-id={{$item['specifications'][0]->id}}
        @else
            data-multi-specification
        @endif
    >
        <div class="thumbnail"> 
            <a href="/wechat/mall/item/{{$item['id']}}">
                <img class="item-img" src="{{$item['image'][0]}}" alt="{{$item['name']}}">  
            </a>
            <div class="caption">
                <h3 class="item-title">{{$item->name}}</h3>
                @if ($item['summary'])
                <p class="sub-title">{{$item['summary']}}</p>
                @endif
                <div class="price-bar">
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
                @if(!$item['is_suit'])
                <div class="item-cart-bar">
                    @if($item['is_single_specification'])
                    <i class="subtract fa fa-minus"></i>
                    <span class="quantity"></span>
                    <i class="add fa fa-plus " ></i>
                    @else
                    <div class="multi-add">
                        <span class="quantity"></span>
                        <span class="tag">选规格</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @if(!$item['is_single_specification'])
        <div class="specifications-wrapper" style="display:none">
            <div class="specifications-header">
                {{$item['title']}}
            </div>
                @include($theme->getViewPath('shop.specifications_control'))
            <div class="specifications-footer">
                <button class="close-btn">确认</button>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>