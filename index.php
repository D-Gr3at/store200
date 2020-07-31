<?php
session_start();
require 'dbcxn.inc.php';
require 'php_mailer.php';

function requery($merchant_id, $transid)
{
	$curl = curl_init();
	$data = "MerchantRegID=" . $merchant_id . "&MerchantTransID=" . $transid;
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://www.onepay.com.ng/api/ValidateTrans/getTrans.php",
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => 2,
		CURLOPT_POSTREDIR => 3,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_CUSTOMREQUEST => "POST"
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		return "cURL Error #:" . $err;
	} else {
		return $response;
	}
}




$result = mysqli_query($conn, "SELECT * FROM products WHERE merchant_id = 'VUV-071403041520'");
$merchant_products = mysqli_fetch_all($result, MYSQLI_ASSOC);
if (isset($_SESSION["customer_id"])) {
	$customer_id = $_SESSION["customer_id"];
	$query = "SELECT * FROM customer WHERE customer_id = $customer_id";
	$resource = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($resource);
	// var_dump()

	$sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id";
	// echo $sql."\n";
	$result = mysqli_query($conn, $sql);
	$addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
	
	$sql = "SELECT * FROM merchant_pickup_stores WHERE merchant_id = 'VUV-071403041520'";
	// echo $sql."\n";
	$result = mysqli_query($conn, $sql);
	$pickups = mysqli_fetch_all($result, MYSQLI_ASSOC);
	// var_dump($pickups);
}

?>

<!-- Mirrored from demo.angelostudio.net/microstore/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Jun 2020 12:06:54 GMT -->
<!-- Added by HTTrack -->
<?php
include('meta.php');
?>

<style>
	.navbar-brands {
		float: left;
		height: 50px;
		/* padding: 15px 15px; */
		font-size: 18px;
		line-height: 20px;
	}

	#recoverPassword:hover {
		cursor: pointer;
	}

	.cart-add {
		margin: 5px;
	}

	.deliveryAddress {
		width: fit-content;
		padding: 4%;
	}

	.side-card {
		background-color: #fff;
		box-shadow: 2px 2px #b2bdb5;
		min-height: 35vh !important;
	}

	.sub-card {
		/* display: block; */
		border: .1rem solid #b2bdb5
	}

	.checkout-line {
		position: relative;
		/* top: 20px;  */
		border: none;
		height: 1px;
		background: #BBB7B7;
		/* margin-bottom: 50px;  */
	}

	.disabledbutton {
		pointer-events: none;
		opacity: 0.4;
	}
</style>

<?php
include('header.php');
?>

<div class="landing-page">
	<section id="hero" class="hero-slider light-typo full-height" data-height="600">
		<div id="hero-slider" class="owl-carousel owl-theme" data-autoplay="4000" data-navigation="false" data-dots="true" data-transition="fadeOut">
			<div class="item m-center" style="background-image: url(img/slider/GIF-3.gif);">
				<div class="center-box">
					<span class="overlay-bg"></span>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 text-center anima fade-up">
								<div class="hero-unit">
									<img id="logo" src="img/logo-green.png" class="img-responsive text-center" alt="shop logo" width="300">
									<p>If you have 1-3 or more products for sale microStore is perfect template for you! MicroStore is a simple modern One page 100% responsive template.</p>
									<ul class="social-links text-center">
										<li><a href="#link"><i class="icon-twitter"></i></a></li>
										<li><a href="#link"><i class="icon-facebook"></i></a></li>
										<li><a href="#link"><i class="icon-vimeo"></i></a></li>
									</ul>
									<a class="btn btn-store outline smooth-scroll" href="#products">browse the goods</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="item m-center" style="background-image: url(img/slider/GIF-2.gif);">
				<div class="center-box">
					<span class="overlay-bg"></span>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 text-center anima fade-up">
								<div class="hero-unit">
									<h2>Nightstand</h2>
									<p>Donec augue lacus, vulputate sed consectetur facilisis, interdum pharetra ligula. Nulla suscipit erat nibh, ut porttitor nisl dapibus eu.</p>
									<a class="btn btn-store outline smooth-scroll" href="#products">buy &#8358;400</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="item m-center" style="background-image: url(img/slider/GIF.gif);">
				<div class="center-box">
					<span class="overlay-bg"></span>
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 text-center anima fade-up">
								<div class="hero-unit">
									<h2>Hammock</h2>
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sapien risus, blandit at fringilla ac, varius sed dolor. Donec augue lacus, vulputate sed consectetur facilisis, interdum pharetra ligula. Nulla suscipit erat nibh, ut porttitor nisl dapibus eu.</p>
									<a class="btn btn-store outline smooth-scroll" href="#products">buy now &#8358;600</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="products" class="padding-top-bottom" ng-controller="productsController">
		<div class="container">
			<header class="section-header text-center anima fade-up">
				<h2>Products</h2>
			</header>
		</div>
		<div class="container">
			<div id="projects-container" class="row" style="margin-right: 0px;">
				<?php
				foreach ($merchant_products as $key => $value) {
					// substr_count($value["image"], 0);
					// var_dump(substr($value["image"], 1));
				?>
					<article class="design product col-xs-12 col-sm-6 col-md-3 anima fade-up float-left" data-toggle="modal" data-target="#myModal">
						<div class="img-box">
							<div class="hover-mask2"></div>
							<img class="img-responsive project-image" src="<?php echo "admin/" . substr($value["image"], 1); ?>" alt="product">
						</div>
						<div class="row product-info col-md-12">
							<div class="col-md-4">
								<p class="project-price">&#8358;<?php echo number_format(floatval($value["price"]), 2, '.', ','); ?></p>
							</div>
							<div class="col-md-8 container">
								<h4 class="project-title container"><?php echo $value["name"] ?></h4>
							</div>
						</div>
						<div class="sr-only project-description" data-oldprice="<?php echo $value["price"]; ?>" id="<?php echo $value["id"] ?>" data-images="img/01.jpg,img/02.jpg,img/03.jpg,img/04.jpg,img/05.jpg">
							<p><?php echo $value["description"] ?></p>

						</div>
					</article>
				<?php } ?>
			</div>
		</div>

		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title modal-head"></h4>
					</div>
					<div class="modal-body">
						<div class="myImage">
							<!-- <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item active">
										<img class="d-block w-100" src="..." alt="First slide">
										</div>
										<div class="carousel-item">
										<img class="d-block w-100" src="..." alt="Second slide">
										</div>
										<div class="carousel-item">
										<img class="d-block w-100" src="..." alt="Third slide">
										</div>
									</div>
									<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
										<span class="carousel-control-prev-icon" aria-hidden="true"></span>
										<span class="sr-only">Previous</span>
									</a>
									<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
										<span class="carousel-control-next-icon" aria-hidden="true"></span>
										<span class="sr-only">Next</span>
									</a>
								</div> -->
							<img id="product-image" alt="" style="max-width: 100% !important;height: 330px;margin-bottom: 15px; width: 565px !important;">
						</div>
						<div class="product-content row">
							<div id="product-price" class="col-md-4"></div>
							<div id="product-description" class="col-md-8" style="font-family: sans-serif; line-height: 1.6;"></div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="cart-btn" class="btn btn-store outline btn-sm cta-button smooth-scroll cart-add" data-id="" data-quantity="1" data-price="" data-label="" data-image="">Add to Cart</button>
					</div>
				</div>

			</div>
		</div>
	</section>

	<section class="gray-bg padding-top-bottom ">
		<div class="container">
			<div class="row">
				<div class="col-md-8 anima fade-right">
					<h3 class="center cta-message">Free international shipping only<strong><span id="countdown"></span></strong></h3>
				</div>
				<div class="col-md-4 text-center anima fade-left d1">
					<a class="btn btn-store outline cta-button smooth-scroll" href="#orderform">Order Now</a>
				</div>
			</div>
		</div>
	</section>

	<section id="about" class="padding-top-bottom ">
		<div class="container">
			<div class="row">
				<div class="col-md-4 anima fadeInLeft">
					<header class="section-header cta-message">
						<h2>About company</h2>
						<h4>Pellentesque interdum eget</h4>
					</header>
				</div>
				<div class="col-md-8 cta-message anima fade-up">
					<p>Pellentesque interdum eget odio eu rutrum. Nunc tempus massa a tellus aliquet sodales. Nunc urna tortor, rhoncus accumsan vestibulum non, feugiat a mauris. In hac habitasse platea dictumst. In sagittis orci nec aliquet tristique.</p>
					<p style="display:none;" id="showme">Phasellus porta eros vel lacus euismod consequat. Phasellus eleifend, nibh non feugiat hendrerit, lacus enim adipiscing eros, a pharetra erat neque eget est. Quisque vitae aliquet urna. Integer suscipit lectus eu enim porttitor fringilla. Ut blandit, urna in auctor euismod, arcu eros pharetra metus, a porta purus libero a nibh.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sapien risus, blandit at fringilla ac, varius sed dolor. Donec augue lacus, vulputate sed consectetur facilisis, interdum pharetra ligula. Nulla suscipit erat nibh, ut porttitor nisl dapibus eu.</p>
					<a class="btn btn-store outline" id="show-btn" href="#">Read more</a>
				</div>
			</div>
		</div>
	</section>

	<header class="section-header text-center">
		<h2>Testimonials</h2>
	</header>
	<section id="testimonial" class="padding-top-bottom image-bg light-typo">
		<div class="container">
			<div class="testimonial">
				<div id="carousel-example-generic" class="carousel slide bs-docs-carousel-example" data-interval="10000">
					<ol class="carousel-indicators">
						<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
						<li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
						<li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
					</ol>
					<div class="carousel-inner">
						<div class="item active">
							<h1>I had a good experience buying from store200. <br>My product arrived on time and it was exctly what I ordered.</h1>
							<br>
							<h3>Malu Ugochukwu</h3>
						</div>
						<div class="item">
							<h1>Good customer service keep up the good job.</h1>
							<br>
							<h3>Wisdom Praise</h3>
						</div>
						<div class="item">
							<h1>Gaskiya you guys are the real deal thank you guys.
								<br> I am enjoying my power bank.
							</h1>
							<br>
							<h3>Hamzat Yusuf</h3>
						</div>
					</div>
					<br><br><br>
				</div>
			</div>
			<div class="overlay-bg"></div>
		</div>
	</section>

	<!-- <section id="newsletter" class="light-typo dark-bg padding-top-bottom">
				<div class="container ">
					<header class="section-header text-center">
						<h2>Stay in touch</h2>
						<h4>Sign Up for Email Updates on on News & Offers</h4>
					</header>
					<div class="row">
						<form id="newsletter-form" action="http://demo.angelostudio.net/microstore/index.html" method="POST" class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 ">
							<div class="input-group">
								<input id="newsletter-mail" name="email" placeholder="Enter your email" class="form-control input-lg" type="email" >
								<span class="input-group-btn">
									<button name="submit" type="submit" class="btn btn-store">Subscribe!</button>
								</span>
							</div>
							<input type="hidden" name="submitted" id="submitted2" value="true">
						</form>
					</div>
				</div>
			</section>	 -->


	<section id="orderform" class="white-bg padding-top-bottom" ng-controller="orderController">
		<div class="container">
			<header class="section-header text-center">
				<h2>Cart Form</h2>
			</header>
			<p><b>Cart:</b> <span class='cart-items-count'>0</span> item<span class='cart-items-count-plural'>s</span></p>
			<form action="" method="post" novalidate id="order-form">
				<div class="row">
					<div class="col-sm-12 col-md-12">
						<table class="table cart-table">
							<thead>
								<tr>
									<th>Image</th>
									<th>Product</th>
									<th>quantity</th>
									<th></th>
									<th>Unit Price</th>
								</tr>
							</thead>
							<tbody class="cart-line-items">
							</tbody>
							<tfoot>
								<tr>
									<td colspan='4'>Subtotal</td>
									<td class='cart-subtotal'></td>
								</tr>
							</tfoot>
						</table>
						<p class='cart-is-empty'>Your cart is empty.</p>
						<div class="anima fade-left d1">
							<a class="btn btn-store outline cta-button smooth-scroll checkout-button">Checkout</a>
						</div>
						<!-- <button class="checkout-button text-white">Checkout</button> -->
					</div>
				</div>
			</form>

		</div>
	</section>
	<?php if (!isset($_SESSION["customer_id"])) { ?>
		<section id="cSection" class="payment-group gray-bg padding-top-bottom" style="display:none">
			<div class="container justify-content-center">
				<div class="row">
					<div class="container text-center">
						<p style="font-weight: bold; font-size:3rem;">Already have an account?</p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 text-center anima fade-left d1 mx-auto">
						<a class="btn btn-store outline cta-button smooth-scroll" onclick="javascript:location.href = 'login.php'">Sign in</a>
					</div>
					<div class="col-md-6 text-center anima fade-left d1 mx-auto">
						<a class="btn btn-store outline cta-button smooth-scroll" onclick="javascript:location.href = 'register.php'">Sign up</a>
					</div>
				</div>
			</div>
		</section>
	<?php } ?>
	<?php if (isset($_SESSION["customer_id"])) { ?>
		<div id="cSection" class="gray-bg payment-group padding-top-bottom" style="display:none">
			<div class="container">
				<h4 class="text-center text-uppercase" style="font-size: 200%;">Checkout options</h4>
				<hr class="checkout-line" />
				<p><strong>Please choose a checkout option by selecting option 1 or option 2:</strong></p>
				<form action="" id="payment-form" onsubmit="return false">
					<div class="row">
						<input type="hidden" name="merchant_id" id="merchant_id" value="VUV-071403041520">
						<input type="hidden" name="address_id" id="address_id">
						<input type="hidden" name="customer_id" id="c_id" value="<?php echo $customer_id; ?>">
						<div class="col-sm-5 col-md-5">
							<p><label><input type="radio" name="delivery_option" id="option_1" data-id="delivery_addr" checked></label><strong> Option 1:</strong></p>
						</div>
						<div class="col-sm-2 col-md-2"></div>
						<div class="col-sm-5 col-md-5">
							<p><label><input type="radio" name="delivery_option" id="option_2" data-id="pick_up"></label><strong> Option 2:</strong></p>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-5 col-md-5 side-card" id="delivery_addr">
							<p class="text-center"><strong>Delivery address</strong></p>
							<hr>
							<p class="text-center"><strong></strong></p>
							<div class="deliveryAddress w-100">
								<p id="addr_p"></p>
							</div>
							<a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right" style="margin-bottom: 4%;" data-toggle="modal" data-target="#addressModal">change</a>
						</div>
						<div class="col-sm-2 col-md-2"></div>
						<div class="col-sm-5 col-md-5 side-card" id="pick_up">
							<p class="text-center"><strong>Pickup Location</strong></p>
							<hr>
							<p class="text-center"><strong></strong></p>
							<div class="deliveryAddress w-100">
								<h4 id="location_title"></h4>
								<p id="selected_location">Please select a pick-up location in your area from our pickup stations nationwide.</p>
							</div>
							<a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right" style="margin-bottom: 4%;" data-toggle="modal" data-target="#pickup_location">select pick-up location</a>
							<!-- <input type="hidden" name="submitted" id="submitted" value="true"> -->
						</div>
						<div class="col-sm-3 col-md-3"></div>
						<div class="col-sm-6 col-md-6" style="margin-top: 4%;">
							<table class="table">
								<thead>
									<tr>
										<th colspan="3" class="text-center">Order sumary</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Total:</td>
										<td></td>
										<td class="text-right cart-subtotal" id="summary_total"></td>
									</tr>
								</tbody>
							</table>
							<p>
								<a name="submit" type="submit" id="payOrder" data-id="no_pickup" class="btn btn-store btn-block" onclick="paynow()">Order Product</a>
							</p>
						</div>
						<div class="col-sm-3 col-md-3"></div>
					</div>

				</form>
			</div>
		</div>
	<?php } ?>
	<div class="modal fade" id="addressModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-uppercase mb-0">Address Book</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<form action="#" method="post" id="changeAddrForm">
							<input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>">
							<?php foreach ($addresses as $key => $value) { ?>
								<div class="radio">
									<label><input type="radio" name="optradio" class="address-option" <?php echo $value["primary_address"] != '0' ? "checked" : ""; ?> data-id="<?php echo $value["id"]; ?>">
										<?php echo $value["street"] . ", " . $value["lga"] . ", " . $value["state"] . ", " . $value["country"] . "."; ?>
									</label>
								</div>
								<hr>
							<?php } ?>
							<div id="server_mssg"></div>
							<a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right" id="changeAddrBtn">Save</a>
							<!-- <input type="submit" value="save"> -->
						</form>
					</div>
				</div>
				<!-- <div class="modal-footer">
					<div class="float-right anima fade-left d1 mx-auto">
						<a class="btn btn-store outline cta-button smooth-scroll" id="signup-btn">Sign up</a>
					</div>
				</div> -->
			</div>

		</div>
	</div>

	<div class="modal fade" id="pickup_location" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-uppercase mb-0">Pickup locations</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<form action="#" method="post" id="pickupForm">
							<!-- <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>"> -->
							<?php foreach ($pickups as $key => $value) { ?>
								<div class="radio">
									<label><input type="radio" name="pickup_addr" class="address-option"  data-id="<?php echo $value["id"]; ?>">
									<?php
										$sql = "SELECT State FROM lga WHERE state_code = '".$value["state"]."' LIMIT 1";
										$state = mysqli_fetch_assoc(mysqli_query($conn, $sql));
										$q = "SELECT Lga FROM lga WHERE Lgaid = '".$value["lga"]."'";
										$lga = mysqli_fetch_assoc(mysqli_query($conn, $q));
										// var_dump($lga);
									?>
										<h4><?php echo $value["title"]; ?></h4>
										<span><?php echo $value["address"] . ", " . $lga["Lga"] . ", " . $state["State"] . ", Nigeria "; ?></span>
										<span>Phone: <?php echo $value["phone"]?></span>
									</label>
								</div>
								<hr>
							<?php } ?>
							<div id="server_mssg"></div>
							<a class="btn btn-sm btn-store outline cta-button smooth-scroll pull-right disabledbutton" id="selectPickup" >Save</a>
							<!-- <input type="submit" value="save"> -->
						</form>
					</div>
				</div>
				<!-- <div class="modal-footer">
					<div class="float-right anima fade-left d1 mx-auto">
						<a class="btn btn-store outline cta-button smooth-scroll" id="signup-btn">Sign up</a>
					</div>
				</div> -->
			</div>

		</div>
	</div>

	<div class="modal fade" id="signin" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-uppercase mb-0">Sign in</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<form action="#" method="post">
							<div class="row">
								<div class="col-sm-12 col-md-12">
									<div class="form-group">
										<label class="control-label">Email</label>
										<input name="email" placeholder="Your email" class="form-control input-lg" type="email">
									</div>
								</div>
								<div class="col-sm-12 col-md-12">
									<div class="form-group">
										<label class="control-label">Password</label>
										<input name="password" placeholder="Password" class="form-control input-lg" type="password">
									</div>
								</div>
								<div class="col-sm-12 col-md-12">
									<p>Forgot password? click <a id="recoverPassword" data-toggle="modal" data-target="#recovery">here</a></p>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<div class="float-right anima fade-left d1 mx-auto">
						<a class="btn btn-store outline cta-button smooth-scroll" id="signin-btn">Sign in</a>
					</div>
				</div>
			</div>

		</div>
	</div>


	<section id="cta" class="padding-top-bottom color-bg light-typo cta">
		<div class="container">
			<div class="row text-center">
				<div class="col-md-4 col-sm-4 news anima fade-up">
					<span>&#10003;</span>
					<!-- <i class="icon-trophy iconBig"></i> -->
					<h3>Quality guaranteed</h3>
				</div>
				<div class="col-md-4 col-sm-4 news anima fade-up d1">
					<span>&#10003;</span>
					<!-- <i class="icon-truck iconBig"></i> -->
					<h3>Home delivery</h3>
				</div>
				<div class="col-md-4 col-sm-4 anima fade-up d2">
					<span>&#10003;</span>
					<!-- <i class="icon-lock iconBig"></i> -->
					<h3>Secure payment</h3>
				</div>
			</div>
		</div>
	</section>

	<section id="contact" class="gray-bg padding-top-bottom">
		<div class="container">
			<header class="section-header text-center">
				<h1><strong>Contact</strong></h1>
				<p>Do you have some kind of problem with our products?</p>
			</header>
			<form action="" method="post" id="contact-form" onsubmit="return false">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4 contact-info cta-message anima fade-right">
						<address>
							<strong>200 STORE</strong><br>
							84 State Road 123<br>
							City, State 24813<br>
							(123) 456 - 7890<br>
							contact@companyname.com
						</address>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-8 anima fade-up d1">
						<div class="form-group">
							<label class="control-label" for="contact-name">Name</label>
							<div class="controls">
								<input id="contact-name" name="contactName" placeholder="Your name" class="form-control input-lg requiredField" type="text" required>
								<input id="operation" name="op" placeholder="Your name" class="form-control input-lg requiredField" value="contact" type="hidden">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label" for="contact-mail">Email</label>
							<div class=" controls">
								<input id="contact-mail" name="email" placeholder="Your email" class="form-control input-lg requiredField" type="email" required>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label" for="contact-message">Message</label>
							<div class="controls">
								<textarea id="contact-message" name="comments" placeholder="Your message" class="form-control input-lg requiredField" rows="5"></textarea>
							</div>
						</div>
						<p>
							<button name="submit" type="submit" class="btn btn-store btn-block" id="send">Send Message</button></p>
						<input type="hidden" name="submitted" id="submitted3" value="true">
					</div>
				</div>
			</form>
		</div>
	</section>

	<!-- Onepay Integration -->
	<div class="outer" id="onepay_frame" style="display: none; height:506%; top:0; position:absolute; width:100%; z-index:99999; background:rgba(0,0,0,0.7)">
		<div class="middle">
			<div class="inner" align="center">
				<p id="loading" style="color:#fff; font-size: 6rem; margin-top: 10rem;">Connecting to payment gateway...</p>
				<div align="center" id="bt" style="display:none; margin-top:10px;">
					<button onclick="javascript:window.location='index.php'" class="btn btn-danger">CANCEL</button>
				</div>
				<!-- <img src="img/wait.gif" id="loading"  alt="" /> -->
				<form id="tt" action="https://www.onepay.com.ng/api/live/main" target="vuvaa_frame" method="POST">
					<input name="product_desc" id="product_desc" type="hidden" value="sdsds" />
					<input name="merch_trans_id" id="merch_trans_id" type="hidden" value="" />
					<input name="merchant_reg_id" id="merchant_reg_id" type="hidden" value="ACC-OPMHT000000248" />
					<input name="client_email" id="client_email" type="hidden" value="<?php echo $row["email"]; ?>" />
					<input name="client_name" id="client_name" type="hidden" value="<?php echo $row["first_name"] . " " . $row["last_name"]; ?>" />
					<input name="client_phone" id="client_phone" type="hidden" value="<?php echo $row["phone"]; ?>" />
					<input name="amt_paid" id="amt_paid" type="hidden" value="" />
				</form>
				<iframe name="vuvaa_frame" id="vuvaa_frame" scrolling="no" width="400" height="650" style="color:#fff; border:none;z-index:9999;display:none; width:360px;margin-left: 20px;" align="center"></iframe>
			</div>
		</div>
	</div>
	<!-- Onepay Integration Ends -->

</div>

<?php
include('import.php');
if (isset($_GET['MerchantTransactionID'])) {
	$transid  = $_GET['MerchantTransactionID'];
	$response = json_decode(requery("ACC-OPMHT000000248", $transid), true);
	$code     = $response["data"]["response_code"];
	// var_dump($code);
	$message  = $response["data"]["response_message"];
	$amt  = $response["data"]["Amt_paid"];
	$payment_reference  = $response["data"]["payment_ref"];
	$m_id = substr($transid, 9);
	$customer_transaction = mysqli_query($conn, "SELECT * FROM transaction_table WHERE transaction_id = '$m_id'");
	$transaction_result = mysqli_fetch_assoc($customer_transaction);
	$customer_id = $transaction_result["customer_id"];
	$order_id = $transaction_result["order_id"];
	$order_details = mysqli_query($conn, "SELECT * FROM orderdetails WHERE orderid = $order_id");
	$order = mysqli_fetch_all($order_details);
	// var_dump($order[0][38]);
	$customer_details = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = $customer_id");
	$customer = mysqli_fetch_assoc($customer_details);

	// $street = $customer["street"];
	// $city = $customer["city"];
	// $country = $customer["country"];
	if($order[0][37] != NULL){
		$sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id AND id = ".$order[0][37];
		// echo $sql."\n";
		$result = mysqli_query($conn, $sql);
		$addr = mysqli_fetch_assoc($result);

		$street = $addr["street"];
		$lga = $addr["lga"];
		$state = $addr["state"];
		$post_code = $addr["post_code"];
		$country = $addr["country"];
		$title = "DEFAULT ADDRESS";
		$phone = $customer["phone"];
	}else{
		$sql = "SELECT * FROM customer_pickup_address WHERE customer_id = $customer_id AND id = ".$order[0][38];
		$addr = mysqli_fetch_assoc(mysqli_query($conn, $sql));
		$q = "SELECT * FROM merchant_pickup_stores WHERE id = ".$addr["pickup_location_id"];
		// echo $sql."\n";
		$m_addr = mysqli_fetch_assoc(mysqli_query($conn, $q));

		$street = $m_addr["address"];
		$sql = "SELECT * FROM lga WHERE Lgaid = '".$m_addr["lga"]."'";
		$lga_addr = mysqli_fetch_assoc(mysqli_query($conn, $sql));
		// var_dump($lga_addr);
		$lga = $lga_addr["Lga"];
		$state = $lga_addr["State"];
		$country = "Nigeria";
		$phone = $m_addr["phone"];
		$title = $m_addr["title"];
	}

	$sql = "SELECT * FROM customer_addresses WHERE customer_id = $customer_id AND primary_address = 1";
	// echo $sql."\n";
	$result = mysqli_query($conn, $sql);
	$address = mysqli_fetch_assoc($result);

	$b_street = $address["street"];
	$b_lga = $address["lga"];
	$b_state = $address["state"];
	$b_post_code = $address["post_code"];
	$b_country = $address["country"];
	$b_phone = $customer["phone"];
	$b_title = "DEFAULT ADDRESS";

	$sql      = "UPDATE transaction_table SET response_code = '$code', response_message = '$message', merchant_payment_reference = '$payment_reference'  WHERE transaction_id = '$m_id'";
	$result   = mysqli_query($conn, $sql);
	if (mysqli_affected_rows($conn) < 1) {
		$sql2 = "UPDATE transaction_table SET response_code = '$code', response_message = '$message', merchant_payment_reference = '$payment_reference' WHERE transaction_id = '$m_id'";
		$result2 = mysqli_query($conn, $sql2);
	}
	if ($code == '00' || $code == '0') {
?>
		<script>
			swal({
				title: "Success!",
				icon: "success",
				text: `<?php echo $message ?>`,
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
					window.location = "index.php"
				})
			);
		</script>
		<?php
		try {


			/* Set the mail sender. */
			$mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Bille');

			// $mail->addCustomHeader("MIME-Version: 1.0");
			$mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
			$mail->addCustomHeader("Content-type:text/css");

			/* Add a recipient. */
			$mail->addAddress($row["email"]);

			/* Set the subject. */
			$mail->Subject = 'STORE200 TRANSACTION SUCCESS';

			$products = "";
			$total = 0;

			foreach ($order as $key => $value) {
				$products .=
					"<tr>
						<td style='padding-top: 0;'>
							<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
								<tbody>
									<tr>
										<td style='padding-right: 10px; padding-bottom: 10px; width: 100px; padding-left: 15px;' rowspan='4'><img style='height: 80px;' src='" . $value[32] . "' alt='" . $value[28] . "' /></td>
										<td style='font-size: 14px; font-weight: bold; color: #666666; padding-bottom: 5px; width: 628.4px;' colspan='2'>" . $value[28] . "</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; width: 628.4px;'>Quantity: " . $value[5] . "</td>
										<td style='width: 129px;'>&nbsp;</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; width: 129px;'>&#8358; " . $value[6] . " Per Unit</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; padding-bottom: 10px; width: 129px;'><strong style='color: #666666;'>&#8358; " . $value[10] . "</strong> Total</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>";
				$total += floatval($value[10]);
			}


			// var_dump($order);
			$messageBody =
				"<p>Dear " . $customer["first_name"] . " " . $customer["last_name"] . ",</p><br>
					<p style='color: green; font-size: 1rem'><em>You have successfully ordered for the following products below.</em></p><br/><br/>
					<table class='devicewidth' style='background-color: #ffffff;' border='0' width='600' cellspacing='0' cellpadding='15' align='center'>
					<tbody>
						<tr>
							<td style='padding-top: 30px;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; text-align: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='padding-bottom: 10px; font-size:2rem;'><a><img src='img/logo.png' alt='200Store' /></a></td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>3828 Mall Road</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Los Angeles, California, 90017</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Phone: 310-807-6672 | Email: info@example.com</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 25px;'><strong>Order Number: </strong>" . $order_id . " | <strong>Order Date:</strong> " . $transaction_result["created"] . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; justify-content: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 55%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Delivery Adderss</td>
											<td style='width: 45%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Billing Address</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 12px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 12px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px; text-transform: uppercase;'>" . $title. ": </td>
											<td style='width: 45%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_title . ": </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $street . ", </td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_street . ", </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $lga . ", " . $state . ", " . $country . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_lga . ", " . $b_state . ", " . $b_country . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $phone . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_phone . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>" . $products . "
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; margin-top: -5px; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 271.4px;' rowspan='5'>&nbsp;</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 335.6px;'>Sub-Total:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 188px; text-align: right; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; width: 335.6px;'>Shipping Fee:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; text-align: right; width: 188px; padding-right: 130px;'>&#8358; 0.00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; width: 335.6px;'>Order Total</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; text-align: right; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; width: 335.6px;'>Payment Term:</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; width: 188px; padding-right: 130px;'>100%</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-bottom: 10px; width: 335.6px;'>Deposit Amount</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; padding-bottom: 10px; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding: 0 10px;'>
								<table class='devicewidthinner' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 100%; text-align: center; font-style: italic; font-size: 13px; font-weight: 600; color: #666666; padding: 15px 0; border-top: 1px solid #eeeeee;' colspan='2'><strong style='font-size: 14px;'></strong>Thank you for your patronage.</td>
										</tr>	
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>";

			/* Set the mail message body. */
			$mail->Body = $messageBody; //strip_tags($emessage);

			/* Finally send the mail. */
			// echo "here ---> ".$mail->send();
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
	} else if ($code == 'RR') {
		// var_dump($code);
		/* Open the try/catch block. */
		try {


			/* Set the mail sender. */
			$mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Bille');

			// $mail->addCustomHeader("MIME-Version: 1.0");
			$mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
			$mail->addCustomHeader("Content-type:text/css");

			/* Add a recipient. */
			$mail->addAddress($row["email"]);

			/* Set the subject. */
			$mail->Subject = 'STORE200 TRANSACTION FAILURE';

			$products = "";
			$total = 0;

			foreach ($order as $key => $value) {
				$products .=
					"<tr>
						<td style='padding-top: 0;'>
							<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
								<tbody>
									<tr>
										<td style='padding-right: 10px; padding-bottom: 10px; width: 100px; padding-left: 15px;' rowspan='4'><img style='height: 80px;' src='" . $value[32] . "' alt='" . $value[28] . "' /></td>
										<td style='font-size: 14px; font-weight: bold; color: #666666; padding-bottom: 5px; width: 628.4px;' colspan='2'>" . $value[28] . "</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; width: 628.4px;'>Quantity: " . $value[5] . "</td>
										<td style='width: 129px;'>&nbsp;</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; width: 129px;'>&#8358; " . $value[6] . " Per Unit</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; padding-bottom: 10px; width: 129px;'><strong style='color: #666666;'>&#8358; " . $value[10] . "</strong> Total</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>";
				$total += floatval($value[10]);
			}


			// var_dump($order);
			$messageBody = "<p>Dear " . $customer["first_name"] . " " . $customer["last_name"] . ",</p><br>
					<p style='color: red; font-size: 1rem;'><em>Your order for the following products below was unsuccessful.</em></p><br/><br/>
					<table class='devicewidth' style='background-color: #ffffff;' border='0' width='600' cellspacing='0' cellpadding='15' align='center'>
					<tbody>
						<tr>
							<td style='padding-top: 30px;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; text-align: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='padding-bottom: 10px; font-size:2rem;'><a><img src='img/logo.png' alt='200Store' /></a></td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>3828 Mall Road</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Los Angeles, California, 90017</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Phone: 310-807-6672 | Email: info@example.com</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 25px;'><strong>Order Number: </strong>" . $order_id . " | <strong>Order Date:</strong> " . $transaction_result["created"] . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; justify-content: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 55%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Delivery Adderss</td>
											<td style='width: 45%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Billing Address</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px; text-transform: uppercase;'>" . $title. ": </td>
											<td style='width: 45%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_title . ": </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $street . ", </td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_street . ", </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $lga . ", " . $state . ", " . $country . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_lga . ", " . $b_state . ", " . $b_country . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $phone . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_phone . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>" . $products . "
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; margin-top: -5px; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 271.4px;' rowspan='5'>&nbsp;</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 335.6px;'>Sub-Total:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 188px; text-align: right; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; width: 335.6px;'>Shipping Fee:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; text-align: right; width: 188px; padding-right: 130px;'>&#8358; 0.00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; width: 335.6px;'>Order Total</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; text-align: right; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; width: 335.6px;'>Payment Term:</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; width: 188px; padding-right: 130px;'>100%</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-bottom: 10px; width: 335.6px;'>Deposit Amount</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; padding-bottom: 10px; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding: 0 10px;'>
								<table class='devicewidthinner' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 100%; text-align: center; font-style: italic; font-size: 13px; font-weight: 600; color: #666666; padding: 15px 0; border-top: 1px solid #eeeeee;' colspan='2'><strong style='font-size: 14px;'></strong>Thank you for your patronage.</td>
										</tr>	
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>";


			/* Set the mail message body. */
			$mail->Body = $messageBody; //strip_tags($emessage);

			/* Finally send the mail. */
			// echo "here ---> ".$mail->send();
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
		?>
		<script>
			swal({
				title: "Error!",
				icon: "error",
				text: `<?php echo $message; ?>`,
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
					window.location = "index.php"
				})
			);
		</script>
<?php
	}elseif($code == '99'){
		try {


			/* Set the mail sender. */
			$mail->setFrom('ibinabotontebille@gmail.com', 'Ibinabo Bille');

			// $mail->addCustomHeader("MIME-Version: 1.0");
			$mail->addCustomHeader("Content-type:text/html;charset=UTF-8");
			$mail->addCustomHeader("Content-type:text/css");

			/* Add a recipient. */
			$mail->addAddress($row["email"]);

			/* Set the subject. */
			$mail->Subject = 'STORE200 TRANSACTION FAILURE';

			$products = "";
			$total = 0;

			foreach ($order as $key => $value) {
				$products .=
					"<tr>
						<td style='padding-top: 0;'>
							<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
								<tbody>
									<tr>
										<td style='padding-right: 10px; padding-bottom: 10px; width: 100px; padding-left: 15px;' rowspan='4'><img style='height: 80px;' src='" . $value[32] . "' alt='" . $value[28] . "' /></td>
										<td style='font-size: 14px; font-weight: bold; color: #666666; padding-bottom: 5px; width: 628.4px;' colspan='2'>" . $value[28] . "</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; width: 628.4px;'>Quantity: " . $value[5] . "</td>
										<td style='width: 129px;'>&nbsp;</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; width: 129px;'>&#8358; " . $value[6] . " Per Unit</td>
									</tr>
									<tr>
										<td style='font-size: 14px; line-height: 18px; color: #757575; text-align: right; padding-bottom: 10px; width: 129px;'><strong style='color: #666666;'>&#8358; " . $value[10] . "</strong> Total</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>";
				$total += floatval($value[10]);
			}


			// var_dump($order);
			$messageBody = "<p>Dear " . $customer["first_name"] . " " . $customer["last_name"] . ",</p><br>
					<p style='color: red; font-size: 1rem;'><em>Your order for the following products below was unsuccessful because you entered an invalid transaction details.</em></p><br/><br/>
					<table class='devicewidth' style='background-color: #ffffff;' border='0' width='600' cellspacing='0' cellpadding='15' align='center'>
					<tbody>
						<tr>
							<td style='padding-top: 30px;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #eeeeee; text-align: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='padding-bottom: 10px; font-size:2rem;'><a><img src='img/logo.png' alt='200Store' /></a></td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>3828 Mall Road</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Los Angeles, California, 90017</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666;'>Phone: 310-807-6672 | Email: info@example.com</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 25px;'><strong>Order Number: </strong>" . $order_id . " | <strong>Order Date:</strong> " . $transaction_result["created"] . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; justify-content: center;' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 55%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Delivery Adderss</td>
											<td style='width: 45%; font-size: 16px; font-weight: bold; color: #666666; padding-bottom: 5px; padding-left: 70px;'>Billing Address</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $customer['last_name'] . ", " . $customer['first_name'] . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px; text-transform: uppercase;'>" . $title. ": </td>
											<td style='width: 45%; font-size: 14px; font-weight:bold; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_title . ": </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $street . ", </td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-left: 70px;'>" . $b_street . ", </td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $lga . ", " . $state . ", " . $country . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_lga . ", " . $b_state . ", " . $b_country . "</td>
										</tr>
										<tr>
											<td style='width: 55%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $phone . "</td>
											<td style='width: 45%; font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; padding-left: 70px;'>" . $b_phone . "</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>" . $products . "
						<tr>
							<td style='padding-top: 0;'>
								<table class='devicewidthinner' style='border-bottom: 1px solid #bbbbbb; margin-top: -5px; width: 800px;' border='0' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 271.4px;' rowspan='5'>&nbsp;</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 335.6px;'>Sub-Total:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; width: 188px; text-align: right; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; width: 335.6px;'>Shipping Fee:</td>
											<td style='font-size: 14px; line-height: 18px; color: #666666; padding-bottom: 10px; border-bottom: 1px solid #eeeeee; text-align: right; width: 188px; padding-right: 130px;'>&#8358; 0.00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; width: 335.6px;'>Order Total</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-top: 10px; text-align: right; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; width: 335.6px;'>Payment Term:</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; width: 188px; padding-right: 130px;'>100%</td>
										</tr>
										<tr>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; padding-bottom: 10px; width: 335.6px;'>Deposit Amount</td>
											<td style='font-size: 14px; font-weight: bold; line-height: 18px; color: #666666; text-align: right; padding-bottom: 10px; width: 188px; padding-right: 130px;'>&#8358; " . $total . ".00</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td style='padding: 0 10px;'>
								<table class='devicewidthinner' border='0' width='800' cellspacing='0' cellpadding='0' align='center'>
									<tbody>
										<tr>
											<td style='width: 100%; text-align: center; font-style: italic; font-size: 13px; font-weight: 600; color: #666666; padding: 15px 0; border-top: 1px solid #eeeeee;' colspan='2'><strong style='font-size: 14px;'></strong>Thank you for your patronage.</td>
										</tr>	
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>";


			/* Set the mail message body. */
			$mail->Body = $messageBody; //strip_tags($emessage);

			/* Finally send the mail. */
			// echo "here ---> ".$mail->send();
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
		?>
		<script>
			swal({
				title: "Error!",
				icon: "error",
				text: "You entered an invalid transaction details.",
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
					window.location = "index.php"
				})
			);
		</script>
	<?php }
}
?>

<script>
	var customer_id = "<?php echo $customer_id; ?>";
	$("#pick_up").addClass("disabledbutton");
	// $("#delivery_addr").addClass("disabledbutton");

	$("input[name='delivery_option']").click(function() {
		if ($(this).attr("data-id") == "delivery_addr") {
			$("#delivery_addr").removeClass("disabledbutton");
			$("#pick_up").addClass("disabledbutton");
			$("#payOrder").addClass("disabledbutton");
			if($("#payOrder").attr("data-id") == "addr"){
				$("#payOrder").removeClass("disabledbutton");
			}
		}
		if ($(this).attr("data-id") == "pick_up") {
			$("#pick_up").removeClass("disabledbutton");
			$("#delivery_addr").addClass("disabledbutton");
			$("#payOrder").addClass("disabledbutton");
			if($("#payOrder").attr("data-label") == "pickup"){
				$("#payOrder").removeClass("disabledbutton");
			}
		}
	});
	$("input[name='pickup_addr']").click(function(){
		if($("input[name='pickup_addr']:checked").val() == "on"){
			$("#selectPickup").removeClass("disabledbutton");
		}else{
			$("#selectPickup").addClass("disabledbutton");
		}
	});

	$("input[name='optradio']").click(function(){
		if($("input[name='optradio']:checked").val() == "on"){
			$("#changeAddrBtn").removeClass("disabledbutton");
		}else{
			$("#changeAddrBtn").addClass("disabledbutton");
		}
	});
	// console.log();

	$("#selectPickup").click(function(){
		let id = $("input[name='pickup_addr']:checked").attr("data-id");
		// console.log(id);
		$.ajax({
			type: "POST",
			url: "utilities.php",
			data: "op=pickupAddr&id=" + id +"&customer_id="+customer_id,
			success: function(response) {
				response = jQuery.parseJSON(response);
				// console.log(response);
				if (response.response_code == 1) {
					$("#selected_location").empty();
					$("#location_title").empty();
					$("#location_title").text(response.title);
					$("#address_id").val(response.id);
					$("#selected_location").text(response.address + ", " + response.lga + ", " + response.state + ", " + "Nigeria.");
					$("#pickup_location").modal('hide');
					$("#payOrder").attr("data-label", "pickup");
					$("#payOrder").removeClass("disabledbutton");
				}else{
					$("#location_title").empty();
					$("#selected_location").empty();
					$("#selected_location").append("<p class='text-center' style='color:red;' id='addr_msg'>Selection failed. Please select another pickup station near you.</p>");
					$("#payOrder").attr("data-label", "no_pickup");
				}
			}
		});
	});

	function toObject(arr) {
		var rv = {};
		for (var i = 0; i < arr.length; ++i)
			if (arr[i] !== undefined) rv[i] = arr[i];
		return rv;
	}

	$("#addr_msg").css("cssText", "display: none !important");
	
	$.ajax({
		type: "POST",
		url: "utilities.php",
		data: "op=customerAddresses&id=" + customer_id,
		success: function(response) {
			response = jQuery.parseJSON(response);
			// console.log(response);
			if (response.response_code == 1) {
				if (response.data != null) {
					$("#addr_p").empty();
					$("#address_id").val(response.data.id);
					$("#addr_p").text(response.data.street + ", " + response.data.lga + ", " + response.data.state + ", " + response.data.country);
					$("#addressModal").modal('hide');
					$("#payOrder").attr("data-id", "addr");
					if($("#payOrder").hasClass("disabledbutton")){
						$("#payOrder").removeClass("disabledbutton");
					}
				} else {
					$("#addr_p").empty();
					$("#addr_p").append("<p class='text-center' style='color:red;' id='addr_msg'>Please select a delivery address before you pay for your order. Click 'change' button to select an address.</p>");
					$("#payOrder").addClass("disabledbutton");
					$("#payOrder").attr("data-id", "no_addr");
					$("#changeAddrBtn").addClass("disabledbutton");
				}
			}
		}
	});
</script>