<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
$dbobject = new dbobject();
$sql = "SELECT DISTINCT(State) as State,state_code FROM lga order by State";
$states = $dbobject->db_query($sql);
//
$sql2 = "SELECT id,rule_name FROM shipping_rules  order by rule_name";
$rules = $dbobject->db_query($sql2);
//
$sql_pickup = "SELECT id,title FROM merchant_pickup_stores WHERE merchant_id = '$_SESSION[merchant_sess_id]'";
$pickup = $dbobject->db_query($sql_pickup);




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
    <h4 class="modal-title" style="font-weight:bold">Delivery Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Shipping.saveShippingPricing">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $menu_id; ?>">
       <div class="row">
          <div class="col-sm-4">
              <div class="form-group">
                  <label for="">
                      label
                  </label>
                  <input type="text" class="form-control" name="label" />
              </div>
           </div>
           <div class="col-sm-4">
               <div class="form-group">
                    <label class="form-label">State</label>
                    <select name="state" onchange="fetchLga(this.value)" id="state" class="form-control">
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
           <div class="col-sm-4">
               <div class="form-group">
                    <label>Add Regions</label>
                    <select id="lga-fds" class="form-control" style="display:block" name="regions[]" >
                    <option value="">:: SELECT A STATE</option>
                    </select>

                </div>
           </div>
           
       </div>
        
         <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
               <label for="">Select a Shipping Rule</label>
               <select name="rules" onchange="alterView(this.value)" id="rules" class="form-control">
                 <option value="">:: SELECT A RULE ::</option>
                  <?php
                   foreach($rules as $row)
                   {
                       echo "<option value='".$row['id']."'>".$row['rule_name']."</option>";
                   }
                   ?>
               </select>
               <label for="additional_rule" style="display:none" id="additional_rule_label">
                   <input id="additional_rule" name="is_additional_rule_set" value="0" type="checkbox" />
                   <small> Add rules for <span id="additional_rule_name"></span></small>
               </label>
              </div>
              
           </div>
           <div class="col-sm-4">
              <div class="form-group">
                  <label for="">Est. Delivery Time</label>
                  <input type="text" name="delivery_time" class="form-control" placeholder="e.g., 3 - 5 business days">
              </div>
           </div>
           <div class="col-sm-4">
             <div class="form-group">
                 <label for="">Pickup Station</label>
                  <select name="pickup_station" id="pickup" class="form-control">
                      <?php
                      foreach($pickup as $row)
                      {
                          echo "<option value='".$row['id']."'>".$row['title']."</option>";
                      }
                      ?>
                  </select>
             </div>
         </div>
         </div>
         <div class="row" style="display:none" id="enter_rate">
             <div class="col-sm-4">
                 <div class="form-group">
                     <label for="">Enter Rate</label>
                      <div class="input-group mb-2 mr-sm-2">
                          <div class="input-group-prepend">
                              <div class="input-group-text">&#x20A6</div>
                          </div>
                          <input type="number" name="flat_rate" id="rate" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" placeholder="0" />
                      </div>
                 </div>
             </div>
         </div>
         <div class="row" style="display:none" id="pickup_div">
<!--
             <div class="col-sm-6">
                 <div class="form-group">
                     <label for="">Select Pickup</label>
                      <select name="" id="pickup" class="form-control">
                          <?php
//                          foreach($pickup as $row)
//                          {
                              //echo "<option value='".$row['id']."'>".$row['title']."</option>";
//                          }
                          ?>
                      </select>
                 </div>
             </div>
-->
         </div>
         <div id="rules_div" style="display:none">
             <div class="row" id="weight_rule_div" style="display:none">
                 <div class="col-sm-12">
                     <div class="form-group">
                         <fieldset>
                             <legend>Pricing Rules for weight</legend>
                             <div id="multiply_weight_div">
                                <div align="right" ><span onclick="addNewWeightOption()" style="cursor:pointer">+ Add more rules</span></div>
                                 <div class="row">
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Min Kg</label>
                                             <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_weight" onkeyup="updateWeightNextValues(this,'first')" readonly value="0" name="min_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Max Kg</label>
                                             <input type="number" min="0" value="1" oninput="this.value = Math.abs(this.value)" class="form-control last_max_weight" onkeyup="updateWeightNextValues(this,'last')" name="max_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-4">
                                         <div class="form-group">
                                             <label for="">Shipping Rate</label>
                                             <div class="input-group mb-2 mr-sm-2">
                                                 <div class="input-group-prepend">
                                                     <div class="input-group-text">&#x20A6</div>
                                                 </div>
                                                 <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="weight_shipping_rate[]" placeholder="0" />
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Min Kg</label>
                                             <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_weight" onkeyup="updateWeightNextValues(this,'first')" value="1" name="min_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Max Kg</label>
                                             <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control last_max_weight" onkeyup="updateWeightNextValues(this,'last')" readonly placeholder="And Above" name="max_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-4">
                                         <div class="form-group">
                                             <label for="">Shipping Rate</label>
                                             <div class="input-group mb-2 mr-sm-2">
                                                 <div class="input-group-prepend">
                                                     <div class="input-group-text">&#x20A6</div>
                                                 </div>
                                                 <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="weight_shipping_rate[]" placeholder="0" />
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                         </fieldset>
                     </div>
                 </div>
             </div>
             <div class="row" id="price_rule_div" style="display:none">
                 <div class="col-sm-12">
                     <div class="form-group">
                         <fieldset>
                             <legend>Pricing Rules for price</legend>
                             <div id="multiply_price_div">
                               <div align="right" ><span onclick="addNewPriceOption()" style="cursor:pointer">+ Add more rules</span></div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Min Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_price" readonly value="0" name="min_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Max Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control last_max_price" value="1" onkeyup="updatePriceNextValues(this,'last')" name="max_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Shipping Rate</label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">&#x20A6</div>
                                                </div>
                                                <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="price_shipping_rate[]" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Min Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_price" onkeyup="updatePriceNextValues(this,'first')" value="1" name="min_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Max Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control last_max_price" onkeyup="updatePriceNextValues(this,'last')" placeholder="And Above" readonly name="max_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Shipping Rate</label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">&#x20A6</div>
                                                </div>
                                                <input type="number"  min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="price_shipping_rate" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                             </div>
                             
                         </fieldset>
                     </div>
                 </div>
             </div>
         </div>
         
        
       
       <div id="err"></div>
        <a href="javascript:void(0)" id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</a>
        
    </form>
</div>
<script src="js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="css/bootstrap-multiselect.css">
<script>
     $(document).ready(function() {
        
//        jj.each((index,item)=>{
//            console.log($(item).val());
//        })
    });
    $("#additional_rule").click(function(){
//        alert("sds");
        var rules_val = $("#rules").val();
        if($("#additional_rule").is(":checked"))
            {
                $("#additional_rule").val("1");
                if(rules_val == "3")
                {
                    $("#weight_rule_div").show();
                    
                }
                if(rules_val == "2")
                {
                    $("#price_rule_div").show();
                }
            }
        else{
            $("#additional_rule").val("0");
                if(rules_val == "3")
                {
                    $("#weight_rule_div").hide();
                }
                if(rules_val == "2")
                {
                    
                    $("#price_rule_div").hide();
                }
        }
        console.log(rules_val);    
    })
    var price_rate_template = `<div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Min Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_price" onkeyup="updatePriceNextValues(this,'first')" name="min_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">Item Max Price</label>
                                            <input type="number" min="0" oninput="this.value = Math.abs(this.value)" class="form-control last_max_price" onkeyup="updatePriceNextValues(this,'last')" placeholder="And Above" readonly name="max_price[]" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Shipping Rate</label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">&#x20A6</div>
                                                </div>
                                                <input type="number" id="rate" min="0" oninput="this.value = Math.abs(this.value)" class="form-control" name="price_shipping_rate[]" placeholder="0" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">&nbsp; </label>
                                            <div class="form-control" style="border:none;"><span class="fa fa-trash" style="cursor:pointer" onclick="deletePriceRate(this)"></span></div>
                                            
                                        </div>
                                    </div>
                                </div>`;
    var weight_rate_template = `<div class="row">
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Min Kg</label>
                                             <input type="text" min="0" oninput="this.value = Math.abs(this.value)" class="form-control first_min_weight" onkeyup="updateWeightNextValues(this,'first')" name="min_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-3">
                                         <div class="form-group">
                                             <label for="">Max Kg</label>
                                             <input type="text" min="0" oninput="this.value = Math.abs(this.value)" class="form-control last_max_weight" onkeyup="updateWeightNextValues(this,'last')" readonly placeholder="And Above" name="max_weight[]" />
                                         </div>
                                     </div>
                                     <div class="col-sm-4">
                                         <div class="form-group">
                                             <label for="">Shipping Rate</label>
                                             <div class="input-group mb-2 mr-sm-2">
                                                 <div class="input-group-prepend">
                                                     <div class="input-group-text">&#x20A6</div>
                                                 </div>
                                                 <input type="number" min="0" name="weight_shipping_rate[]" oninput="this.value = Math.abs(this.value)" class="form-control" placeholder="0" />
                                             </div>
                                         </div>
                                     </div>
                                     <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">&nbsp; </label>
                                            <div class="form-control" style="border:none;"><span class="fa fa-trash" style="cursor:pointer" onclick="deleteWeightRate(this)"></span></div>
                                            
                                        </div>
                                    </div>
                                 </div>`;
    function addNewPriceOption()
    {
        var jj   = $(".first_min_price").last().val();
        var last = $(".last_max_price").last();
        var last_value = parseInt(jj)+1;
        $(last).prop("readonly",false);
        $(last).val(last_value);
        $("#multiply_price_div").append(price_rate_template);
        var jj   = $(".first_min_price").last().val(last_value);
    }
    function deletePriceRate(el)
    {
        var main_row = $(el).closest(".row");
        var cur_last_price = $(main_row).find(".last_max_price").val();
        var der = $(main_row).prev();
        $(der).find(".last_max_price").val(cur_last_price);
        $(main_row).remove();
    }
    
    function addNewWeightOption()
    {
        var jj   = $(".first_min_weight").last().val();
        var last = $(".last_max_weight").last();
        var last_value = parseInt(jj)+1;
        $(last).prop("readonly",false);
        $(last).val(last_value);
        $("#multiply_weight_div").append(weight_rate_template);
        var jj   = $(".first_min_weight").last().val(last_value);
    }
    function deleteWeightRate(el)
    {
        var main_row = $(el).closest(".row");
        var cur_last_price = $(main_row).find(".last_max_weight").val();
        var der = $(main_row).prev();
        $(der).find(".last_max_weight").val(cur_last_price);
        $(main_row).remove();
    }
    function updatePriceNextValues(el,position)
    {
        var c_val = $(el).val();
        var main_row = $(el).closest(".row");
        if(position == "last")
            {
               $(main_row).next().find(".first_min_price").val(c_val); 
            }
        else if(position == "first"){
                $(main_row).prev().find(".last_max_price").val(c_val);
            console.log($(main_row).prev().find(".last_max_price"));
            }
    }
    function updateWeightNextValues(el,position)
    {
        var c_val = $(el).val();
        var main_row = $(el).closest(".row");
        if(position == "last")
            {
               $(main_row).next().find(".first_min_weight").val(c_val); 
            }
        else if(position == "first"){
                $(main_row).prev().find(".last_max_weight").val(c_val);
            console.log($(main_row).prev().find(".last_max_weight"));
            }
    }
    function alterView(val)
    {
        
        $("#additional_rule").prop('checked',false);
        $("#pickup_div").hide();
        if(val == 1)
            {
                showFlateRate();
            }
        else if(val == 2)
            {
                showWeightRate()
            }
        else if(val == 3)
            {
                showPriceRate()
            }
        else if(val == 4)
            {
                showFreeShipping()
            }
        else if(val == 5)
            {
                showPickup()
            }
    }
    function showFlateRate()
    {
        $("#enter_rate").show();
        $("#rules_div").hide();
        $("#additional_rule_label").hide();
    }
    function showFreeShipping()
    {
        $("#enter_rate").hide();
        $("#rules_div").hide();
        $("#additional_rule_label").hide(); 
    }
    function showPickup()
    {
        showFreeShipping()
        $("#pickup_div").show();
    }
    function showWeightRate()
    {
        $("#enter_rate").hide();
        $("#rules_div").show();
        $("#additional_rule_label").show();
        $("#additional_rule_name").html(" <b>price.</b>");
        $("#price_rule_div").hide();
        $("#weight_rule_div").show();
    }
    function showPriceRate()
    {
        $("#enter_rate").hide();
        $("#rules_div").show();
        $("#additional_rule_label").show();
        $("#additional_rule_name").html(" <b>weight.</b>");
        $("#price_rule_div").show();
        $("#weight_rule_div").hide();
    }
    function saveRecord()
    {
        
        $("#save_facility").text("Loading......");
        var dd = $("#form1").serialize();
        console.log(dd);
        $.post("utilities.php",dd,function(re)
        {
            $("#save_facility").text("Save");
            console.log(re);
            if(re.response_code == 0)
                {
                    
                    $("#err").css('color','green')
                    $("#err").html(re.response_message)
                    getpage('delivery_location.php','page');
                    
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
        $.post("utilities.php",{op:'Helper.shippingRegionsgetLga',state:el},function(re){
            $("#lga-fds").empty();
            $("#lga-fds").prop('multiple','multiple');
            $("#lga-fds").html(re.state);
            $('#lga-fds').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150,
                buttonWidth: '250px'
        });
        },'json');
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
    /* autocomplete tagsinput*/
.label-info {
  background-color: #5bc0de;
  display: inline-block;
  padding: 0.2em 0.6em 0.3em;
  font-size: 75%;
  font-weight: 700;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25em;
}
    .btn-group{
        border: 1px solid #ccc;
        border-radius: 3px
    }
</style>