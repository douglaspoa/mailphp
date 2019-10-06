<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V1</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="public/images/icons/favicon.ico"/>
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/vendor/select2/select2.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="public/css/util.css">
	<link rel="stylesheet" type="text/css" href="public/css/main.css">
	<!--===============================================================================================-->
</head>
<body>

<div class="limiter">
	<div class="container-login100">
		<div class="wrap-login100">
			<div class="login100-pic js-tilt" data-tilt>
				<img src="public/images/img-01.png" alt="IMG">
			</div>
			    <?= form_open('mail/email'); ?>
					<span class="login100-form-title">
						Email Login
					</span>

				<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
					<?php
					$data= array(
						'type' => 'email',
						'name' => 'email',
						'placeholder' => 'Email',
						'class' => 'input100'
					);
					echo form_input($data);

					?>
					<span class="focus-input100"></span>
					<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
				</div>

				<div class="wrap-input100 validate-input" data-validate = "Password is required">
					<?php
					$data= array(
						'type' => 'password',
						'name' => 'password',
						'placeholder' => 'Senha',
						'class' => 'input100'
					);
					echo form_input($data);
					?>
					<span class="focus-input100"></span>
					<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
				</div>

				<div class="container-login100-form-btn">
					<?php
					$data = array(
						'type' => 'submit',
						'value'=> 'Submit',
						'class'=> 'login100-form-btn'
					);
					echo form_submit($data); ?>
				</div>
				<?= form_close();?>

		</div>
	</div>
</div>




<!--===============================================================================================-->
<script src="public/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="public/vendor/bootstrap/js/popper.js"></script>
<script src="public/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="public/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="public/vendor/tilt/tilt.jquery.min.js"></script>
<script >
    $('.js-tilt').tilt({
        scale: 1.1
    })
</script>
<!--===============================================================================================-->
<script src="public/js/main.js"></script>

</body>
</html>
