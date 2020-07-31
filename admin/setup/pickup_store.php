<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
$dbobject = new dbobject();
$sql = "SELECT DISTINCT(State) as State,state_code FROM lga order by State";
$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
//$sql_pastor = "SELECT username,firstname,lastname FROM userdata WHERE role_id = '003'";
//$pastors = $dbobject->db_query($sql_pastor);




if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $menu_id = $_REQUEST['menu_id'];
    $sql_menu = "SELECT * FROM menu WHERE menu_id = '$menu_id' LIMIT 1";
    $menu = $dbobject->db_query($sql_menu);
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
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
    myCalendar.setSensitiveRange(null, "<?php echo date('Y-m-d') ?>");
   myCalendar.hideTime();
}
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Pickup Store Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="PickupStore.savePickupLocation">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $menu_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Title/Label</label>
                    <input type="text" autocomplete="off" name="title" value="<?php echo $menu[0]['menu_name']; ?>"  class="form-control" />
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo $menu[0]['menu_url']; ?>" placeholder="">
               </div>
           </div>
           
       </div>
        
         <div class="row">
            <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Select State</label>
                    <select onchange="fetchLga(this.value)" name="state" class="form-control">
                       <?php
                        foreach($states as $row)
                        {
                        ?>
                            <option value="<?php echo $row['state_code']; ?>"><?php echo $row['State']; ?></option>
                        <?php
                        }
                        ?>
                       
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label>Local Government Area</label>
                    <select id="lga-fds" class="form-control" style="display:block" name="lga" >
                    <option value="">:: SELECT A STATE</option>
                    </select>

                </div>
           </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
               <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input name="phone" class="form-control" id="phone">
                </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                    <label for="instruction">Pickup Instruction</label>
                    <textarea name="pickup_instructions" class="form-control" id="instruction" cols="20" rows="5"></textarea>
                </div>
            </div>
        </div>
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
<script src="js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="css/bootstrap-multiselect.css">
<script>
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
                    getpage('pickup_store_list.php','page');
                    
                }
            else
                {
                     $("#err").css('color','red')
                    $("#err").html(re.response_message)
                    $("#warning").val("0");
                }
                
        },'json')
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
       $('#lga-fds').multiselect('destroy');
        $("#lga-fds").html("<option>Loading Lga</option>");
        $.post("utilities.php",{op:'Helper.getLga',state:el},function(re){
            $("#lga-fds").empty();
//            $("#lga-fds").prop('multiple','multiple');
            $("#lga-fds").html(re.state);
            $('#lga-fds').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
                maxHeight: 150,
                buttonWidth: '250px'
        });
        },'json');
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
<style>
    .btn-group{
        border: 1px solid #ccc;
        border-radius: 3px
    }
</style>