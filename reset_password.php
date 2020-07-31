<?php
require 'dbcxn.inc.php';
include('meta.php');
include("header.php");

?>
<style>
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
                                    Reset your password
                                </p>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="m-sm-4">
                                        <div class="text-center">
                                            <img src="img/logo-green-black-text.png" alt="Chris Wood" class="img-fluid" width="132" height="80" />
                                        </div>
                                        <form id="resetForm" onsubmit="return false">
                                            <!-- <input type="hidden" name="op" value="Users.login"> -->
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input class="form-control form-control-lg" type="password" name="password" required placeholder="New password" />
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input class="form-control form-control-lg" type="password" name="cpassword" required placeholder="Confirm password" />
                                            </div>
                                            <div>

                                                <!-- <div class="custom-control custom-checkbox align-items-center">
                                                    <input type="checkbox" class="custom-control-input" value="remember-me" name="remember-me" checked>
                                                    <label class="custom-control-label text-small">Remember me next time</label>
                                                </div> -->
                                            </div>
                                            <div id="server_mssg"></div>
                                            <div class="text-center mt-3">
                                                <button id="resetButton" class="btn btn-lg btn-success btn-block">Reset</button>
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
<?php
include_once("import.php");
if (isset($_GET["reset_password"])) {

    $link = $_GET["reset_password"];
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
        // $query = "UPDATE customer SET expired = 1, updated = '$date' WHERE verification_link = '$link'";
        // mysqli_query($conn, $query);
?>
        <script>
            $("#resetButton").click(function() {
                var resetData = $("#resetForm").serialize();
                let form = $("#resetForm");
                form.parsley().validate();
                var email = "<?php echo $customer["email"]?>";

                if (form.parsley().isValid()) {
                    $(".overlay_content").show();
                    $.ajax({
                        type: "POST",
                        url: "utilities.php",
                        data: "op=resetPassword&" + resetData+"&email="+email,
                        success: function(response) {
                            $(".overlay_content").hide();
                            response = JSON.parse(response);
                            if (response.response_code == 1) {
                                swal({
                                    title: "Success!",
                                    icon: "success",
                                    text: "Password changed successfully",
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
                            } else if (response.response_code == 13) {
                                $("#server_mssg").text(response.response_message).css("color", "red");
                            }
                        }
                    });
                }
            });
        </script>
    <?php
    } else {
        // $query = "DELETE FROM customer WHERE verification_link = '$link'";
        // mysqli_query($conn, $query);
    ?>
        <script>
            swal({
                title: "Expired!",
                icon: "error",
                text: "Verification link has expired",
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
                    window.location = "forgot_password.php"
                })
            );
        </script>
<?php
    }
}
?>