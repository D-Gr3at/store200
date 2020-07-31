<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
$dbobject = new dbobject();
//$sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
//$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
//$sql_pastor = "SELECT username,firstname,lastname FROM userdata WHERE role_id = '003'";
//$pastors = $dbobject->db_query($sql_pastor);




if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $coupon_id = $_REQUEST['coupon_id'];
    $sql_coupon = "SELECT * FROM coupon WHERE id = '$coupon_id' LIMIT 1";
    $coupon = $dbobject->db_query($sql_coupon);
}else
{
    $operation = 'new';
}
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
    <h4 class="modal-title" style="font-weight:bold">Coupon Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Coupon.saveCoupon">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                  <label class="form-label">Coupon Code</label>
                   <div class="input-group mb-2 mr-sm-2">
                        <input type="text" autocomplete="off" name="id" id="coupon_code" readonly value="<?php echo $coupon[0]['id']; ?>"  class="form-control" />
                        <?php
                           if($operation == "new")
                           {
                        ?>
                        <div class="input-group-append" style="cursor:pointer" onclick="generateCoupon()">
                            <div class="input-group-text">Generate</div>
                        </div>
                        <?php
                            }
                        ?>
                   </div>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Coupon Name</label>
                    <input type="text" autocomplete="off" name="name" class="form-control" value="<?php echo $coupon[0]['name']; ?>" placeholder="">
                </div>
           </div>
       </div>
       
       <div class="row">
           <div class="col-sm-3">
               <div class="form-group">
                    <label class="form-label">Coupon Value</label>
                     <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-append" style="cursor:pointer" onclick="generateCoupon()">
                            <div class="input-group-text">&#x20A6</div>
                        </div>
                    <input type="number" oninput="this.value = Math.abs(this.value)" autocomplete="off" name="value"  value="<?php echo $coupon[0]['value']; ?>"  class="form-control" />
                   </div>
                </div>
           </div>
           <div class="col-sm-3">
               <div class="form-group">
                    <label class="form-label">Set Status</label>
                    <select name="is_active" id="" class="form-control">
                        <option <?php echo ($coupon[0]['is_active'] == "1")?"selected":""; ?> value="1">Active</option>
                        <option <?php echo ($coupon[0]['is_active'] == "0")?"selected":""; ?> value="0">Inactive</option>
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Coupon Expire Date</label>
                    <input type="text" name="expire_date" class="form-control" id="expire_date" value="<?php echo $coupon[0]['expire_date']; ?>" placeholder="">
                </div>
           </div>
       </div>
       
       
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Link to a customer</label>
                    <select onchange="set_customers(this.value)" name="set_customer_link" class="form-control">
                        <option <?php echo ($coupon[0]['customer_link'] == "*")?"selected":""; ?> value="*">All customers</option>
                        <option <?php echo ($coupon[0]['customer_link'] != "*" && $operation == "edit")?"selected":""; ?> value="**">Set a customer ID</option>
                    </select>
                </div>
           </div>
           <div class="col-sm-6" style="display:<?php echo ($coupon[0]['customer_link'] != '*' && $operation == "edit")?"block":none ?>" id="customer_link">
               <div class="form-group">
                    <label class="form-label">Enter customer email</label>
                    <input type="text" name="customer_link" autocomplete="off" class="form-control" value="<?php echo $coupon[0]['customer_link']; ?>" placeholder="">
                </div>
           </div>
       </div>
       <div class="row">
           
        </div>
       
        
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
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
                    setTimeout(()=>{
                        $('#defaultModalPrimary').modal('hide');
                    },1000)
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