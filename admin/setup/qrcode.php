<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
//$sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
//$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
$product_sql  = "SELECT product_long_url,name FROM products WHERE id = '$_REQUEST[product_id]' LIMIT 1";
$products     = $dbobject->db_query($product_sql);
$product_name = $products[0]['name'];
$product_id   = $_REQUEST['product_id'];
$product_url  = $products[0]['product_long_url'];



//if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
//{
//    $operation = 'edit';
//    $coupon_id = $_REQUEST['coupon_id'];
//    $sql_coupon = "SELECT * FROM coupon WHERE id = '$coupon_id' LIMIT 1";
//    $coupon = $dbobject->db_query($sql_coupon);
//}else
//{
//    $operation = 'new';
//}
?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["expire_date"]);
    myCalendar.setSensitiveRange("<?php echo date('Y-m-d') ?>",null);
   myCalendar.hideTime();
}
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"><?php echo $product_name; ?> QRCODE</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
<!--       <input type="hidden" name="op" value="Coupon.saveCoupon">-->
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <div class="row">
           <div class="col-sm-3">
           </div>
           <div class="col-sm-6" align="center">
               <div class="qrcode" id="qrs">
                   loading..
               </div>
               <button id="save_facility" onclick="printDiv('qrs')" class="btn btn-primary mb-1">Print</button>
               <button id="save_facility" onclick="saveImg()" class="btn btn-primary mb-1">Save</button>
           </div>
          <div class="col-sm-3">
               
           </div>
       </div>
       
       <div id="err"></div>
        
        
    </form>
</div>

<script src="js/jquery.classyqr.min.js"></script>
<script>
    var p_url = "<?php echo $product_url; ?>";
    $(document).ready(function() {
        $('#qrs').ClassyQR({
           create: true, // signals the library to create the image tag inside the container div.
           type: 'text', // text/url/sms/email/call/locatithe text to encode in the QR. on/wifi/contact, default is TEXT
           text: p_url // the text to encode in the QR. 
        });
    });
    function saveImg()
    {
        var src = $("#qrs img").attr('src');
        $('<a href= "'+src+'" target="blank" download></a>')[0].click();
//        window.location = src;
    }
</script>


<script>
    
    var opera = "<?php echo $operation; ?>";
    if(opera == "new")
        {
            generateCoupon();
        }
    
    function saveRecord()
    {
        $("#save_facility").text("Loading......");
        var dd = $("#form1").serialize();
        $.post("utilities.php",dd,function(re)
        {
            $("#save_facility").text("Save");
            console.log(re);
            if(re.response_code == 0)
                {
                    
                    $("#err").css('color','green')
                    $("#err").html(re.response_message)
                    getpage('coupon_list.php','page');
                    
                }
            else
                {
                     $("#err").css('color','red')
                    $("#err").html(re.response_message)
                    $("#warning").val("0");
                }
                
        },'json')
    }
    function generateCoupon()
    {
        $.post("utilities.php",{op:'Coupon.generateCouponID'},function(re){
           $("#coupon_code").val(re);
            
        });
    }
    function set_customers(vv)
    {
//        var vv = "";
        if(vv == "**")
            {
                $("#customer_link").show();
            }
        else{
            $("#customer_link").hide();
        }
    }
    
//    function automatic()
//    {
//        if($("#auto").is(':checked'))
//        {
//            $("#auto_val").val(1)
//        }else{
//             $("#auto_val").val(0)
//        }
//    }
//    
    function fetchLga(el)
    {
        getRegions(el);
        $("#lga-fds").html("<option>Loading Lga</option>");
        $.post("utilities.php",{op:'Church.getLga',state:el},function(re){
            $("#lga-fds").empty();
            $("#lga-fds").html(re.state);
            
        },'json');
//        $.blockUI();
    }
    function getRegions(state_id)
    {
        $("#church_region_select").html("<option>Loading....</option>");
        $.post("utilities.php",{op:'Church.getRegions',state:state_id},function(re){
            $("#church_region_select").empty();
            $("#church_region_select").html(re);
            
        });
    }
    
    function fetchAccName(acc_no)
    {
        if(acc_no.length == 10)
            {
                var account  = acc_no;
                var bnk_code = $("#bank_name").val();
                $("#acc_name").text("Verifying account number....");
                $("#account_name").val("");
                $.post("utilities.php",{op:"Church.getAccountName",account_no:account,bank_code:bnk_code},function(res){
                    
                    $("#acc_name").text(res);
                    $("#account_name").val(res);
                });
            }else{
                $("#acc_name").text("Account Number must be 10 digits");
            }
        
    }
</script>