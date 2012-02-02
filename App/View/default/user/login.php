<div class="span10" id="user-signup-page">

	<h1>Login To Your Account</h1>
	<form id="user-form" method="post" action="" class="form-stacked">

		<fieldset>
			<legend><img src="<?=$baseUrl;?>images/user/password.png">Password</legend>
			<div class="clearfix ">
				<div class="input">
					<input class="span5 validate[required]" onblur="validateConfirmation();" type="text" name="email" id="email" placeholder="Email Address">
					<span rel="email" class="help-inline"></span>
				</div>
			</div>
			<div class="clearfix ">
				<div class="input">
					<input class="span5 validate[required,minSize[5]]" onblur="validateConfirmation();" type="password" name="password" id="password" placeholder="Password">
					<span rel="password" class="help-inline"></span>
				</div>
			</div>
			<p><a href="<?=$baseUrl;?>user/forgotpw">Forgotten Password?</a></p>
		</fieldset>
			
		<div class="actions">
			<button class="btn primary" type="submit">Register</button>
		</div>
	
	</form>

</div>