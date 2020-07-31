<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();
?>
  <style>
    fieldset 
    { 
    display: block;
    margin-left: 2px;
    margin-right: 2px;
    padding-top: 0.35em;
    padding-bottom: 0.625em;
    padding-left: 0.75em;
    padding-right: 0.75em;
    border: 1px solid #ccc;
    }
    
    legend
    {
        font-size: 14px;
        padding: 5px;
        font-weight: bold;
    }
      .action_btn:hover{
          text-decoration: none
      }
      .dropdown{
          display: inline;
          float: left;
          
      }
      .action_btn:hover{
          color: crimson;
      }
</style>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title"><i data-feather="sliders"></i>Orders List</h5>
        <h6 class="card-subtitle text-muted"><i class='align-middle mr-1' data-feather='user'></i> The report contains Orders for pending, confirmed, and fullfiled.</h6>
    </div>
    <div class="card-body">
     
       <div class="row" style="margin-bottom:20px">
             
<!--
             <div class="col-sm-2">
             <a class="btn btn-warning" onclick="getModal('setup/delivery_location.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Add a location</a>
         </div>
-->
         </div>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Order ID</th>
                                <th>Product Name</th>
                                <th>Selling Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Order Status</th>
                                <th>Shipping Status</th>
                                <th>Customer ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="../js/jquery.blockUI.js"></script>-->
<script>
  var table;
  var editor;
  var op = "Orders.orderList";
  $(document).ready(function() {
    table = $("#page_list").DataTable({
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      serverSide: true,
      paging: true,
      oLanguage: {
        sEmptyTable: "No record was found, please try another query"
      },

      ajax: {
        url: "utilities.php",
        type: "POST",
        data: function(d, l) {
          d.op = op;
          d.li = Math.random();
          d.state    = $("#state").val();
          d.payment  = $("#payment").val();
          d.filter   = $("#filter").val();
          d.churches = $("#churches").val();
          d.region = $("#region").val();
//          d.end_date = $("#end_date").val();
        }
      }
    });
  });

  function declineOrder(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to decline this order ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.declineOrder', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function confirmOrder(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to confirm this order ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.confirmOrder', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function shipOrder(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to confirm that this order has been shipped ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.shipOrder', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function orderInTransit(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to confirm that this order is in transit ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.orderInTransit', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function orderInWarehouse(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to confirm that this order is in warehouse ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.orderInWarehouse', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function orderDelivered(id, customer_id, product_id, order_id){
    var cnf = confirm("Are you sure you want to confirm that this order has been delivered ?");
    if(cnf){
        $.blockUI();
        $.post('utilities.php',{op:'Orders.orderDelivered', id: id, customer_id: customer_id, product_id: product_id, order_id: order_id},function(resp){
            $.unblockUI();
            if(resp.responseCode == 0){
//                           alert(resp.response_message);
                getpage('order_list.php','page'); 
            }
                   
        },'json')
    }
  }

  function do_filter() {
    table.draw();
  }
    function hide_div(el)
    {
        if(el.id == "branch_filter")
        {
            $("#churches_div").show();
            $("#region_div").hide();
            $("#filter").val(el.value);
        }else{
            $("#churches_div").hide();
            $("#region_div").show();
            $("#filter").val(el.value);
        }
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
    function fetchLga(el)
    {
        $("#lga-fd").html("<option>Loading Lga</option>");
        $.post("utilities.php",{op:'Church.getLga',state:el},function(re){
//            $("#lga-fd").empty();
            console.log(re);
            $("#lga-fd").html(re.state);
            $("#church_id").html(re.church);
            
        },'json');
    }
    function churchByState(el)
    {
        
        $.post("utilities.php",{op:'Church.churchByState',state:el},function(re){
//            $("#lga-fd").empty();
            console.log(re);
            $("#churches").empty();
            $("#churches").html(re);
            
        });
    }
</script>