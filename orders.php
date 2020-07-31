<?php
session_start();
include("dbcxn.inc.php");

if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
} else {
    $customer_email = $_SESSION["email"];
    $customer_id = $_SESSION["customer_id"];
    $query = "SELECT * FROM orderdetails WHERE customerid = $customer_id";
    $resource = mysqli_query($conn, $query);
    $row = mysqli_fetch_all($resource);
    $count = count($row);
    // print_r($row);
    if (isset($_GET["page"])) {
        $page_number = $_GET["page"];
        // var_dump($page_number);
    } else {
        $page_number = 1;
    }
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

    .move {
        display: flex;
    }

    .mv-right {
        margin-left: 80%;
    }

    .pImage {
        cursor: pointer;
    }
</style>

<div class="card side-card col-sm-8 col-md-8" style="padding: 25px; height: 70vh; margin-bottom: 11%" id="orderedSection">
    <div class="mic">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg>
        <div class="card-header" style="font-size: 3rem; margin-bottom:4%">Ordered Products(<?php echo $count; ?>)</div>
    </div>
    <div class="card-body info">
        <div class="container-fluid" style="height: 100%;">
            <form action="#" method="POST">
                <div class="row" id="p_info">

                </div>
            </form>
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class=" d1" style="padding-bottom: 2rem; display:none;" id="prevDiv">
                        <a class="btn btn-sm btn-store outline cta-button smooth-scroll" id="prev">
                            << Prev </a> </div> </div> <div class="col-md-6 col-sm-6 ">
                                <div class=" d1" id="nextDiv" style=" margin-left: 60%;">
                                    <a class="btn btn-sm btn-store outline cta-button smooth-scroll" id="next">
                                        Next >>
                                    </a>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var total_product_count = "<?php echo $count; ?>";
        if (total_product_count == 0) {
            $("#nextDiv").css("cssText", "display: none !important;");
        }
        // console.log(total_product_count);
        var page_number = "<?php echo $page_number; ?>";

        // console.log(page_number);
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
        $(document).ready(function() {

            $("#back").click(function() {
                $("#link_content").empty();
                $("#link_content").load("account_overview.php", function(response, status, xhr) {
                    if (status == "error") {
                        console.log(xhr.status + " " + xhr.statusText + " " + response);
                    }
                });
            });

            function appendLeadingZeroes(n) {
                if (n <= 9) {
                    return "0" + n;
                }
                return n
            }

            var email = "<?php echo $customer_id; ?>";
            $.ajax({
                type: "GET",
                url: "utilities.php?email=" + email + "&page=" + page_number,
                processData: false,
                contentType: "application/json",
                data: "op=orders",
                success: function(r) {
                    var r = jQuery.parseJSON(r); //JSON.parse(r);
                    if (r.response_code == 1) {
                        var orders = r.data;
                        orders.forEach((product, index) => {
                            let status = '';
                            if (product.order_status == '0') {
                                status = "<strong style = 'font-size: 1.3rem; color: #B7950B;' > PENDING APPROVAL </strong>";
                            } else if (product.order_status == '1') {
                                if (product.shipping_status == '0') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> NOT SHIPPED </strong>";
                                } else if (product.shipping_status == '1') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #A9DFBF;'> SHIPPED </strong>";
                                } else if (product.shipping_status == '2') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN TRANSIT </strong>";
                                } else if (product.shipping_status == '3') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN WAREHOUSE </strong>";
                                } else if (product.shipping_status == '4') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #27AE60;'> DELIVERED</strong>";
                                }
                            } else if (product.order_status = '2') {
                                status = "<strong style = 'font-size: 1.3rem; color:tomato;'> REJECTED </strong>";
                            } else if (product.order_status = '3') {
                                status = "<strong style = 'font-size: 1.3rem; color:tomato;'> CANCELLED </strong>";
                            } else if (product.order_status = '4') {
                                status = "<strong style = 'font-size: 1.3rem; color: #5DADE2;'> CLOSED </strong>";
                            }
                            let date = new Date(product.createdon);
                            order_date = date.getDate() + "/" + (appendLeadingZeroes(date.getMonth() + 1)) + "/" + date.getFullYear();
                            var strr = "<div class='col-sm-12 col-md-12' style='margin-bottom: 3%;'>" +
                                "<div class='row'>" +
                                "<div class='col-md-3 col-sm-3' style='height: 8%'>" +
                                "<img class='pImage item-load-second'  src='" + product.product_image + "' data-id='" + product.product_id + "' alt='' width='120' height='100'/>" +
                                "</div>" +
                                "<div class='col-md-6 col-sm-6' id='pDesc' style='height: 8%'>" +
                                "<strong>" + product.product_name + "</strong><br>" +
                                "<small>Quantity: " + product.quantity + "</small><br>" +
                                "<small>Order ID: " + product.orderid + "</small><br>" +
                                "<small>Price: &#8358; " + formatNumber(product.discount_price) + "</small><br>" +
                                "<small>ORDERED ON: " + order_date + "</small>" +
                                "</div>" +
                                "<div class='col-md-2 col-sm-2' style='text-align: center;'" +
                                status +
                                "</div>" +
                                "</div>" +
                                "</div>";
                            $("#p_info").append(strr);

                        });
                        if (page_number > 1) {
                            $("#prevDiv").css("cssText", "display: block !important;");
                        }
                        if (page_number * 4 >= total_product_count) {
                            $("#nextDiv").css("cssText", "display: none !important;");
                        }
                    } else if (r.response_code == 20) {
                        $("#p_info").empty();
                        $("#p_info").append("<p class='text-center' style='font-weight: bold'>You have no orders yet.</p>")
                    }
                },
                error: function(r) {
                    console.log("Something went wrong!");
                }
            });

            $("#next").click(function() {
                page_number++;
                $.ajax({
                    type: "GET",
                    url: "utilities.php?email=" + email + "&page=" + page_number,
                    processData: false,
                    contentType: "application/json",
                    data: "op=next",
                    success: function(r) {
                        var r = jQuery.parseJSON(r); //JSON.parse(r);
                        var orders = r.data;
                        // console.log(orders);
                        if (r.response_code == '1') {
                            $("#p_info").empty();
                            orders.forEach((product, index) => {
                                let status = '';
                                if (product.order_status == '0') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #B7950B;' > PENDING APPROVAL </strong>";
                                } else if (product.order_status == '1') {
                                    if (product.shipping_status == '0') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> NOT SHIPPED </strong>";
                                    } else if (product.shipping_status == '1') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #A9DFBF;'> SHIPPED </strong>";
                                    } else if (product.shipping_status == '2') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN TRANSIT </strong>";
                                    } else if (product.shipping_status == '3') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN WAREHOUSE </strong>";
                                    } else if (product.shipping_status == '4') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #27AE60;'> DELIVERED</strong>";
                                    }
                                } else if (product.order_status = '2') {
                                    status = "<strong style = 'font-size: 1.3rem; color:tomato;'> REJECTED </strong>";
                                } else if (product.order_status = '3') {
                                    status = "<strong style = 'font-size: 1.3rem; color:tomato;'> CANCELLED </strong>";
                                } else if (product.order_status = '4') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #5DADE2;'> CLOSED </strong>";
                                }
                                let date = new Date(product.createdon);
                                order_date = date.getDate() + "/" + (appendLeadingZeroes(date.getMonth() + 1)) + "/" + date.getFullYear();
                                var strr = "<div class='col-sm-12 col-md-12' style='margin-bottom: 3%;'>" +
                                    "<div class='row'>" +
                                    "<div class='col-md-3 col-sm-3' style='height: 8%'>" +
                                    "<img class='pImage item-load-second' src='" + product.product_image + "' data-id='" + product.product_id + "' alt=''  width='120' height='100'/>" +
                                    "</div>" +
                                    "<div class='col-md-6 col-sm-6' id='pDesc' style='height: 8%'>" +
                                    "<strong>" + product.product_name + "</strong><br>" +
                                    "<small>Quantity: " + product.quantity + "</small><br>" +
                                    "<small>Order ID: " + product.orderid + "</small><br>" +
                                    "<small>Price: &#8358; " + formatNumber(product.discount_price) + "</small><br>" +
                                    "<small>ORDERED ON: " + order_date + "</small>" +
                                    "</div>" +
                                    "<div class='col-md-2 col-sm-2' style='text-align: center;'" +
                                    status +
                                    "</div>" +
                                    "</div>" +
                                    "</div>";
                                $("#p_info").append(strr);
                            });
                        }
                        // console.log(page_number);
                        let prev = document.getElementById("prevDiv");
                        prev.setAttribute("style", "display:block !important;");
                        if (r.start + 4 >= total_product_count) {
                            let next = document.getElementById("nextDiv");
                            next.setAttribute("style", "display:none !important");
                        }
                    },
                    error: function(r) {
                        console.log("Something went wrong!");
                    }
                });
            })

            $("#prev").click(function() {
                page_number--;
                $.ajax({
                    type: "GET",
                    url: "utilities.php?email=" + email + "&page=" + page_number,
                    processData: false,
                    contentType: "application/json",
                    data: "op=prev",
                    success: function(r) {
                        var r = jQuery.parseJSON(r); //JSON.parse(r);
                        var orders = r.data;
                        if (r.response_code == '1') {
                            $("#p_info").empty();
                            orders.forEach((product, index) => {
                                let status = '';
                                if (product.order_status == '0') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #B7950B;' > PENDING APPROVAL </strong>";
                                } else if (product.order_status == '1') {
                                    if (product.shipping_status == '0') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> NOT SHIPPED </strong>";
                                    } else if (product.shipping_status == '1') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #A9DFBF;'> SHIPPED </strong>";
                                    } else if (product.shipping_status == '2') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN TRANSIT </strong>";
                                    } else if (product.shipping_status == '3') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #B7950B;'> IN WAREHOUSE </strong>";
                                    } else if (product.shipping_status == '4') {
                                        status = "<strong style = 'font-size: 1.3rem; color: #27AE60;'> DELIVERED</strong>";
                                    }
                                } else if (product.order_status = '2') {
                                    status = "<strong style = 'font-size: 1.3rem; color:tomato;'> REJECTED </strong>";
                                } else if (product.order_status = '3') {
                                    status = "<strong style = 'font-size: 1.3rem; color:tomato;'> CANCELLED </strong>";
                                } else if (product.order_status = '4') {
                                    status = "<strong style = 'font-size: 1.3rem; color: #5DADE2;'> CLOSED </strong>";
                                }
                                let date = new Date(product.createdon);
                                order_date = date.getDate() + "/" + (appendLeadingZeroes(date.getMonth() + 1)) + "/" + date.getFullYear();
                                var strr = "<div class='col-sm-12 col-md-12' style='margin-bottom: 3%;'>" +
                                    "<div class='row'>" +
                                    "<div class='col-md-3 col-sm-3' style='height: 8%'>" +
                                    "<img class='pImage item-load-second' src='" + product.product_image + "' data-id='" + product.product_id + "'  alt='' width='120' height='100'/>" +
                                    "</div>" +
                                    "<div class='col-md-6 col-sm-6' id='pDesc' style='height: 8%'>" +
                                    "<strong>" + product.product_name + "</strong><br>" +
                                    "<small>Quantity: " + product.quantity + "</small><br>" +
                                    "<small>Order ID: " + product.orderid + "</small><br>" +
                                    "<small>Price: &#8358; " + formatNumber(product.discount_price) + "</small><br>" +
                                    "<small>ORDERED ON: " + order_date + "</small>" +
                                    "</div>" +
                                    "<div class='col-md-2 col-sm-2' style='text-align: center;'" +
                                    status +
                                    "</div>" +
                                    "</div>" +
                                    "</div>";
                                $("#p_info").append(strr);
                            });
                        }
                        // console.log(page_number);
                        let next = document.getElementById("nextDiv");
                        next.setAttribute("style", "display:block !important; margin-left: 60%;");
                        if (r.start <= 0) {
                            let prev = document.getElementById("prevDiv");
                            prev.setAttribute("style", "display:none !important");
                        }
                    },
                    error: function(r) {
                        console.log("Something went wrong!");
                    }
                });
            });





        })

        $(document).on('click', '.item-load-second', function() {
            id = $(this).attr("data-id");
            $("#link_content").empty();
            $("#link_content").load("product_details.php?page=" + page_number + "&id=" + id);
        })
    </script>