<?php
require 'dbcxn.inc.php';
include('meta.php');
include("header.php");

?>
<style>
    #clickSend:hover {
        cursor: pointer;
        text-decoration: none;
    }

    .overlay_content {
        background-color: #000000b3;
        top: 0;
        bottom: 0;
        position: fixed;
        display: none;
        left: 0;
        right: 0;
        z-index: 3000;
        text-align: center;
    }

    .overlay_content p {
        font-size: 5rem;
    }
</style>
<section id="user_login" class="white-bg padding-top-bottom" style="height: 95vh;">
    <div class="container userLogin">
        <main class="main d-flex w-100" style="margin-left: 300px;">
            <div class="container d-flex flex-column">
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">

                            <div class="text-center mt-4">
                                <h1 class="h2" style="color:#000">Welcome to Store 200</h1>
                                <p class="lead" style="color:#ccc">
                                    Sign in to your account
                                </p>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">
                                        <!-- <div class="text-center">
                                            <img src="img/logo-green-black-text.png" alt="Chris Wood" class="img-fluid" width="132" height="80" />
                                        </div> -->
                                        <form id="loginForm" onsubmit="return false">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control form-control-lg" type="text" name="email" required placeholder="Enter your email" />
                                            </div>
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input class="form-control form-control-lg" type="password" id="loginpassword" name="password" required placeholder="Enter your password" />
                                                <small><a href="forgot_password.php">Forgot password?</a> </small>
                                                <label style="margin-left: 67%;" for=""><input type="checkbox" name="show" onclick="myFunction('loginpassword')">Password</label>
                                            </div>
                                            <div id="server_mssg"></div>
                                            <div class="text-center mt-3">
                                                <button id="loginButton" class="btn btn-lg btn-md btn-sm btn-success btn-block">Sign in</button>
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
    <div class="overlay_content">
        <img src="img/loading.gif" />
        <!-- <p style="color: #fff;">LOADING...</p> -->
    </div>
</section>

<script>
    function myFunction(id) {
        var x = document.getElementById(id);
        // console.log(x);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>
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
    if ($diff < 3 && $customer["verified"] == '1') {
?>
        <script>
            swal({
                title: "Already Verified!",
                icon: "warning",
                text: "Account has been verified already",
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
    }
    if ($diff < 3 && $customer["verified"] == '0') {
        $query = "UPDATE customer SET expired = 1, verified = 1 WHERE verification_link = '$link'";
        mysqli_query($conn, $query);
    ?>
        <script>
            swal({
                title: "Verified!",
                icon: "success",
                text: "Account verified successfully",
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
    }
    if ($diff > 3) {
        $query = "DELETE FROM customer WHERE verification_link = '$link'";
        mysqli_query($conn, $query);
    ?>
        <script>
            swal({
                title: "Verification failed!",
                icon: "error",
                text: "Account verification link has expired. Please register to resend verification link.",
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