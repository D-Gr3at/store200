<?php
require 'dbcxn.inc.php';
include('meta.php');
include("header.php");

?>
<section id="user_login" class="white-bg padding-top-bottom" style="height: 90vh !important;">
    <div class="container userLogin">
        <main class="main d-flex w-100" style="margin-left: 300px;">
            <div class="container d-flex flex-column">
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">

                            <div class="text-center mt-4">
                                <h1 class="h2" style="color:#000">Welcome to Store 200</h1>
                                <p class="lead" style="color:#ccc">
                                    Enter your email address to reset password
                                </p>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">
                                        <div class="text-center">
                                            <img src="img/logo-green-black-text.png" alt="Chris Wood" class="img-fluid" width="132" height="80" />
                                        </div>
                                        <form id="recoveryForm" onsubmit="return false">
                                            <!-- <input type="hidden" name="op" value="Users.login"> -->
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control form-control-lg" type="text" name="email" required placeholder="Enter your email" />
                                            </div>
                                            <!-- <div class="form-group">
                                                <label>Password</label>
                                                <input class="form-control form-control-lg"  type="password" name="password" required placeholder="Enter your password" />
                                                <small><a href="forgot_password.php">Forgot password?</a> </small>
                                            </div> -->
                                            <div>

                                                <!-- <div class="custom-control custom-checkbox align-items-center">
                                                    <input type="checkbox" class="custom-control-input" value="remember-me" name="remember-me" checked>
                                                    <label class="custom-control-label text-small">Remember me next time</label>
                                                </div> -->
                                            </div>
                                            <div id="server_mssg"></div>
                                            <div class="text-center mt-3">
                                                <button id="sendButton" class="btn btn-lg btn-success btn-block">Send</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>
<?php
include_once("import.php");
if (isset($_GET["activation_code"])) {

    $link = $_GET["activation_code"];
    $query = "SELECT * FROM customer WHERE verification_link = '$link'";
    $result = mysqli_query($conn, $query);
    $customer = mysqli_fetch_assoc($result);
    $date = date('Y-m-d H:i:s');
    $hours = (strtotime($date) - strtotime($customer["verify_date"])) / 3600;
    if ($hours > 12) {
        $diff = floor((strtotime($date) - strtotime($customer["verify_date"])) / 3600 - 12);
    } else {
        $diff = floor((strtotime($date) - strtotime($customer["verify_date"])) / 3600);
    }
    if ($diff < 3) {
        $query = "UPDATE customer SET expired = 1 WHERE verification_link = '$link'";
        mysqli_query($conn, $query);
?>
        <script>
            swal({
                title: "Verified!",
                icon: "success",
                text: "Email verified successfully",
                button: {
                    text: "OK",
                    value: true,
                    visible: true,
                    className: "btn-success",
                    closeModal: true,
                },
                closeOnClickOutside: false
            }).then(
                $(".swal-button").click(function() {
                    window.location = "login.php"
                })
            );
        </script>
    <?php
    } else {
        $query = "DELETE FROM customer WHERE verification_link = '$link'";
        mysqli_query($conn, $query);
    ?>
        <script>
            swal({
                title: "Verification failed!",
                icon: "error",
                text: "Email verification has expired. Please register to resend verification email.",
                button: {
                    text: "OK",
                    value: true,
                    visible: true,
                    className: "btn-danger",
                    closeModal: true,
                },
                closeOnClickOutside: false
            }).then(
                $(".swal-button").click(function() {
                    window.location = "register.php"
                })
            );
        </script>
<?php
    }
}
?>

<script>
    $(document).ready(function() {
        $("#sendButton").click(function() {
            let form = $("#recoveryForm");
            form.parsley().validate();
            if (form.parsley().isValid()) {
                $.ajax({
                    type: "POST",
                    url: "utilities.php",
                    data: "op=passwordRecovery&" + form.serialize(),
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.response_code == 1) {
                            swal({
                                title: "Sent!",
                                icon: "success",
                                text: "Email sent successfully! Please check your mail to reset your password.",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn-success",
                                    closeModal: true,
                                },
                                closeOnClickOutside: false
                            }).then(
                                $(".swal-button").click(function() {
                                    window.location = "login.php"
                                })
                            );
                            // location.href = "index.php";
                        } else if (response.response_code == 13) {
                            swal({
                                title: "Not Verified!",
                                icon: "warning",
                                text: "The account associated with this email has not been verified.",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn-primary",
                                    closeModal: true,
                                },
                                closeOnClickOutside: false
                            })
                            // $("#server_mssg").text(response.response_message).css("color", "red");
                        } else if (response.response_code == 20){
                            swal({
                                title: "Invalid Email!",
                                icon: "error",
                                text: "The email provided does not exist.",
                                button: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn-danger",
                                    closeModal: true,
                                },
                                closeOnClickOutside: false
                            })
                        }
                    }
                });
            }
        });
    });
</script>