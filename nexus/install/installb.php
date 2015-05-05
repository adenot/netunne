<?php
	$step=4;
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/top.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/include/mask_list.php");
?>

<script language="Javascript">

function validaSenha(SENHA, CONFIRMACAO) {
	
	if(SENHA==CONFIRMACAO){
		if(SENHA=='')
			return false;
		return true;
	}else{
		return false;
	}
}

function validaForm(FORM, ACTION) {

	if(!validaSenha(FORM.manager_password.value, FORM.confirm_password.value)) {
		alert("<?=_("Make sure the 'Manager Password' and the 'Confirm Password' fields \\n contain the same input and aren't blank.")?>");
		return false;
	}
	
	re = /^\w+$/i;
	if (!FORM.manager_password.value.match(re)) {
		alert("<?=_("The password must contain just letters or numbers.")?>");
		return false;
	}
	
	if(FORM.ip.value == '' || FORM.mask.value == '') {
		alert("<?=_("You must specify an IP and a Mask.")?>");
		return false;
	}
	FORM.action = ACTION;
	FORM.submit();
}

function passKeyup () {
	if(validaSenha(hd_info.manager_password.value, hd_info.confirm_password.value))
		document.getElementById('passverify').src='images/true.png';
	else 
		document.getElementById('passverify').src='images/false.png';
}

</script>

<form name="hd_info" method="POST" action="act_install.php?pag=2">
<table align="center" width="80%" class=description cellpadding=0 cellspacing=8>
<tr><td height="20" colspan=2>&nbsp;</td></tr>
<tr><td colspan=3>
<b><?=_("Administrator Password")?></b>
</td></tr>
<tr>
	<td align="right">
		<br><?=_("You must set a password for the user admin")?><br>&nbsp;
	</td>
	<td bgcolor="#DFDFDF" width=3></td>
	<td valign=middle>
		<table align="center" width="100%" class=description>
			<tr>
				<td>
				<?=_("Password")?>
				</td>
				<td>
					<input type="password" value="<?=$_SESSION['password']?>" name="manager_password" style="width:180px;" onkeyup="passKeyup();">
				</td>
			</tr>
			<tr>
				<td>
				<?=_("Confirm")?>
				</td>
				<td>
				<input type="password" value="<?=$_SESSION['password']?>" name="confirm_password" style="width:180px;" onkeyup="passKeyup();">
				<!--<input type="text" name="checkpass" value="No!" readonly style="width:30px;text-align:left;background-color:#FFFFFF;border:0;">-->
				<img src="images/false.png" align=texttop id="passverify" width=16 height=16>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr><td height="20" colspan=3>&nbsp;</td></tr>
<tr><td height="20" colspan=3>
<b><?=_("Network Card")?></b>
</td>
</tr>
<tr>
	<td align="right">
		<br><?=_("Please enter an IP and netmask for the first network card found in your computer")?><br>&nbsp;
	</td>
	<td bgcolor="#DFDFDF" width=2></td>
	<td valign=middle>
		<table align="center" width="100%" class=description>
			<tr>
				<td>
				<?=_("IP")?>
				</td>
				<td>
				<input type="text" name="ip" value="<?=$defip?>" style="width:180px;">
				</td>
			</tr>
				<td>				
				<?=_("Netmask");?>
				</td>
				<td>
				<select name="mask" style="width:180px">
				<?php
				foreach ($listaMask as $masknum)
					echo "<option value=\"$masknum\" ".iif($defmask==$masknum,"selected","").">$masknum</option>";
				?>
				</select>
				</td>
			</tr>
			<tr>
				<td colspan=2 class=smalldescription>
				<?=_("This IP must be accessible from your local network in order to allow remote logon")?>
				</td>
			</tr>				
		</table>
	</td>
</tr>

<tr><td height="20" colspan=3>&nbsp;</td></tr>
</table>

<p align=center>
<input type="submit" class="button-left" value="<?=_("Back")?>" name="backbutton">
&nbsp;&nbsp;
&nbsp;&nbsp;
<input type="button" onclick="validaForm(hd_info, 'act_install.php?pag=2')" class="button-right" value="<?=_("Proceed")?>" name="proceed">
</p>

<script>passKeyup();</script>
<?php
	require_once("include/bottom.php");
?>
