<?php
session_start();
include('meta.php');
include("header.php");
include("dbcxn.inc.php");

if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
}

$customer_id = $_SESSION["customer_id"];

$query = "SELECT * FROM customer WHERE customer_id = $customer_id";
$resource = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($resource);
?>
<style>
    .side-card {
        background-color: #fff;
        box-shadow: 2px 2px #b2bdb5;
    }

    ul {
        list-style-type: none;
    }

    .menu-item-list {
        padding: 0;
        text-align: center;
    }

    .menu-item-list li {
        margin-top: 25px;
        margin-bottom: 20px;
        text-decoration-color: black;
    }

    .menu-item-list li a {
        color: black;
        text-decoration: none;
        text-transform: uppercase;
    }

    .menu-item-list li a:hover {
        text-decoration: none;
    }

    .profile-heading {
        margin-top: 12px;
        text-transform: uppercase;
    }

    .sub-card {
        /* display: block; */
        border: .1rem solid #b2bdb5
    }

    .info {
        /* margin-top: 2rem; */
        justify-content: center;
        margin-left: 6%;
    }

    .p-info {
        margin-top: 20px;
    }

    /* .menu-item:active{
        background-color: #b2bdb5;
    } */
</style>

<section id="user_dashboard" class="gray-bg padding-top-bottom user-dashboard" style=" margin-top: 4%">
    <div class="container">
        <div class="row">
            <div class="card side-card col-sm-3 col-md-3 anima fade-right" style="margin-right: 20px;">
                <div class="card-body">
                    <ul class="menu-item-list">
                        <li><a href="" class="menu-item" id="account_link">Account</a></li>
                        <li><a href="" class="menu-item" id="order_link">Orders</a></li>
                        <li><a href="" class="menu-item" id="password_link">Change Password</a></li>
                        <!-- <li><a href="" class="menu-item">Orders</a></li> -->
                        <hr>
                        <li><a href="" class="menu-item btn btn-lg btn-danger anima fade-up d1" onclick="javascript:location.href = 'logout.php'" style="color: white; margin-bottom: 2rem;">Logout</a></li>
                    </ul>
                </div>
            </div>
            <!-- <div class="space"></div> -->
            <div id="link_content" style="height: 100%;">

            </div>
        </div>
    </div>
</section>

<?php
include_once("import.php");
?>

<script>
    $(document).ready(function() {

        const queryString = window.location.search;
        // console.log(queryString);
        if (queryString != null) {
            const urlParams = new URLSearchParams(queryString);
            // console.log(urlParams);
            const product = urlParams.get('type');
            // console.log(product);
            if (product == 'updateCustomer') {
                // console.log("Here");
                Command: toastr["success"]("Updated Personal details successfully!")

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
            if(product == 'updateCustomerAddr') {
                Command: toastr["success"]("Updated address successfully!")

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

            if(product == 'updateCustomerpassword') {
                Command: toastr["success"]("Updated password successfully!")

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
        }

        $("#link_content").load("account_overview.php");

        $("#account_link").click(function() {
            $("#link_content").empty();
            $("#link_content").load("account_overview.php");
        });

        $("#order_link").click(function() {
            $("#link_content").empty();
            $("#link_content").load("orders.php");
        });

        $("#password_link").click(function() {
            $("#link_content").empty();
            $("#link_content").load("change_password.php");
        });

        $("#user_edit").click(function() {
            $("#link_content").empty();
            $("#link_content").load("edit_user.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });

        });

        $("#delivery_addr").click(function() {
            $("#link_content").empty();
            $("#link_content").load("edit_address.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });

        });

    });
</script>