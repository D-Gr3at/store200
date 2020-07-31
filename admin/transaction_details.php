<?php
    include_once("libs/dbfunctions.php");
    $dbobject     = new dbobject();
    $id           = $_REQUEST['payment_id'];
    
?>
<style>
    b{
        color:#000
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"> Transaction Breakdown</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <div class="tab">
								<ul class="nav nav-tabs" role="tablist">
									<li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab"><i class="fa fa-plane"></i> Flight Details</a></li>
									<li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" role="tab"><i class="fa fa-users"></i> Passenger Details</a></li>
									
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab-1" role="tabpanel">
									<div class="row">
									    <div class="col-sm-6">
									        <table class="">
                                                <tr>
<!--                                                    <td>Flight Type</td>-->
                                                    <td><small>Flight type</small><h4>Roud Trip</h4></td>
                                                    <td><small>PNR</small><h4>748d8d4</h4></td>
                                                </tr>
                                                <tr>
<!--                                                    <td>PNR</td>-->
                                                    <td><small>Amount</small><h4>NGN 50,000.00</h4></td>
                                                    <td></td>
                                                </tr>
                                                
                                            </table>
									    </div>
									    <div class="col-sm-6" align="center">
									        <h2>Lagos</h2>
									        <i class="fa fa-exchange-alt"></i>
									        <h2>Abuja</h2>
									    </div>
									</div>
								    <br>
                                   <h4>Itinerary</h4>
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <td><b>Departure Time</b></td>
                                                <td><b>Arrival Time</b></td>
                                                <td><b>Duration Time</b></td>
                                                <td><b>No. of Stops</b></td>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>Tues 14 Oct. 2020 12:15 PM</td>
                                            <td>Tues 14 Oct. 2020 01:15 PM</td>
                                            <td>1hr 00min</td>
                                            <td>0</td>
                                        </tr>
                                    </table>
										<div align="center"><i class="fa fa-exchange-alt"></i></div>
										<table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <td><b>Departure Time</b></td>
                                                    <td><b>Arrival Time</b></td>
                                                    <td><b>Duration Time</b></td>
                                                    <td><b>No. of Stops</b></td>
                                                </tr>
                                            </thead>
										    
                                            <tr>
										        <td>Tues 14 Oct. 2020 12:15 PM</td>
										        <td>Tues 14 Oct. 2020 01:15 PM</td>
										        <td>1hr 00min</td>
										        <td>0</td>
										    </tr>
										</table>
									</div>
									<div class="tab-pane" id="tab-2" role="tabpanel">
										<div class="row">
										    <div class="col-sm-12" id="photo_display">
                                                <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0" style="font-weight:bold">Primary Passenger</h5>
                                                    </div>
                                                    <div class="card-body ">
                                                       <div class="row">
                                                           <div class="col-sm-12">
                                                               <table class="table-sm table-condense">
                                                                <tr>
                                                                    <td><small>Passenger Type</small><h4>ADT</h4></td>
                                                                    <td><small>Surname </small><h4>Malu</h4></td>
                                                                    <td><small>Other Names</small><h4>Ugo Joe</h4></td>
                                                                    
                                                                </tr>
                                                                <tr>
                                                                    <td><small>Telephone</small><h4>070607556</h4></td>
                                                                    <td><small>Email</small><h4>jinx@mail.com</h4></td>
                                                                    <td><small>DOB</small><h4>2nd December 2010</h4></td>
                                                                </tr>
                                                                </table>
                                                           </div>
                                                       </div>
                                                      </div>
                                                    </div>
                                                      <hr class="my-0">
                                                      <br>
                                                    <div class="card mb-3">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0" style="font-weight:bold">Accompanying Passenger(s)</h5>
                                                    </div>
                                                    <div class="card-body ">
                                                      <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title mb-0">Passenger 1</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table-sm table-condense">
                                                                    <tr>
                                                                        <td><small>Passenger Type</small><h4>ADT</h4></td>
                                                                        <td><small>Surname </small><h4>Malu</h4></td>
                                                                        <td><small>Other Names</small><h4>Ugo Joe</h4></td>

                                                                    </tr>
                                                                    <tr>
                                                                        <td><small>Telephone</small><h4>070607556</h4></td>
                                                                        <td><small>Email</small><h4>jinx@mail.com</h4></td>
                                                                        <td><small>DOB</small><h4>2nd December 2010</h4></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title mb-0">Passenger 2</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table-sm table-condense">
                                                                    <tr>
                                                                        <td><small>Passenger Type</small><h4>ADT</h4></td>
                                                                        <td><small>Surname </small><h4>Malu</h4></td>
                                                                        <td><small>Other Names</small><h4>Ugo Joe</h4></td>

                                                                    </tr>
                                                                    <tr>
                                                                        <td><small>Telephone</small><h4>070607556</h4></td>
                                                                        <td><small>Email</small><h4>jinx@mail.com</h4></td>
                                                                        <td><small>DOB</small><h4>2nd December 2010</h4></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                      </div>
                                                      <hr class="my-0">
                                                </div>
                                            </div>
										</div>
									</div>
									
								</div>
							</div>
</div>