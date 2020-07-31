var Cart = {};

Cart.on = function(eventName, callback) {
  if (!Cart.callbacks[eventName]) Cart.callbacks[eventName] = [];
  Cart.callbacks[eventName].push(callback);
  return Cart;
};

Cart.trigger = function(eventName, args) {
  if (Cart.callbacks[eventName]) {
    for (var i = 0; i<Cart.callbacks[eventName].length; i++) {
      Cart.callbacks[eventName][i](args||{});
    }
  }
  return Cart;
};

Cart.save = function() {
  // console.log("you are calling me")
  localStorage.setItem('cart-items', JSON.stringify(Cart.items));
  Cart.trigger('saved');
  return Cart;
};

Cart.empty =  function() {
  Cart.items = [];
  Cart.trigger('emptied');
  Cart.save();
  return Cart;
};

Cart.removeItem =  function(product_id) {
//    var kk = "";
    Cart.items = Cart.items.filter((item)=>
    {
        if(item.id != product_id)
            return true
     })
  Cart.trigger('emptied');
  Cart.save();
  return Cart;
};

Cart.indexOfItem = function(id) {
  for (var i = 0; i<Cart.items.length; i++) {
    if (Cart.items[i].id===id) return i;
  }
  return null;
};



Cart.removeEmptyLines = function() {
  newItems = [];
  for (var i = 0; i<Cart.items.length; i++) {
    if (Cart.items[i].quantity>0) newItems.push(Cart.items[i]);
  }
  Cart.items = newItems;
  return Cart;
};

Cart.addItem = function(item) {
  //  console.log("wow ",item)
  if (!item.quantity) item.quantity = 1;
  var index = Cart.indexOfItem(item.id);
  if (index===null) {
    Cart.items.push(item);
  } else {
    Cart.items[index].quantity = parseInt(Cart.items[index].quantity) + parseInt(item.quantity);
    // console.log(typeof Cart.items[index].quantity);
    
  }
  Cart.removeEmptyLines();
  if (item.quantity > 0) {
    Cart.trigger('added', {item: item});
  } else {
    Cart.trigger('removed', {item: item});
  }
  Cart.save();
    onAdd(Cart.itemsCount(),item)
  return Cart;
};

Cart.itemsCount = function() {
  var accumulator = 0;
  for (var i = 0; i<Cart.items.length; i++) {
    accumulator += parseInt(Cart.items[i].quantity);
  }
  return accumulator;
};

Cart.currency = 'NGN ';

Cart.displayPrice = function(price) {
  if (price===0) return 'Free';
  var floatPrice = price;
//  var floatPrice = price/100;
  var decimals = floatPrice==parseInt(floatPrice, 10) ? 0 : 2;
  floatPrice = parseFloat(floatPrice);
  return Cart.currency + floatPrice.toFixed(decimals);
};

Cart.linePrice = function(index) {
  return Cart.items[index].price * Cart.items[index].quantity;
};

Cart.subTotal = function() {
  var accumulator = 0;
  for (var i = 0; i<Cart.items.length; i++) {
    accumulator += Cart.linePrice(i);
  }
  return accumulator;
};

Cart.init = function() {
  var items = localStorage.getItem('cart-items');
  if (items) {
    Cart.items = JSON.parse(items);
  } else {
    Cart.items = [];
  }
  Cart.callbacks = {};
  return Cart;
}

function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

Cart.initJQuery = function() {

  Cart.init();

  Cart.templateCompiler = function(a,b){return function(c,d){return a.replace(/#{([^}]*)}/g,function(a,e){return Function("x","with(x)return "+e).call(c,d||b||{})})}};
  // console.log(this);
  Cart.lineItemTemplate = "<tr>" +
     "<td><img src='#{this.image}' width='50px' height='50px' alt='#{this.label}' /></td>" + 
//    "<td></td>" + 
    "<td>#{this.label}<div style='cursor:pointer' onclick='deleteCart(\"#{this.id}\")'><small><i class='fa fa-trash'></i> Remove</small></div></td>" + 
    "<td><button type='button' class='cart-add' data-id='#{this.id}' data-quantity='-1'>-</button>   #{this.quantity}   <button type='button' class='cart-add' data-id='#{this.id}' data-quantity='1'>+</button></td>" + 
    "<td>&times;</td>" + 
    "<td>#{formatNumber(Cart.displayPrice(this.price)+'.00')}</td>" + 
  "</tr>";

  $(document).on('click', '.cart-add', function(e) {
    e.preventDefault();
    var button = $(this);
    var item = {
      id: button.attr("data-id"),
      price: button.attr("data-price"),
      quantity: button.attr('data-quantity'),
      label: button.attr('data-label'),
      image: button.attr('data-image')
    }
    // console.log("this btn",item);
    Cart.addItem(item);
  });

  // function getCommaSeparatedTwoDecimalsNumber(number) {
  //   const fixedNumber = Number.parseFloat(number).toFixed(2);
  //   return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  // }

  // function formatNumber(num) {
  //   return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
  // }

  var updateReport = function() {
    var count = Cart.itemsCount();
    $('.cart-items-count').text(count);
    console.log();
    if(Cart.subTotal() == 0){
      $('.cart-subtotal').html(formatNumber(Cart.displayPrice(Cart.subTotal())));
    }else{
      $('.cart-subtotal').html(formatNumber(Cart.displayPrice(Cart.subTotal()))+".00");
    }
    
    if (count===1) {
      $('.cart-items-count-singular').show();
      $('.cart-items-count-plural').hide();
    } else {
      $('.cart-items-count-singular').hide();
      $('.cart-items-count-plural').show();
    }
  };

  var updateCart = function() {
    if (Cart.items.length>0) {
      var template = Cart.templateCompiler(Cart.lineItemTemplate);
      var lineItems = "";
      for (var i = 0; i<Cart.items.length; i++) {
        lineItems += template(Cart.items[i]);
      }
      $('.cart-line-items').html(lineItems);
      $('.cart-table').show();
      $('.cart-is-empty').hide();
    } else {
      $('.cart-table').hide();
      $('.cart-is-empty').show();
    }
  };

  var update = function() {
    updateReport();
    updateCart();
  };
  update();

  Cart.on('saved', update);

  return Cart;
};

