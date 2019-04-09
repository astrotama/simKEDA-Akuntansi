<?php
function apbd_resetanggaran_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('apbd_resetanggaran_main_form');
	return drupal_render($output_form);
	
}

function apbd_resetanggaran_main_form($form, &$form_state) {

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		//'#title' =>  t('Simpan'),
		'#value' => 'Reset Anggaran',
		
	);
	return $form;
}

function apbd_resetanggaran_main_form_submit($form, &$form_state) {

drupal_set_message('Reset...');	
//fixing_double();
fixing_double_uk();

}

function reset_angaran() {
drupal_set_message('Reset PPKD...');

db_delete('anggperuk')
	->condition('kodeuk', '00', '=')
	->condition('jumlah', '0', '=')
	->condition('jumlahp', '0', '=')
	->execute();

$num = db_update('anggperuk') // Table name no longer needs {}
  ->fields(array(
    'kodeuk' => '00',
  ))
  ->condition('kodeuk', '81', '=')
  ->condition('kodero', '42%', 'LIKE')
  ->execute();

$num = db_update('anggperuk') // Table name no longer needs {}
  ->fields(array(
    'kodeuk' => '00',
  ))
  ->condition('kodeuk', '81', '=')
  ->condition('kodero', '43%', 'LIKE')
  ->execute();

$num = db_update('anggperuk') // Table name no longer needs {}
  ->fields(array(
    'kodeuk' => '00',
  ))
  ->condition('kodeuk', '81', '=')
  ->condition('kodero', '413%', 'LIKE')
  ->execute();

$num = db_update('anggperuk') // Table name no longer needs {}
  ->fields(array(
    'kodeuk' => '00',
  ))
  ->condition('kodeuk', '81', '=')
  ->condition('kodero', '414%', 'LIKE')
  ->execute();

$num = db_update('kegiatanskpd') // Table name no longer needs {}
  ->fields(array(
    'kodeuk' => '00',
  ))
  ->condition('kodeuk', '81', '=')
  ->condition('isppkd', '1', '=')
  ->execute();

drupal_set_message('Reset PPKD...Ok');  


drupal_set_message('Preparing...');
db_delete('anggperuktemp')->execute();
db_delete('anggperdatemp')->execute();
drupal_set_message('Preparing...Ok');

drupal_set_message('Menyiaplan buffer rekening non anggaran...');

$res = db_query("insert into anggperuktemp (kodero, kodeuk)
select distinct jurnalitem.kodero, jurnal.kodeuk from jurnal inner join jurnalitem on jurnal.jurnalid=jurnalitem.jurnalid where jurnalitem.kodero like '4%'");	
$res = db_query("delete from anggperuktemp where concat(kodero, kodeuk) in (select concat(kodero, kodeuk) from anggperuk)");	

$res = db_query("insert into anggperdatemp (kodero)
select distinct kodero from jurnalitem where kodero like '61%'");	
$res = db_query("delete from anggperdatemp where kodero in (select kodero from anggperda)");	

drupal_set_message('Menyiaplan buffer rekening non anggaran...Ok');

drupal_set_message('Posting rekening non anggaran...');
$res = db_query("insert into anggperuk (tahun, kodero, kodeuk) select tahun, kodero, kodeuk from anggperuktemp");	
$res = db_query("insert into anggperda (tahun, kodero, kodeuk) select tahun, kodero, kodeuk from anggperdatemp");	
drupal_set_message('Posting rekening non anggaran...Ok');	
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
