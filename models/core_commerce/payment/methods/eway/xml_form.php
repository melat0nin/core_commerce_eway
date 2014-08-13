<?php   $form = Loader::helper('form'); ?>

<div id="ccm-core-commerce-checkout-form-payment" class="ccm-core-commerce-checkout-form">
<h1><?php  echo t('Payment Information')?></h1>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="50%">Credit Card Name</td>
	<td><?php  echo $form->text('x_card_name')?></td>
</tr>
<tr>
    <td width="50%">Credit Card Number</td>
    <td><?php  echo $form->text('x_card_num')?> <span style="font-size:9px">(enter number without spaces or dashes)</span></td>
</tr>
<tr>
    <td>Expiration Date</td>
	<td><select name="x_exp_date_mon">
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">September</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option>
		</select>
		<select name="x_exp_date_year">
			<?php 
				$s = date('Y'); $e = $s + 15;
				for ($i = $s; $i <= $e; $i++) { ?>
					<option value="<?php echo  substr($i, -2) ?>"><?php echo  $i ?></option>
			<?php  } ?>
		</select>
		</td>
</tr>
<tr>
    <td>Card Code (CCV)</td>
    <td><?php  echo $form->text('x_card_code')?> <span style="font-size:9px">(3 or 4 digit number)</span></td>
</tr>
</table>

</div>

<?php  echo t("Click 'Next' to submit your payment for processing by E-Way."); ?>
