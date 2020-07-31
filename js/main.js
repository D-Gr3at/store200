$(document).on("click", ".checkout-button", function () {
    var paymentGroup = document.querySelector(".payment-group");
    // console.log(paymentGroup);
    if (paymentGroup.style.display == "none") {
        paymentGroup.setAttribute("style", "display:block !important");
        location.href = "#cSection";
    } else {
        paymentGroup.setAttribute("style", "display:none !important");
    }
});


$(document).ready(function () {
    // $(function(){
    Cart.initJQuery();
    Cart.currency = 'NGN ';
    // });
});
function onAdd(item_count) // this function will be called whenever an item is added to the cart
{

}



function deleteCart(id) {
    Cart.removeItem(id)
}
// TO EMPTY CART CALL THIS METHOD
//Cart.empty();
// TO REMOVE AN ITEM CALL THIS METHOD, and pass the product id you want to remove
//Cart.removeItem(product_id)
// TO ADD ITEM TO CART CALL THIS METHOD
//var itemx = {
//               id: '123456',
//                price: '15000',
//               quantity: 1,
//               label: 'Shoe',
//               image: 'img/shoe.png'
//             }
//                  Cart.addItem(itemx);


$('.hover-mask2').click(function (e) {
    $(".modal-head").empty();
    $("#product-image").attr("src", "");
    $("#product-description").empty();
    var image = $(this).siblings();

    var src = image[0].attributes[1].nodeValue;
    var price = $(this).parent().siblings(".product-info").find(".project-price").text();
    var cartPrice = $(this).parent().siblings(".project-description").attr("data-oldprice");
    var productId = $(this).parent().siblings(".project-description").attr("id");
    var description = $(this).parent().siblings(".project-description").find("p").text();
    $(".modal-head").text($(this).parent().next().find(".project-title").text())
    $("#product-image").attr("src", src);
    // console.log("here",productId);
    $("#product-price").text(price);
    $("#product-description").text(description);
    $("#cart-btn").attr("data-image", src);
    $("#cart-btn").attr("data-price", cartPrice);
    $("#cart-btn").attr("data-id", productId);
    $("#cart-btn").attr("data-quantity", "1");
    $("#cart-btn").attr("data-label", $(this).parent().next().find(".project-title").text());

});

$(document).on("click", "#cart-btn", function () {
    Command: toastr["success"]("Product added to cart!")

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
    // console.log($(this)[0].attributes["data-id"].nodeValue);

    $("#myModal").modal("hide");

});

// $("#signin-btn").click(function(){
//     $(".landing-page").hide();
//     $('#signin').modal('hide');
//     console.log($(".user-page"));
// });

function move() {
    location.href = "#hero";
}
// console.log($("#merchant_id").val());
function paynow() {
    var addr_op = "";
    var forms = $('#payment-form');
    forms.parsley().validate();

    if (forms.parsley().isValid()) {

        // products
        let orderedItems = localStorage.getItem("cart-items");
        // products ends
        move();
        $("#vuvaa_frame").show();
        let address = document.getElementById('pick_up');
        // console.log(address);
        $(".outer").show();
        $("#product_desc").val("200 Store Transaction");
        let params = $("#product_desc").val();
        let amount = $("#summary_total").text();
        let realamt = amount.substring(4);
        // console.log(realamt);
        if (realamt.includes(',')) {
            $("#amt_paid").val(realamt.replace(',', ''));
        } else {
            $("#amt_paid").val(realamt);
        }
        // console.log($("#amt_paid").val());
        if (!address.classList.contains("disabledbutton")) {
            addr_op = "pick_up";
        } else {
            addr_op = "delivery_addr";
        }
        // console.log(addr_op);     
        // console.log($("#amt_paid").val());
        $.post("utilities.php",
            {
                "op": "log_trans",
                "addr_op": addr_op,
                "desc": params,
                "merchant_reg_id": $("#merchant_reg_id").val(),
                "amt": $("#amt_paid").val(),
                "customer_id": $("#customer_id").val(),
                "address_id": $("#address_id").val(),
                "merchant_id": $("#merchant_id").val(),
                "order": orderedItems
                // "customer_id": $("#id").val()
            }, function (data) {
                // console.log(data);
                if (data.response_code == 1) {
                    $("#merch_trans_id").val(data.transaction_id);
                    $("#tt").submit();
                    $("#loading").show();
                    $('#vuvaa_frame').load(function () {
                        $("#loading").hide();
                        $("#bt").show();
                    });
                    localStorage.clear();
                } else if (data.response_code == 20) {
                    Command: toastr["error"]("Please select an address.")

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
                } else {
                    alert("Kindly re-initiate transaction");
                    $("#loading").hide();
                    $(".outer").hide();
                }
            }, 'json');

    }
}

$(document).ready(function () {

    $("#send").click(function(){
        
        var forms = $('#contact-form');
        forms.parsley().validate();
        if(forms.parsley().isValid()){
            $.post("utilities.php",
            {
                "name": $("#contact-name").val(), 
                "op": $("#operation").val(), 
                "email": $("#contact-mail").val(),
                "message": $("#contact-message").val(), 
                "submitted": $("#submitted3").val()
            }, 
            function(data){
                
                if(data.response_code == 1){
                    Command: toastr["success"](data.response_message+"!")

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
                    $("#contact-form").trigger("reset");
                }else{
                    Command: toastr["error"](data.response_message+"!")

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
            },'json');
        }
    });

    $("#signupButton").click(function () {
        let formData = $("#signupForm").serialize();
        var form = $("#signupForm");
        form.parsley().validate();

        if (form.parsley().isValid()) {
            $(".overlay_content").show();
            $.ajax({
                type: "POST",
                url: "utilities.php",
                data: "op=saveCustomer&" + formData,
                success: function (response) {
                    $(".overlay_content").hide();
                    response = JSON.parse(response);
                    if (response.response_code == 1) {
                        swal({
                            title: "Success!",
                            icon: "success",
                            text: "An email has been sent to the address provided to verify your account. Please check your email. If you didn't get the email after 5 minutes please click the 'Resend verification link' button on this page.",
                            button: {
                                text: "OK",
                                value: true,
                                visible: true,
                                className: "btn-success",
                                closeModal: true,
                            },
                            closeOnClickOutside: false
                        }).then(
                            $(".swal-button").click(function () {
                                $("#signupButton").css("cssText", "display: none !important;");
                                $("#resendButton").css("cssText", "display: block !important;");
                                $("#signupForm").trigger("reset");
                            })
                        );
                        $(document).on("click", "#resendButton", function () {
                            resendEmail(response.email);
                        });
                    } else if (response.response_code == 20) {
                        $("#server_mssg").text(response.response_message).css("color", "red");
                    } else {
                        swal({
                            title: "Error!",
                            icon: "error",
                            text: "Failed to register, Please check the information provided for error.",
                            button: {
                                text: "OK",
                                value: true,
                                visible: true,
                                className: "btn-danger",
                                closeModal: true,
                            },
                            closeOnClickOutside: false
                        });
                    }
                }
            });
        }
    })

    function resendEmail(val) {
        $.ajax({
            type: "POST",
            url: "utilities.php",
            data: "op=resendVerification&email=" + val,
            success: function (response) {
                response = JSON.parse(response);
                if (response.response_code == 1) {
                    swal({
                        title: "Success!",
                        icon: "success",
                        text: "An email has been sent. Please check your mail.",
                        button: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "btn-success",
                            closeModal: true,
                        },
                        closeOnClickOutside: false
                    }).then(
                        $(".swal-button").click(function () {
                            $("#signupButton").css("cssText", "display: none !important;");
                            $("#resendButton").css("cssText", "display: block !important;");
                            $("#signupForm").trigger("reset");
                        })
                    );
                } else if (response.response_code == 20) {
                    swal({
                        title: "Already Verified!",
                        icon: "warning",
                        text: "Your account has been verified already.",
                        button: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "btn-danger",
                            closeModal: true,
                        },
                        closeOnClickOutside: false
                    }).then(
                        $(".swal-button").click(function () {
                            location.href = "login.php";
                        })
                    );
                } else {

                }
            }
        });
    }

    $("#loginButton").click(function () {
        var loginData = $("#loginForm").serialize();
        let form = $("#loginForm");
        form.parsley().validate();

        if (form.parsley().isValid()) {
            $(".overlay_content").show();
            $.ajax({
                type: "POST",
                url: "utilities.php",
                data: "op=login&" + loginData,
                success: function (response) {
                    $(".overlay_content").hide();
                    response = JSON.parse(response);
                    if (response.response_code == 1) {
                        location.href = "index.php";
                    } else if (response.response_code == 20) {
                        $("#server_mssg").text(response.response_message).css("color", "red");
                    } else if (response.response_code == 13) {
                        $("#server_mssg").empty();
                        $("#server_mssg").append("<p style='color: red;'>" + response.response_message + "Please click <a id='clickSend'>Send</a> to verify account.</p>");
                        $(document).on("click", "#clickSend", function () {
                            resendEmail(response.email);
                            $("#server_mssg").css("cssText", "display:none !important");
                        })
                    }
                }
            });
        }
    });

    $("#changeAddrBtn").click(function () {
        let address_value = $("input[name='optradio']:checked").val();
        let address_id = $("input[name='optradio']:checked").attr("data-id");
        let id = $("#customer_id").val();
        // console.log(address_value, address_id);
        $.ajax({
            type: "POST",
            url: "utilities.php",
            data: "op=updateDefaultAddr&address_id=" + address_id + "&id=" + id,
            success: function (response) {
                // $(".overlay_content").hide();
                response = JSON.parse(response);
                if (response.response_code == 1) {
                    $("#addr_p").empty();
                    $("#address_id").val(response.data.id);
                    // $("#addr_msg").css("cssText", "display: none !important");
                    $("#addr_p").text(response.data.street + ", " + response.data.lga + ", " + response.data.state + ", " + response.data.country);
                    $("#addressModal").modal('hide');
                    $("#payOrder").attr("data-id", "addr");
                    $("#payOrder").removeClass("disabledbutton");
                    // location.href = "index.php";
                    // location.href = "index.php#orderform";
                } else if (response.response_code == 20) {
                    $("#server_mssg").text(response.response_message).css("color", "red");
                } else if (response.response_code == 13) {
                    $("#server_mssg").empty();
                    $("#server_mssg").append("<p style='color: red;'>" + response.response_message + "Please click <a id='clickSend'>Send</a> to verify account.</p>");
                    $(document).on("click", "#clickSend", function () {
                        resendEmail(response.email);
                        $("#server_mssg").css("cssText", "display:none !important");
                    })
                }
            }
        });
    });

});
