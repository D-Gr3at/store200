<?php
session_start();
require 'dbcxn.inc.php';

if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
} else {
    $customer_id = $_SESSION["customer_id"];
    $query = "SELECT * FROM customer WHERE customer_id = $customer_id";
    $resource = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($resource);

    $sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id";
    // echo $sql."\n";
    $result = mysqli_query($conn, $sql);
    $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // var_dump($addresses);

    $query = "SELECT DISTINCT State, state_code FROM lga";
    $resource = mysqli_query($conn, $query);
    $states = mysqli_fetch_all($resource, MYSQLI_ASSOC);
}
?>

<style>
    .mic {
        display: flex;
    }

    .mic svg {
        margin-right: 20px;
    }

    .mic svg {
        cursor: pointer;
    }

    .wrapper {
        width: 500px;
        /* margin: 80px auto 0; */
    }

    .wrapper .accordion_wrap .accordion_header {
        width: 100%;
        height: 50px;
        background: #fff;
        padding: 15px;
        /* color: #39a098; */
        font-weight: 700;
        /* border-bottom: 2px solid #39a098; */
        position: relative;
        cursor: pointer;
    }

    .wrapper .accordion_wrap:first-child .accordion_header {
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    .wrapper .accordion_wrap:last-child .accordion_header {
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
        border-bottom: 2px solid transparent;
    }

    /* .wrapper .accordion_wrap .accordion_header:hover{
        color: #01645d;
        border-color: #01645d;
    } */

    /* .wrapper .accordion_wrap .accordion_header:hover:before,
    .wrapper .accordion_wrap .accordion_header:hover:after{
        background: #01645d;
    } */

    /* .wrapper .accordion_wrap:last-child .accordion_header:hover{
        border-bottom: 2px solid transparent;

    } */

    .wrapper .accordion_wrap .accordion_header:before,
    .wrapper .accordion_wrap .accordion_header:after {
        content: "";
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 15px;
        width: 20px;
        height: 2px;
        background: black;
    }

    .wrapper .accordion_wrap .accordion_header:after {
        transform: rotate(-90deg);
        transition: all 0.5s ease;
    }

    .wrapper .accordion_wrap .accordion_body {
        width: 100%;
        height: 0px;
        transition: all 0.5s ease;
        /* background: #39a098; */
        overflow: hidden;
    }

    .wrapper .accordion_wrap .accordion_body p {
        padding: 15px;
        font-size: 15px;
        line-height: 22px;
        color: black;
    }

    .wrapper .accordion_wrap .accordion_header.active {
        color: forestgreen;
        /* border-color: black; */
        border-bottom: 2px solid black;
    }

    .wrapper .accordion_wrap:last-child .accordion_header.active {
        border-bottom: 2px solid black;
        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
    }

    .wrapper .accordion_wrap .accordion_header.active:before,
    .wrapper .accordion_wrap .accordion_header.active:after {
        background: #01645d;
    }

    .wrapper .accordion_wrap .accordion_header.active:after {
        transform: rotate(0);
    }

    .wrapper .accordion_wrap .accordion_header.active+.accordion_body {
        height: 180px;
    }

    .btn-del.outline {
        background: 0 0;
        border: 2px solid #DC143C;
        box-shadow: none;
        color: #DC143C;
        text-decoration: none;
        -webkit-transition: all .3s ease-out;
        transition: all .3s ease-out;
        font-weight: 600;
    }

    .btn-del.outline:hover{
        color: #fff;
        background-color: #DC143C;
    }
    .addr-menu{
        min-height: 50vh !important;
    }
</style>

<div class="card side-card col-sm-8 col-md-8 addr-menu" style="padding: 25px; margin-bottom:11%">
    <div class="mic">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg>
        <div class="card-header" style="font-size: 3rem; margin-bottom:0px">Delivery Addresses</div>
        <div class="form-group" style="position: absolute; right: 4%;">
            <button id="more_addr" class="btn btn-md btn-sm btn-store outline smooth-scroll text-right">Add Address</button>
        </div>
    </div>
    <div class="card-body info">
        <div class="container-fluid">
            <div class="wrapper">
                <?php $count = 1;
                foreach ($addresses as $key => $address) {
                    if ($address["primary_address"] == '1') { ?>
                        <div class="accordion_wrap">
                            <div class="accordion_header text-uppercase">
                                default address
                            </div>
                            <div class="accordion_body">
                                <p><?php echo $address["street"] . ", " . $address["lga"] . ", " . $address["state"]; ?></p>
                                <a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right edit_addr" data-id="<?php echo $address["id"] ?>">edit</a>
                                <!-- <a class="btn btn-sm btn-store btn-del outline cta-button smooth-scroll pull-right delete_addr" data-id="<?php echo $address["id"] ?>">delete</a> -->
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="accordion_wrap">
                            <div class="accordion_header text-uppercase">
                                address <?php echo $count; ?>
                            </div>
                            <div class="accordion_body">
                                <p><?php echo $address["street"] . ", " . $address["lga"] . ", " . $address["state"]; ?></p>
                                <a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right edit_addr" data-id="<?php echo $address["id"] ?>">edit</a>
                                <a class="btn btn-sm btn-store btn-del outline cta-button smooth-scroll delete_addr" data-id="<?php echo $address["id"] ?>">delete</a>
                            </div>
                        </div>
                <?php }
                    $count++;
                } ?>
            </div>
        </div>
    </div>
</div>

<script>
    var address_number = 1;
    $(document).ready(function() {

        $(".accordion_header").click(function() {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                $(this).addClass("active");
            }
        });

        $(document).on("click", ".edit_addr", function() {
            let id = $(this).attr("data-id");
            $("#link_content").empty();
            $("#link_content").load("add_address.php?id=" + id);
        });

        $(document).on("click", ".delete_addr", function() {
            let id = $(this).attr("data-id");
            $.ajax({
                type: "POST",
                url: "utilities.php",
                data: "op=deleteAddr&id=" + id,
                success: function(response) {
                    response = JSON.parse(response);
                    let data = response.data;
                    if (response.response_code == 1) {
                        // $("#link_content").empty();
                        $("#link_content").load("edit_address.php");
                        
                    } else if (response.response_code == 20) {
                        // $("#server_mssg").text(response.response_message).css("color", "red");
                        Command: toastr["error"]("Error deleting address")

                        toastr.options = {
                            "closeButton": false,
                            "debug": false,
                            "newestOnTop": false,
                            "progressBar": false,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "200",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        }
                        $("#link_content").load("edit_address.php");
                    }
                }
            });
        })

        $("#back").click(function() {
            $("#link_content").empty();
            $("#link_content").load("account_overview.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });

        });

        $("#more_addr").click(function() {
            $("#link_content").empty();
            $("#link_content").load("add_address.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });

        });

    });
</script>