<?php
function cetak_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	//$setorid = arg(2);	
	if(arg(3)=='pdf'){		
		/*$url = url(current_path(), array('absolute' => TRUE));		
		$url = str_replace('/pdf', '', $url);
		
		$output = printspm($kodekeg);
		apbd_ExportSPM($output, 'SPM', $url);*/
	
	} else {
	
		drupal_set_title('Cetak Laporan');
		$output_form = drupal_get_form('cetak_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function cetak_main_form($form, &$form_state) {
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	//drupal_set_message($kodeuk);
	$form['formdokumen']['kodeuk']= array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);	
	$form['formdokumen']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => 10,
	);	
	//drupal_set_message(date('j F Y'));
	$form['formdokumen']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' =>date('j F Y') ,
	);	
	$form['formdokumen']['cetak']= array(
		'#type' => 'submit',
		'#value' => 'CETAK',
	);	
    
	//CETAK BAWAH
	
	
	return $form;
}

function cetak_main_form_validate($form, &$form_state) {
	//$sppno = $form_state['values']['sppno'];
		
}
	
function cetak_main_form_submit($form, &$form_state) {
	$margin = $form_state['values']['margin'];
	$tanggal = $form_state['values']['tanggal'];
	$kodeuk=apbd_getuseruk();
	if($kodeuk==null)
		$kodeuk='81';
	$bulan+=1;
	drupal_goto('laporanrekappen/'.$bulan.'/'.$margin.'/'.$tanggal);

}



?>
