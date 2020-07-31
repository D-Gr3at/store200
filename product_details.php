<?php
session_start();
include("dbcxn.inc.php");

if (!isset($_SESSION["customer_id"])) {
    header("Location: index.php");
} else {
    $customer_email = $_SESSION["email"];
    $query = "SELECT * FROM orderdetails WHERE customerid = '$customer_email'";
    $resource = mysqli_query($conn, $query);
    $row = mysqli_fetch_all($resource);
    $count = count($row);

    $product_id = $_GET["id"];

    $page_number = $_GET["page"];
    // var_dump($page_number);

    $query = "SELECT * FROM orderdetails WHERE product_id = '$product_id'";
    $result = mysqli_query($conn, $query);
    $order_product = mysqli_fetch_assoc($result);
    // var_dump($order_product["order_status"]);

    if ($order_product["order_status"] == '0') {
        $status = "<strong style = 'font-size: 1.5rem; color: #B7950B;' > PENDING APPROVAL </strong>";
    } elseif ($order_product["order_status"] == '1') {
        if ($order_product["shipping_status"] == '0') {
            $status = "<strong style = 'font-size: 1.5rem; color: #B7950B;'> NOT SHIPPED </strong>";
        } elseif ($order_product["shipping_status"] == '1') {
            $status = "<strong style = 'font-size: 1.5rem; color: #A9DFBF;'> SHIPPED </strong>";
        } elseif ($order_product["shipping_status"] == '2') {
            $status = "<strong style = 'font-size: 1.5rem; color: #B7950B;'> IN TRANSIT </strong>";
        } elseif ($order_product["shipping_status"] == '3') {
            $status = "<strong style = 'font-size: 1.5rem; color: #B7950B;'> IN WAREHOUSE </strong>";
        } elseif ($order_product["shipping_status"] == '4') {
            $status = "<strong style = 'font-size: 1.5rem; color: #27AE60;'> DELIVERED</strong>";
        }
    } elseif ($order_product["order_status"] = '2') {
        $status = "<strong style = 'font-size: 1.5rem; color:tomato;'> REJECTED </strong>";
    } elseif ($order_product["order_status"] = '3') {
        $status = "<strong style = 'font-size: 1.5rem; color:tomato;'> CANCELLED </strong>";
    } elseif ($order_product["order_status"] = '4') {
        $status = "<strong style = 'font-size: 1.5rem; color: #5DADE2;'> CLOSED </strong>";
    }

    $sql = "SELECT * FROM products WHERE id = '$product_id'";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
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

    .pImage {
        cursor: pointer;
    }
</style>

<div class="card side-card col-sm-8 col-md-8" style="padding: 25px; height: 100%;" id="orderedSection">
    <div class="mic">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 172 172" style=" fill:#000000;" id="back">
            <g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <path d="M0,172v-172h172v172z" fill="none"></path>
                <g fill="#000000">
                    <path d="M116.45833,9.63021l-64.5,71.66667l-4.25521,4.70313l4.25521,4.70313l64.5,71.66667l10.75,-9.40625l-60.24479,-66.96354l60.24479,-66.96354z"></path>
                </g>
            </g>
        </svg>
        <div class="card-header" style="font-size: 3rem; margin-bottom:0px">Product Details</div>
    </div>
    <div class="card-body info">
        <div class="container-fluid" style="height: 100%;">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="product-image">
                        <img src="<?php echo $order_product["product_image"]; ?>" alt="" width="300" height="200"/>
                    </div><br>
                    <div class="row">
                        <div class="col-md-12 col-sm-12" style="display: inline-block;">
                            <span style="font-weight:bold;font-size:120%;"><?php echo $product["name"] ?></span>
                            <span class="text-right" style="font-size:120%; float:right;" id="product_p">Price: &#8358 </span>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12 col-sm-12">
                            <span>ORDER STATUS: <?php echo $status; ?></span>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12 col-sm-12">
                            <span>ORDERED DATE: <strong><?php echo date_format(date_create($order_product["createdon"]), "d/m/Y"); ?></strong></span>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12 col-sm-12">
                            <span>ORDER ID: <strong><?php echo $order_product["orderid"];?></strong></span>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12 col-sm-12">
                            <span>QUANTITY: <strong><?php echo $order_product["quantity"]; ?></strong></span>
                        </div>
                    </div>     
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="product-desc">
                        <p><?php echo $product["description"]; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var price = "<?php echo $product["price"] ?>";
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
        $("#product_p").append(formatNumber(price));
        var page_number = "<?php echo $page_number; ?>";
        $("#back").click(function() {
            $("#link_content").empty();
            $("#link_content").load("orders.php?page="+page_number);
        });
    </script>