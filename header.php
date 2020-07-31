<div id="main-nav" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
			</button>
			<a class="navbar-brands" href="#home" onclick="javascript:location.href = 'index.php'">
				<img id="navlogo" src="img/logo-green-black-text.png" alt="200 store" width="122" height="45">
			</a>

		</div>
		<div class="collapse navbar-collapse">
			<ul id="navigation" class="nav navbar-nav navbar-right text-center">
				<li><a href="" onclick="javascript:location.href = 'index.php#hero'">Home</a></li>
				<li><a href="" onclick="javascript:location.href = 'index.php#products'">Products</a></li>
				<li><a href="" onclick="javascript:location.href = 'index.php#about'">About</a></li>
				<li><a href="" onclick="javascript:location.href = 'index.php#contact'">Contact</a></li>
				<li><a href="" onclick="javascript:location.href = 'index.php#orderform'">Cart <span class="badge badge-infos cart-items-count">0</span></a></li>
				<?php
				if (!isset($_SESSION["customer_id"])) {
				?>
					<li><a href="#" onclick="javascript:location.href = 'login.php'">Login</a></li>
					<li><a href="#" onclick="javascript:location.href = 'register.php'">Register</a></li>
				<?php } else { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="background-color: white!important;">Hi, <?php echo $_SESSION["first_name"]; ?><span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="" onclick="javascript:location.href = 'user_dashboard.php'" id="acct">Account</a></li>
							<!-- <li><a href="" onclick="javascript:location.href = 'user_dashboard.php?type=\'order\''" id="orders">Orders</a></li> -->
							<!-- <li><a href="#">France</a></li> -->
							<li class="divider"></li>
							<li><a type="button" class="btn btn-sm pl-3" onclick="javascript:location.href = 'logout.php'">logout</a></li>
						</ul>
					</li>
				<?php } ?>

			</ul>
		</div>

	</div>
</div>

<script>
	$(document).ready(function() {
		
		// $("#orders").click(function() {
		// 	// location.href = "user_dashboard.php";
		// 	$("#link_content").empty();
		// 	console.log($("#orders"));
		// });
	})
</script>