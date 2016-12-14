
function Cart(cartData){
            
    var cartData = cartData || [];
    var $cartBar = $('.cart-bar');
    var $cartList = $cartBar.find('.cart-list');
    
    function constructor(){
        $.each(cartData, function (i, val){
            addCartItem(val);
        });
        
        $(document).on('touchend','[data-specification-id] .add', doAddClick);
        $(document).on('touchend ','[data-specification-id] .subtract', doSubtractClick);
        $('.cart-list-wrapper .clear-btn').click(clearCart);
        $('.cart-mask').click(closeCart);
        $('.cart-bar .menu-icon').click(function(e){
            var $menu = $(".shop-menu");
            if ($menu.is(':hidden'))
                $menu.show();
            else
                $menu.hide();
        });
        $('.cart-bar .cart-click-zone').click(function(e){
            if ($(".cart-list-wrapper").is(':hidden'))
                openCart();
            else
                closeCart();
        });
        $('.item-cart-bar .multi-add').click(function(e){
            var $item = $(e.target).closest('.item');
            $item.find('.specifications-wrapper').show();
            $('.main-mask').show();
        });
        $('.specifications-wrapper .close-btn').click(function(e){
            $(e.target).closest('.specifications-wrapper').hide();
            $('.main-mask').hide();
        });

        $('.order-submit').click(function(){
            location.href = "/wechat/order/new";
        });
    }

    function doAddClick(e){
        var $item = $(e.target).closest('[data-item-id]');
        var itemId = $item.attr('data-item-id');
        var specificationId = $item.attr('data-specification-id');

        //如果点击元素是购物车（即不存在添加购物车的可能性）
        if ($item.hasClass('cart-item')){
            changeCart(itemId, specificationId, 1);
        }
        else{
            var name = $item.find('.item-title').text();

            if (!specificationId){
                $item = $(e.target).closest('[data-specification-id]');
                specificationId = $item.attr('data-specification-id');
                name += '-' + $item.find('.specification-title').text();
            }

            var price = 0;
            var $priceSpan = $item.find('.price>.value');
            if ($priceSpan.length != 0)
                price = Number($priceSpan.text());

            var bonusRequire = 0;
            var $bonusRequireSpan = $item.find('.bonus-require>.value');
            if ($bonusRequireSpan.length != 0)
                bonusRequire = Number($bonusRequireSpan.text());
            
            changeCart(itemId, specificationId, 1, name, price, bonusRequire);
        }
    }

    function doSubtractClick(e){
        var $item = $(e.target).closest('[data-item-id]');
        var itemId = $item.attr('data-item-id');
        var specificationId = $item.attr('data-specification-id');

        if (!specificationId){
            specificationId = $(e.target).closest('[data-specification-id]')
            .attr('data-specification-id');
        }

        changeCart(itemId, specificationId, -1);
        event.preventDefault(e);  
    }

    function openCart()
    {
        if (cartData.length != 0){
            var $wrapper = $(".cart-list-wrapper");
            var $mask = $(".cart-mask");
            $wrapper.show();
            $mask.show();
        }
    }

    function closeCart()
    {
        var $wrapper = $(".cart-list-wrapper");
        var $mask = $(".cart-mask");
        $wrapper.hide();
        $mask.hide();
    }

    function changeCart(itemId, specificationId, quantity, name, price, bonusRequire)
    {
        var cartIndex = -1;
        for (var i in cartData){
            var item = cartData[i];
            if (item.itemId == itemId && item.specificationId == specificationId){
                cartIndex = i;
                break;
            }
        }

        if (cartIndex == -1){
            var newItemData = {
                itemId : itemId, 
                specificationId: specificationId, 
                quantity: quantity,
                name: name, 
                price: price || 0,
                bonusRequire: bonusRequire || 0,
            }
            cartData.push(newItemData);
            addCartItem(newItemData);
        }
        else{
            var itemData = cartData[cartIndex];
            itemData.quantity += quantity;
            if (itemData.quantity <= 0){
                removeCartItem(itemData);
                cartData.splice(cartIndex, 1);

                if (cartData.length == 0)
                    closeCart();
            }
            else{
                updateCartItem(itemData);
            }
        }

        $.ajax({
            url: '/wechat/api/putCartData',
            type: 'PUT',
            data: {
                cartData: JSON.stringify(cartData),
            },
        });
    }

    function clearCart(){
        cartData = [];
        $('.item.selected .selected').removeClass('selected');
        $('.item.selected').removeClass('selected');
        $cartList.empty();
        flushCartTotal();

        $.ajax({
            url: '/wechat/api/putCartData',
            type: 'PUT',
            data: {
                cartData: JSON.stringify(cartData),
            },
        });
    }

    function addCartItem(newItemData)
    {
        $cartList.append(getNewCartItemHTML(newItemData));
        flushItemHTML(newItemData.specificationId);
        flushCartTotal();
    }

    function removeCartItem(itemData)
    {
        $cartList.find('[data-item-id="' + itemData.itemId + 
            '"][data-specification-id="' + itemData.specificationId + '"]')
            .remove();
        flushItemHTML(itemData.specificationId);
        flushCartTotal();
    }

    function updateCartItem(itemData)
    {
        $cartList.find('[data-item-id="' + itemData.itemId + 
            '"][data-specification-id="' + itemData.specificationId + '"] .quantity')
            .text(itemData.quantity);
        flushItemHTML(itemData.specificationId);
        flushCartTotal();
    }

    function flushItemHTML(specificationId)
    {
        var $specification = $('[data-specification-id="' + specificationId + '"]');

        if ($specification.length != 0) {
            var filterCartItems = $.grep(cartData, function(value){
                return value.specificationId == specificationId;
            });

            if(filterCartItems.length == 1){
                var cartItem = filterCartItems[0];

                if (cartItem.quantity > 0){
                    $specification.find('.quantity').text(cartItem.quantity);
                    $specification.addClass('selected');
                }
                else{
                    $specification.find('.quantity').text('');
                    $specification.removeClass('selected');
                }

                var $itemDIV = $('[data-item-id="' + cartItem.itemId + '"][data-multi-specification]');

                if ($itemDIV.length != 0) {
                    var filterCartItems = $.grep(cartData, function(value){
                        return value.itemId == cartItem.itemId;
                    });

                    var quantity = 0;
                    for (var i in filterCartItems){
                        quantity += filterCartItems[i].quantity;
                    }

                    $itemDIV.find('.caption .quantity').text(quantity);

                    if (quantity > 0){
                        $itemDIV.addClass('selected');
                    }
                    else{
                        $itemDIV.removeClass('selected');
                    }
                }
            }
        }
            
    }

    function flushCartTotal()
    {
        var count = 0;
        var totalPrice = 0;
        var totalBonusRequire = 0;

        for (var i in cartData){
            var itemData = cartData[i];
            count++;
            totalPrice += (itemData.price || 0) * itemData.quantity;
            totalBonusRequire += (itemData.bonusRequire || 0) * itemData.quantity;
        }

        $cartBar.find('.total-price>.value').text(totalPrice);
        $cartBar.find('.total-bonus-require>.value').text(totalBonusRequire);

        
        if (totalPrice == 0)
            $cartBar.find('.total-price').addClass('null-value');
        else {
            $cartBar.find('.total-price').removeClass('null-value');
        }

        if (totalBonusRequire == 0)
            $cartBar.find('.total-bonus-require').addClass('null-value');
        else {
            $cartBar.find('.total-bonus-require').removeClass('null-value');
        }

        if (count == 0)
            closeCart();
    }

    function getNewCartItemHTML(cartItem){
        var priceHTML = "";
        if (cartItem.price){
            priceHTML = '<span class="price">'+
                            '<i class="fa fa-cny" aria-hidden="true"></i>'+
                            cartItem.price + 
                        '</span>';
        }
        if (cartItem.bonusRequire){
            if (priceHTML != ""){
                priceHTML += '<span class="price-and-bonus">+</span>';
            }
            priceHTML += '<span class="bonus-require">'+
                            '<i class="fa fa-btc" aria-hidden="true"></i>'+
                            cartItem.bonusRequire +
                        '</span>';
        }

        return '<li class="cart-item selected"  data-item-id="' + cartItem.itemId + 
                    '" data-specification-id="' + cartItem.specificationId + '">'+
                    '<span class="name">' + cartItem.name + '</span>'+
                    priceHTML +
                    '<div class="item-cart-bar">'+
                        '<i class="subtract fa fa-minus"></i>'+
                        '<span class="quantity">' + cartItem.quantity + '</span>'+
                        '<i class="add fa fa-plus "></i>'+
                    '</div>'+
                '</li>';
    }
    $(function(){ 
        constructor();
    })
}