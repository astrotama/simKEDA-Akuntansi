<?php
function laporan_resume_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('laporan_resume_main_form');	
		
	return drupal_render($output_form) . $btn . $output . $btn;
		
}

function laporan_resume_main_form($form, &$form_state) {
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '01' => t('JANUARI'), 	
			 '02' => t('FEBRUARI'),
			 '03' => t('MARET'),	
			 '04' => t('APRIL'),	
			 '05' => t('MEI'),	
			 '06' => t('JUNI'),	
			 '07' => t('JULI'),	
			 '08' => t('AGUSTUS'),	
			 '09' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   ),
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Resume',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function laporan_resume_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	resumedata($bulan);
}

function resumedata($bulan) {

//PEMBIAYAAN
$query = db_update('anggperda_rea')
->fields(  
		array(
			'realisasi' . $bulan => 0,
		)
	);
$res = $query->execute();		

$resu_rea = db_query('select ji.kodero,sum(ji.kredit-ji.debet) as kd,sum(ji.debet-ji.kredit) as dk from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where month(j.tanggal)<=:bulan group by ji.kodero', array(':bulan' => $bulan));
foreach ($resu_rea as $data_rea) {
	
	if (substr($data_rea->kodero,0,2)=='61')
		$rea = $data_rea->kd;
	else
		$rea = $data_rea->dk;
	if ($rea=='') $rea = 0;
	
	$query = db_update('anggperda_rea')
	->fields(  
			array(
				'realisasi' . $bulan => $rea,
			)
		);
	$query->condition('kodero', $data_rea->kodero, '=');
	$res = $query->execute();		
}

//PENDAPATAN
$query = db_update('anggperuk_rea')
->fields(  
		array(
			'realisasi' . $bulan => 0,
		)
	);
$res = $query->execute();		

$resu_rea = db_query('select j.kodeuk, ji.kodero, sum(ji.kredit-ji.debet) as rea from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where month(j.tanggal)<=:bulan group by j.kodeuk, ji.kodero', array(':bulan' => $bulan));
foreach ($resu_rea as $data_rea) {
	
	$rea = $data_rea->rea;
	if ($rea=='') $rea = 0;
	
	$query = db_update('anggperuk_rea')
	->fields(  
			array(
				'realisasi' . $bulan => $rea,
			)
		);
	$query->condition('kodeuk', $data_rea->kodeuk, '=');
	$query->condition('kodero', $data_rea->kodero, '=');
	$res = $query->execute();		
}


//BELANJA
$query = db_update('anggperkeg_rea')
->fields(  
		array(
			'realisasi' . $bulan => 0,
		)
	);
$res = $query->execute();	

$resu_rea = db_query('select j.kodekeg, ji.kodero, sum(ji.debet-ji.kredit) as rea from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where month(j.tanggal)<=:bulan group by j.kodekeg, ji.kodero', array(':bulan' => $bulan));
foreach ($resu_rea as $data_rea) {
	
	$rea = $data_rea->rea;
	if ($rea=='') $rea = 0;
	
	$query = db_update('anggperkeg_rea')
	->fields(  
			array(
				'realisasi' . $bulan => $rea,
			)
		);
	$query->condition('kodekeg', $data_rea->kodekeg, '=');
	$query->condition('kodero', $data_rea->kodero, '=');
	$res = $query->execute();		
}
	
	
}

?>


