<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Church Type List</h5>
        <h6 class="card-subtitle text-muted">The report contains types of group a church can fall into.</h6>
    </div>
    <div class="card-body">
<!--      <a class="btn btn-warning" onclick="getModal('setup/church_type_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Church Type</a>-->
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                    <label for=""></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Is part of split?</th>
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
  var op = "Church.churchTypeList";
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
//          d.start_date = $("#start_date").val();
//          d.end_date = $("#end_date").val();
        }
      }
    });
  });

  function do_filter() {
    table.draw();
  }
    function action_btn(val,menu)
    {
        $.blockUI({ message:'<img src="images/loading.gif" alt=""/>&nbsp;&nbsp;processing request please wait . . .'});
        $.post('utilities.php',{op:'web_report.pageActivation',state:val,menu_id:menu},function(res){
            $.unblockUI();
            response = JSON.parse(res);
            if(response.response_code == 0)
                {
                    swal({
                          icon:'success',
                          text:response.response_message
                      });
                    getpage('web/pages.php','page');
                }
            else{
                alert(response.response_message);
                    swal({
                      icon: "error",
                      text: response.response_message,
                    });
                }
            
        })
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
</script>