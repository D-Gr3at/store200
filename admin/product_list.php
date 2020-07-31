<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
  <style>
      .df{
          text-decoration:line-through
      }
</style>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Product List</h5>
        <h6 class="card-subtitle text-muted">The report contains products that have been setup in the system.</h6>
    </div>
    <div class="card-body">
      <a class="btn btn-info" onclick="getpage('setup/product_setup.php','page')"  href="javascript:void(0)" >Create Product</a>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Inventory</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Product Link</th>
<!--                                <th>Description</th>-->
                                <th>Action</th>
                                <th>Is visible?</th>
                                <th>Action</th>
                                
<!--                                <th>Images</th>-->
                                <th>Created</th>
                                <th></th>
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
<!--
<div class="qrcode" id="qr">
</div>
-->

<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="js/jquery.classyqr.min.js"></script>-->
<script>
//    $(document).ready(function() {
//        $('#qr').ClassyQR({
//           create: true, // signals the library to create the image tag inside the container div.
//           type: 'text', // text/url/sms/email/call/locatithe text to encode in the QR. on/wifi/contact, default is TEXT
//           text: 'Welcome to jQueryScript!' // the text to encode in the QR. 
//        });
//    });
</script>
<script>
  var table;
  var editor;
  var op = "Product.productList";
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
//          d.start_date = $("#start_date").val();
//          d.end_date = $("#end_date").val();
        }
      }
    });
  });

  function do_filter() {
    table.draw();
  }
    function hideProduct(id,changeTo)
    {
        let cnf = confirm("Are you sure you want to change product visibility?");
        if(cnf == true)
        {
            $.post("utilities.php",{op:"Product.setVisibility",change_to:changeTo,product_id:id},function(ee){
                    if(ee.responseCode == 0)
                    {
                        alert(ee.responseMessage);
                        getpage('product_list.php',"page");
                    }
                    else
                    {
                        alert(ee.responseMessage);
                    }
            },'json')
        }
    }
    function deleteMenu(id)
    {
        let cnf = confirm("Are you sure you want to delete menu?");
        if(cnf == true)
        {
            $.blockUI();
            $.post("utilities.php",{op:"Menu.deleteMenu",menu_id:id},function(re){
                $.unblockUI();
                alert(re.response_message);
                getpage('menu_list.php',"page");
            },'json')
        }
    }
    function setFeature(id)
    {
        var con = confirm("Are you sure you want to set this as a featured product?");
        if(con)
        {
            $.blockUI();
            $.post("utilities.php",{op:"Product.setFeatureProduct",product_id:id},function(re){
                $.unblockUI();
                if(re.response_code == 0)
                {
                    $("#err").alert(re.response_mesage)
                    getpage('product_list.php','page'); 
                }
                else
                {
                    alert(re.response_mesage)
                }
            },'json');
        }
    }
    function unsetFeature(id)
    {
        var con = confirm("Are you sure you want to remove this product from featured products?");
        if(con)
        {
            $.post("utilities.php",{op:"Product.unsetFeatureProduct",product_id:id},function(data){
                alert(data.response_message);
                    getpage('product_list.php',"page");
            },'json')
        }
    }
    function getModal(url,div)
    {
//        alert('dfd');
        $('#'+div).html("<h2>Loading....</h2>");
//        $('#'+div).block({ message: null });
        $.post(url,{},function(re)
        {
            $('#'+div).html(re);
        })
    }
</script>