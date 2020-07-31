<?php
    include_once("../libs/dbfunctions.php");
    $dbobject     = new dbobject();
    $id           = $_REQUEST['payment_id'];
    
?>
<style>
    b{
        color:#000
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Product Edit</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <div class="tab">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab"><i class="fa fa-cart"></i>Basic Details</a></li>
									<li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" role="tab"><i class="fa fa-image"></i>Image</a></li>
									<li class="nav-item"><a class="nav-link" href="#secondary_img" data-toggle="tab" role="tab"><i class="fa fa-image"></i> Inventory and Variant</a></li>
									<li class="nav-item"><a class="nav-link" href="#add_info_tab" data-toggle="tab" role="tab"><i class="fa fa-plus"></i> Additional Info</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab-1" role="tabpanel">
<!--									    <div class="card-body">-->
									<form>
									    <div class="form-row">
									        <div class="form-group col-md-8">
									            <label class="form-label">Name</label>
									            <input type="text" id="prd_name" class="form-control" onkeyup="big_prd_name(this.value)" placeholder="Product Name">
									        </div>
									        <div class="form-group col-md-4">
									            <label class="form-label">Ribbon</label>
									            <input type="text" id="prd_ribbon" class="form-control" placeholder="e.g new arrival">
									        </div>
									    </div>
										<div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label class="form-label">Price</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">&#x20A6</div>
                                                    </div>
                                                    <input type="number" id="prd_price" min="0" oninput="this.value = Math.abs(this.value)" onchange="updateVariantPrices(this.value)" class="form-control" placeholder="0" />
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="" title="Your resource material(s) used in getting a unit of this product">Purchase Price</label>
                                                <input type="number" id="prd_purchase_price" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="updateVariantPrices(this.value)" class="form-control" placeholder="0" />
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="" title="">Make Visible?</label>
                                                <select name="visible" id="prd_visibility" class="form-control">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                               <label for="">Pick a Brand</label>
                                                <select name="brands" id="product_brand" class="form-control">
                                                    <option value="1">All Brands</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                               <label for="">Pick a Category</label>
                                                <select name="categories" id="product_cat" class="form-control">
                                                    <option value="1">All Products</option>
                                                </select>
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="form-label w-100">Description</label>
											<textarea id="prd_description" class="form-control" placeholder="Textarea" rows="5"></textarea>
										</div>
										<hr>
										
                                        
									</form>
<!--								</div>-->
									</div>
									<div class="tab-pane" id="tab-2" role="tabpanel">
										<div class="row">
										    <div class="col-sm-12" id="photo_display">
                                                <div id="extraupload"></div>
                                            </div>
										</div>
									</div>
                                    <div class="tab-pane" id="secondary_img" role="tabpanel">
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
                                       <input id="variantManage" onclick="showManageVariant(this)" type="checkbox" class="custom-control-input" />
                                       <label class="custom-control-label" for="variantManage">Manage pricing and inventory for variants</label> 
                                  </div>
                                </div>
							</div>
						</div>
                    </div>
                    <div class="row">
						<div class="col-md-8">
							<div class="card" id="variant_card" style="display:none">
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
							<div class="card" id="variant_inventory" style="display:block">
								<div class="card-header">
									<h5 class="card-title">Inventory</h5>
<!--									<h6 class="card-subtitle text-muted">Single horizontal row.</h6>-->
								</div>
								<div class="card-body">
                                  
                                   <div class="custom-control custom-checkbox mb-1 mr-sm-2">
                                       <input id="inventory_track" checked onclick="trackInventory()" type="checkbox" class="custom-control-input" />
                                       <label class="custom-control-label" for="inventory_track">Track Inventory</label>
                                       <br>
                                       <br>
                                       <div class="row form-group">
                                          <div class="col-sm-3" id="status_div" style="display:none">
                                               <label for="">Status</label>
                                               <select name="status" id="status" class="form-control">
                                                   <option value="in stock">In Stock</option>
                                                   <option value="out of stock">Out of Stock</option>
                                               </select>
                                          </div>
                                           <div class="col-sm-3" id="inventory_div">
                                               <label for="">Inventory</label>
                                               <input type="number" min="0" id="pd_inventory" oninput="this.value = Math.abs(this.value)" class="form-control">
                                           </div>
                                           <div class="col-sm-3">
                                               <label for="">SKU</label>
                                               <input type="text" id="pd_sku" class="form-control">
                                           </div>
                                           <div class="col-sm-3">
                                               <label for="">Weight</label>
                                               <input type="number" id="pd_weight" class="form-control">
                                           </div>
                                       </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                    </div>
                                    <div class="tab-pane" id="add_info_tab" role="tabpanel">
                                        <div class="row">
										    <div class="col-sm-12" id="photo_display">
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
                                            </div>
										</div>
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
                            <textarea name="des" id="info_description" class="form-control"  rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="add_additional_info('add')">Add</button>
                    </div>
                </div>
            </div>
        </div>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
<script>
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
        dynamicFormData: function()
        {
            $.blockUI();
            var data = {op:'Product.SaveProduct', product:preparePostData() }
            return data;
        },
        onSuccess:function(files,data,xhr,pd)
        {
            $.unblockUI();
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
                    maxFileSize:1000000,
                    autoSubmit:false,
                    dynamicFormData: function()
                    {
                        var data = {myfile:"INDIA", op:'Product.addFeatureImage'}
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        
                        alert('done');
                    }
                });
</script>