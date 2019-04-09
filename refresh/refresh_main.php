<?php
function refresh_main($arg=NULL, $nama=NULL) {
	$output_form = drupal_get_form('refresh_main_form');
	return drupal_render($output_form);// . $output;
}

function refresh_main_form($form, &$form_state) {
	
	$form['formdata']['clear']= array(
		'#type' => 'submit',
		'#value' =>  'Clear Cache',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	$form['formdata']['unblock']= array(
		'#type' => 'submit',
		'#value' =>  'Unblock',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
	);
	
	return $form;
}

function refresh_main_form_submit($form, &$form_state) {
	if ($form_state['clicked_button']['#value'] == $form_state['values']['clear']) {
		$num = db_delete('cache')
		  ->execute();
		$num1 = db_delete('cache_block')
		  ->execute();
		$num2 = db_delete('cache_bootstrap')
		  ->execute();
		$num3 = db_delete('cache_views')
		  ->execute();
		$num4 = db_delete('cache_field')
		  ->execute();
		$num5 = db_delete('cache_filter')
		  ->execute();
		$num6 = db_delete('cache_form')
		  ->execute();
		$num7 = db_delete('cache_image')
		  ->execute();
		$num8 = db_delete('cache_libraries')
		  ->execute();
		$num9 = db_delete('cache_menu')
		  ->execute();
		$num10 = db_delete('cache_page')
		  ->execute();
		$num11 = db_delete('cache_path')
		  ->execute();
		$num12 = db_delete('cache_update')
		  ->execute();
		$num13 = db_delete('cache_views_data')
		  ->execute();
		 
		 drupal_set_message('berhasil hapus cache');
		
	}elseif($form_state['clicked_button']['#value'] == $form_state['values']['unblock']){
		$num = db_delete('flood')
		  ->execute();
		  
		  drupal_set_message('berhasil hapus flood');
	}
}
	
?>