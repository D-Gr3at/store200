<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
$dbobject = new dbobject();
$sql = "SELECT * FROM merchant_page_settings WHERE merchant_id = '$_SESSION[merchant_sess_id]'";
$settings = $dbobject->db_query($sql,false);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Page Settings List</h5>
        <h6 class="card-subtitle text-muted">The report contains setting for webpage that have you have created.</h6>
    </div>
    <div class="card-body">
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-3">
                   <?php
                    if($settings == 0)
                    {
                    ?>
                    <button  id="search" class="btn btn-info btn-block" onclick="getModal('setup/settings.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Setup Page</button>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
			
                                <th>S/N</th>
                                <th>Primary Color</th>
                                <th>Secondary Color</th>
                                <th>Menu Font Color</th>
                                <th>Business Name Position</th>
                                <th>Show Business Name?</th>
                                <th>Business Name</th>
                                <th>Logo Width</th>
                                <th>Logo</th>
                                
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
  var op = "web_report.pageList";
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
    
    function deleteMenu(id)
    {
        let cnf = confirm("Are you sure you want to delete menu?");
        if(cnf == true)
            {
                $.blockUI();
                $.post("utilities.php",{op:"Menu.deleteMenu",menu_id:id},function(re){
                    $.unblockUI();
                    alert(re.response_message);
                    getpage('menu_list.php',"page");
                },'json')
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
</script>