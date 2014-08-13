<?php 
if($form == 'hosted') {
	require_once dirname(__FILE__) . '/hosted_form.php';
} else {
	require_once dirname(__FILE__) . '/xml_form.php';
}

