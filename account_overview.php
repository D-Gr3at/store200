<?php
session_start();
include("dbcxn.inc.php");
if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
}else{
    $customer_id = $_SESSION["customer_id"];
    $query = "SELECT * FROM customer WHERE customer_id = $customer_id";
    $resource = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($resource);

    $sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<style>
    /* .mic {
        display: flex;
    } */

    .mic svg {
        margin-right: 20px;
    }

    .mic svg {
        cursor: pointer;
    }
</style>

<div class="card side-card col-sm-8 col-md-8" style="padding: 25px; height: 67vh;">
    <div class="mic">
        <!-- <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg> -->
        <div class="card-header" style="font-size: 3rem; margin-bottom:0px; margin-left: 8%">Account Overview</div>
    </div>
    <div class="card-body info">
        <div class="sub-card col-sm-5 col-md-5" style="margin: 10px;">
            <div class="card-title profile-heading">
                <h4>Personal Information</h4>
            </div>
            <div class="p-info">
                <hr>
                <p><?php echo $row["first_name"] . " " . $row["last_name"]; ?></p>
                <p><?php echo $row["email"] ?></p>
                <p><?php echo $row["phone"] ?></p>
                <div class=" d1" style="padding-bottom: 2rem;">
                    <a class="btn btn-sm btn-store outline cta-button smooth-scroll" id="user_edit">edit</a>
                </div>
            </div>
        </div>
        <div class="sub-card col-sm-5 col-md-5" style="margin: 10px;">
            <div class="card-title profile-heading">
                <h4>Delivery Address</h4>
            </div>
            <hr>
            <div class="p-info">
                <h5>DEFAULT ADDRESS:</h5>
                <?php foreach($addresses as $key => $address){
                    if($address["primary_address"] == '1') {?>
                        <p><?php echo $address["street"].", ".$address["lga"].", ".$address["state"].", ".$address["country"]; ?></p>
                <?php } } $check = array(); $check = array_filter($addresses, function($val, $key){
                    return $val["primary_address"] == '1';
                }, ARRAY_FILTER_USE_BOTH); if($check == NULL){?>
                    <p>NO DEFAULT ADDRESS</p>
                <?php }?>
                <div class=" d1" style="padding-bottom: 2rem;">
                    <a class="btn btn-sm btn-store outline cta-button smooth-scroll" id="delivery_addr">View</a>
                    <a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right" id="add_addr">Add</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $("#user_edit").click(function () {
            // $("#link_content").empty();
            $("#link_content").load("edit_user.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText+" "+response);
                }
            });

        });

        $("#delivery_addr").click(function () {
            $("#link_content").empty();
            $("#link_content").load("edit_address.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText+" "+response);
                }
            });

        });


        $("#add_addr").click(function () {
            $("#link_content").empty();
            $("#link_content").load("add_address.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText+" "+response);
                }
            });

        });

    });
</script>