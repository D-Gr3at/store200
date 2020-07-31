<?php
include_once("libs/dbfunctions.php");
include_once("class/users.php");

$dbobject = new dbobject();
$user = new Users();
$result = json_decode($user->verifyLink($_REQUEST['ga']),TRUE);
//var_dump($result);
if($result['response_code'] != 0)
{
    echo "<h3>".$result['response_message']."</h3>";
}else
{
    


?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<title>The Lord's Chosen Charismatic Revival Church</title>
	<link rel="stylesheet" href="css/parsley.css">
	<link rel="preconnect" href="http://fonts.gstatic.com/" crossorigin>
	<link rel="icon" href="img/icon.png" sizes="32x32" />
	<!-- PICK ONE OF THE STYLES BELOW -->
	 <link href="css/classic.css" rel="stylesheet"> 
	<!-- <link href="css/corporate.css" rel="stylesheet"> -->
	<!-- <link href="css/modern.css" rel="stylesheet"> -->

	<!-- BEGIN SETTINGS -->
	<!-- You can remove this after picking a style -->
	<style>
		body {
			opacity: 0;
		}
	</style>
	<script src="js/settings.js"></script>
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
   myCalendar.hideTime();
}
</script>
<body style="background: url(img/banner.jpg)">
	<main class="main d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row">
				<div class="col-sm-12 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2" style="color:#fff">Welcome <?php echo $result['data']['lastname']." ".$result['data']['firstname'] ?> to The Lord's Chosen Charismatic Revival Church</h1>
							<p class="lead" style="color:#fff">
								Sign in to your account
							</p>
						</div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Password Reset</h5>
                            </div>
                            <div class="card-body">
                                <form id="form1">
                                    <input type="hidden" name="op" value="Users.doForgotPasswordChange">
                                    <input type="hidden" name="username" value="<?php echo $result['data']['username'] ?>">

                                    <div class="form-group">
                                        <label>Enter  password</label>
                                        <input class="form-control form-control-lg" type="password" name="password" required placeholder="Enter your new password" />
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input class="form-control form-control-lg" name="confirm_password" type="password" required placeholder="Confirm your password" />
                                        <small>
                                        </small>
                                    </div>
                                    <div>


                                    </div>
                                    <div id="server_mssg"></div>
                                    <div class="text-center mt-3">
                                        <a href="javascript:saveRecord()" class="btn btn-lg btn-warning btn-block">Change Password</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <script src="js/jquery.min.js"></script>
                            <script src="js/jquery.blockUI.js"></script>
                            <script src="js/parsely.js"></script>

                            <script src="js/sweet_alerts.js"></script>
                            <script src="js/main.js"></script>
                        <script>
                            function saveRecord()
                            {
                                $("#save_facility").text("Loading......");
                                var dd = $("#form1").serialize();
                                $.post("utilities_default.php",dd,function(re)
                                {
                                    $("#save_facility").text("Save");
                                    console.log(re);
                                    if(re.response_code == 0)
                                        {
                                            alert(re.response_message);
                                            setTimeout(() => {
                                                    window.location = 'logout.php';
                                                }, 2000);
                                        }
                                    else
                                        alert(re.response_message)
                                },'json')
                            }



                        </script>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
<?php
}
?>