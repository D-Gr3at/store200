<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Place an order</h5>
<!--        <h6 class="card-subtitle text-muted">The report contains User Roles that have been setup in the system.</h6>-->
    </div>
    <div class="card-body">
            <div class="row" id="place_order">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-header alert-success">
                            <h5 class="card-title">Select a product</h5>
                        </div>
                        <div class="card-body">
                        <input style="border: 1px solid #f5911e;" type="text" id="search_menu" placeholder="Search for a product" class="form-control input-lg">

                        </div>
                    </div>
                    <label for="">Placing order for?</label>
                    <div>
                        <label for="self"><input type="radio" id="self" onclick="displayCust(this)" value="self" checked name="oder_by" /> Self</label> &nbsp; &nbsp;
                        <label for="customer"><input type="radio" id="customer" value="customer" onclick="displayCust(this)" name="oder_by" /> A customer</label>
                    </div>
                    <div id="custDiv" style="display:none">
                        <label for="">Enter Customer Email</label>
                        <input type="text" id="customer_email" class="form-control">
                        <small>you will be required to enter an authorization token sent to the email address</small>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <button onclick="sendOtp()" class="btn btn-success">Proceed to payment</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <p><b>Cart:</b> <span class='cart-items-count'>0</span> item<span class='cart-items-count-plural'>s</span></p>
                        <table class='table cart-table'>
                          <thead>
                            <tr>
                              <th>Image</th>
                              <th>Product</th>
                              <th>quantity</th>
                              <th></th>
                              <th>Unit Price</th>
                            </tr>
                          </thead>
                          <tbody class='cart-line-items'>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan='4'>Subtotal</td>
                              <td class='cart-subtotal'></td>
                            </tr>
                          </tfoot>
                        </table>
                        <p class='cart-is-empty'>Your cart is empty.</p>
                </div>
            </div>
           
           
            <div class="row" id="customer_approval" style="display:none">
                <div class="col-sm-2">
                    
                </div>
                <div class="col-sm-8">
                    <h3 align="center">Customer's Order Approval</h3>
                    <div class="form-group">
                        <label for="">Enter OTP sent to ugo.malu@gmail.com</label>
                        <input type="text" class="form-control">
                        
                    </div>
                    <button class="btn btn-primary">Confrim OTP</button>
                    <button class="btn btn-danger">Back to Order</button>
                </div>
                <div class="col-sm-2">
                    
                </div>
            </div>
    </div>
    
</div>
<div class="outer" style="display: none; height:100%; top:0; position:absolute; width:100%; z-index:99; background:rgba(0,0,0,0.5)">
      <div class="middle">
        <div class="inner" align="center" >
          <p id="loading" style="color:#fff">Loading.....</p>
          <div align="center" id="bt" style="display:none; margin-top:10px">
              <button onclick="javascript:window.location='index.php'">CANCEL</button>
          </div>
          <!-- <img src="img/wait.gif" id="loading"  alt="" /> -->
          <form id="tt" action="https://www.onepay.com.ng/api/live/main" target="vuvaa_frame" method="POST">
            <input name="product_desc" id="product_desc" type="hidden" value="sdsds" />
            <input name="merch_trans_id" id="merch_trans_id" type="hidden" value="" />
            <input name="merchant_reg_id" id="merchant_reg_id" type="hidden" value="ACC-OPMHT000000222" />
            <input name="client_email" id="client_email" type="hidden" value="fd@fdf.dfd" />
            <input name="client_name" id="client_name" type="hidden" value="dfdfd" />
            <input  name="client_phone" id="client_phone" type="hidden" value="4554564" />
            <input name="amt_paid" id="amt_paid" type="hidden" value="" />
          </form>
          <iframe name="vuvaa_frame" id="vuvaa_frame" scrolling="no" width="500" height="650" style="color:#fff; border:none;z-index:9999;display:none;" align="center" ></iframe>
          
          
        </div>
      </div>
    </div>

<link rel="stylesheet" href="css/jquery.auto-complete.css">
<script src="js/jquery.auto-complete.js"></script>

<script>
    Cart.empty();
   
    function displayCust(e)
    {
        if($(e).val() == "self")
            {
                $("#custDiv").hide();
            }else{
                $("#custDiv").show();
            }
        
    }
    function deleteCart(id)
    {
        Cart.removeItem(id)
    }
    function sendOtp()
    {
        console.log(Cart.items);
        if(Cart.items.length == 0)
        {
            console.log("empty cart");
        }else{
            console.log("cart not empty")
        }
        $.post("utilities.php",{op:"Orders.sendOtp",payload:Cart.items, customer_id:"sam@mail.com"},function(dd){
            console.log(dd);
        })
    }
    function confss(dd)
    {
        var selected = $(dd).find('option:selected');
        if(selected.data('variant') != "")
            {
                console.log(selected.data('variant'));
                var eeed = JSON.parse(selected.data('variant'));
                console.log(eeed);
            }
//        alert(selected.data('id'));
    }
    function getModal(url,div)
    {
//        alert('dfd');
        $('#'+div).html("<h2>Loading....</h2>");
//        $('#'+div).block({ message: null });
        $.post(url,{},function(re){
            $('#'+div).html(re);
        })
    }
    
    $(document).ready(function(){

 $('#search_menu').autoComplete({
			minChars: 3,
			source: function(term, response){
				term = term.toLowerCase();
                 try { xhr.abort(); } catch(e){}
                $.getJSON('utilities.php', { op: "Product.getproduct",entered_text:term }, function(data){                                                               
                        response(data.responseBody.items); });
			},
			renderItem: function (item, search){
//                console.log()
				return '<div class="autocomplete-suggestion" data-id="'+item.id+'" data-price="'+item.price+'"  data-label="'+item.name+'" data-quantity="1" data-image="'+item.primaryImage+'"> '+item.name+'<h6 class="card-subtitle text-muted">NGN '+item.price+'</h6></div>';
			},
			onSelect: function(e, term, item){
                alert('added to cart');
                console.log(item);
                var itemx = {
                      id: item.data('id'),
                      price: item.data('price'),
                      quantity: item.data('quantity'),
                      label: item.data('label'),
                      image: item.data('image')
                  }
                  Cart.addItem(itemx);
			}
		});

    });
    
   
    
</script>
