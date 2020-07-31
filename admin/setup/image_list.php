<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$p_id = $_REQUEST['product_id'];
?>
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
<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
    
    
    
    
    
    var feat = [];
            $(".update_image").each(function(){
                var img_path = $(this).parent().find('.image_path').val();
                var img_id = $(this).parent().find('.image_id').val();
                console.log(img_id)
                 feat.push( $(this).uploadFile({
                            url:"utilities.php",
                            fileName:"upfile",
                            showPreview:true,
                            previewHeight: "100px",
                            previewWidth: "100px",
                            maxFileCount:1,
                            statusBarWidth:'280px',
                            multiple:false,
                            uploadStr:'Update Image',
                            allowedTypes:"jpg,png",
                            maxFileSize:1000000,
                            autoSubmit:true,
                            returnType:"json",
                            onSubmit:function(files)
                            {
                                var c = confirm("Are you sure you want to update this image?");
                                if(c)
                                    {
                                        $("#tab-2").block({message:"Updating image. Kindly wait.."});
                                        return true;
                                    }else{
                                        return false;
                                    }
                            },
                            dynamicFormData: function()
                            {
                                var data = {op:'Product.updateProductImage',u_type:'feature',image_id:img_id,image_location:img_path }
                                return data;
                            },
                            onSuccess:function(files,data,xhr,pd)
                            {
                                $("#tab-2").unblock();
                                console.log(data);
                                if(data.response_code == 0)
                                    {
                                        rreload();
                                    }else
                                    {
                                        $(this).reset();
                                        $('.ajax-file-upload-red').click();
                                    }
        //                        featureImg.startUpload();
                            }
                        })
                   )
            });
    
    
    
    $(".add_image").each(function(){
                
                $(this).uploadFile({
                            url:"utilities.php",
                            fileName:"upfile",
                            showPreview:true,
                            previewHeight: "100px",
                            previewWidth: "100px",
                            maxFileCount:1,
                            multiple:false,
                            uploadStr:'Add Image',
                            statusBarWidth:'280px',
                            allowedTypes:"jpg,png",
                            maxFileSize:1000000,
                            autoSubmit:true,
                            returnType:"json",
                            onSubmit:function(files)
                            {
                                var c = confirm("Are you sure you want to add this image?");
                                if(c)
                                    {
                                        $("#tab-2").block({message:"Adding image. Kindly wait.."});
                                        return true;
                                    }else{
                                        return false;
                                    }
                            },
                            dynamicFormData: function()
                            {
                                var data = {op:'Product.addFeatureImage',product_id:'<?php echo $p_id; ?>' }
                                return data;
                            },
                            onSuccess:function(files,data,xhr,pd)
                            {
                               $("#tab-2").unblock();
                                console.log(data);
                                if(data.response_code == 0)
                                    {
                                        rreload()
                                    }else
                                    {
                                        $(this).reset();
                                        $('.ajax-file-upload-red').click();
                                    }
        //                        featureImg.startUpload();
                            }
                        })
            });
</script>