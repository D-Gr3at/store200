<?php
include('meta.php');
include("header.php");
require 'dbcxn.inc.php';


$query = "SELECT DISTINCT State, state_code FROM lga";
$resource = mysqli_query($conn, $query);
$states = mysqli_fetch_all($resource, MYSQLI_ASSOC);
// var_dump($states);
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
    .overlay_content p{
        font-size: 5rem;
    }
</style>
<section id="user_login" class="white-bg padding-top-bottom">
    <div class="container pull-right">
        <main class="main d-flex w-100">
            <div class="container d-flex flex-column">
                <div class="row">
                    <div class="col-sm-12 col-md-10 col-lg-8 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">

                            <div class="text-center mt-3">
                                <h1 class="h2" style="color:#000">Welcome to Store 200</h1>
                                <p class="lead" style="color:#ccc">
                                    Create an account
                                </p>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="col-sm-6 col-md-10 col-lg-12">
                                        <!-- <div class="text-center">
                                            <img src="img/logo-green-black-text.png" alt="Chris Wood" class="img-fluid" width="132" height="70" />
                                        </div> -->
                                        <form id="signupForm" onsubmit="return false">
                                            <fieldset>
                                                <legend>
                                                    <p style="font-size:2.5rem; margin-bottom: 2%;"><strong>Personal Information</strong></p>
                                                </legend>
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">First Name</label>
                                                            <input name="firstname" placeholder="First name" class="form-control" type="text" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Last Name</label>
                                                            <input name="lastname" placeholder="Last name" class="form-control" type="text" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Email</label>
                                                            <input name="email" placeholder="Your email" class="form-control" type="email" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Phone</label>
                                                            <input name="phone" placeholder="Your phone number" class="form-control" type="text" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Password</label>
                                                            <input name="password" placeholder="Password" class="form-control" type="password" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Confirm Password</label>
                                                            <input name="confirmPassword" placeholder="Confirm password" class="form-control" type="password" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <legend>
                                                    <p style="font-size:2.5rem; margin-bottom: 2%;"><strong>Delivery address</strong></p>
                                                </legend>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Adress</label>
                                                            <input name="street" placeholder="Street" class="form-control" type="text" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group" data-dropup-auto="false">
                                                            <label class="control-label">State</label>
                                                            <select name="state" id="state" class="form-control" required>
                                                                <option value="">::SELECT STATE::</option>
                                                                <?php
                                                                foreach ($states as $key => $value) {
                                                                    echo "<option value='" . $value["state_code"] . "'>" . $value["State"] . "</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <!-- <input name="state" placeholder="State" class="form-control" type="text" required> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">LGA</label>
                                                            <select name="lga" id="lga" class="form-control" disabled="disabled" required>
                                                                <option value="">::NO LGA TO SELECT::</option>
                                                            </select>
                                                            <!-- <input name="city" placeholder="City" class="form-control" type="text" required> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8 col-md-8">
                                                        <div class="form-group">
                                                            <label class="control-label">Country</label>
                                                            <input name="country" placeholder="country" class="form-control" type="text" value="Nigeria" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Postal Code</label>
                                                            <input name="postalCode" placeholder="Postal code" class="form-control" type="text">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div id="server_mssg"></div>
                                            <div class="text-center mt-3">
                                                <button id="signupButton" class="btn btn-lg btn-md btn-sm btn-success btn-block">Sign up</button>
                                                <!-- <button id="resendButton" class="btn btn-lg btn-md btn-sm btn-xs btn-success btn-block" style="display: none;">Resend Verification link</button> -->
                                            </div>
                                        </form>
                                        <div class="text-center mt-3">
                                            <button id="resendButton" class="btn btn-lg btn-md btn-sm btn-success btn-block" style="display: none;">Resend Verification link</button>
                                        </div>
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
?>

<script>
    $(document).ready(function() {
        $("#state").change(function() {
            let value = $(this).val();
            if (value != '') {
                $.ajax({
                    type: "GET",
                    url: "utilities.php",
                    data: "op=getLGAs&state_code=" + value,
                    success: function(response) {
                        response = JSON.parse(response);
                        let data = response.data;
                        if (response.response_code == 1) {
                            $("#lga").removeAttr("disabled");
                            $("#lga").empty();
                            $("#lga").append("<option value='' selected>::SELECT LGA::</option>");
                            data.forEach((element, index) => {
                                $("#lga").append("<option value='" + element.Lga + "'>" + element.Lga + "</option>");
                            });
                        } else if (response.response_code == 20) {
                            $("#server_mssg").text(response.response_message).css("color", "red");
                        }
                    }
                });
            } else {
                $("#lga").empty();
                $("#lga").append("<option value=''>::NO LGA TO SELECT::</option>");
                $("#lga").prop("disabled", true);
            }
        });
    });
</script>