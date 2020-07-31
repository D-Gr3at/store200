<?php
include_once("libs/dbfunctions.php");
$dbobject    = new dbobject();
$sql_pastors = "SELECT firstname,lastname,username,church_id FROM userdata WHERE role_id = '003' ";
$pastors     = $dbobject->db_query($sql_pastors);
//var_dump($pastors);
?>
<link rel="stylesheet" href="css/bootstrap-select.css" />
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
   myCalendar.hideTime();
}
</script>
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Transfer Pastor</h5>
    </div>
    <div class="card-body">
        <form id="form1">
            <input type="hidden" name="op" value="Church.transferPastor">
            <input type="hidden" name="page" value="">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Search Pastor</label>
                        <select onchange="loadChurch(this.value)" name="pastor_username" class="form-control select2" data-toggle="select2">
                          <option value="">Select a pastor</option>
                           <?php
                                foreach($pastors as $row)
                                {
                                    $full_name = $row[lastname]." ".$row[firstname].", ".$row['username'];
                                    echo "<option value='".$row['username']."'>".$full_name."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Search Church</label>
                        <select id="transfer_church" name="church_id" class="form-control select2" data-toggle="select2">
                             
                        </select>
                    </div>
                </div>
            </div>
            
            <div id="server_mssg"></div>
            <div class="mt-3">
                <a href="javascript:saveRecord()" class="btn btn-lg btn-primary">Transfer</a>
            </div>
        </form>
    </div>
</div>
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">-->

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.js"></script>
<script>
    function saveRecord()
    {
        let cc = confirm("Are you sure you want to transfer this pastor?");
        if(cc)
            {
               $("#save_facility").text("Loading......");
                var dd = $("#form1").serialize();
                $.post("utilities.php",dd,function(re)
                {
                    $("#save_facility").text("Save");
                    console.log(re);
                    if(re.response_code == 0)
                        {
                            $("#server_mssg").html(re.response_message);
                        }
                    else
                       $("#server_mssg").html(re.response_message)
                },'json') 
            }
    }
    function loadChurch(el)
    {
        $.blockUI();
        $("#transfer_church").empty();
        $.post("utilities.php",{op:"Church.getChurches",pastor:el},function(dd)
        {
            $.unblockUI();
            $("#transfer_church").append(dd)
        });
        
    }
    $(function() {
			// Select2
			$(".select2").each(function() {
				$(this)
					.wrap("<div class=\"position-relative\"></div>")
					.select2({
						placeholder: "Select value",
						dropdownParent: $(this).parent()
					});
			})
    });
</script>