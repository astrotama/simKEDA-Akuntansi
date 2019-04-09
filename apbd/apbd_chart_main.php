<?php

function apbd_chart_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';


	drupal_set_title('ANALISIS APBD');
	
	$output_form = drupal_get_form('apbd_chart_main_form');

	return drupal_render($output_form);
	
}

function apbd_chart_main_form($form, &$form_state) {

		
	$showchart = arg(2);
	$param1 = arg(3);
	$param2 = arg(4);
	$param3 = arg(5);
	$param4 = arg(6);
	
	$param5 = arg(7);


	if ($showchart == 'rekeningberjalan') {

		//REKENING
		$query = db_select('apbdrekap' . $param1, 'a')->extend('PagerDefault');
		$query->fields('a', array('namarincian'));
		$query->condition('a.koderincian', $param2, '=');
		$query->limit(1);
		$results = $query->execute();
		foreach ($results as $datas) {	
			$rekening = $param2 . ' - ' . $datas->namarincian;
		}	

		if ($param3=='ZZ') 
			$skpd= 'KABUPATEN';
		else {
			$query = db_select('unitkerja' . $param1, 'u');
			$query->fields('u', array('namasingkat'));
			$query->condition('u.kodeuk', $param3, '=');
			$results = $query->execute();
			foreach ($results as $data) {
				$skpd= $data->namasingkat;
			}				
		}		

		$form['item1'] = array(
			'#type' => 'item',
			'#title' =>  t('Unit Kerja'),
			'#markup' => '<p>' . $skpd . '</p>',
		);	
		$form['item2'] = array(
			'#type' => 'item',
			'#title' =>  t('Rekening'),
			'#markup' => '<p>' .  $rekening . '</p>',
		);	
		$form['formdata']['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Tutup'),
		);
		
		//apbd_chart_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
		$form['chart1'] = array(
			'#type' => 'markup',
			'#markup' => apbd_chart_rekening_berjalan($param1, $param2, $param3, false),		//$selected,

		);	
		
		$form['chart2'] = array(
			'#type' => 'markup',
			'#markup' => apbd_chart_rekening_berjalan($param1, $param2, $param3, true),		//$selected,

		);	
	} 
	return $form;
}


function apbd_chart_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen) {

$tahunakhir = $tahun-4;
if ($tahunakhir<2008) $tahunakhir = 2008;

$arrtahun = array();
$arr_anggaran = array();
$arr_realisasi = array();


for ($i=$tahunakhir; $i<=$tahun; $i++) {
	
	$query = db_select('apbdrekap' . $i, 'r');
	$query->addExpression('SUM(r.anggaran2/1000)', 'jmlanggaran');
	$query->addExpression('SUM(r.realisasi/1000)', 'jmlrealisasi');
	$query->condition('r.koderincian', $koderincian, '=');
	if ($kodeskpd!='ZZ') $query->condition('r.kodeskpd', $kodeskpd, '=');


	$results = $query->execute();
	foreach ($results as $datas) {	
		
		$arrtahun[] = $i;
		$arr_anggaran[]= apbd_get_dbvalue($datas->jmlanggaran);
		$arr_realisasi[]= apbd_get_dbvalue($datas->jmlrealisasi);
		
	}	

}


if ($inpersen) {
	$chart = array(
		'#type' => 'chart',
		'#chart_type' => 'column',
		'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
		'#title' => t('Analisis Pencapaian Realisasi Anggaran (%)'),
		'#legend_position' => 'right',
		'#data_labels' => TRUE,
		'#tooltips' => TRUE,
		
	);

	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<$x; $i++) {
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
	$chart = array(
		'#type' => 'chart',
		'#chart_type' => 'column',
		'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
		'#title' => t('Analisis Anggaran/Realisasi'),
		'#legend_position' => 'right',
		'#data_labels' => TRUE,
		'#tooltips' => TRUE,
		
	);
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => ' Rb',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Rb',
	);
}

$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arrtahun,
);

$apbdbelanja['apbd_chart_rekening_berjalan'] = $chart;

return drupal_render($apbdbelanja);

}

?>
