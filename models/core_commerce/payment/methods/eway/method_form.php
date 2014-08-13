<?php   $form = Loader::helper('form'); ?>

<style type="text/css">
.eway_method_form li { clear: both; margin-top: 20px; }
.eway_method_form label { float: left; width: 180px; font-weight: bold; margin-right: 20px; height: 30px; }
</style>

<fieldset>
<legend><?php echo  t('E-Way Configuration') ?></legend>

<ul class="eway_method_form" style="list-style: none;">

<li><label><?php  echo t('E-Way API Login ID')?> <span class="ccm-required">*</span></label>
	<?php  echo $form->text('PAYMENT_METHOD_EWAY_API_LOGIN', $PAYMENT_METHOD_EWAY_API_LOGIN)?>
</li>

<li><label><?php  echo t('E-Way API') ?><span class="ccm-required">*</span></label>
	<?php  echo $form->select('PAYMENT_METHOD_EWAY_API_TYPE', array('XML' => 'XML API', 'HOSTED' => 'eWay Shared API'), $PAYMENT_METHOD_EWAY_API_TYPE)?>
</li>

<li>
	<label><?php  echo t('Gateway Mode')?> <span class="ccm-required">*</span></label>
	<div style="float: left; margin-bottom: 20px;">
	<?php  echo $form->radio('PAYMENT_METHOD_EWAY_MODE', 'REAL-TIME', $PAYMENT_METHOD_EWAY_MODE == 'REAL-TIME')?><?php  echo t('Real Time')?> <br/>
	<?php  echo $form->radio('PAYMENT_METHOD_EWAY_MODE', 'REAL-TIME-CVN', $PAYMENT_METHOD_EWAY_MODE == 'REAL-TIME-CVN')?><?php  echo t('Real Time with CVN')?><br/>
	<?php  echo $form->radio('PAYMENT_METHOD_EWAY_MODE', 'GEO-IP-ANTI-FRAUD', $PAYMENT_METHOD_EWAY_MODE == 'GEO-IP-ANTI-FRAUD')?><?php  echo t('Beagle Anti-Fraud')?>
	</div>
</li>

<li>
	<label><?php  echo t('Live Transactions')?> <span class="ccm-required">*</span></label>
	<div style="float: left; margin-bottom: 20px;">
	<?php  echo $form->radio('PAYMENT_METHOD_EWAY_LIVE', 'true', $PAYMENT_METHOD_EWAY_LIVE == 'true')?><?php  echo t('Enabled')?> <br/>
	<?php  echo $form->radio('PAYMENT_METHOD_EWAY_LIVE', 'false', $PAYMENT_METHOD_EWAY_LIVE == 'false')?><?php  echo t('No (Testing Mode)')?>
	</div>
</li>

</ul>

</fieldset>
<br/>
