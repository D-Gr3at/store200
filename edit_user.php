<?php
session_start();
require 'dbcxn.inc.php';
// require 'admin/libs/desencrypt.php';

// $encrypt = new DESEncryption();

if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
}
$customer_id = $_SESSION["customer_id"];
$query = "SELECT * FROM customer WHERE customer_id = $customer_id";
$resource = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($resource);
// var_dump($row["password"]);
// $p = $encrypt->des($row["email"], $row["password"], 1, 0, null,null);
// var_dump($p);
// $p1 = $encrypt->hexToString($row["password"]);
// var_dump($p1);
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

    .v-password {
        display: flex;
    }

    .v-password input {
        margin-right: 10px;
    }
    footer{
        position: fixed !important;
    }
</style>

<div class="card side-card col-sm-8 col-md-8" style="padding: 25px; margin-bottom:20%;">
    <div class="mic">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg>
        <div class="card-header" style="font-size: 3rem; margin-bottom:0px">Edit Personal Information</div>
    </div>
    <div class="card-body info">
        <div class="container-fluid">
            <form action="#" method="POST" id="updatecustomer" onsubmit="return false">
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">First Name</label>
                            <input name="First name" id="firstname" placeholder="First name" value="<?php echo $row["first_name"] ?>" class="form-control" type="text" required>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Last Name</label>
                            <input name="Last name" id="lastname" placeholder="Last name" value="<?php echo $row["last_name"] ?>" class="form-control" type="text" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>" class="form-control input-lg" type="hidden">
                            <input name="email" id="email" placeholder="Your email" value="<?php echo $row["email"] ?>" class="form-control" type="email" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Phone</label>
                            <input name="phone" id="phone" placeholder="Your phone number" value="<?php echo $row["phone"] ?>" class="form-control" type="text" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12" style="padding-bottom: 5px;">
                        <div class="form-group">
                            <input name="submit" value="save" id="save_pinfo" class="btn btn-lg btn-md btn-sm btn-store outline smooth-scroll" type="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#save_pinfo").click(function() {
            var forms = $('#updatecustomer');
            forms.parsley().validate();
            if (forms.parsley().isValid()) {
                $.post("utilities.php", {
                        "op": "updateCustomer",
                        "firstname": $("#firstname").val(),
                        "lastname": $("#lastname").val(),
                        "email": $("#email").val(),
                        "phone": $("#phone").val(),
                        "password": $("#myPassword").val(),
                        "customer_id": $("#customer_id").val()
                    },
                    function(data) {
                        if (data.response_code == 1) {
                            location.href = "user_dashboard.php?type=updateCustomer";
                        }else {
                            Command: toastr["error"](data.response_message + "!")

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
                        }
                    }, 'json');
            }
        });

        $("#back").click(function() {
            $("#link_content").empty();
            $("#link_content").load("account_overview.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });
        });
    });

    function myFunction() {
        var x = document.getElementById("myPassword");
        // console.log(x);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>