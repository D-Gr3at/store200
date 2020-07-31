<?php
include_once("../libs/dbfunctions.php");
//include_once("../class/notification.php");
//$nn = new EmailTemplate();
//echo $nn->emailVerification(array('body'=>'<h2>Hello Ugo,</h2>Thank you.','title'=>'email verify'));
$dbobject = new dbobject();
//$ss = "SELECT role_id,role_name FROM role WHERE role_id = '003'";
//$ee = $dbobject->db_query($ss);
//$c_type[] = array('id'=>$ee[0]['role_id'],'name'=>$ee[0]['role_name']);
if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation  = 'edit';
    $id         = $_REQUEST['product_id'];
    $sql        = "SELECT * FROM products WHERE id = '$id' LIMIT 1";
    $product  = $dbobject->db_query($sql);
//    var_dump($splitting);
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
   myCalendar.hideTime();
}
</script>
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
    .pointer{
        cursor: pointer;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"><?php echo $product[0]['name']; ?> Images</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Split.saveSplit">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="code" value="<?php echo $code; ?>">
       
       <div class="tab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab">Primary Image</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" onclick="rreload()" role="tab">Other Image(s)</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-1" role="tabpanel">
                   <div class="row">
                       <div class="col-sm-5"><p>ID: <?php echo $product[0]['id']; ?></p><img class="img-thumbnail" src="<?php echo $product[0]['image']; ?>" alt=""></div>
                        <div class="col-sm-7"><h5>Change Image</h5><div id="extraupload"></div>
                        </div>
                   </div>
                   <input type="hidden" id="image_path" value="<?php echo $product[0]['image']; ?>" />
                   <input type="hidden" id="product_id"    value="<?php echo $product[0]['id']; ?>" />
                </div>
                <div class="tab-pane" id="tab-2" role="tabpanel">
                    <table id="prod_img" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Image</th>
                                <th>Action</th>
<!--                                <th></th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $p_id = $product[0]['id'];
                                $sql = "SELECT * FROM product_images WHERE product_id = '$p_id'";
                                $result = $dbobject->db_query($sql);
//                           
                                for($x=4; $x>=0; $x--)
                                {
                                    if(isset($result[$x]))
                                    {
                                        $row = $result[$x];
                                        $count = 5-$x;
                                        echo "<tr><td>$count</td><td><img style='display:block' src='$row[location]' width='70' height='70' class='img-thumbnail' /><span onclick='remove_img(\"$row[location]\",\"$row[id]\")' class='pointer badge badge-danger'>Remove Image</span></td><td><div class='update_image'></div><input class='image_path' type='hidden' value='$row[location]' /><input class='image_id' type='hidden' value='$row[id]' /></td></tr>";
                                    }else
                                    {
                                        $count = 5-$x;
                                        echo "<tr><td>$count</td><td>Free image frame</td><td><div class='add_image'></div></td></tr>";
                                    }
                                }
//                           
                            ?>
                        </tbody>
                    </table>
                </div>
               
            </div>
        </div>
       
<!--
        <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary">Submit</button>
-->
    </form>
</div>

<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
        function rreload()
        {
            $("#tab-2").block();
            $.post('setup/image_list.php',{product_id:'<?php echo $p_id ?>'},function(ee){
                $("#tab-2").unblock();
                $("#tab-2").html(ee);
                
                
            })
        }
    var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    dragDropStr:"<span><b>Drop Product Image here</b></span>",
                    multiple:false,
                    allowedTypes:"jpg,png",
                    maxFileSize:1000000,
                    autoSubmit:true,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        var c = confirm("Are you sure you want to set this image?");
                        if(c)
                            {
                                $("#tab-1").block({message:"Updating image. Kindly wait.."});
                                return true;
                            }else{
                                return false;
                            }
                        
                    },
                    dynamicFormData: function()
                    {
                        var ops = "<?php echo $operation; ?>";
                        var data = {op:'Product.updateProductImage',product_id:$('#product_id').val(), image_location:$('#image_path').val(),u_type:'primary' }
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $("#tab-1").unblock();
                        console.log(data);
                        if(data.response_code == 0)
                            {
                                server_product_id = data.data.product_id;
                                featureImg.startUpload();
                            }else
                            {
                                coverImg.reset();
                                $('.ajax-file-upload-red').click();
                            }
                    }
                });

    function remove_img(link,id)
    {
        var c = confirm("Are you sure you want to remove this image?");
        if(c)
        {
            $("#tab-2").block({message:"Removing image. Kindly wait.."});
            $.post('utilities.php',{op:'Product.removeImage',image_id:id,image_location:link},function(ww){
                $("#tab-2").unblock();
                alert(ww.response_message);
                rreload();
            },'json');
        }
        
    }
    function saveRecord()
    {
//       if(coverImg.selectedFiles == 0)
//                    {
//                        alert("kindly select an image file for product.")
//                    }
//                else{
//                        coverImg.startUpload();
//                    }
    }
    
            
  
</script>
<style>
    .ajax-upload-dragdrop{
        width: 270px !important;
    }
</style>