<ul class="specification-list">
    @foreach($item->specifications as $specification)
    <li class="specification" data-specification-id="{{$specification['id']}}">
        <span class="specification-title">{{$specification['name']}}</span>
        <div class="price-bar" >
            @if($specification['price'])
            <span class="price">
                <i class="fa fa-cny" aria-hidden="true"></i>
                <span class="value">{{$specification['price']}}</span> 
            </span>
            @endif
        
            @if ($specification['price'] && $specification['bonus_require'])
            <span class="price-and-bonus">
                +
            </span>
            @endif
            
            @if($specification['bonus_require'])
            <span class="bonus-require">
                <i class="fa fa-btc" aria-hidden="true"></i>
                <span class="value">{{$specification['bonus_require']}}</span>
            </span>
            @endif
        </div>
        <div class="item-cart-bar">
            <i class="subtract fa fa-minus"></i>
            <span class="quantity"></span>
            <i class="add fa fa-plus "></i>
        </div>
    </li>
    @endforeach
</ul>