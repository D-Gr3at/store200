<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();
?>
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
</style>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title"><i data-feather="sliders"></i>Delivery Locations List</h5>
        <h6 class="card-subtitle text-muted">The report contains delivery locations by state.</h6>
    </div>
    <div class="card-body">
     
       <div class="row" style="margin-bottom:20px">
             
             <div class="col-sm-2">
             <a class="btn btn-warning" onclick="getModal('setup/delivery_location.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Add a location</a>
         </div>
         </div>
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>State</th>
                                <th>Region</th>
                                <th>Deivery Time</th>
                                <th>Pickup Station</th>
                                <th>Shipping Rule</th>
                                <th>Action</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="../js/jquery.blockUI.js"></script>-->
<script>
  var table;
  var editor;
  var op = "Shipping.deliveryLocationList";
  $(document).ready(function() {
    table = $("#page_list").DataTable({
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      serverSide: true,
      paging: true,
      oLanguage: {
        sEmptyTable: "No record was found, please try another query"
      },

      ajax: {
        url: "utilities.php",
        type: "POST",
        data: function(d, l) {
          d.op = op;
          d.li = Math.random();
          d.state    = $("#state").val();
          d.payment  = $("#payment").val();
          d.filter   = $("#filter").val();
          d.churches = $("#churches").val();
          d.region = $("#region").val();
//          d.end_date = $("#end_date").val();
        }
      }
    });
  });

  function do_filter() {
    table.draw();
  }
    function hide_div(el)
    {
        if(el.id == "branch_filter")
        {
            $("#churches_div").show();
            $("#region_div").hide();
            $("#filter").val(el.value);
        }else{
            $("#churches_div").hide();
            $("#region_div").show();
            $("#filter").val(el.value);
        }
    }
    
    function getModal(url,div)
    {
//        alert('dfd');
        $('#'+div).html("<h2>Loading....</h2>");
//        $('#'+div).block({ message: null });
        $.post(url,{},function(re){
            $('#'+div).html(re);
        })
    }
    function fetchLga(el)
    {
        $("#lga-fd").html("<option>Loading Lga</option>");
        $.post("utilities.php",{op:'Church.getLga',state:el},function(re){
//            $("#lga-fd").empty();
            console.log(re);
            $("#lga-fd").html(re.state);
            $("#church_id").html(re.church);
            
        },'json');
    }
    function churchByState(el)
    {
        
        $.post("utilities.php",{op:'Church.churchByState',state:el},function(re){
//            $("#lga-fd").empty();
            console.log(re);
            $("#churches").empty();
            $("#churches").html(re);
            
        });
    }
</script>