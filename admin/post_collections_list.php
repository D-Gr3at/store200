<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Posted Collection List</h5>
        <h6 class="card-subtitle text-muted">The report contains Collections that have been posted in the system.</h6>
    </div>
    <div class="card-body">
     <?php
        if($_SESSION['church_type_id_sess'] == 1 || $_SESSION['church_type_id_sess'] == 4 )
        {}else
        {
            if($_SESSION['role_id_sess'] == 002 )
            {
       
     ?>
      <a class="btn btn-warning" onclick="getModal('setup/post_collection.php','modal_div')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Post Collection</a>
       <?php
            }
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
                                <th>Church Name</th>
                                <th>Payment ID</th>
                                <th>Amount Collected</th>
                                <th>Amount Payable</th>
                                <th>Payment Status</th>
                                <th>Date of Collection</th>
                                <th>Accountant Approval</th>
                                <th>Monitoring Unit Approval</th>
                                <th>Head Usher Approval</th>
                                <th width="300px">Action</th>
                                <th>Posted Date</th>
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



<div id="mypanels" style="display:none" >
      <header>
          <div style="float:left;display:inline;width:50%; margin-bottom:20px ">
             <img src="http://www.accessng.com/tlc/img/logo.png" style="max-width:25%" alt="">
          </div>
          <div style="clear:both"></div>
          <div style="float:left;display:inline; width:50%">
              <p style="color:#ccc">Your Payment ID</p>
              <p style="font-size:20px">1911182</p>
          </div>
            <div style="float:left; display:inline; width:50%; text-align:right">
              <h1>Total:</h1>
              <h2 style="font-size:60px">NGN 10000.00</h2>
            </div>
            <div style="clear:both"></div>
      </header>
      <div>
          <div align="center">
              <small>DOMA AUTONOMOUS</small>
          </div>
      </div>
      <hr>
</div>
<link rel="stylesheet" href="css/classic.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>
<script src="js/printThis.js"></script>
<!--<script src="js/print.js"></script>-->
<script>
//    printDiv('561484','500.00','Utako Branch');
    function printDiv(p_id,amt,ch_name)
    {
    //  $("#hide_on_print").css();
      var divToPrint=`<div id="mypanels"  >
                          <header>
                              <div style="float:left;display:inline;width:50%; margin-bottom:20px ">
                                 <img src="http://www.accessng.com/tlc/img/logo.png" style="max-width:25%" alt="">
                              </div>
                              <div style="clear:both"></div>
                              <div style="float:left;display:inline; width:50%">
                                  <p style="color:#ccc">Your Payment ID</p>
                                  <p style="font-size:20px">`+p_id+`</p>
                              </div>
                                <div style="float:left; display:inline; width:50%; text-align:right">
                                  <h1>Total:</h1>
                                  <h2 style="font-size:60px">NGN `+amt+`</h2>
                                </div>
                                <div style="clear:both"></div>
                          </header>
                          <div>
                              <div align="center">
                                  <small>`+ch_name+`</small>
                              </div>
                          </div>
                          <hr>
                    </div>`;
      var newWin=window.open();
      newWin.document.open();
      newWin.document.write('<html><link rel="stylesheet" type="text/css" href="styles/style.css"><link rel="stylesheet" type="text/css" href="vendor/bootstrap/dist/css/bootstrap.css"></link><link rel="stylesheet" type="text/css" href="css/printcss.css"></link><body><div class="block-fluid clearfix">'+divToPrint+'</div></body></html>');
      newWin.document.close();
      //setTimeout(function(){newWin.close();},20);
    }

function printing(seldiv,p_id,c_id,amt)
    {
        
        printDiv(seldiv)
         $('#mypanels').printThis({
                importCSS:true,
                importStyle:true,
                loadCSS:'http://www.accessng.com/tlc/css/print.css',
                pageTitle:'Payment Slip'
            });  
//        await prepareData(p,a,c).then(()=>{
//            $('#mypanels').printThis({
//                importCSS:true,
//                importStyle:true,
//                loadCSS:'css/print.css',
//                pageTitle:'Payment Slip'
//            });   
//        });  
    }
async function prepareData(p,a,c)
    {
        var p_id = p;
        var amount = a;
        var c_name = c;
        var data = { payment_id: p_id, amt:amount, church_name:c_name }
            var template = `<header>
                      <div style="float:left;display:inline;width:50%; margin-bottom:20px ">
                         <img src="img/logo.png" style="max-width:25%" alt="">
                      </div>
                      <div style="clear:both"></div>
                      <div style="float:left;display:inline; width:50%">
                          <p style="color:#ccc" >Your Payment ID</p>
                          <p style="font-size:20px">{{payment_id}}</p>
                      </div>
                    <div  style="float:left; display:inline; width:50%; text-align:right">
                      <h1>Total:</h1>
                      <h2 style="font-size:60px">NGN {{amt}}</h2>
                    </div>
                    <div style="clear:both"></div>
                  </header>
                  <div>
                      <div align="center">
                          <small>{{church_name}}</small>
                      </div>

                  </div>
                  <hr>`;
            var text = Mustache.render(template, data); 
            $("#mypanels").html(text);
    }

</script>
<script>
  var table;
  var editor;
  var op = "Collection.postCollectionList";
  $(document).ready(function() {
    table = $("#page_list").DataTable({
        autoWidth: false,
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      },
      { width: "50%", targets: 3 }
      ],
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
    function suspend_collection(el,status)
    {
        if(status == 0)
            {
                 let cc = confirm("Are you sure you want to suspend this collection?");
                if(cc)
                {
                    swal({
                      text: 'Reason for suspending collection',
                      content: "input",
                      button: {
                        text: "Save",
                        closeModal: true,
                      },
                    })
                    .then(why => {
                      if (why == "") 
                      {
                          throw null;
                      }else
                      {
                            $.blockUI();
                            $.post('utilities.php',{op:'Collection.suspendCollection',collection_id:el,reason:why,suspension:status},function(res){
                                $.unblockUI();
                                if(res.response_code == 0)
                                    {
                                        swal({
                                          text: res.response_message,
                                          icon: "success",
                                        }).then((val)=>{
                                            getpage('post_collections_list.php','page');
                                        });
                                    }else{
                                        swal({
                                          text: res.response_message,
                                          icon: "error",
                                        });
                                    }
                            },'json');
                            return null;    
                      }
                       
                    })
                }
            }
        else{
             let cc = confirm("Are you sure you want to unsuspend this collection?");
                if(cc)
                    {
                        $.blockUI();
                        $.post('utilities.php',{op:'Collection.suspendCollection',collection_id:el,suspension:status},function(res){
                            $.unblockUI();
                            if(res.response_code == 0)
                                {
                                    swal({
                                      text: res.response_message,
                                      icon: "success",
                                    }).then((val)=>{
                                            getpage('post_collections_list.php','page');
                                        });
                                }else{
                                    swal({
                                      text: res.response_message,
                                      icon: "error",
                                    });
                                }
                        },'json');
                        return null;   
                    }
        }
       
        if(cc)
            {
//                var div  = document.createElement("span");
//                swal({
//						  title:"Applicant Details",
//						  content:div,
//							button:false
//						  icon:"warning",
//
//						});
               
               
            }
   }
    function approve_collection(id)
    {
        let cnf = confirm("Are you sure you want to approve this collection");
        if(cnf)
        {
            $.blockUI();
            $.post('utilities.php',{op:'Collection.approveCollection',collection_id:id},function(res){
                $.unblockUI();
                if(res.response_code == 0)
                    {
                        alert(res.response_message);
                        getpage('post_collections_list.php','page');
                    }else
                        {
                            alert(res.response_message);
                        }
            },'json');
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