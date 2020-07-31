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

$sql = "SELECT DISTINCT(position) as pos FROM advert WHERE  expire_date > CURDATE() ";
$result = $dbobject->db_query($sql);
$takenSlot = array();
if(count($result) > 0)
{
    foreach($result as $row)
    {
        $takenSlot[] = $row['pos'];
    }
}
$slots = array(1,2,3,4,5);
$diff = array_diff($slots,$takenSlot);
//var_dump($diff);

if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $cat_id = $_REQUEST['cat_id'];
    $sql_category = "SELECT * FROM product_categories WHERE id = '$cat_id' LIMIT 1";
    $cat = $dbobject->db_query($sql_category);
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
    <h4 class="modal-title" style="font-weight:bold">Advert Placement</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Advert.saveAdvert">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $cat_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Carousel Positions </label>
                    <select name="" id="" class="form-control">
                       
                        <?php
                        if(count($diff) > 0)
                        {
                            echo '<option value="" hidden>::Select a position on the carousel::</option>';
                            foreach($diff as $val)
                            {
                                echo "<option value='$val'>$val</option>";
                            }
                        }
                        else
                        {
                            echo "<option value=''>:: NO POSITION AVAILABLE ::</option>";
                        }
                            
                        ?>
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Advert Duration</label>
                    <select name="" id="" class="form-control">
                        <?php
                            for($x=7; $x<91; $x=$x+7)
                            {
                                $week = $x/7;
                                $s = ($week == 1)?"":"s";
                                echo "<option value='$x'>$week week$s</option>";
                            }
                        ?>
                    </select>
                    
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Title</label>
                   <input type="text" class="form-control" />
               </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Banner Image</label>
                   <div id="extraupload"></div>
               </div>
           </div>
        </div>
        
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
<style>
    .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar{
                width: auto !important;
            }
</style>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
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
                    getpage('category_list.php','page');
                    
                }
            else
                {
                     $("#err").css('color','red')
                    $("#err").html(re.response_message)
                    $("#warning").val("0");
                }
                
        },'json')
    }
    
var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    multiple:false,
                    allowedTypes:"jpg,png",
                    maxFileSize:1000000,
					returnType:'json',
                    autoSubmit:false,
					onLoad:function(obj)
					{
						var opr = "<?php echo $operation; ?>";
						if(opr == "edit")
						{
							$.ajax({
                                cache: false,
                                url: "utilities.php?op=Merchant.getFileDetails",
                                dataType: "json",
                                success: function(data) 
                                {
                                     obj.createProgress(data["name"],data["path"],data["size"]);
                                }
                            });
						}
					},
                    dynamicFormData: function()
                    {
                        $("#save_facility").text("Loading......");
                        var dd = $("#form1").serialize();
                        var data = dd;
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $("#save_facility").text("Save");
                        console.log(data);
                        if(data.response_code == 0)
                         {
                    
                             $("#err").css('color','green')
                               $("#err").html(data.response_message)
                              getpage('merchant_list.php','page');
                    
                         }
                         else
                         {
                              $("#err").css('color','red')
                              $("#err").html(data.response_message)
                             $("#warning").val("0");
                         }

                    }
                });
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