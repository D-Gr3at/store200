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

    if (isset($_GET["id"])) {
        $address_id = $_GET["id"];
        $operation = "Edit";
        $sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id AND id = $address_id";
        // echo $sql."\n";
        $result = mysqli_query($conn, $sql);
        $address = mysqli_fetch_assoc($result);
        // var_dump($address);
    } else {
        $operation = "Add";
    }

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

    /* Hide default input */
    .toggle input {
        display: none;
    }

    /* The container and background */
    .toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 20px;
    }

    .sliders {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #aaa;
        border: 1px solid #aaa;
        border-radius: 5px;
        transition: all 0.4s;
    }

    /* The sliding button */
    .sliders:before {
        position: absolute;
        content: "";
        width: 25px;
        height: 16px;
        left: 2px;
        top: 1px;
        background-color: #eee;
        border-radius: 5px;
        transition: all 0.4s;
    }

    /* On checked */
    input:checked+.sliders {
        background-color: #32CD32;
    }

    input:checked+.sliders:before {
        transform: translateX(20px);
    }
</style>

<div class="card side-card col-sm-8 col-md-8" style="padding: 25px; height: 100%; margin-bottom:10%">
    <div class="mic">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg>
        <div class="card-header" style="font-size: 3rem; margin-bottom:0px"><?php echo $operation; ?> Delivery Address</div>
    </div>
    <div class="card-body info">
        <div class="container-fluid">
            <form action="#" method="POST" id="customerAddrForm" onsubmit="return false">
                <div class="row">
                    <div id="field-row">
                        <?php if ($operation == 'Add') { ?>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Street</label>
                                    <input name="street" id="street" placeholder="Street" class="form-control" type="text" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <select name="state" id="state" class="form-control state" required>
                                        <option value="">::SELECT STATE::</option>
                                        <?php
                                        foreach ($states as $key => $value) {
                                            echo "<option value='" . $value["state_code"] . "'>" . $value["State"] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 lga">
                                <div class="form-group">
                                    <label class="control-label">LGA</label>
                                    <input name="operation" id="operation" value="<?php echo $operation; ?>" type="hidden">
                                    <input name="id" id="customer_id" value="<?php echo $customer_id; ?>" type="hidden">
                                    <select name="lga" id="lga" class="form-control lga" required>
                                        <option value="">::NO LGA TO SELECT::</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-8 col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Country</label>
                                    <input name="country" id="country" placeholder="Country" value="Nigeria" class="form-control" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Postal Code</label>
                                    <input name="postcode" id="postcode" placeholder="Postal code" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 pull-right">
                                <div class="form-group">
                                    <label class="toggle">
                                        <input name="primary_address" type="checkbox" />
                                        <span class="sliders"></span><br>
                                    </label><span> Default Address</span>
                                </div>
                            </div>
                        <?php }
                        if ($operation == 'Edit') { ?>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Street</label>
                                    <input name="street" id="street" placeholder="Street" value="<?php echo $address["street"]; ?>" class="form-control" type="text" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <select name="state" id="state" class="form-control state" required>
                                        <option value="">::SELECT STATE::</option>
                                        <?php
                                        foreach ($states as $key => $value) {
                                            if ($value["State"] == $address["state"]) {
                                                echo "<option value='" . $value["state_code"] . "' selected>" . $value["State"] . "</option>";
                                            } else {
                                                echo "<option value='" . $value["state_code"] . "'>" . $value["State"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 lga">
                                <div class="form-group">
                                    <label class="control-label">LGA</label>
                                    <input name="operation" id="operation" value="<?php echo $operation; ?>" type="hidden">
                                    <input name="address_id" id="address_id" value="<?php echo $address_id; ?>" type="hidden">
                                    <input name="id" id="customer_id" value="<?php echo $customer_id; ?>" type="hidden">
                                    <select name="lga" id="lga" class="form-control lga" required>
                                        <option value="">::NO LGA TO SELECT::</option>
                                        <?php
                                            $query = "SELECT Lga FROM lga WHERE State = '" . $address["state"] . "'";
                                            $result = mysqli_query($conn, $query);
                                            $lgas = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                            foreach ($lgas as $key => $value) {
                                                if ($value["Lga"] == $address["lga"]) {
                                                    echo "<option value='" . $value["Lga"] . "' selected>" . $address["lga"] . "</option>";
                                                } else {
                                                    echo "<option value='" . $value["Lga"] . "'>" . $value["Lga"] . "</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-8 col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Country</label>
                                    <input name="country" id="country" placeholder="Country" value="Nigeria" class="form-control" type="text" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Postal Code</label>
                                    <input name="postcode" id="postcode" placeholder="Postal code" value="<?php echo $address["post_code"]; ?>" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 pull-right">
                                <div class="form-group">
                                    <label class="toggle">
                                        <input name="primary_address" type="checkbox" <?php echo $address["primary_address"] != '0'? "checked": ''; ?> />
                                        <span class="sliders"></span><br>
                                    </label><span> Default Address</span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-sm-10 col-md-10"></div>
                    <div class="col-sm-12 col-md-12" style="margin-top: 1%;">
                        <div class="form-group">
                            <input name="submit" value="Save" id="save_addr" class="btn btn-lg btn-md btn-sm btn-store outline smooth-scroll" type="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var address_number = 1;
    $(document).ready(function() {

        $("#save_addr").click(function() {
            var forms = $('#customerAddrForm');
            var serializedForm = forms.serialize();
            console.log(serializedForm);

            forms.parsley().validate();
            if (forms.parsley().isValid()) {
                $.post("utilities.php", {
                        "op": "customerAddr",
                        "data": serializedForm
                    },
                    function(data) {
                        if (data.response_code == 1) {
                            $("#link_content").empty();
                            $("#link_content").load("edit_address.php", function(response, status, xhr) {
                                if (status == "error") {
                                    // alert(msg + xhr.status + " " + xhr.statusText);
                                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                                }
                            })
                            //location.href = "user_dashboard.php?type=updateCustomerAddr";
                            // location.href = 'user_dashboard.php';
                        } else {
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
            $("#link_content").load("edit_address.php", function(response, status, xhr) {
                if (status == "error") {
                    // alert(msg + xhr.status + " " + xhr.statusText);
                    console.log(xhr.status + " " + xhr.statusText + " " + response);
                }
            });

        });

        $(".state").change(function() {
            let value = $(this).val();
            // console.log($(this).parent().parent().next());
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