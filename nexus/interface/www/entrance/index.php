<?php
	/****************************************************************
	*																*
	* 			Console Tecnologia da Informa��o Ltda				*
	* 				E-mail: contato@console.com.br					*
	* 				Arquivo Criado em Sep 5, 2006					*
	*																*
	****************************************************************/
	include "../common.php";
	
	
?>
<HTML style="width:100%;height:100%">
    <SCRIPT language="JavaScript" src="<?=DIRJS?>main.js"></SCRIPT>
    <SCRIPT>var blackout_opac;</SCRIPT>

<style>
.eventbox {
	font-family:Verdana;
	font-size:10px;
	color:#AAEEAA;
	margin:10px 10px 10px 10px;
	width:314px;
	height:106px;
	overflow:hidden;
}
.eventbox:first-line {
	color:#FFFFFF;
}
</style>						   

<BODY 
	style="background-color: #101C06;
		   width:100%;
		   height:100%;
		   margin:0px;
		   background-image: url(<?=DIRIMG?>/entrance/bg-righttop.jpg);
		   background-repeat: no-repeat;
		   background-position:100% 0%;
		   "
	onload="parent.document.title='<?=constant("PRODNAME")?>';pageunblackout(this);document.getElementById('passbox').focus();getevents();"  onunload="pageblackout(this);">
<table align=center border=0 cellspacing=0 cellpadding=0 height="100%" width="100%">
	<tr>
		<!--<td rowspan=2 align="left" valign="bottom"><img src="<?=DIRIMG?>/entrance/bg-leftbottom.jpg"></td>-->
		<td colspan=2 valign="bottom" align="center" height="100%">
<TABLE align=center border=0 cellspacing=0 cellpadding=0>
	<form action="auth.php" name="authform" id="authform" method="POST">
	<input type="hidden" name="user" value="admin">
	<tr>
		<td colspan=3><img src="<?=DIRIMG?>/entrance/logo.jpg"></td>
	</tr>
	<tr>
		<td align=right><img src="<?=DIRIMG?>/entrance/pass-left.jpg"></td>
		<td width="204" style="background-image: url(<?=DIRIMG?>/entrance/pass-bg.jpg);">
			<table border=0 cellspacing=0 cellpadding=0 width="100%">
				<tr>
				<td width=49 align=center><span style="font-family:Verdana;font-size:10px;color:white;"><?=_("Password")?></span></td>
				<td><table style="margin-left:3px;" border=0 cellspacing=0 cellpadding=0>
				<tr><td><img src="<?=DIRIMG?>/entrance/passbox-left.jpg"></td>  
				<td><input 
					type="password" 
					name="pass"
					id="passbox"
					<?php if ($_GET[error]=="have_auth") { 
						echo "value='".$_SESSION["have_auth"]["password"]."'\n";
					} ?>
					style="
						width:140px;
						height:20px;
						border:1px solid #2F4B0E;
						padding: 1px 4px 1px 4px;
						font-family:Verdana;
						font-size:12px;
						color:#4E7E1B;
						"></td>
				<td><img 
					src="<?=DIRIMG?>/entrance/passbox-right.jpg"></td>
				</tr>
				</table>
				</td>
				</tr>
			</table>
		</td>
		<td><img 
			style="cursor:pointer;"
			onclick="getElementById('authform').submit();"
			src="<?=DIRIMG?>/entrance/pass-right.jpg"></td>
	</tr>
	<?php
		if ($_GET[error]) {
			
			switch ($_GET[error]) {
				case "login_error":
					$msg = _("Invalid Password");
					break;
				case "login_expired":
					$msg = _("Login expired. Please Log again");
					break;
				case "have_auth":
					echo "<input type=\"hidden\" name=\"action\" value=\"have_auth\">\n";
					$msg = sprintf(_("ip %s logged in. confirm?"),$_GET[ip]);
					break;
			}
			
	?>
	<tr>
		<td colspan=3 align="center" style="
			background-image: url(<?=DIRIMG?>/entrance/msgbox.jpg);
			padding:5px 25px 3px 25px;
			font-family:Verdana;
			font-size:9px;
			color:black;
			height:26px;
			"><?=$msg?>
			</td>
	</tr>
	<?php
		}
	?>
	</form>
</table><!--
<table border="1" width="100%">
  <tr>
    <td width="33%" rowspan="3">a</td>
    <td width="33%">1</td>
    <td width="34%" rowspan="3">b</td>
  </tr>
  <tr>
    <td width="33%">2</td>
  </tr>
  <tr>
    <td width="33%">3</td>
  </tr>
</table>



	<?php
		if ($_GET[error]=="have_auth") {
	?>
	<TABLE style="border:1px solid black;background-color:#eeeeee" align=center border=0 cellspacing=6 cellpadding=0>
		<TR><td><form action="auth.php" method="POST">
			<input type="hidden" name="action" value="have_auth">
			<?=$_GET[ip]?><input type="submit" name="have_auth" value="login">
			</form>
		</td></TR>
	</table>
	<?php
		} else {
	?>
	<TABLE style="border:1px solid black;background-color:#eeeeee" align=center border=0 cellspacing=6 cellpadding=0>
		<TR><td>
			<form action="auth.php" method="POST">
			<input type="textbox" size=20 value="admin" name="user"><br>
			<input type="password" size=20 name="pass"></br>
			<input type="submit" name="submit" value="Login">
			</form>
		</td>
		</tr>
	</table>
	<?php } ?>
	-->
	<p><BR><BR></p>
		</td>
	</tr>
	<tr>
		<td align="left" valign="bottom"><img src="<?=DIRIMG?>/entrance/bg-leftbottom.jpg"></td>

		<td 
			align="right" valign="bottom">
			<table
				id="eventbox_table"
				align="right"
				style="
					visibility:hidden;
					margin:0px 30px 30px 0px;
					_margin:0px 10px 40px 0px;
					" 
				background="<?=DIRIMG?>/entrance/eventbox.jpg" border=0 cellpadding=0 cellspacing=0 
				width=334 height=126>
				<tr>
					<td>
					<div id="eventbox" 
						class="eventbox"
					></div></td>
				</tr>
			</table>
		</td>
	</tr>	
</table>
	
</body></HTML>
  
  