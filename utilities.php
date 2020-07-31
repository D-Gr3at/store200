<?php

error_reporting(1);
require 'admin/libs/desencrypt.php';
require 'dbcxn.inc.php';
require 'php_mailer.php';

$op = isset($_POST["op"]) ? $_POST["op"] : '';

if ($op == "contact") {
    $name = filter_var($_POST["name"]);
    $email = filter_var($_POST["email"]);
    $message = filter_var($_POST["message"]);
    $date = date("Y-m-d h:i:s");

    $query = "INSERT INTO contact (name, email, message, created) VALUE ('$name', '$email', '$message', '$date')";
    $response = mysqli_query($conn, $query);
    if ($response) {
        echo json_encode(array("response_code" => 1, "response_message" => "Message sent successfully"));
    } else {
        echo json_encode(array("response_code" => 70, "response_message" => "Failed to send message"));
    }
}

if ($op == "deleteAddr") {
    $id = $_POST["id"];
    $date = date("Y-m-d h:i:s");

    $query = "DELETE FROM customer_addresses WHERE id = $id";
    // echo $query."\n";
    $response = mysqli_query($conn, $query);
    if ($response) {
        echo json_encode(array("response_code" => 1, "response_message" => "Deleted successfully"));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Failed to delete"));
    }
}

if ($op == "log_trans") {
    $date = date("Y-m-d h:i:s");
    $transaction_id = date("Ymdhis");
    $customer_id = $_POST["customer_id"];
    $q = "SELECT email FROM customer WHERE customer_id = $customer_id";
    $customer = mysqli_fetch_assoc(mysqli_query($conn, $q));
    $email = $customer["email"];
    $address_id = $_POST["address_id"];
    $addr_op = $_POST["addr_op"];
    $merchant_reg_id = $_POST["merchant_reg_id"];
    $description = filter_var($_POST["desc"]);
    $merchant_id = $_POST["merchant_id"];
    $order = json_decode($_POST["order"], true);

    $order_id = date("mdhis");
    if($addr_op == "pick_up"){
        foreach ($order as $key => $value) {
            $total_amount = floatval($value['price']) * floatval($value['quantity']);
            $query = "INSERT INTO orderdetails(orderid, product_id, customerid, quantity, original_price, discount_price, total, product_name, product_image, merchant_id, createdon, pickup_location_id) 
                        VALUE('$order_id','" . $value['id'] . "', '" . $customer_id . "', " . $value['quantity'] . "," . $value['price'] . ", " . $value['price'] . "," . $total_amount . ", '" . $value['label'] . "','" . $value['image'] . "', '" . $merchant_id . "', '" . $date . "', $address_id)";
            // echo $query."\n";
            $order_response = mysqli_query($conn, $query);
        }
    }else{
        $q = "UPDATE customer_addresses SET primary_address = 0, updated = '$date' WHERE customer_id = $customer_id";
        mysqli_query($conn, $q);
        $sql = "UPDATE customer_addresses SET primary_address = 1, updated = '$date' WHERE id = $address_id";
        mysqli_query($conn, $sql);

        foreach ($order as $key => $value) {
            $total_amount = floatval($value['price']) * floatval($value['quantity']);
            $query = "INSERT INTO orderdetails(orderid, product_id, customerid, quantity, original_price, discount_price, total, product_name, product_image, merchant_id, createdon, address_id) 
                        VALUE('$order_id','" . $value['id'] . "', '" . $customer_id . "', " . $value['quantity'] . "," . $value['price'] . ", " . $value['price'] . "," . $total_amount . ", '" . $value['label'] . "','" . $value['image'] . "', '" . $merchant_id . "', '" . $date . "', $address_id)";
            // echo $query."\n";
            $order_response = mysqli_query($conn, $query);
        }
    }

    $total_amount = floatval($_POST["amt"]);
    $query = "INSERT INTO transaction_table(transaction_id, transaction_amount, order_id, source_acct, destination_acct, transaction_desc, customer_id, merchant_id, created) 
                    VALUE('$transaction_id', $total_amount, '$order_id', '$email', '$merchant_id', '$description', '$customer_id', '$merchant_id', '$date')";
    // echo $query."\n";
    $transaction_response = mysqli_query($conn, $query);

    if ($transaction_response && $order_response) {
        echo json_encode(array("response_code" => 1, "response_message" => "successful", "transaction_id" => $transaction_id));
    } else {
        echo json_encode(array("response_code" => 70, "response_message" => "Failed", "transaction_id" => $transaction_id));
    }
}

if ($op == "search_countries") {
    if (isset($_POST["query"])) {
        $request = filter_var($_POST["query"], FILTER_SANITIZE_STRING);
        $query = "SELECT * FROM tbl_countries WHERE (country_name LIKE '%" . $request . "%')";
        // echo $query."\n";
        $result = mysqli_query($conn, $query);
        // var_dump($result);
        // $result = mysqli_result($conn, $result, $conn);
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $data["name"] = $row["country_name"];
                $data["code"] = $row["phonecode"];
                $data["c_code"] = $row["country_code"];
                $data["id"] = $row["id"];
            }
        }
        if (isset($_POST['typehead_search'])) {
        } else {
            $data = array_unique($data);
            echo json_encode($data);
        }
    }
}

if ($_GET["op"] == "orders") {
    $start = $_GET["start"];
    $email = $_GET["email"];
    $page = $_GET["page"];
    $page_number = (floatval($page) - 1) * 4;
    $data = array();
    if ($page != NULL) {
        $sql = "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC LIMIT $page_number, 4";
        // echo $sql."\n";
        $result = mysqli_query($conn, $sql);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $result = mysqli_query($conn, "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC");
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if ($products != NULL) {
        foreach ($products as $key => $value) {
            array_push($data, $value);
        }
        echo json_encode(array("response_code" => 1, "response_message" => "success", "data" => $data));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "failure"));
    }
}

if ($_GET["op"] == "next") {
    // var_dump($_GET);
    // $start = $_GET["start"];
    $email = $_GET["email"];
    $page = $_GET["page"];
    $page_number = (floatval($page) - 1) * 4;

    $data = array();

    if ($page_number != NULL) {
        $sql = "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC LIMIT $page_number, 4";
        // echo $sql."\n";
        $result = mysqli_query($conn, $sql);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $sql = "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC LIMIT $start, 4";
        // echo $sql."\n";
        $result = mysqli_query($conn, $sql);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // var_dump($products);
    if ($products != NULL) {
        foreach ($products as $key => $value) {
            array_push($data, $value);
        }
        echo json_encode(array("response_code" => 1, "response_message" => "success", "data" => $data, "start" => $page_number));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "failure"));
    }
}

if ($_GET["op"] == "getLGAs") {
    $state_code = $_GET["state_code"];

    $sql = "SELECT Lga FROM lga WHERE state_code = '$state_code'";
    // echo $sql."\n";
    $result = mysqli_query($conn, $sql);
    $lgas = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($lgas != NULL) {
        echo json_encode(array("response_code" => 1, "response_message" => "success", "data" => $lgas));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "NO LGAs FOUND"));
    }
}

if ($_GET["op"] == "prev") {
    // $start = floatval($_GET["start"]);
    $email = $_GET["email"];
    $page = $_GET["page"];
    $page_number = (floatval($page) - 1) * 4;
    // var_dump($page_number);

    $data = array();

    if ($page_number >= 0) {
        $sql = "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC LIMIT $page_number, 4";
        // echo $sql."\n";
        $result = mysqli_query($conn, $sql);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        if ($start >= 0) {
            $sql = "SELECT * FROM orderdetails WHERE customerid = '$email' ORDER BY id DESC LIMIT $start, 4";
            // echo $sql."\n";
            $result = mysqli_query($conn, $sql);
            $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }

    if ($products != NULL) {
        foreach ($products as $key => $value) {
            array_push($data, $value);
        }
        echo json_encode(array("response_code" => 1, "response_message" => "success", "data" => $data, "start" => $page_number));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "failure"));
    }
}

if ($op == "saveCustomer") {
    // var_dump($_POST);
    $first_name = filter_var($_POST["firstname"]);
    $last_name = filter_var($_POST["lastname"]);
    $email = filter_var($_POST["email"]);
    $phone = filter_var($_POST["phone"]);
    $password = filter_var($_POST["password"]);
    $confirmPassword = filter_var($_POST["confirmPassword"]);
    if ($password != $confirmPassword) {
        echo json_encode(array("response_code" => 20, "response_message" => "Password does not match."));
        return;
    }

    $q = "SELECT * FROM customer WHERE email = '$email' LIMIT 1";
    $resource = mysqli_query($conn, $q);
    if (mysqli_fetch_assoc($resource) != NULL) {
        echo json_encode(array("response_code" => 20, "response_message" => "Email already exist."));
        return;
    }
    $encrypt = new DESEncryption();
    $key = $email;
    $cipher_password     = $encrypt->des($key, $password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);
    $date = date('Y-m-d h:i:s');
    $verification_link = md5(uniqid(rand(), true));
    $street = filter_var($_POST["street"]);
    $state = filter_var($_POST["state"]);
    $query = "SELECT State FROM lga WHERE state_code = '$state' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $state = mysqli_fetch_assoc($result);
    $lga = filter_var($_POST["lga"]);
    $country = filter_var($_POST["country"]);
    $post_code = filter_var($_POST["postalCode"]);
    $query = "INSERT INTO customer(first_name, last_name, email, password, phone, created, verification_link, verified, verify_date, expired) 
                    VALUE('$first_name', '$last_name', '$email', '$str_cipher_password', '$phone', '$date', '$verification_link', 0, '$date', 0)";
    // echo $query."\n";
    $customer_response = mysqli_query($conn, $query);

    $sql = "SELECT customer_id FROM customer ORDER BY customer_id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $customer = mysqli_fetch_assoc($result);

    $query = "INSERT INTO customer_addresses(customer_id, street, lga, state, country, created, post_code, primary_address)
                    VALUE(" . $customer["customer_id"] . ", '$street', '$lga', '".$state["State"]."', '$country', '$date', '$post_code', 1)";
    $addr_response = mysqli_query($conn, $query);

    // $customer
    if ($customer_response && $addr_response) {
        try {

            /* Set the mail sender. */
            $mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Bille');

            $mail->addCustomHeader("MIME-Version: 1.0");
            $mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
            $mail->addCustomHeader("Content-type:text/css");

            /* Add a recipient. */
            $mail->addAddress($email);

            /* Set the subject. */
            $mail->Subject = 'ACCOUNT VERIFICATION';

            $baseURL = "http://localhost/microstore/";

            $messageBody =
                "
                        <!DOCTYPE html>
                        <html>
                            <head>
                                <title></title>
                                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                                <meta name='viewport' content='width=device-width, initial-scale=1'>
                                <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                                <style type='text/css'>
                                    @media screen {
                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: normal;
                                            font-weight: 400;
                                            src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: normal;
                                            font-weight: 700;
                                            src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: italic;
                                            font-weight: 400;
                                            src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: italic;
                                            font-weight: 700;
                                            src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
                                        }
                                    }

                                    /* CLIENT-SPECIFIC STYLES */
                                    body,
                                    table,
                                    td,
                                    a {
                                        -webkit-text-size-adjust: 100%;
                                        -ms-text-size-adjust: 100%;
                                    }

                                    table,
                                    td {
                                        mso-table-lspace: 0pt;
                                        mso-table-rspace: 0pt;
                                    }

                                    img {
                                        -ms-interpolation-mode: bicubic;
                                    }

                                    /* RESET STYLES */
                                    img {
                                        border: 0;
                                        height: auto;
                                        line-height: 100%;
                                        outline: none;
                                        text-decoration: none;
                                    }

                                    table {
                                        border-collapse: collapse !important;
                                    }

                                    body {
                                        height: 100% !important;
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        width: 100% !important;
                                    }

                                    /* iOS BLUE LINKS */
                                    a[x-apple-data-detectors] {
                                        color: inherit !important;
                                        text-decoration: none !important;
                                        font-size: inherit !important;
                                        font-family: inherit !important;
                                        font-weight: inherit !important;
                                        line-height: inherit !important;
                                    }

                                    /* MOBILE STYLES */
                                    @media screen and (max-width:600px) {
                                        h1 {
                                            font-size: 32px !important;
                                            line-height: 32px !important;
                                        }
                                    }

                                    /* ANDROID CENTER FIX */
                                    div[style*='margin: 16px 0;'] {
                                        margin: 0 !important;
                                    }
                                </style>
                            </head>

                            <body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'>
                                <div
                                    style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'>
                                    We're thrilled to have you here! Get ready to dive into your new account. </div>
                                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                    <!-- LOGO -->
                                    <tr>
                                        <td bgcolor='#FFA73B' align='center'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td align='center' valign='top' style='padding: 40px 10px 40px 10px;'> </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor='#FFA73B' align='center' style='padding: 0px 10px 0px 10px;'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='center' valign='top'
                                                        style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'>
                                                        <h1 style='font-size: 48px; font-weight: 400; margin: 2;'>Welcome!</h1> 
                                                        <img src='https://www.vuvaa_shop.com/shop/store-200/logo/VUV-071403041520.png' width='125' height='120' style='display: block; border: 0px;' />
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', 'Helvetica, Arial, sans-serif'; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>We're excited to have you get started. First, you need to confirm your
                                                            account. Just press the button below. <br><em style='color:red'>Verification Link expires after 3 hours</em></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'>
                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                            <tr>
                                                                <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'>
                                                                    <table border='0' cellspacing='0' cellpadding='0'>
                                                                        <tr>
                                                                            <td align='center' style='border-radius: 3px;' bgcolor='#FFA73B'>
                                                                                <a href='" . $baseURL . "login.php?activation_code=" . $verification_link . "' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;'>
                                                                                    Confirm Account
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 0px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>If that doesn't work, copy and paste the following link in your
                                                            browser:</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 20px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'><a href='#' target='_blank'
                                                                style='color: #FFA73B;'>" . $baseURL . "login.php?activation_code=" . $verification_link . "</a></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>If you have any questions, just reply to this email—we're always happy
                                                            to help out.</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>Cheers,<br>Store 200</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </body>
                        </html>                   
                        ";

            /* Set the mail message body. */
            $mail->Body = $messageBody; //strip_tags($emessage);

            /* Finally send the mail. */
            if (!$mail->send()) {
                /* PHPMailer error. */
                echo $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            /* PHPMailer exception. */
            echo $e->errorMessage();
        } catch (\Exception $e) {
            /* PHP exception (note the backslash to select the global namespace Exception class). */
            echo $e->getMessage();
        }
        echo json_encode(array("response_code" => 1, "response_message" => "Registered successfully.", "email" => $email));
        // sleep(10800);
    } else {
        echo json_encode(array("response_code" => 13, "response_message" => "Registration failed", "email" => $email));
    }
}

if ($op == "resendVerification") {
    // var_dump($_POST);
    $date = date("Y-m-d h:i:s");
    $email = $_POST["email"];
    $query = "SELECT * FROM customer where email = '$email'";
    $resource = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($resource);
    if ($row["verified"] == '0') {
        $verification_link = md5(uniqid(rand(), true));
        $query = "UPDATE customer SET verification_link = '$verification_link', updated = '$date', verify_date = '$date', expired = 1 WHERE customer_id = " . $row["customer_id"];
        mysqli_query($conn, $query);
        try {

            /* Set the mail sender. */
            $mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Tonte Bille');

            $mail->addCustomHeader("MIME-Version: 1.0");
            $mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
            $mail->addCustomHeader("Content-type:text/css");

            /* Add a recipient. */
            $mail->addAddress($email);

            /* Set the subject. */
            $mail->Subject = 'ACCOUNT VERIFICATION';

            $baseURL = "http://localhost/microstore/";

            $messageBody =
                "
                        <!DOCTYPE html>
                        <html>
                            <head>
                                <title></title>
                                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                                <meta name='viewport' content='width=device-width, initial-scale=1'>
                                <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                                <style type='text/css'>
                                    @media screen {
                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: normal;
                                            font-weight: 400;
                                            src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: normal;
                                            font-weight: 700;
                                            src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: italic;
                                            font-weight: 400;
                                            src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
                                        }

                                        @font-face {
                                            font-family: 'Lato';
                                            font-style: italic;
                                            font-weight: 700;
                                            src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
                                        }
                                    }

                                    /* CLIENT-SPECIFIC STYLES */
                                    body,
                                    table,
                                    td,
                                    a {
                                        -webkit-text-size-adjust: 100%;
                                        -ms-text-size-adjust: 100%;
                                    }

                                    table,
                                    td {
                                        mso-table-lspace: 0pt;
                                        mso-table-rspace: 0pt;
                                    }

                                    img {
                                        -ms-interpolation-mode: bicubic;
                                    }

                                    /* RESET STYLES */
                                    img {
                                        border: 0;
                                        height: auto;
                                        line-height: 100%;
                                        outline: none;
                                        text-decoration: none;
                                    }

                                    table {
                                        border-collapse: collapse !important;
                                    }

                                    body {
                                        height: 100% !important;
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        width: 100% !important;
                                    }

                                    /* iOS BLUE LINKS */
                                    a[x-apple-data-detectors] {
                                        color: inherit !important;
                                        text-decoration: none !important;
                                        font-size: inherit !important;
                                        font-family: inherit !important;
                                        font-weight: inherit !important;
                                        line-height: inherit !important;
                                    }

                                    /* MOBILE STYLES */
                                    @media screen and (max-width:600px) {
                                        h1 {
                                            font-size: 32px !important;
                                            line-height: 32px !important;
                                        }
                                    }

                                    /* ANDROID CENTER FIX */
                                    div[style*='margin: 16px 0;'] {
                                        margin: 0 !important;
                                    }
                                </style>
                            </head>

                            <body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'>
                                <div
                                    style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'>
                                    We're thrilled to have you here! Get ready to dive into your new account. </div>
                                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                    <!-- LOGO -->
                                    <tr>
                                        <td bgcolor='#FFA73B' align='center'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td align='center' valign='top' style='padding: 40px 10px 40px 10px;'> </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor='#FFA73B' align='center' style='padding: 0px 10px 0px 10px;'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='center' valign='top'
                                                        style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'>
                                                        <h1 style='font-size: 48px; font-weight: 400; margin: 2;'>Welcome!</h1> 
                                                        <img src='https://www.vuvaa_shop.com/shop/store-200/logo/VUV-071403041520.png' width='125' height='120' style='display: block; border: 0px;' />
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', 'Helvetica, Arial, sans-serif'; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>We're excited to have you get started. First, you need to confirm your
                                                            account. Just press the button below. <br><em style='color:red'>Verification Link expires after 3 hours</em></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'>
                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                            <tr>
                                                                <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'>
                                                                    <table border='0' cellspacing='0' cellpadding='0'>
                                                                        <tr>
                                                                            <td align='center' style='border-radius: 3px;' bgcolor='#FFA73B'>
                                                                                <a href='" . $baseURL . "login.php?activation_code=" . $verification_link . "' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;'>
                                                                                    Confirm Account
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 0px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>If that doesn't work, copy and paste the following link in your
                                                            browser:</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 20px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'><a href='#' target='_blank'
                                                                style='color: #FFA73B;'>" . $baseURL . "login.php?activation_code=" . $verification_link . "</a></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>If you have any questions, just reply to this email—we're always happy
                                                            to help out.</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td bgcolor='#ffffff' align='left'
                                                        style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;'>
                                                        <p style='margin: 0;'>Cheers,<br>Store 200</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </body>
                        </html>                   
                        ";

            /* Set the mail message body. */
            $mail->Body = $messageBody; //strip_tags($emessage);

            /* Finally send the mail. */
            if (!$mail->send()) {
                /* PHPMailer error. */
                echo $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            /* PHPMailer exception. */
            echo $e->errorMessage();
        } catch (\Exception $e) {
            /* PHP exception (note the backslash to select the global namespace Exception class). */
            echo $e->getMessage();
        }
        echo json_encode(array("response_code" => 1, "response_message" => "Email sent."));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Account has already been verified."));
    }
}

if ($op == "login") {
    // var_dump($_POST);
    $email = filter_var($_POST["email"]);
    $password = filter_var($_POST["password"]);

    $encrypt = new DESEncryption();
    $key = $email;
    $cipher_password     = $encrypt->des($key, $password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);

    $query = "SELECT * FROM customer WHERE email = '$email' LIMIT 1";
    $resource = mysqli_query($conn, $query);
    $result = mysqli_fetch_assoc($resource);
    $count = count($result);
    if ($result["verified"] == '0') {
        echo json_encode(array("response_code" => 13, "response_message" => "Account has not been verified.", "email" => $email));
        return;
    }
    if ($count > 0) {
        // var_dump($result["password"]);
        // var_dump($str_cipher_password);
        if ($result["password"] == $str_cipher_password) {
            session_start();
            $_SESSION["email"] = $result["email"];
            $_SESSION["first_name"] = $result["first_name"];
            $_SESSION["last_name"] = $result["last_name"];
            $_SESSION["customer_id"] = $result["customer_id"];
            $_SESSION["phone"] = $result["phone"];
            echo json_encode(array("response_code" => 1));
        } else {
            echo json_encode(array("response_code" => 20, "response_message" => "Incorrect password."));
        }
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Email does not exist."));
    }
}

if ($op == "updateCustomer") {
    $date = date("Y-m-d h:i:s");
    $first_name = filter_var($_POST["firstname"]);
    $last_name = filter_var($_POST["lastname"]);
    $phone = filter_var($_POST["phone"]);
    $password = filter_var($_POST["password"]);
    $customer_id = $_POST["customer_id"];

    $encrypt = new DESEncryption();
    $key = $_POST["email"];
    $cipher_password     = $encrypt->des($key, $password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);

    $sql = "UPDATE customer SET first_name = '$first_name', last_name = '$last_name', phone = '$phone', password = '$str_cipher_password', updated = '$date' WHERE customer_id = $customer_id AND email = '$key'";
    // echo $sql."\n";
    $update_sql = mysqli_query($conn, $sql);
    if ($update_sql) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully."));
    } else {
        echo json_encode(array("response_code" => 13, "response_message" => "Update failed"));
    }
}

if ($op == "updateDefaultAddr") {
    // var_dump($_POST);
    $date = date("Y-m-d h:i:s");
    $id = $_POST["address_id"];
    $customer_id = $_POST["id"];

    $sql = "UPDATE customer_addresses SET primary_address = 0, updated = '$date' WHERE customer_id = $customer_id";
    mysqli_query($conn, $sql);
    $query = "UPDATE customer_addresses SET primary_address = 1, updated = '$date' WHERE id = $id";
    // echo $query."\n";
    $result = mysqli_query($conn, $query);

    $q = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id AND id = $id";
    $r = mysqli_query($conn, $q);
    $default_addr = mysqli_fetch_assoc($r);
    if ($result) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully.", "data" => $default_addr));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Update failed."));
    }
}

if ($op == "customerAddresses") {
    // var_dump($_POST);
    $date = date("Y-m-d h:i:s");
    // $id = $_POST["id"];
    $customer_id = $_POST["id"];

    $q = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id AND primary_address = 1";
    $r = mysqli_query($conn, $q);
    $addr = mysqli_fetch_assoc($r);
    // var_dump($addr["state"]);
    if ($r) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully.", "data" => $addr));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Update failed."));
    }
}

if ($op == "pickupAddr") {
    // var_dump($_POST);
    $date = date("Y-m-d h:i:s");
    // $id = $_POST["id"];
    $pickup_id = $_POST["id"];
    $query = "SELECT * FROM merchant_pickup_stores WHERE id = $pickup_id";
    $res = mysqli_query($conn, $query);
    $pickup_addr = mysqli_fetch_assoc($res);
    // var_dump($pickup_addr);
    $sql = "SELECT * FROM lga WHERE state_code = '" . $pickup_addr["state"] . "' LIMIT 1";
    $state = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $q = "SELECT Lga FROM lga WHERE Lgaid = '" . $pickup_addr["lga"] . "'";
    $lga = mysqli_fetch_assoc(mysqli_query($conn, $q));
    $customer_id = $_POST["customer_id"];
    $query = "SELECT * FROM customer_pickup_address WHERE customer_id = $customer_id";
    $res = mysqli_query($conn, $query);
    if (mysqli_fetch_assoc($res) != NULL) {
        $q = "UPDATE customer_pickup_address SET pickup_location_id = $pickup_id, updated = '$date' WHERE customer_id = $customer_id";
    } else {
        $q = "INSERT INTO customer_pickup_address(customer_id, pickup_location_id, created) VALUE ($customer_id, $pickup_id, '$date')";
    }
    // echo $q."\n";
    $r = mysqli_query($conn, $q);
    $sql = "SELECT id FROM customer_pickup_address ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    // var_dump($state["State"]);
    if ($r) {
        echo json_encode(array("response_code" => 1, "response_message" => "Record save successfully", "id" => $row["id"], "state" => $state["State"], "lga" => $lga["Lga"], "address" => $pickup_addr["address"], "title" => $pickup_addr["title"]));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Failed to save record."));
    }
}

if ($op == "customerAddr") {
    // var_dump($_POST);
    // return;
    $params = array();
    parse_str($_POST["data"], $params);
    // var_dump($params);
    // return;
    $date = date("Y-m-d h:i:s");
    $street = filter_var($params["street"]);
    $lga = filter_var($params["lga"]);
    $state = filter_var($params["state"]);
    $country = filter_var($params["country"]);
    $post_code = filter_var($params["postcode"]);
    $customer_id = $params["id"];
    $operation = $params["operation"];
    $address_id = $params["address_id"];
    $primary_address = $params["primary_address"];

    $sql = "SELECT State FROM lga WHERE state_code = '$state' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $state = $row["State"];

    if ($operation != "Edit") {
        if ($primary_address == 'on') {
            $q = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id";
            $r = mysqli_query($conn, $q);
            $addr = mysqli_fetch_assoc($r);
            if ($addr != NULL) {
                $sql = "UPDATE customer_addresses SET primary_address = 0, updated = '$date' WHERE customer_id = $customer_id";
                mysqli_query($conn, $sql);
            }
            $sql = "INSERT INTO customer_addresses(customer_id, street, lga, state, country, post_code, created, primary_address) VALUE($customer_id, '$street', '$lga', '$state', '$country', '$post_code', '$date', 1)";
        } else {
            $sql = "INSERT INTO customer_addresses(customer_id, street, lga, state, country, post_code, created, primary_address) VALUE($customer_id, '$street', '$lga', '$state', '$country', '$post_code', '$date', 0)";
        }
    } else {
        if ($primary_address == 'on') {
            $q = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id";
            $r = mysqli_query($conn, $q);
            $addr = mysqli_fetch_assoc($r);
            if ($addr != NULL) {
                $sql = "UPDATE customer_addresses SET primary_address = 0, updated = '$date' WHERE customer_id = $customer_id";
                mysqli_query($conn, $sql);
            }
            $sql = "UPDATE customer_addresses SET street = '$street', lga = '$lga', state = '$state', country = '$country', post_code = '$post_code', updated = '$date', primary_address = 1 WHERE customer_id = $customer_id AND id = $address_id";
        } else {
            $sql = "UPDATE customer_addresses SET street = '$street', lga = '$lga', state = '$state', country = '$country', post_code = '$post_code', updated = '$date', primary_address = 0 WHERE customer_id = $customer_id AND id = $address_id";
        }
    }
    // echo $sql."\n";
    // return;
    $update_sql = mysqli_query($conn, $sql);
    if ($update_sql) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully."));
    } else {
        echo json_encode(array("response_code" => 13, "response_message" => "Update failed"));
    }
}

if ($op == "changePassword") {
    // var_dump($_POST);
    $date = date("Y-m-d h:i:s");
    $current_password = filter_var($_POST["current_password"]);
    $new_password = filter_var($_POST["new_password"]);
    $confirm_new_password = filter_var($_POST["confirm_new_password"]);
    $customer_id = $_POST["customer_id"];
    if ($new_password != $confirm_new_password) {
        echo json_encode(array("response_code" => 20, "response_message" => "Password does not match"));
        return;
    }

    $query = "SELECT * FROM customer WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $encrypt = new DESEncryption();
    $key = $row["email"];
    $cipher_password     = $encrypt->des($key, $current_password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);

    if ($str_cipher_password != $row["password"]) {
        echo json_encode(array("response_code" => 20, "response_message" => "Your current password is incorrect."));
        return;
    }

    $cipher_password     = $encrypt->des($key, $new_password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);
    $sql = "UPDATE customer SET password = '$str_cipher_password', updated = '$date' WHERE customer_id = $customer_id";
    // echo $sql."\n";
    $update_sql = mysqli_query($conn, $sql);
    if ($update_sql) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully."));
    } else {
        echo json_encode(array("response_code" => 13, "response_message" => "Update failed"));
    }
}

if ($op == "passwordRecovery") {
    $email = filter_var($_POST["email"]);

    $sql = "SELECT * FROM customer WHERE email = '$email' LIMIT 1";
    $resource = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($resource);

    if ($row["verified"] == '1') {
        $date = date('Y-m-d h:i:s');
        $verification_link = md5(uniqid(rand(), true));
        $sql = "UPDATE customer SET verification_link = '$verification_link', expired = 0, updated = '$date', verify_date = '$date' WHERE email = '$email'";
        // echo $sql."\n";
        $update_sql = mysqli_query($conn, $sql);
        try {

            $query = "SELECT * FROM customer ORDER BY customer_id DESC LIMIT 1";
            $resource = mysqli_query($conn, $query);
            $customer = mysqli_fetch_assoc($resource);

            /* Set the mail sender. */
            $mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Bille');

            $mail->addCustomHeader("MIME-Version: 1.0");
            $mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
            $mail->addCustomHeader("Content-type:text/css");

            /* Add a recipient. */
            $mail->addAddress($email);

            /* Set the subject. */
            $mail->Subject = 'PASSWORD RESET';

            $baseURL = "http://localhost/microstore/";

            $messageBody =
                "   
                    <html>
                    <head>
                    <title></title>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                    <style type='text/css'>
                        @media screen {
                            @font-face {
                            font-family: 'Lato';
                            font-style: normal;
                            font-weight: 400;
                            src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
                            }
                            
                            @font-face {
                            font-family: 'Lato';
                            font-style: normal;
                            font-weight: 700;
                            src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
                            }
                            
                            @font-face {
                            font-family: 'Lato';
                            font-style: italic;
                            font-weight: 400;
                            src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
                            }
                            
                            @font-face {
                            font-family: 'Lato';
                            font-style: italic;
                            font-weight: 700;
                            src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
                            }
                        }
                        
                        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
                        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
                        img { -ms-interpolation-mode: bicubic; }

                        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
                        table { border-collapse: collapse !important; }
                        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

                        a[x-apple-data-detectors] {
                            color: inherit !important;
                            text-decoration: none !important;
                            font-size: inherit !important;
                            font-family: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                        }

                        div[style*='margin: 16px 0;'] { margin: 0 !important; }
                    </style>
                    </head>
                    <body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'>

                    <div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'>
                        Looks like you lost your password. Let's see if we can get you back into your account.
                    </div>

                    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <!-- LOGO -->
                        <tr>
                            <td bgcolor='#7c72dc' align='center'>
                                <table border='0' cellpadding='0' cellspacing='0' width='480' >
                                    <tr>
                                        <td align='center' valign='top' style='padding: 40px 10px 40px 10px;'>
                                            <a href='http://litmus.com' target='_blank'>
                                                <img alt='Logo' src='https://s3-us-west-2.amazonaws.com/s.cdpn.io/665940/helloglogo.png' width='100' height='100' style='display: block;  font-family: 'Lato', Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;' border='0'>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor='#7c72dc' align='center' style='padding: 0px 10px 0px 10px;'>
                                <table border='0' cellpadding='0' cellspacing='0' width='480' >
                                    <tr>
                                        <td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'>
                                        <h1 style='font-size: 32px; font-weight: 400; margin: 0;'>Trouble signing in?</h1>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                                <table border='0' cellpadding='0' cellspacing='0' width='480' >
                                <tr>
                                    <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' >
                                    <p style='margin: 0;'>Resetting your password is easy. Just press the button below and follow the instructions. We'll have you up and running in no time.<br><p style='color: red;'>This link expires in 3 hours.</p></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td bgcolor='#ffffff' align='left'>
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                        <tr>
                                        <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'>
                                            <table border='0' cellspacing='0' cellpadding='0'>
                                            <tr>
                                                <td align='center' style='border-radius: 3px;' bgcolor='#7c72dc'><a href='" . $baseURL . "reset_password.php?reset_password=" . $verification_link . "' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7c72dc; display: inline-block;'>Reset Password</a></td>
                                            </tr>
                                            </table>
                                        </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'>
                                <table border='0' cellpadding='0' cellspacing='0' width='480' >
                                    <tr>
                                    <td bgcolor='#111111' align='left' style='padding: 40px 30px 20px 30px; color: #ffffff; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' >
                                        <h2 style='font-size: 24px; font-weight: 400; margin: 0;'>Unable to click on the button above?</h2>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td bgcolor='#111111' align='left' style='padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' >
                                        <p style='margin: 0;'>Click on the link below or copy/paste in the address bar.</p>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td bgcolor='#111111' align='left' style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' >
                                        <p style='margin: 0;'><a href='" . $baseURL . "reset_password.php?reset_password=" . $verification_link . "' target='_blank' style='color: #7c72dc;'>See how easy it is to get started</a></p>
                                    </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    </body>
                    </html>               
                ";
            /* Set the mail message body. */
            $mail->Body = $messageBody; //strip_tags($emessage);

            /* Finally send the mail. */
            if (!$mail->send()) {
                /* PHPMailer error. */
                echo $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            /* PHPMailer exception. */
            echo $e->errorMessage();
        } catch (\Exception $e) {
            /* PHP exception (note the backslash to select the global namespace Exception class). */
            echo $e->getMessage();
        }
        echo json_encode(array("response_code" => 1, "response_message" => "Email successfully sent."));
    } elseif ($row["verified"] == '0') {
        echo json_encode(array("response_code" => 13, "response_message" => "Account has not been verified."));
    } else {
        echo json_encode(array("response_code" => 20, "response_message" => "Email provided does not exist."));
    }
}

if ($op == "resetPassword") {
    $date = date("Y-m-d h:i:s");
    $password = filter_var($_POST["password"]);
    $cpassword = filter_var($_POST["cpassword"]);
    $email = $_POST["email"];

    if ($password != $cpassword) {
        echo json_encode(array("response_code" => 13, "response_message" => "Password does not match."));
    }

    $encrypt = new DESEncryption();
    $key = $email;
    $cipher_password     = $encrypt->des($key, $password, 1, 0, null, null);
    $str_cipher_password = $encrypt->stringToHex($cipher_password);

    $sql = "UPDATE customer SET password = '$str_cipher_password', updated = '$date' WHERE email = '$key'";
    // echo $sql."\n";
    $update_sql = mysqli_query($conn, $sql);
    if ($update_sql) {
        echo json_encode(array("response_code" => 1, "response_message" => "Updated successfully."));
    } else {
        echo json_encode(array("response_code" => 13, "response_message" => "Update failed"));
    }
}
                                                                                          