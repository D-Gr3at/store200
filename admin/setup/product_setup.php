<?php
include_once('../libs/dbfunctions.php');
include_once('../class/brand.php');
include_once('../class/category.php');
include_once('../class/product.php');

$brandObj = new brand();
$categoryObj = new Category();
$productObj = new Product();
$category = json_decode($categoryObj->getCategory(array()),TRUE);
$brands   = json_decode($brandObj->getBrand(array()),TRUE);
if(isset($_REQUEST['product_id']))
{
    $operation = "edit";
    $data = array("product_id"=>$_REQUEST['product_id']);
//    var_dump($productObj->getProductDetails($data));
    $product = json_decode($productObj->getProductDetails($data),true);
}
else
{
    $operation = "new";
}

$product = $product['responseBody'];
//var_dump($product);
?>
   <div class="container-fluid p-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a onclick="getpage('product_list.php','page')" href="javascript:void(0)">Product list</a></li>
                            <li class="breadcrumb-item active">Product setup</li>
                        </ol>
                    </nav>
                    <div class="row top-btn" style="z-index:999999">
                        <div class="col-12 col-xl-8"></div>
                        <div class="col-12 col-xl-4" align="right">
                            <button onclick="saveProduct()" style="display:<?php echo ($operation == "edit")?'none':'block'; ?>" class="btn btn-block btn-success btn-lg top-btn">SAVE PRODUCT</button>
                            <button onclick="editProduct()" style="display:<?php echo ($operation == "edit")?'block':'none'; ?>" class="btn btn-block btn-success btn-lg top-btn">EDIT PRODUCT</button>
                        </div>
                    </div>
					<h1 class="h3 mb-3" id="big_prd_name"><?php echo $product['name']; ?></h1>

					<div class="row">
						<div class="col-12 col-xl-8">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Product Info</h5>
<!--									<h6 class="card-subtitle text-muted">Default Bootstrap form layout.</h6>-->
								</div>
								<div class="card-body">
									<form>
									    <div class="form-row">
									        <div class="form-group col-md-8">
									            <label class="form-label">Name<span class="asterik">*</span></label>
									            <input type="text" id="prd_name" class="form-control" onkeyup="big_prd_name(this.value)" placeholder="Product Name" value="<?php echo $product['name']; ?>">
									        </div>
									        <div class="form-group col-md-4">
									            <label class="form-label">Ribbon</label>
									            <input type="text" id="prd_ribbon" value="<?php echo $product['ribbon']; ?>" class="form-control" placeholder="e.g new arrival">
									        </div>
									    </div>
										<div class="form-row">
                                           <div class="form-group col-md-3">
                                                <label for="" title="">Make Visible?<span class="asterik">*</span></label>
                                                <select name="visible" id="prd_visibility" class="form-control">
                                                    <option value="1" <?php echo ($product['visibility'] == "1")?"selected":""; ?> >Yes</option>
                                                    <option value="0" <?php echo ($product['visibility'] == "0")?"selected":""; ?> >No</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Unit Price<span class="asterik">*</span></label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" id="prd_price" min="0" value="<?php echo $product['price']; ?>" oninput="this.value = Math.abs(this.value)" onchange="dispricecut(); updateVariantPrices()" onkeyup="dispricecut(); updateVariantPrices()" class="form-control" placeholder="0" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="" title="Cost of material(s) used in getting a unit of this product">Unit Purchase Price</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" id="prd_purchase_price" min="0" oninput="this.value = Math.abs(this.value)" value="<?php echo $product['purchasePrice']; ?>"  class="form-control" placeholder="0" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class="form-label">Discount Price</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" value="<?php echo $product['discountPrice']; ?>" readonly id="dis_price" min="0" oninput="this.value = Math.abs(this.value)"  class="form-control" placeholder="0" />
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                               <label for="">Pick a Brand<span class="asterik">*</span></label>
                                                <select name="brands" id="product_brand" class="form-control">
                                                        
                                                    <?php
                                                        foreach($brands['data'] as $row)
                                                        {
                                                            $brand_name = $row['name'];
                                                            $brand_id = $row['id'];
                                                            $selected = ($product['brandID'] == $brand_id)?"selected":"";
                                                            echo "<option $selected value='$brand_id'>$brand_name</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                               <label for="">Pick a Category<span class="asterik">*</span></label>
                                                <select name="categories" id="product_cat" class="form-control">
                                                    <option value="1">All Products</option>
                                                    <?php
                                                        foreach($category['data'] as $row)
                                                        {
                                                            $category_name = $row['name'];
                                                            $category_id = $row['id'];
                                                            $selected = ($product['categoryID'] == $category_id)?"selected":"";
                                                            echo "<option $selected value='$category_id'>$category_name</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                               <label for="">Set discount</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    
                                                    <input type="number" id="dis_percentage" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calDiscount(this.value)" value="<?php echo $product['discountedPercentage']; ?>" onchange="calDiscount(this.value)"  class="form-control" placeholder="0" />
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="form-label w-100">Description</label>
											<textarea id="prd_description" class="form-control" placeholder="Textarea" rows="5"><?php echo $product['description']; ?></textarea>
										</div>
										<hr>
										<div class="card-header">
                                            <h5 class="card-title">Additional Info</h5>
        									<h6 class="card-subtitle text-muted">Share information like "Return Policy" and "Care Instructions" with your customers.</h6>
<!--
                                               <div class="form-group">
                                                    <label class="form-label w-100"><a href="javascript:void(0)"><i class="fa fa-plus"></i> Add Additional Info</a></label>
                                                </div>
-->
                                               <ul style="list-style:none; padding:5px" id="additional_div">
                                                   
                                               </ul>
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#additional_section_modal">
                                                <i class="fa fa-plus"></i> Add Additional Info
                                                </a>
                                        </div>
                                        
									</form>
								</div>
							</div>
						</div>
						<div class="col-12 col-xl-4">
							<div style="display:<?php echo ($operation == "edit")?'none':'block'; ?>">
							    <div class="card">
							        <div class="card-header">
							            <h5 class="card-title">Upload Product Image (Cover)<span class="asterik">*</span></h5>
							            <h6 class="card-subtitle text-muted">Upload ONLY ONE cover image.</h6>
							        </div>
							        <div class="card-body">
							            <div id="extraupload"></div>
							        </div>
							    </div>
							    <div class="card">
							        <div class="card-header">
							            <h5 class="card-title">Upload Product Image (Feature)</h5>
							            <h6 class="card-subtitle text-muted">Upload maximum of five(5) images.</h6>
							        </div>
							        <div class="card-body">
							            <div id="extraupload_feature"></div>
							        </div>
							    </div>
							</div>
							<div style="display:<?php echo ($operation == "edit")?'block':'none'; ?>">
							    <div class="card">
							        <div class="card-header">
							            <h5 class="card-title">Product Image (Cover)</h5>
                                    </div>
                                    <div class="card-body">
                                        <img src="<?php echo $product['primaryImage']; ?>" style="max-width:30%" alt="">
                                    </div>
                                </div>
							</div>
							<div style="display:<?php echo ($operation == "edit")?'block':'none'; ?>">
							    <div class="card">
							        <div class="card-header">
							            <h5 class="card-title">Product Image (Feature)</h5>
                                    </div>
                                    <?php
                                        $counter = 0;
                                        $templ = "";
                                        foreach($product['otherImages'] as $val)
                                        {
                                            if($counter == 0)
                                            {
                                                $templ = $templ.'<div class="card-body">';
                                            }
                                            elseif($counter == 3)
                                            {
                                                $templ = $templ.'</div>';
                                                $templ = $templ.'<div class="card-body">';
                                                $counter = 0;
                                            }
                                                $templ = $templ.'<img src="'.$val.'" class="img-thumbnail" style="max-width:30%" alt="">';
                                            
                                            $counter++;
                                        }
                                    $closing_tag = ($counter < 3)?"</div>":"";
                                    echo $templ.$closing_tag;
                                    ?>
                                </div>
							</div>
							
						</div>
                    </div>
                    <div class="row">
						<div class="col-md-8">
							<div class="card">
								<div class="card-header">
									<h5 class="card-title">Product Options</h5>
									<h6 class="card-subtitle text-muted">Manage the options this product comes in.</h6>
                                    <small>Does your product come in different options, like Size, Color or Material? Add them here.</small>
								</div>
								<div class="card-body">
                                    <ul id="displayOption" style="list-style:none; padding:5px">
                                        
                                    </ul>
								    
									<a href="javascript:void(0)" data-toggle="modal" id="option_add" data-target="#defaultModalDanger"><i class="fa fa-plus"></i> Add an option</a>
									
								</div>
								<div class="card-footer">
                                  <div class="custom-control custom-checkbox mb-1 mr-sm-2">
                                       <input id="variantManage" onclick="showManageVariant(this)" type="checkbox" class="custom-control-input" <?php echo ($product['manageVariantInventory'] == "1")?"checked":"" ?> />
                                       <label class="custom-control-label" for="variantManage">Manage pricing and inventory for variants</label> 
                                  </div>
                                </div>
							</div>
						</div>
                    </div>
                    <div class="row">
						<div class="col-md-8">
							<div class="card" id="variant_card" style="display:<?php echo ($product['manageVariantInventory'] == "1")?"block":"none" ?>">
								<div class="card-header">
									<h5 class="card-title">Manage Variants</h5>
<!--									<h6 class="card-subtitle text-muted">Single horizontal row.</h6>-->
								</div>
								<div class="card-body">
								    <div class="custom-control custom-checkbox mb-1 mr-sm-2">
                                       <input id="inventory_track_variant" checked onclick="trackVariantInventory()" type="checkbox" class="custom-control-input" />
                                       <label class="custom-control-label" for="inventory_track_variant">Track Variant Inventory</label>
                                    </div>
									<table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width:25%;">Variant</th>
                                                <th style="width:25%">Charge(-/+)</th>
                                                <th class="d-none d-md-table-cell" style="width:15%">Price</th>
                                                <th class="inventory_variant">Inventory</th>
                                                <th class="status_variant" style="display:none">Status</th>
                                                <th>Visibility</th>
                                            </tr>
                                        </thead>
                                        <tbody id="variant_body">
                                            
                                        </tbody>
                                    </table>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8">
							<div class="card" id="variant_inventory" style="display:<?php echo ($product['manageVariantInventory'] == "1")?"none":"block" ?>">
								<div class="card-header">
									<h5 class="card-title">Inventory</h5>
<!--									<h6 class="card-subtitle text-muted">Single horizontal row.</h6>-->
								</div>
								<div class="card-body">
                                  
                                   <div class="custom-control custom-checkbox mb-1 mr-sm-2">
                                       <input id="inventory_track" <?php echo ($product['trackInventory'] == 1 && $operation == "edit")?"checked":($operation != "edit")?"checked":""; ?> onclick="trackInventory()" type="checkbox" class="custom-control-input" />
                                       <label class="custom-control-label" for="inventory_track">Track Inventory</label>
                                       <br>
                                       <br>
                                       <div class="row form-group">
                                          <div class="col-sm-3" id="status_div" style="display:none">
                                               <label for="">Status<span class="asterik">*</span></label>
                                               <select name="status" id="status" class="form-control">
                                                   <option value="In Stock" <?php echo ($product['stockStatus'] == "In Stock")?"selected":""; ?> >In Stock</option>
                                                   <option value="Out of Stock">Out of Stock</option>
                                               </select>
                                          </div>
                                           <div class="col-sm-3" id="inventory_div">
                                               <label for="">Inventory<span class="asterik">*</span></label>
                                               <input type="number" min="0" id="pd_inventory" oninput="this.value = Math.abs(this.value)" value="<?php echo $product['inventory'] ?>" class="form-control">
                                           </div>
                                           <div class="col-sm-3">
                                               <label for="">SKU<span class="asterik">*</span></label>
                                               <input type="text" id="pd_sku" value="<?php echo $product['sku'] ?>" class="form-control">
                                           </div>
                                           <div class="col-sm-3">
                                               <label for="">Weight<span class="asterik">*</span></label>
                                               <input type="number" id="pd_weight" value="<?php echo $product['weight'] ?>" class="form-control">
                                           </div>
                                       </div>
                                   </div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-xl-4" align="right">
                                <button onclick="saveProduct()" style="display:<?php echo ($operation == "edit")?'none':'block'; ?>" class="btn btn-block btn-success btn-lg">SAVE PRODUCT</button>
                                <button onclick="editProduct()" style="display:<?php echo ($operation == "edit")?'block':'none'; ?>" class="btn btn-block btn-success btn-lg">EDIT PRODUCT</button>
                            </div>
                        </div>
                    </div>

				</div>
				
			
		<div class="modal fade" id="defaultModalDanger" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_title">Add Product Option</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                       <input type="hidden" id="opt_id" value='' />
                       <div class="form-group">
                           <label for="">Option Name</label>
                           <input type="text" id="optname" placeholder="e.g Size or Color" class="form-control" />
                       </div>
                       <div class="form-group">
                           <div>
                               <label for="">Choices for this option</label>
                           </div>
                            
                            <input type="text" id="optval" value="" data-role="tagsinput"   />
                            <small class="text-muted">Separate choices with commas e.g., Small, Medium, Large,</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="opt_add_btn" onclick="populateOptions()">Add</button>
                        <button type="button" class="btn btn-danger" id="opt_edit_btn" style="display:none" onclick="editOptions()">Edit</button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="additional_section_modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Additional Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                        <div class="row form-group">
                            <label for="">Info Section Title</label>
                            <input type="text" id="info_title" class="form-control">
                        </div>
                        <div class="row form-group">
                            <label for="">Description</label>
                            <textarea name="des" id="info_description" class="form-control"  rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="add_additional_info('add')">Add</button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
        <script src="js/bootstrap-tagsinput.js"></script>
        <script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
        
        <script>
            var options = <?php echo (json_encode($product['variantJson']) == null && $operation == "edit")?json_encode([]):($operation != "edit")?json_encode([]):json_encode($product['variantJson']); ?>;
//            console.log(typeof(options));
            displayOption();
            var additional_info = [];
            var displayOptions = [];
            var server_product_id = "";
            <?php
            if($operation == "edit")
            {
            ?>
            popAddInfo(<?php echo json_encode($product['additional_info']); ?>);
            createTemplateOnEdit(<?php echo json_encode($product['variant']); ?>);
            populateOptionsEdit();
            <?php
            }
            ?>
//            getProductCat('');
//            getbrands();
            
            function populateOptions()
            {
                var p = $("#optval").val();
                var n = $("#optname").val();
                options.push({name:n,valuesd:p});
                var f = {name:n,valuesd:p.split(',')};
                console.log(f);
                displayOptions.push(f);
                displayOption();
                populateVariantTable();
                $("#optval").tagsinput('refresh');
                $('#defaultModalDanger').modal('hide');
                $('#optval').tagsinput('removeAll');
                $("#optname").empty();
                updateVariantPrices();
            }
            function populateOptionsEdit()
            {
                options.forEach((item)=>{
                    var f = {name:item.name,valuesd:item.valuesd.split(',')};
                    displayOptions.push(f);
                    displayOption();
//                    populateVariantTable();
//                    updateVariantPrices();
                })
                
            }
            function editOptions()
            {
                var key           = $("#opt_id").val();
                var edited_values = $("#optval").val();
                var edited_name   = $("#optname").val();
                
                displayOptions.filter((item,index,arr)=>{
                    if(index == key)
                        {
                            arr[index] = {name:edited_name,valuesd:edited_values.split(',')};
                            displayOptions = arr;
                            return true;
                        }
                });
                options.filter((item,index,arrs)=>{
                    if(index == key)
                        {
                            arrs[index] = {name:edited_name,valuesd:edited_values};
                            options = arrs;
                            return true;
                        }
                });
                console.log(displayOptions);
                displayOption();
                populateVariantTable();
                $('#defaultModalDanger').modal('hide');
                $('#optval').tagsinput('removeAll');
                $("#optname").empty();
                $("#opt_edit_btn").toggle();
                $("#opt_add_btn").toggle();
            }
            function displayOption()
            {
                $("#displayOption").empty();
                if(options.length > 0)
                    {
                        options.forEach((item,index)=>{
                            $("#displayOption").append(`<li style='padding:5px;border-bottom:1px solid #ccc'>${item.name}<span style='float:right;font-weight:bold'>${item.valuesd}&nbsp;&nbsp;<i onclick='deleteOption("${index}")' class='action_btn fa fa-trash'></i>&nbsp; | &nbsp; <i data-id="${index}" onclick='loadModal(this)' class='action_btn fa fa-edit'></i></span></li>`);
                        })
                    }
                
            }
            function loadModal(e)
            {
                $('#optval').tagsinput('removeAll');
                $("#optname").empty();
                
                var id = $(e).data('id');
                $("#opt_id").val(id);
                $("#optname").val(options[id].name);
//                $("#optval").val(options[id].valuesd);
                $('#optval').tagsinput('add', options[id].valuesd);
                
                $("#opt_edit_btn").toggle();
                $("#opt_add_btn").toggle();
                
                $("#option_add").trigger("click");
                
//                alert(myBookId);
            }
            function deleteOption(key)
            {
//                console.log('before splice',displayOptions);
                displayOptions.filter((item,index,arr)=>{
                    if(index == key)
                        {
                            displayOptions.splice(key,1);
                            return true;
                        }
                });
                options.filter((item,index,arr)=>{
                    if(index == key)
                        {
                            options.splice(key,1);
                            return true;
                        }
                });
//                console.log('after splice',displayOptions);
                displayOption();
                populateVariantTable();
            }
            function getAllValues()
            {
                var x = [];
                console.log("lo",displayOptions);
                displayOptions.forEach((item,index)=>{
                    x.push(item.valuesd);
//                    console.log('gt',item[index].values);
                });
                return x;
            }
            function showManageVariant(el)
            {
                var jj = $("#variantManage").is(":checked");
                if(jj == true)
                    {
                        $("#variant_card").show();
                        $("#variant_inventory").hide();
                    }
                else{
                        $("#variant_card").hide();
                        $("#variant_inventory").show();
                }
            }
            function trackInventory()
            {
                var jj = $("#inventory_track").is(":checked");
//                alert(jj)
                if(jj == true)
                    {
                        $("#inventory_div").show();
                        $("#status_div").hide();
                    }
                else{
                        $("#status_div").show();
                        $("#inventory_div").hide();
                }
            }
            function trackVariantInventory()
            {
                var jj = $("#inventory_track_variant").is(":checked");
//                alert(jj)
                if(jj == true)
                    {
                        $(".inventory_variant").show();
                        $(".status_variant").hide();
                    }
                else{
                        $(".status_variant").show();
                        $(".inventory_variant").hide();
                }
            }
            function populateVariantTable()
            {
                var all_vv = getAllValues();
                var oop = "<?php echo $operation; ?>";
                var test = doVariantCombination(all_vv);
                var template = "";
                if(test.length < 1)
                    {
                       template = ''; 
                    }else
                    {
                        if(oop =="edit"){
                            template = createTemplateOnEdit(test);
                        }else{
                            template = createTemplate(test);
                        }
                        
                    }
                
                $("#variant_body").html(template);
//                console.log(template);
            }
            function createTemplate(test)
            {
                console.log(test);
                var template = "";
                test.forEach((item)=>{
                            template += `<tr>
                                            <td>${item.substring(0,item.length-2)}</td>
                                            <td>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" onchange="updatePriceCharge(this)" class="form-control" value="0" placeholder="0">
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">&#x20A6 <span class="update_prices">${$("#prd_price").val()}</span></td>
                                            <td class="inventory_variant table-action">
                                                <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" value="1" />
                                            </td>
                                            <td class="status_variant" style="display:none"><select class="form-control"><option value='In Stock'>In Stock</option><option value='Out of Stock'>Out of Stock</option></select></td>
                                            <td><i style="cursor:pointer;" onclick="showVariantProduct(this)" class="fa fa-eye"></i><input type="hidden" value="1" /></td>
                                        </tr>`;
                        })
                return template;
            }
            function createTemplateOnEdit(test)
            {
                console.log('kimo',test);
                var template = "";
                var stk_stat = "";
                var eye = "";
                test.forEach((item)=>{
                    stk_stat = "";
                    eye = (item.visibility == "1")?"fa-eye":"fa-eye-slash";
                            template += `<tr>
                                            <td><input type="hidden" value="${item.id}" />${item.name}</td>
                                            <td>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" onchange="updatePriceCharge(this)" class="form-control" value="${item.charge}" placeholder="0">
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">&#x20A6 <span class="update_prices">${item.price}</span></td>
                                            <td class="inventory_variant table-action">
                                                <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" value="${item.inventory}" />
                                            </td>
                                            <td class="status_variant" style="display:none"><select class="form-control"><option value='In Stock'>In Stock</option><option value='Out of Stock'>Out of Stock</option></select></td>
                                            <td><i style="cursor:pointer;" onclick="showVariantProduct(this)" class="fa ${eye}"></i><input type="hidden" value="${item.visibility}" /></td>
                                        </tr>`;
                        })
                $("#variant_body").html(template);
//                console.log('chiki',template);
            }
            function doVariantCombination(array, prefix)
            {
                prefix = prefix || '';
                if (!array.length) {
                    return prefix;
                }

                var result = array[0].reduce(function (result, value) {
                    return result.concat(doVariantCombination(array.slice(1), prefix + value+" | "));
                }, []);
                return result;
            }
            function showVariantProduct(e)
            {
                var visibi = $(e).siblings().val();
                console.log($(e).siblings().val())
                if(visibi == "0")
                    {
                        $(e).removeClass("fa-eye-slash")
                        $(e).addClass("fa-eye")
                        $(e).siblings().val("1")
                    }else{
                        $(e).removeClass("fa-eye")
                        $(e).addClass("fa-eye-slash")
                        $(e).siblings().val("0")
                    }
                
            }
            function updateVariantPrices()
            {
                $(".update_prices").text($("#dis_price").val());
               // calDiscount($("#dis_percentage").val());
            }
//            console.log(doVariantCombination(allArrays));
           function add_additional_info(action)
            {
                var eli = "";
                if(action != 'delete')
                    {
                       additional_info.push({title:$("#info_title").val(),description:$("#info_description").val()}); 
                    }
                if(additional_info.length < 1)
                    {
                        $("#additional_div").html("");
                    }else
                    {
                        popAddInfo(additional_info)
                    }
                $("#info_title").val("");
                $("#info_description").val("");
            }
            function popAddInfo(additional_info)
            {
                var eli = "";
                if(!jQuery.isEmptyObject(additional_info))
                    {
                        additional_info.forEach((item,index)=>{
                           eli += `<li><i style='cursor:pointer' onclick="delete_additional('${index}')" class='fa fa-trash'></i> &nbsp; <b>${item.title}<b><br>${item.description}</li>`; 
                        })
                        $("#additional_div").html(eli);
                        $('#additional_section_modal').modal('hide');
                    }
                
            }
            function delete_additional(key)
            {
                additional_info.filter((item,index,arr)=>{
                    if(index == key)
                        {
                            additional_info.splice(key,1);
                            return true;
                        }
                });
                add_additional_info("delete");
            }
            function big_prd_name(vv)
            {
                $("#big_prd_name").text(vv);
            }
            function getProductCat(cat_id)
            {
                var temp = '';
                $.post('utilities.php',{op:'Category.getCategory',merchant_id:''},function(ee){
                    var oopt = ee.data
                    oopt.forEach((item)=>{
                        temp += `<option value='${item.id}'>${item.name}</option>`;
                    })
                    $("#product_cat").html(temp);
                },'json')
            }
            
            function getbrands()
            {
                var temp = '';
                $.post('utilities.php',{op:'Brand.getBrand'},function(ee){
                    var oopt = ee.data
                    oopt.forEach((item)=>{
                        temp += `<option value='${item.id}'>${item.name}</option>`;
                    })
                    $("#product_brand").html(temp);
                },'json')
            }
            
//            $(document).ready(function() {
//    documentation of this library is found @ http://hayageek.com/docs/jquery-upload-file.php
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
                    autoSubmit:false,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Saving product information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        var ops = "<?php echo $operation; ?>";
                        var data = {op:'Product.SaveProduct',operation:ops, product:preparePostData() }
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
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
//                        featureImg.startUpload();
                    }
                });
            
                var featureImg = $("#extraupload_feature").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:5,
                    allowedTypes:"jpg,png",
                    returnType:"json",
                    maxFileSize:1000000,
                    autoSubmit:false,
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Uploading Feature images. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        var data = {myfile:"INDIA",product_id:server_product_id, op:'Product.addFeatureImage'}
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
                        console.log(data);
//                        alert('done');
                        getpage('product_list.php','page');
                    }
                });
            
                function saveProduct()
                {
                    if(coverImg.selectedFiles == 0)
                    {
                        alert("kindly select an image file for product.")
                    }
                else{
                        coverImg.startUpload();
                    }
                }
            function editProduct()
            {
                $.post("utilities.php",{op:"Product.SaveProduct",operation:"edit",product:preparePostData()},function(ee){
                    console.log('success',ee)
                },'json')
            }
            function preparePostData()
            {
//                var data = [];
                var add_data       = additional_info;
                var prd_data       = '';
                var t_variant      = ($("#inventory_track_variant").is(':checked'))?1:0;
                
                var m_variant      = ($("#variantManage").is(':checked'))?1:0;
                var i_variant      = ($("#inventory_track").is(':checked'))?1:0;
                var m_variant_data = getManagedInventoryData();
                var has_variant    = (m_variant_data.length > 0)?1:0;
                var p_id           = "<?php echo $product['id'] ?>";
                
                var general_data = {product_id:p_id, name:$("#prd_name").val(), ribbon:$("#prd_ribbon").val(), price:$("#prd_price").val(), description:$("#prd_description").val(), is_visibility:$("#prd_visibility").val(), category:$("#product_cat").val(), is_track_variant:t_variant,has_variant:has_variant, is_manage_variant:m_variant, is_track_inventory:i_variant, inventory:$("#pd_inventory").val(), sku:$("#pd_sku").val(), pd_weight:$("#pd_weight").val(),stock_status:$("#status").val(), additional_data:add_data, product_option:options, manage_variant:m_variant_data, discount_price:$("#dis_price").val(), discounted_percentage:$("#dis_percentage").val(),is_discounted:$("input[name='set_discount']").val(),brand:$("#product_brand").val(), purchase_price:$("#prd_purchase_price").val() }
                return general_data;
                
            }
            function getManagedInventoryData()
            {
                var tr_array = [];
                $('#variant_body tr').each(function (i, row) {

                    // reference all the stuff you need first
                        var $row     = $(row),
                        $variant     = $row.find('td:eq(0)').text(),
                        $charge      = $row.find('td:eq(1) input[type="number"]'),
                        $price       = $row.find('td:eq(2) span').text(),
                        $inventory   = $row.find('td:eq(3) input[type="number"]'),
                        $status      = $row.find('td:eq(4) select'),
                        $visible     = $row.find('td:eq(5) input[type="hidden"]'),
                        $id          = $row.find('td:eq(0) input[type="hidden"]')
                    
                        tr_array.push({id:$id.val(),variant:$variant, charge:$charge.val(), price:$price, inventory:$inventory.val(), status:$status.val(), visible:$visible.val()});

                });
                return tr_array;
            }
            function updatePriceCharge(el)
            {
//                console.log($(el).parent('div .input-group').parent('td').siblings('td.d-none').children().text());
                
                 var eed = $(el).parent('div .input-group').parent('td').siblings('td.d-none').children();
                 
                var chg = $(el).val();
                eed.text(parseInt(chg)+parseInt($("#dis_price").val()));
//                alert(eed+" - "+$(el).val())
            }
            function dispricecut()
            {
                calDiscount($("#dis_percentage").val());
            }
            function calDiscount(val)
            {
                var price = $("#prd_price").val();
                if(val != 0 )
                    {
                        if(price != 0 && price != "" && val != 0 )
                        {
                            if(val > 100)
                            {
                                alert("Discount price cannot be greater than 100");
                                $("#dis_percentage").val("");
                                $("#dis_price").val("");
                                updateVariantPrices();
                            }
                            else
                            {
                                var discounted_price = price - ((val/100) * price);
                                $("#dis_price").val(discounted_price);
                                updateVariantPrices();
                            }

                        }else{
                            alert('Set a unit price first');
                            $("#dis_percentage").val("");
                        }
                    }
                else{
                      $("#dis_price").val(price);  
                    }
            }
//            });
            
        </script>
        
        
        <style>
            .asterik{
                color:red;
                font-weight: bold
            }
            .top-btn {
              position: -webkit-sticky;
              position: sticky;
              top: 0;
/*              background-color: yellow;*/
/*
              padding: 50px;
              font-size: 20px;
*/
            }        
            .label-info {
                background-color: #5bc0de;
            }
            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25em;
            }
            .bootstrap-tagsinput{
              width:100%;  
            }
            .bootstrap-tagsinput input {
                width:inherit;  
            }
            .card-title
            {
                font-weight: bold;
                color: :#000;
                font-size: 18px
            }
            .action_btn{
                cursor: pointer;
            }
            .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar{
                width: auto !important;
            }
        </style>
        