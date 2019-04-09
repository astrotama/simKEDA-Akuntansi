<?php

function pendapatan_chart_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';

	$namasingkat = 'SELURUH SKPD';
    
	$bulan = arg(2);
	$kodeuk = arg(3);
	
	if ($bulan=='') {
		$kodeuk = 'ZZ';
		$bulan = date('m');
	}
	
	if ($kodeuk != 'ZZ') {
		$query = db_select('unitkerja', 'uk');
		$query->fields('uk', array('namasingkat'));
		$query->condition('kodeuk', $kodeuk, '=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$skpd = '|' . $data->namasingkat;
			}
		}	
	} else
		$skpd = '|KABUPATEN';
	
	drupal_set_title('PENDAPATAN ' . $bulan . $skpd);
	
	$output_form = drupal_get_form('pendapatan_chart_main_form');

	return drupal_render($output_form);
	
}

function pendapatan_chart_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$showchart = $form_state['values']['showchart'];
	
	//drupal_set_message($row[2014][1]); 

	$uri = 'pendapatan/chart/' . $bulan.'/'. $kodeuk . '/' . $showchart;
	drupal_goto($uri);
	
}


function pendapatan_chart_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$showchart = 'jenis_rb';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$showchart = arg(4);
	}
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= '|' . $data->namasingkat;
			}
		}	
	}
	
	$form['formdata']['bulan'] = array(
		'#type' => 'value',
		'#value' => $bulan,		//$selected,
	);

	$form['formdata']['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,		//$selected,
	);

	$form['formdata']['showchart']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Jenis Grafis'), 
		'#default_value' => $showchart,
		
		'#options' => array(	
			 'jenis_ar' => t('PENDAPATAN PER BULAN (ANGGARAN+REALISASI)'),
			 'jenis_rb' => t('PENDAPATAN PER BULAN (REALISASI SAJA)'),
			 'sebulan_rupiah' => t('RINGKASAN PENDAPATAN SETAHUN (RUPIAH)'),
			 'sebulan_persen' => t('RINGKASAN PENDAPATAN SETAHUN (PERSEN)'),
		   ),
	);		
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Tampilkan'),
	);
	
	if (($showchart == 'jenis_ar') or ($showchart == 'jenis_rb')) {
		$form['chartmain'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_kelompok_chart($kodeuk, $showchart),		//$selected,

		);	
		
		$form['chart1'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis41_chart($kodeuk, $showchart),		//$selected,

		);	
		
		$form['chart2'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis42_chart($kodeuk, $showchart),		//$selected,

		);	
		
		$form['chart3'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis43_chart($kodeuk, $showchart),		//$selected,

		);	
		
	} else if (($showchart == 'sebulan_rupiah') or ($showchart == 'sebulan_persen'))  {
		$inpersen = ($showchart == 'sebulan_persen');
		$form['chartmain'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_kelompok_chart_final($bulan, $kodeuk, $inpersen),		//$selected,

		);	
		
		$form['chart1'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis41_chart_final($bulan, $kodeuk, $inpersen),		//$selected,

		);	
		$form['chart2'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis42_chart_final($bulan, $kodeuk, $inpersen),		//$selected,

		);	
		$form['chart3'] = array(
			'#type' => 'markup',
			'#markup' => pendapatan_chart_jenis43_chart_final($bulan, $kodeuk, $inpersen),		//$selected,

		);		
		
	} 
	return $form;
}

function pendapatan_chart_kelompok_chart($kodeuk, $showchart) {
//DONE

$arr_rea_41 = array();
$arr_rea_42 = array();
$arr_rea_43 = array();


$arr_agg_41 = array();
$arr_agg_42 = array();
$arr_agg_43 = array();

$arr_kum_41 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_42 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_43 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

	
$arr_rea_41 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_42 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_43 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodekelompok'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi1/1000)', 'bulan1');
$query->addExpression('SUM(a.realisasi2/1000)', 'bulan2');
$query->addExpression('SUM(a.realisasi3/1000)', 'bulan3');
$query->addExpression('SUM(a.realisasi4/1000)', 'bulan4');
$query->addExpression('SUM(a.realisasi5/1000)', 'bulan5');
$query->addExpression('SUM(a.realisasi6/1000)', 'bulan6');
$query->addExpression('SUM(a.realisasi7/1000)', 'bulan7');
$query->addExpression('SUM(a.realisasi8/1000)', 'bulan8');
$query->addExpression('SUM(a.realisasi9/1000)', 'bulan9');
$query->addExpression('SUM(a.realisasi10/1000)', 'bulan10');
$query->addExpression('SUM(a.realisasi11/1000)', 'bulan11');
$query->addExpression('SUM(a.realisasi12/1000)', 'bulan12');
$query->condition('a.kodeakun', '4', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
$query->groupBy('a.kodekelompok');

//drupal_set_message($query);
$results = $query->execute();
foreach ($results as $datas) {
	//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';

	$b1 = apbd_get_dbvalue($datas->bulan1); $b2 = apbd_get_dbvalue($datas->bulan2);
	$b3 = apbd_get_dbvalue($datas->bulan3); $b4 = apbd_get_dbvalue($datas->bulan4);
	$b5 = apbd_get_dbvalue($datas->bulan5); $b6 = apbd_get_dbvalue($datas->bulan6);
	$b7 = apbd_get_dbvalue($datas->bulan7); $b8 = apbd_get_dbvalue($datas->bulan8);
	$b9 = apbd_get_dbvalue($datas->bulan9); $b10 = apbd_get_dbvalue($datas->bulan10);
	$b11 = apbd_get_dbvalue($datas->bulan11); $b12 = apbd_get_dbvalue($datas->bulan12);

	$k1 = $b1;
	$k2 = $k1 + $b2;
	$k3 = $k2 + $b3;
	$k4 = $k3 + $b4;
	$k5 = $k4 + $b5;
	$k6 = $k5 + $b6;
	$k7 = $k6 + $b7;
	$k8 = $k7 + $b8;
	$k9 = $k8 + $b9;
	$k10 = $k9 + $b10;
	$k11 = $k10 + $b11;
	$k12 = $k11 + $b12;
	
	switch ($datas->kodekelompok) {
		case "41":
			$arr_agg_41 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_41 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_41 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));
			break;

		case "42":
			$arr_agg_42 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_42 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_42 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));
			 break;
		case "43":
			$arr_agg_43 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_43 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_43 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));
			 break;

	}	
}	//foreach ($results as $datas)

	

$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Pendapatan Tahun 2016'),
);
if ($showchart=='jenis_ar') {
	$chart['anggaran41'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1-PAD (Anggaran)'),
		'#data' => $arr_agg_41,
	);
}
$chart['kumulatif41'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.1-PAD (Kumulatif)'),
	'#data' => $arr_kum_41,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi41'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1-PAD (Bulanan)'),
		'#data' => $arr_rea_41,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran42'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2-DP (Anggaran)'),
		'#data' => $arr_agg_42,
	);
}
$chart['kumulatif42'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.2-DP (Kumulatif)'),
	'#data' => $arr_kum_42,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi42'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2-DP (Bulanan)'),
		'#data' => $arr_rea_42,
	);
}
if ($showchart=='jenis_ar') {
	$chart['anggaran43'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3-LLP (Anggaran)'),
		'#data' => $arr_agg_43,
	);
}
$chart['kumulatif43'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3-LLPA (Kumulatif)'),
		'#data' => $arr_kum_43,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi43'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3-LLP (Bulanan)'),
		'#data' => $arr_rea_43,
	);
}


$chart_register['pendapatan_chart_kelompok_chart'] = $chart;

return drupal_render($chart_register);
	
}

function pendapatan_chart_jenis41_chart($kodeuk, $showchart) {
//DONE

$arr_rea_411 = array();
$arr_rea_412 = array();
$arr_rea_413 = array();
$arr_rea_414 = array();


$arr_agg_411 = array();
$arr_agg_412 = array();
$arr_agg_413 = array();
$arr_agg_414 = array();

$arr_kum_411 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_412 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_413 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_414 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

	
$arr_rea_411 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_412 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_413 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_414 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodejenis'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi1/1000)', 'bulan1');
$query->addExpression('SUM(a.realisasi2/1000)', 'bulan2');
$query->addExpression('SUM(a.realisasi3/1000)', 'bulan3');
$query->addExpression('SUM(a.realisasi4/1000)', 'bulan4');
$query->addExpression('SUM(a.realisasi5/1000)', 'bulan5');
$query->addExpression('SUM(a.realisasi6/1000)', 'bulan6');
$query->addExpression('SUM(a.realisasi7/1000)', 'bulan7');
$query->addExpression('SUM(a.realisasi8/1000)', 'bulan8');
$query->addExpression('SUM(a.realisasi9/1000)', 'bulan9');
$query->addExpression('SUM(a.realisasi10/1000)', 'bulan10');
$query->addExpression('SUM(a.realisasi11/1000)', 'bulan11');
$query->addExpression('SUM(a.realisasi12/1000)', 'bulan12');
$query->condition('a.kodekelompok', '41', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
$query->groupBy('a.kodejenis');
					 
//drupal_set_message($query);
$results = $query->execute();
foreach ($results as $datas) {
	//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';
	$b1 = apbd_get_dbvalue($datas->bulan1); $b2 = apbd_get_dbvalue($datas->bulan2);
	$b3 = apbd_get_dbvalue($datas->bulan3); $b4 = apbd_get_dbvalue($datas->bulan4);
	$b5 = apbd_get_dbvalue($datas->bulan5); $b6 = apbd_get_dbvalue($datas->bulan6);
	$b7 = apbd_get_dbvalue($datas->bulan7); $b8 = apbd_get_dbvalue($datas->bulan8);
	$b9 = apbd_get_dbvalue($datas->bulan9); $b10 = apbd_get_dbvalue($datas->bulan10);
	$b11 = apbd_get_dbvalue($datas->bulan11); $b12 = apbd_get_dbvalue($datas->bulan12);

	$k1 = $b1;
	$k2 = $k1 + $b2;
	$k3 = $k2 + $b3;
	$k4 = $k3 + $b4;
	$k5 = $k4 + $b5;
	$k6 = $k5 + $b6;
	$k7 = $k6 + $b7;
	$k8 = $k7 + $b8;
	$k9 = $k8 + $b9;
	$k10 = $k9 + $b10;
	$k11 = $k10 + $b11;
	$k12 = $k11 + $b12;	
	
	switch ($datas->kodejenis) {

		case "411":
			$arr_agg_411 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_411 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_411 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));
								 
			break;
			
		case "412":
			$arr_agg_412 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_412 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_412 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));								 
			break;
			
		case "413":
			$arr_agg_413 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_413 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_413 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));								 
			break;
			
		case "414":
			$arr_agg_414 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_414 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_414 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));								 
			break;

	}	
}	//foreach ($results as $datas)

	
$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Pendapatan Asli Daerah Tahun 2016'),
);
if ($showchart=='jenis_ar') {
	$chart['anggaran411'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.1-Pajak (Anggaran)'), 
		'#data' => $arr_agg_411,
	);
}
$chart['kumulatif411'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.1.1-Pajak (Kumulatif)'),
	'#data' => $arr_kum_411,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi411'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.1-Pajak (Bulanan)'),
		'#data' => $arr_rea_411,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran412'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.2-Retribusi (Anggaran)'),
		'#data' => $arr_agg_412,
	);
}
$chart['kumulatif412'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.1.2-Retribusi (Kumulatif)'),
	'#data' => $arr_kum_412,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi412'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.2-Retribusi (Bulanan)'),
		'#data' => $arr_rea_412,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran413'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.3-HPKD (Anggaran)'),
		'#data' => $arr_agg_413,
	);
}
$chart['kumulatif413'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.1.3-HPKD (Kumulatif)'),
		'#data' => $arr_kum_413,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi413'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.3-HPKD (Bulanan)'),
		'#data' => $arr_rea_413,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran414'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.4-Lainnya (Anggaran)'),
		'#data' => $arr_agg_414,
	);
}
$chart['kumulatif414'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.1.4-Lainnya (Kumulatif)'),
		'#data' => $arr_kum_414,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi414'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.1.4-Lainnya (Bulanan)'),
		'#data' => $arr_rea_414,
	);
}


$chart_register['pendapatan_chart_jenis41_chart'] = $chart;

return drupal_render($chart_register);
	
}

function pendapatan_chart_jenis42_chart($kodeuk, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_rea_421 = array();
$arr_rea_422 = array();
$arr_rea_423 = array();

$arr_agg_421 = array();
$arr_agg_422 = array();
$arr_agg_423 = array();

	
$arr_kum_421 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_422 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_423 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

	
$arr_rea_421 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_422 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_423 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodejenis'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi1/1000)', 'bulan1');
$query->addExpression('SUM(a.realisasi2/1000)', 'bulan2');
$query->addExpression('SUM(a.realisasi3/1000)', 'bulan3');
$query->addExpression('SUM(a.realisasi4/1000)', 'bulan4');
$query->addExpression('SUM(a.realisasi5/1000)', 'bulan5');
$query->addExpression('SUM(a.realisasi6/1000)', 'bulan6');
$query->addExpression('SUM(a.realisasi7/1000)', 'bulan7');
$query->addExpression('SUM(a.realisasi8/1000)', 'bulan8');
$query->addExpression('SUM(a.realisasi9/1000)', 'bulan9');
$query->addExpression('SUM(a.realisasi10/1000)', 'bulan10');
$query->addExpression('SUM(a.realisasi11/1000)', 'bulan11');
$query->addExpression('SUM(a.realisasi12/1000)', 'bulan12');
$query->condition('a.kodekelompok', '42', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
$query->groupBy('a.kodejenis');

//drupal_set_message($query);
$results = $query->execute();
foreach ($results as $datas) {
	//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';
	$b1 = apbd_get_dbvalue($datas->bulan1); $b2 = apbd_get_dbvalue($datas->bulan2);
	$b3 = apbd_get_dbvalue($datas->bulan3); $b4 = apbd_get_dbvalue($datas->bulan4);
	$b5 = apbd_get_dbvalue($datas->bulan5); $b6 = apbd_get_dbvalue($datas->bulan6);
	$b7 = apbd_get_dbvalue($datas->bulan7); $b8 = apbd_get_dbvalue($datas->bulan8);
	$b9 = apbd_get_dbvalue($datas->bulan9); $b10 = apbd_get_dbvalue($datas->bulan10);
	$b11 = apbd_get_dbvalue($datas->bulan11); $b12 = apbd_get_dbvalue($datas->bulan12);

	$k1 = $b1;
	$k2 = $k1 + $b2;
	$k3 = $k2 + $b3;
	$k4 = $k3 + $b4;
	$k5 = $k4 + $b5;
	$k6 = $k5 + $b6;
	$k7 = $k6 + $b7;
	$k8 = $k7 + $b8;
	$k9 = $k8 + $b9;
	$k10 = $k9 + $b10;
	$k11 = $k10 + $b11;
	$k12 = $k11 + $b12;	
	
	switch ($datas->kodejenis) {
		case "421":
			$arr_agg_421 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_421 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_421 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));								 
			break;
			
		case "422":
			$arr_agg_422 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_422 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_422 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;
			
		case "423":
			$arr_agg_423 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_423 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_423 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;


	}	
}	//foreach ($results as $datas)


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Pendapatan Dana Perimbangan Tahun 2016'),
);
if ($showchart=='jenis_ar') {
	$chart['anggaran421'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.1-Bagsil (Anggaran)'), 
		'#data' => $arr_agg_421,
	);
}
$chart['kumulatif421'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.2.1-Bagsil (Kumulatif)'),
	'#data' => $arr_kum_421,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi421'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.1-Bagsil (Bulanan)'),
		'#data' => $arr_rea_421,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran422'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.2-DAU (Anggaran)'),
		'#data' => $arr_agg_422,
	);
}
$chart['kumulatif422'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.2.2-DAU (Kumulatif)'),
	'#data' => $arr_kum_422,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi422'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.2-DAU (Bulanan)'),
		'#data' => $arr_rea_422,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran423'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.3-DAK (Anggaran)'),
		'#data' => $arr_agg_423,
	);
}
$chart['kumulatif423'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.2.3-DAK (Kumulatif)'),
		'#data' => $arr_kum_423,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi423'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.2.3-DAK (Bulanan)'),
		'#data' => $arr_rea_423,
	);
}



$chart_register['pendapatan_chart_jenis42_chart'] = $chart;

return drupal_render($chart_register);
	
}

function pendapatan_chart_jenis43_chart($kodeuk, $showchart) {
//DONE

$arr_rea_431 = array();
$arr_rea_432 = array();
$arr_rea_433 = array();
$arr_rea_434 = array();
$arr_rea_435 = array();

$arr_agg_431 = array();
$arr_agg_432 = array();
$arr_agg_433 = array();
$arr_agg_434 = array();
$arr_agg_435 = array();

$arr_kum_431 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_432 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_433 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_434 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_kum_435 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

	
$arr_rea_431 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_432 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_433 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_434 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_rea_435 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(7, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
					 
$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodejenis'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi1/1000)', 'bulan1');
$query->addExpression('SUM(a.realisasi2/1000)', 'bulan2');
$query->addExpression('SUM(a.realisasi3/1000)', 'bulan3');
$query->addExpression('SUM(a.realisasi4/1000)', 'bulan4');
$query->addExpression('SUM(a.realisasi5/1000)', 'bulan5');
$query->addExpression('SUM(a.realisasi6/1000)', 'bulan6');
$query->addExpression('SUM(a.realisasi7/1000)', 'bulan7');
$query->addExpression('SUM(a.realisasi8/1000)', 'bulan8');
$query->addExpression('SUM(a.realisasi9/1000)', 'bulan9');
$query->addExpression('SUM(a.realisasi10/1000)', 'bulan10');
$query->addExpression('SUM(a.realisasi11/1000)', 'bulan11');
$query->addExpression('SUM(a.realisasi12/1000)', 'bulan12');
$query->condition('a.kodekelompok', '43', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
$query->groupBy('a.kodejenis');

//drupal_set_message($query);
$results = $query->execute();
foreach ($results as $datas) {
	//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';
	$b1 = apbd_get_dbvalue($datas->bulan1); $b2 = apbd_get_dbvalue($datas->bulan2);
	$b3 = apbd_get_dbvalue($datas->bulan3); $b4 = apbd_get_dbvalue($datas->bulan4);
	$b5 = apbd_get_dbvalue($datas->bulan5); $b6 = apbd_get_dbvalue($datas->bulan6);
	$b7 = apbd_get_dbvalue($datas->bulan7); $b8 = apbd_get_dbvalue($datas->bulan8);
	$b9 = apbd_get_dbvalue($datas->bulan9); $b10 = apbd_get_dbvalue($datas->bulan10);
	$b11 = apbd_get_dbvalue($datas->bulan11); $b12 = apbd_get_dbvalue($datas->bulan12);

	$k1 = $b1;
	$k2 = $k1 + $b2;
	$k3 = $k2 + $b3;
	$k4 = $k3 + $b4;
	$k5 = $k4 + $b5;
	$k6 = $k5 + $b6;
	$k7 = $k6 + $b7;
	$k8 = $k7 + $b8;
	$k9 = $k8 + $b9;
	$k10 = $k9 + $b10;
	$k11 = $k10 + $b11;
	$k12 = $k11 + $b12;	
	
	switch ($datas->kodejenis) {
		case "431":
			$arr_agg_431 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_431 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_431 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "432":
			$arr_agg_432 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_432 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_432 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;
			
		case "433":
			$arr_agg_433 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_433 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_433 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;
			
		case "434":
			$arr_agg_434 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_434 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_434 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;
			
		case "435":
			$arr_agg_435 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_rea_435 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_435 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));									 
			break;

	}	
}	//foreach ($results as $datas)


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Pendapatan Lain-Lain Pendapatan Daerah yang Sah Tahun 2016'),
);
if ($showchart=='jenis_ar') {
	$chart['anggaran431'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.1-Hibah (Anggaran)'), 
		'#data' => $arr_agg_431,
	);
}
$chart['kumulatif431'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3.1-Hibah (Kumulatif)'),
	'#data' => $arr_kum_431,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi431'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.1-Hibah (Bulanan)'),
		'#data' => $arr_rea_431,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran432'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.2-Darurat (Anggaran)'),
		'#data' => $arr_agg_432,
	);
}
$chart['kumulatif432'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3.2-Darurat (Kumulatif)'),
	'#data' => $arr_kum_432,
);
if ($showchart=='jenis_rb') {
	$chart['realisasi432'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.2-Darurat (Bulanan)'),
		'#data' => $arr_rea_432,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran433'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.3-BHProv (Anggaran)'),
		'#data' => $arr_agg_433,
	);
}
$chart['kumulatif433'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3.3-BHProv (Kumulatif)'),
		'#data' => $arr_kum_433,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi433'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.3-BHProv (Bulanan)'),
		'#data' => $arr_rea_433,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran434'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.4-DPOKh (Anggaran)'),
		'#data' => $arr_agg_434,
	);
}
$chart['kumulatif434'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3.4-DPOKh (Kumulatif)'),
		'#data' => $arr_kum_434,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi434'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.4-DPOKh (Bulanan)'),
		'#data' => $arr_rea_434,
	);
}

if ($showchart=='jenis_ar') {
	$chart['anggaran435'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.5-Bankeu (Anggaran)'),
		'#data' => $arr_agg_435,
	);
}
$chart['kumulatif435'] = array(
	'#type' => 'chart_data',
	'#title' => t('4.3.5-Bankeu (Kumulatif)'),
		'#data' => $arr_kum_435,
	);
if ($showchart=='jenis_rb') {
	$chart['realisasi435'] = array(
		'#type' => 'chart_data',
		'#title' => t('4.3.5-Bankeu (Bulanan)'),
		'#data' => $arr_rea_435,
	);
}
	
	

$chart_register['pendapatan_chart_jenis43_chart'] = $chart;

return drupal_render($chart_register);
	
}


function pendapatan_chart_kelompok_chart_final($bulan, $kodeuk, $inpersen) {

$arr_kelompok = array();
$arr_anggaran = array();
$arr_realisasi = array();
$arr_persen = array();

$x = -1;

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodekelompok'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodeakun', '4', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
$query->groupBy('a.kodekelompok');

//dpq($query);
$results = $query->execute();
foreach ($results as $datas) {	
	$x = $x+1;
	
	$arr_anggaran[$x]= apbd_get_dbvalue($datas->anggaran);							//ANGGARAN
	$arr_realisasi[$x]= apbd_get_dbvalue($datas->realisasi);							//REALISASI DEFAULT
	$arr_persen[$x] = round(apbd_hitungpersen($arr_anggaran[$x], $arr_realisasi[$x]),2);
	
	switch ($datas->kodekelompok) {
		case "41":
			$arr_kelompok[$x] = '41-PAD';		//LABEL						
			break;	
		case "42":
			$arr_kelompok[$x] = '42-Perimbangan';		//LABEL						
			break;	
		case "43":
			$arr_kelompok[$x] = '43-Lainnya';		//LABEL						
			break;	
			
	}
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Kelompok Pendapatan Tahun 2016'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		//'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		//'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	'#labels' => $arr_kelompok,
);

$apbdpendapatan['pendapatan_chart_kelompok_chart_final'] = $chart;

return drupal_render($apbdpendapatan);
	
}

function pendapatan_chart_jenis41_chart_final($bulan, $kodeuk, $inpersen) {

$arr_jenis = array(array('411', '411-Pajak'), array('412', '412-Retribusi'), array('413', '413-HPKD'), array('414', '414-Lainnya'));	

$arr_kelompok = array();
$arr_anggaran = array();
$arr_realisasi = array();

$x = -1;
for ($i=0; $i<=3; $i++){
	$ada = false;

	//Read Data
	/*
	$query = db_select('pendapatan' . $bulan, 'p');
	$query->addExpression('SUM(p.anggaran2/1000)', 'jmlanggaran');
	$query->addExpression('SUM(p.realisasi' . $bulan . '/1000)', 'jmlrealisasi');
	
	$query->condition('p.kodero', db_like($arr_jenis[$i][0]) . '%', 'LIKE');
	if ($kodeuk !='ZZ') $query->condition('p.kodeuk', $kodeuk, '=');
	*/
	
	$query = db_select('apbdrekap', 'a');
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
	$query->condition('a.kodejenis', $arr_jenis[$i][0], '=');
	if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
	
	$results = $query->execute();
	foreach ($results as $datas) {	
		$agg = apbd_get_dbvalue($datas->anggaran);
		$rea = apbd_get_dbvalue($datas->realisasi);
	}

	$x = $x+1;
	$arr_kelompok[$x] = $arr_jenis[$i][1];				//LABEL
	
	$arr_anggaran[$x]= $agg;							//ANGGARAN
	$arr_realisasi[$x]= $rea;							//REALISASI DEFAULT
	
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Pendapatan Asli Daerah Tahun 2016'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		//'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		//'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	'#labels' => $arr_kelompok,
);

$apbdpendapatan['pendapatan_chart_jenis41_chart_final'] = $chart;

return drupal_render($apbdpendapatan);
	
}

function pendapatan_chart_jenis42_chart_final($bulan, $kodeuk, $inpersen) {

$arr_jenis = array(array('421', '421-Bagi Hasil'), array('422', '422-DAU'), array('423', '423-DAK'));	

$arr_kelompok = array();
$arr_anggaran = array();
$arr_realisasi = array();

$x = -1;
for ($i=0; $i<=2; $i++){
	$ada = false;

	//Read Data
	/*
	$query = db_select('pendapatan' . $bulan, 'p');
	$query->addExpression('SUM(p.anggaran2/1000)', 'jmlanggaran');
	$query->addExpression('SUM(p.realisasi' . $bulan . '/1000)', 'jmlrealisasi');
	
	$query->condition('p.kodero', db_like($arr_jenis[$i][0]) . '%', 'LIKE');
	if ($kodeuk !='ZZ') $query->condition('p.kodeuk', $kodeuk, '=');
	*/

	$query = db_select('apbdrekap', 'a');
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
	$query->condition('a.kodejenis', $arr_jenis[$i][0], '=');
	if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
	
	$results = $query->execute();
	foreach ($results as $datas) {	
		$agg = apbd_get_dbvalue($datas->anggaran);
		$rea = apbd_get_dbvalue($datas->realisasi);
	}

	$x = $x+1;
	$arr_kelompok[$x] = $arr_jenis[$i][1];		//LABEL
	
	$arr_anggaran[$x]= $agg;							//ANGGARAN
	$arr_realisasi[$x]= $rea;							//REALISASI DEFAULT
	
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Dana Perimbangan Tahun 2016'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		//'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		//'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	'#labels' => $arr_kelompok,
);

$apbdpendapatan['pendapatan_chart_jenis42_chart_final'] = $chart;

return drupal_render($apbdpendapatan);
	
}


function pendapatan_chart_jenis43_chart_final($bulan, $kodeuk, $inpersen) {
$arr_jenis = array(array('431', '431-Hibah'), array('432', '432-Darurat'), array('433', '433-Bagi Hasil'), array('434', '434-DPOK'), array('435', '435-Bankeu'));	

$arr_kelompok = array();
$arr_anggaran = array();
$arr_realisasi = array();

$x = -1;
for ($i=0; $i<=4; $i++){
	$ada = false;

	//Read Data
	/*
	$query = db_select('pendapatan' . $bulan, 'p');
	$query->addExpression('SUM(p.anggaran2/1000)', 'jmlanggaran');
	$query->addExpression('SUM(p.realisasi' . $bulan . '/1000)', 'jmlrealisasi');
	
	$query->condition('p.kodero', db_like($arr_jenis[$i][0]) . '%', 'LIKE');
	if ($kodeuk !='ZZ') $query->condition('p.kodeuk', $kodeuk, '=');
	*/

	$query = db_select('apbdrekap', 'a');
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
	$query->condition('a.kodejenis', $arr_jenis[$i][0], '=');
	if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
	
	$results = $query->execute();
	foreach ($results as $datas) {	
		$agg = apbd_get_dbvalue($datas->anggaran);
		$rea = apbd_get_dbvalue($datas->realisasi);
	}

	$x = $x+1;
	$arr_kelompok[$x] = $arr_jenis[$i][1];		//LABEL
	
	$arr_anggaran[$x]= $agg;							//ANGGARAN
	$arr_realisasi[$x]= $rea;							//REALISASI DEFAULT
	
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Lain-Lain Daerah yang Sah Tahun 2016'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		//'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		//'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	'#labels' => $arr_kelompok,
);

$apbdpendapatan['pendapatan_chart_jenis43_chart_final'] = $chart;

return drupal_render($apbdpendapatan);
	
}


?>
