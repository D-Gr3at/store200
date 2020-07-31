<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Splitting List for <small>(HQ/State HQ)</small></h5>
        <h6 class="card-subtitle text-muted">The report contains Collection Split that have been setup in the system.</h6>
    </div>
    <div class="card-body">
     <?php
        if($_SESSION['role_id_sess'] == 001)
        {
     ?>
      <a class="btn btn-warning" onclick="getModal('setup/splitting_hq_setup.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Split</a>
       <?php
        }
        ?>
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
                                <th>State</th>
                                <th>State Account Number</th>
                                <th>Bank</th>
                                <th>Percentage to State</th>
                                <th>Percentage to HQ</th>
                                <th>HQ Account Number</th>
                                <th>Action</th>
                                <th>Posted By</th>
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
  var op = "Split.hqSplitList";
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
    function deleteSplit(code,min,max)
    {
//        var gg = confirm("");
            swal({
                  icon:'warning',
                  text:"This action will delete every split between "+min+" and "+max+" naira. \n click OK to proceed",
                  buttons: true,
                  successMode: true,
              })
            .then((mss)=>{
                if(mss)
                {
                    $.blockUI();
                    $.post('utilities.php',{op:"Split.deleteSplit",split_code:code},function(res){
                        alert('Deleted!');
                        
                        $.unblockUI();
                        getpage('splitting_list.php','page')
                    })
                }
            
            });
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