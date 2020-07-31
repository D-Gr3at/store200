<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$sql = "SELECT * FROM merchant_page_settings WHERE merchant_id = '$_SESSION[merchant_sess_id]'";
$settings = $dbobject->db_query($sql);
if(isset($_GET['operation']))
{
    $operation = "edit";
}
else
{
    $operation ="new";
}
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">General Settings</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form action="" id="settings_form" onsubmit="return false">
        <input type="hidden" name="op" value="web_report.saveGeneralSettings">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label for="">Primary Color</label>
                    <input type="color" value="<?php echo $settings[0]['primary_color']; ?>"  class="form-control" name="primary_color" />
                </div>
           </div>
           <div class="col-sm-6">
                <div class="form-group">
                     <label for="">Secondary Color</label>
                    <input type="color" value="<?php echo $settings[0]['secondary_color']; ?>"   class="form-control" name="secondary_color" />
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                     <label for="">Menu's Font Color</label>
                    <input type="color" value="<?php echo $settings[0]['menu_font_color']; ?>"  class="form-control" name="menu_font_color" />
                </div>
           </div>

       <div class="col-sm-6">
                <div class="form-group">
                     <label for="">Adjust Logo Size</label>
                   <input class="form-control" type="range" min="1" onchange="displayVal(this.value)" name="logo_max_width" max="100" value="<?php echo $settings[0]['logo_max_width']; ?>">
                   <small id="showval"><?php echo $settings[0]['logo_max_width']; ?></small><small>px</small>
                </div>
           </div>
       </div>
       
       <div class="row">
           <div class="col-sm-6">
                <div class="form-group">
                     <label for="sh_display">Show Display Name
                     <input type="checkbox" id="sh_display" class="" name="show_display_name_logo_old" <?php echo ($settings[0]['show_display_name_logo'] == '1')?'checked':''; ?> />
                     
                    </label>
                    <input type="hidden" id="show_display_name_logo" name="show_display_name_logo" value="<?php echo $settings[0]['show_display_name_logo']; ?>">
                </div>
           </div>
       </div>
       <div class="row">
<!--
           <div class="col-sm-6">
               <div class="form-group">
                     <label for="">Logo</label>
                    <input type="file"  class="form-control" name="display_name" />
                </div>
           </div>
-->
           
       </div>
        
      <button class="btn btn-success" id="save_facility" onclick="saveRecord()">SAVE</button>
    </form>
</div>
<script>
    function saveRecord()
    {
        $("#save_facility").text("Loading......");
        var dd = $("#settings_form").serialize();
        $.post("utilities.php",dd,function(re)
        {
            $("#save_facility").text("Save");
            alert(re);
        })
    }
    $("#sh_display").click(function(){
        if($("#sh_display").is(':checked'))
        {
            $("#show_display_name_logo").val(1);
        }else{
            $("#show_display_name_logo").val(0);
        }
    })
    
    function displayVal(v)
    {
        $("#showval").text(v);
    }
</script>