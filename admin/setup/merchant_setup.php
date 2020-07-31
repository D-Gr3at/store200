<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
$dbobject = new dbobject();
//var_dump($_SESSION);
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
    $merchant_id = $_REQUEST['merchant_id'];
    $sql_merchant = "SELECT * FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
    $merchant = $dbobject->db_query($sql_merchant);
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
    <h4 class="modal-title" style="font-weight:bold">Merchant Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Merchant.saveMerchant">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $merchant_id; ?>">
       <!-- onkeyup="validateCode(this.value)" -->
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input type="text" oninput="this.value = (this.value).match(/^[a-zA-Z0-9\s\-]+$/g)" onkeyup="display_url()" autocomplete="off" <?php echo ($operation == "edit")?"readonly":""; ?> id="biz_name" name="merchant_name"  value="<?php echo $merchant[0]['merchant_name']; ?>"  class="form-control" />
                    <small id="biz_url"><?php echo $merchant[0]['main_url'];  ?></small>
                    <input type="hidden" value="<?php echo $merchant[0]['main_url'];  ?>" name="main_url" id="main_url">
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="text" <?php echo ($operation == "edit")?"readonly":""; ?> name="merchant_email" class="form-control" value="<?php echo $merchant[0]['merchant_email']; ?>" placeholder="">
                    <small>This will be your login username</small>
                </div>
           </div>
	   </div>
	   <?php
	   if($operation != "edit")
	   {
	   ?>
	   <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" autocomplete="off"  name="merchant_password"  value="<?php echo $merchant[0]['']; ?>"  class="form-control" />
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="merchant_confirm_password" class="form-control" value="<?php echo $merchant[0]['']; ?>" placeholder="">
                </div>
           </div>
	   </div>
	   <?php
	      }
	   ?>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Business Address</label>
                    <input type="text" autocomplete="off" name="merchant_address"  value="<?php echo $merchant[0]['merchant_address']; ?>"  class="form-control" />
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Business Phone</label>
                    <input type="number" maxlength="11" min="0" name="merchant_phone" class="form-control" value="<?php echo $merchant[0]['merchant_phone']; ?>" placeholder="">
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Bank Name</label>
                    <select name="bank_code" onchange="clearAcc()" id="bank_name" class="form-control">
                    <option value="">::SELECT A BANK::</option>
                    <?php
                            $sql = "SELECT * FROM app_banks WHERE bank_type='commercial' order by bank_name asc";
                            $result = $dbobject->db_query($sql);
                            foreach($result as $row)
                            {
                                $selected = ($merchant[0]['bank_code'] == $row['code'])?"selected":"";
                                echo "<option $selected value='".$row['code']."'>".$row['bank_name']."</option>";
                            }
                        ?>
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Account No</label>
                    <input type="number" min="0" id="account_no" name="account_no" onkeyup="fetchAccName(this.value)" class="form-control" value="<?php echo $merchant[0]['account_no']; ?>" placeholder="">
                    <input type="hidden" name="account_name" id="account_name" value="<?php echo $merchant[0]['account_name']; ?>" />
                    <small id="acc_name"><?php echo $merchant[0]['account_name']; ?></small>
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Business Description</label>
                    <input type="text" autocomplete="off" name="merchant_details"  value="<?php echo $merchant[0]['merchant_details']; ?>"  class="form-control" />
                </div>
           </div>
           <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Business Industry</label>
                    <select name="industry" id="" class="form-control">
                    <option value="">:: SELECT AN INDUSTRY ::</option>
                        <?php
                            $sql = "SELECT * FROM job_industry";
                            $result = $dbobject->db_query($sql);
                            foreach($result as $row)
                            {
                                $selected = ($merchant[0]['industry'] == $row['id'])?"selected":"";
                                echo "<option $selected value='".$row['id']."'>".$row['name']."</option>";
                            }
                        ?>
                    </select>
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <label for="">Upload logo</label>
               <div id="extraupload"></div>
           </div>
           <div class="col-sm-6">
			   <label for="">Status</label>
			   <select class="form-control" name="active_merchant" id="">
				   <option value="">::SET STATUS::</option>
				   <option <?php echo ($merchant[0]['active_merchant'] == "1")?"selected":""; ?> value="1">Active</option>
				   <option <?php echo ($merchant[0]['active_merchant'] == "0")?"selected":""; ?> value="0">::Inactive::</option>
			   </select>
		   </div>
<!--
           <div class="col-sm-3">
			   <label for="">Fee Charge</label>
			   <input type="number" name="charge_fee" value="<?php echo $merchant[0]['charge_fee']; ?>" min="0" class="form-control" />
		   </div>
-->
       </div>
          
           
    
        
        
       
       
       <div class="row">
           <div class="col-sm-6">
                <div id="err"></div>
                <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
           </div>
        </div>
        
        
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
        coverImg.startUpload();
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
    
    function clearAcc()
	{
		$("#account_no").val("");
		$("#acc_name").text("");
		$("#account_name").val("");
	}
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
    function display_url()
    {
        var n = $("#biz_name").val();
        var ch_names = n.toLowerCase();
        var ch_name = ch_names.replace(" ","-");
        $("#biz_name").val(ch_name);
        $("#biz_url").text("https://www.vuvaa.com/shop/"+ch_name);
        $("#main_url").text("https://www.vuvaa.com/shop/"+ch_name);
    }
    function fetchAccName(acc_no)
    {
        if($("#bank_name").val() == "")
        {
           alert("Kindly select a bank");
           $("#account_no").val("");
        }else{
            if(acc_no.length == 10)
            {
                var account  = acc_no;
                var bnk_code = $("#bank_name").val();
                $("#acc_name").text("Verifying account number....");
                $("#account_name").val("");
                $.post("utilities.php",{op:"Merchant.getAccountName",account_no:account,bank_code:bnk_code},function(res){
                    $("#acc_name").text(res);
                    $("#account_name").val(res);
                });
            }else{
                $("#acc_name").text("Account Number must be 10 digits");
            }
        }
        
    }
</script>