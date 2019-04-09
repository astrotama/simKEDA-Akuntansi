<?php

function setting_ttdlaporan_form($form, &$form_state) {
	
	$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	if ($current_url != $referer)
		$_SESSION["setting_ttdlaporan"] = $referer;
	else
		$referer = $_SESSION["setting_ttdlaporan"];
	
	$bupatinama = variable_get('bupatinama', '');
	$bupatijabatan = variable_get('bupatijabatan', '');
	$wabupnama = variable_get('wabupnama', '');
	$wabupjabatan = variable_get('wabupjabatan', '');
	$wabupjabatanatasnama = variable_get('wabupjabatanatasnama', '');
	$kepalanama = variable_get('kepalanama', '');
	$kepalajabatan = variable_get('kepalajabatan', '');
	$kepalanip = variable_get('kepalanip', '');
	$kepalajabatanatasnama = variable_get('kepalajabatanatasnama', '');
	$sekretarisnama = variable_get('sekretarisnama', '');
	$sekretarisjabatan = variable_get('sekretarisjabatan', '');
	$sekretarisnip = variable_get('sekretarisnip', '');
	$sekretarisjabatanatasnama = variable_get('sekretarisjabatanatasnama', '');

	$setdanama = variable_get('setdanama', '');
	$setdajabatan = variable_get('setdajabatan', '');
	$setdanip = variable_get('setdanip', '');
	$setdajabatanatasnama = variable_get('setdajabatanatasnama', '');

	$kabidnama = variable_get('kabidnama', '');
	$kabidjabatan = variable_get('kabidjabatan', '');
	$kabidnip = variable_get('kabidnip', '');
	$kabidjabatanatasnama = variable_get('kabidjabatanatasnama', '');
	
	$ttdlaporan = variable_get('ttdlaporan', '');
	
	$form['referer'] = array (
		'#type' => 'value',
		'#value' => $referer,
	);
	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD', 'SEKRETARIS DAERAH', 'SEKRETARIS DINAS', 'KABID AKUNTANSI');
	$form['ttdlaporan']= array(
		'#type'         => 'select', 
		'#title' =>  t('PENANDA TANGAN LAPORAN'),
		'#options' => $penandatangan,
		'#default_value'=> $ttdlaporan, 
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_ttdlaporan',
			'wrapper' => 'ttdlaporan-wrapper',
		),					
	);				

	// Wrapper for rekdetil dropdown list
	$form['wrapperttdlaporan'] = array(
		'#prefix' => '<div id="ttdlaporan-wrapper">',
		'#suffix' => '</div>',
	);
	
	if (isset($form_state['values']['ttdlaporan'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$ttdlaporans = $form_state['values']['ttdlaporan'];
	} else {
		$ttdlaporans = $ttdlaporan;
	}
	//drupal_set_message($ttdlaporan);
	if ($ttdlaporans == '0') {
		
	
	
	//drupal_set_message($ttdlaporans);
	$form['wrapperttdlaporan']['formbupati'] = array ( 
		'#type' => 'fieldset',
		'#title'=> 'BUPATI',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formbupati']['bupatinama'] = array (
			'#title'=> 'Nama',
			'#type' => 'textfield',
			'#default_value' => $bupatinama,
		);
		$form['wrapperttdlaporan']['formbupati']['bupatijabatan'] = array (
			'#type' => 'textfield',
			'#title'=> 'Jabatan',
			'#default_value' => $bupatijabatan,
		);
	} else if ($ttdlaporans == '1') {
	
	$form['wrapperttdlaporan']['formwabup'] = array (
		'#type' => 'fieldset',
		'#title'=> 'WAKIL BUPATI',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formwabup']['wabupnama'] = array(
		  '#type' =>'textfield', 
		  '#title'=> 'Nama',
		  '#default_value' => $wabupnama,
		);
		$form['wrapperttdlaporan']['formwabup']['wabupjabatan'] = array (
			'#type' => 'textfield',
			'#title'=> 'Jabatan',
			'#default_value' => $wabupjabatan,
		);
		$form['wrapperttdlaporan']['formwabup']['wabupjabatanatasnama'] = array (
			'#type' => 'textfield',
			'#title'=> 'Atas Nama',
			'#default_value' => $wabupjabatanatasnama,
		);
	} else if ($ttdlaporans == '2') {
	
	$form['wrapperttdlaporan']['formkepala'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KEPALA BPKAD',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formkepala']['kepalanama'] = array(
		  '#type' =>'textfield', 
		  '#title'=> 'Nama',
		  '#default_value' => $kepalanama,
		);
		$form['wrapperttdlaporan']['formkepala']['kepalajabatan'] = array (
			'#type' => 'textfield',
			'#title'=> 'Jabatan',
			'#default_value' => $kepalajabatan,
		);
		$form['wrapperttdlaporan']['formkepala']['kepalanip'] = array (
			'#type' => 'textfield',
			'#title'=> 'NIP',
			'#default_value' => $kepalanip,
		);
		$form['wrapperttdlaporan']['formkepala']['kepalajabatanatasnama'] = array (
			'#type' => 'value',			
			'#value' => '',
		);
	} else if ($ttdlaporans == '3') {
	
	$form['wrapperttdlaporan']['formsetda'] = array (
		'#type' => 'fieldset',
		'#title'=> 'SEKRETARIS DAERAH',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formsetda']['setdanama'] = array(
		  '#type' =>'textfield', 
		  '#title'=> 'Nama',
		  '#default_value' => $setdanama,
		);
		$form['wrapperttdlaporan']['formsetda']['setdajabatan'] = array (
			'#type' => 'textfield', 
			'#title'=> 'Jabatan',
			'#default_value' => $setdajabatan,
		);
		$form['wrapperttdlaporan']['formsetda']['setdanip'] = array (
			'#type' => 'textfield', 
			'#title'=> 'NIP',
			'#default_value' => $setdanip,
		);
		$form['wrapperttdlaporan']['formsetda']['setdajabatanatasnama'] = array (
			'#type' => 'textfield',
			'#title'=> 'Atas Nama',
			'#default_value' => $setdajabatanatasnama,
		);
	} else if ($ttdlaporans == '4') {
	
	$form['wrapperttdlaporan']['formsekretaris'] = array (
		'#type' => 'fieldset',
		'#title'=> 'SEKRETARIS DINAS',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formsekretaris']['sekretarisnama'] = array(
		  '#type' =>'textfield', 
		  '#title'=> 'Nama',
		  '#default_value' => $sekretarisnama,
		);
		$form['wrapperttdlaporan']['formsekretaris']['sekretarisjabatan'] = array (
			'#type' => 'textfield', 
			'#title'=> 'Jabatan',
			'#default_value' => $sekretarisjabatan,
		);
		$form['wrapperttdlaporan']['formsekretaris']['sekretarisnip'] = array (
			'#type' => 'textfield', 
			'#title'=> 'NIP',
			'#default_value' => $sekretarisnip,
		);
		$form['wrapperttdlaporan']['formsekretaris']['sekretarisjabatanatasnama'] = array (
			'#type' => 'textfield',
			'#title'=> 'Atas Nama',
			'#default_value' => $sekretarisjabatanatasnama,
		);
	} else  {
	
	$form['wrapperttdlaporan']['formkabid'] = array (
		'#type' => 'fieldset',
		'#title'=> 'KEPALA BIDANG AKUTANSI',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
		$form['wrapperttdlaporan']['formkabid']['kabidnama'] = array(
		  '#type' =>'textfield', 
		  '#title'=> 'Nama',
		  '#default_value' => $kabidnama,
		);
		$form['wrapperttdlaporan']['formkabid']['kabidjabatan'] = array (
			'#type' => 'textfield', 
			'#title'=> 'Jabatan',
			'#default_value' => $kabidjabatan,
		);
		$form['wrapperttdlaporan']['formkabid']['kabidnip'] = array (
			'#type' => 'textfield', 
			'#title'=> 'NIP',
			'#default_value' => $kabidnip,
		);
		$form['wrapperttdlaporan']['formkabid']['kabidjabatanatasnama'] = array (
			'#type' => 'textfield',
			'#title'=> 'Atas Nama',
			'#default_value' => $kabidjabatanatasnama,
		);
	}
	
	$form['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
				


	
	return $form;
}


function setting_ttdlaporan_form_submit($form, &$form_state) {
	$referer = $form_state['values']['referer'];
	
	$ttdlaporan= $form_state['values']['ttdlaporan'];
	
	variable_set('ttdlaporan', $ttdlaporan);
	
	if ($ttdlaporan == '0') {
		$bupatinama = $form_state['values']['bupatinama'];
		$bupatijabatan= $form_state['values']['bupatijabatan'];
		
		variable_set('bupatinama', $bupatinama);
		variable_set('bupatijabatan', $bupatijabatan);
	} else if ($ttdlaporan == '1') {
		$wabupnama = $form_state['values']['wabupnama'];
		$wabupjabatan= $form_state['values']['wabupjabatan'];
		$wabupjabatanatasnama= $form_state['values']['wabupjabatanatasnama'];
		
		variable_set('wabupnama', $wabupnama);
		variable_set('wabupjabatan', $wabupjabatan);
		variable_set('wabupjabatanatasnama', $wabupjabatanatasnama);
	} else if ($ttdlaporan == '2') {
		$kepalanama = $form_state['values']['kepalanama'];
		$kepalajabatan= $form_state['values']['kepalajabatan'];
		$kepalajabatanatasnama= $form_state['values']['kepalajabatanatasnama'];
		$kepalanip= $form_state['values']['kepalanip'];
		
		variable_set('kepalanama', $kepalanama);
		variable_set('kepalajabatan', $kepalajabatan);
		variable_set('kepalajabatanatasnama', $kepalajabatanatasnama);
		variable_set('kepalanip', $kepalanip);

	} else if ($ttdlaporan == '3') {
		$setdanama = $form_state['values']['setdanama'];
		$setdajabatan= $form_state['values']['setdajabatan'];
		$setdanip= $form_state['values']['setdanip'];
		$setdajabatanatasnama= $form_state['values']['setdajabatanatasnama'];
		
		variable_set('setdanama', $setdanama);
		variable_set('setdajabatan', $setdajabatan);
		variable_set('setdanip', $setdanip);
		variable_set('setdajabatanatasnama', $setdajabatanatasnama);
		
	} else if ($ttdlaporan == '4') {
		$sekretarisnama = $form_state['values']['sekretarisnama'];
		$sekretarisjabatan= $form_state['values']['sekretarisjabatan'];
		$sekretarisnip= $form_state['values']['sekretarisnip'];
		$sekretarisjabatanatasnama= $form_state['values']['sekretarisjabatanatasnama'];
		
		variable_set('sekretarisnama', $sekretarisnama);
		variable_set('sekretarisjabatan', $sekretarisjabatan);
		variable_set('sekretarisnip', $sekretarisnip);
		variable_set('sekretarisjabatanatasnama', $sekretarisjabatanatasnama);
		
	} else {
		$kabidnama = $form_state['values']['kabidnama'];
		$kabidjabatan= $form_state['values']['kabidjabatan'];
		$kabidnip= $form_state['values']['kabidnip'];
		$kabidjabatanatasnama= $form_state['values']['kabidjabatanatasnama'];
		
		variable_set('kabidnama', $kabidnama);
		variable_set('kabidjabatan', $kabidjabatan);
		variable_set('kabidnip', $kabidnip);
		variable_set('kabidjabatanatasnama', $kabidjabatanatasnama);
	}
	
	drupal_goto($referer);
		
}

function _ajax_ttdlaporan($form, $form_state) {
	
	return $form['wrapperttdlaporan'];
	
}

?>