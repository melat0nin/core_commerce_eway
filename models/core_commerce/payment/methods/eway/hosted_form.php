<?php   $form = Loader::helper('form'); ?>

<?php   foreach($fields as $key => $value) { ?>

	<input type="hidden" name="<?php  echo $key?>" value="<?php  echo $value?>" />

<?php   } ?>

<?php  echo t("Click 'Next' to proceed to eWay to finish your order."); ?>
