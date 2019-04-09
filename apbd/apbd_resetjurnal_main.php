<?php
function apbd_resetjurnal_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('apbd_resetjurnal_main_form');
	return drupal_render($output_form);
	
}

function apbd_resetjurnal_main_form($form, &$form_state) {

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		//'#title' =>  t('Simpan'),
		'#value' => 'Reset',
		
	);
	return $form;
}
function apbd_resetjurnal_main_form_submit($form, &$form_state) {
/*
db_delete('jurnal')->execute();
db_delete('jurnalitem')->execute();
db_delete('jurnalitemlo')->execute();
db_delete('jurnalitemlra')->execute();

db_delete('apbdtrans')->execute();
db_delete('apbdtransitem')->execute();

//DOKUMEN
db_set_active('penatausahaan');
$query = db_update('dokumen')
->fields(
		array(
			'jurnalsudah' => 0,
		)
	);
$query->execute();
db_set_active();
*/

}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */


?>
