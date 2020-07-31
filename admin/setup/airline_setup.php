<?php
include_once("../libs/dbfunctions.php");
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
    $airline_id = $_REQUEST['airline_id'];
    $sql_church = "SELECT * FROM airline WHERE id = '$airline_id' LIMIT 1";
    $airline = $dbobject->db_query($sql_church);
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
    <h4 class="modal-title" style="font-weight:bold">Airline Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Airline.saveAirline">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $airline_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Airline Name</label>
                    <input type="text" name="airline_name" class="form-control" value="<?php echo $airline[0]['airline_name']; ?>" placeholder="">
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Airline Code</label>
                    <input type="text" autocomplete="off" name="airline_code" onkeyup="validateCode(this.value)" value="<?php echo $airline[0]['airline_code']; ?>"  class="form-control" />
                </div>
           </div>
       </div>
        
         <div class="row">
           <!-- <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Provider Type</label>
                    <select name="provider_type" class="form-control">
                       <option value="">:: SELECT PROVIDER ::</option>
                       <option value="AmadeusProvider"  >AmadeusProvider</option>
                       <option value="TravelFusionProvider"  >TravelFusionProvider</option>
                       <option value="TravelFusion2Provider">TravelFusion2Provider</option>
                       <option value="ERetailWebFareProvider">ERetailWebFareProvider</option>
                       
                    </select>
                </div>
           </div> -->
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Set Status</label>
                    <select name="active"  class="form-control" >
                        <option value="" hidden>:: SELECT STATUS ::</option>
                        <option value="1" <?php echo ($airline[0]['active'] == 1)?"selected":""; ?>>Active</option>
                        <option value="0" <?php echo ($airline[0]['active'] == 0)?"selected":""; ?> >Deactivate</option>
                    </select>
                </div>
           </div>
        </div>
        
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
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
                    getpage('airline_list.php','page');
                    
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