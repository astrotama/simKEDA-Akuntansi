<?php

function belanja_chart_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';

	$bulan = arg(2);
	$kodeuk = arg(3);
	$jenis = arg(4);
	$sumberdana = arg(5);
	
	
	if ($kodeuk=='') {
		$kodeuk = 'ZZ';
		$namasingkat = 'SELURUH SKPD';
		$sumberdana ='SEMUA';
		$jenis ='SEMUA';
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
	
	drupal_set_title('BELANJA ' . $bulan . $skpd);
	
	$output_form = drupal_get_form('belanja_chart_main_form');

	return drupal_render($output_form);
	
}


function belanja_chart_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$jenis= $form_state['values']['jenis'];
	$sumberdana = $form_state['values']['sumberdana'];
	$showchart = $form_state['values']['showchart'];
	
	//drupal_set_message($row[2014][1]); 

	$uri = 'belanja/chart/' . $bulan.'/'. $kodeuk . '/' . $jenis . '/' . $sumberdana . '/' . $showchart;
	drupal_goto($uri);
	
}


function belanja_chart_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$namasingkat = 'SELURUH SKPD';
	$sumberdana ='SEMUA';
	$jenis ='SEMUA';
	$bulan = date('m');
	$showchart = 'nochart';
	
	$param1 = '';		
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$jenis = arg(4);
		$sumberdana = arg(5);
		$showchart = arg(6);
		
		$param1 = arg(7);
	}
	
	$form['formdata']['bulan'] = array(
		'#type' => 'value',
		'#value' => $bulan,		//$selected,
	);

	$form['formdata']['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,		//$selected,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Jenis Belanja'), 
		'#default_value' => $jenis,
		
		'#options' => array(	
			 'SEMUA' => t('SEMUA'), 	
			 'GAJI' => t('GAJI'), 	
			 'LANGSUNG' => t('LANGSUNG'),
			 'PPKD' => t('PPKD'),	
		   ),
	);		

	$opt_sumberdana['SEMUA'] ='SEMUA';
	$query = db_select('sumberdanalt', 's');
	$query->fields('s', array('nomor','sumberdana'));
	$query->orderBy('nomor', 'ASC');;
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$opt_sumberdana[$data->sumberdana] = $data->sumberdana;
		}
	}	

	
	$form['formdata']['sumberdana'] = array(
		'#type' => 'select',
		'#title' =>  t('Sumber Dana'),
		'#options' => $opt_sumberdana,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sumberdana,
	);

	$form['formdata']['showchart']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Jenis Grafis'), 
		'#default_value' => $showchart,
		
		'#options' => array(	
			 'total_ab' => t('TOTAL BELANJA PER BULAN (ANGGARAN+BELANJA)'),
			 'total_kb' => t('TOTAL BELANJA PER BULAN (BELANJA SAJA)'),
			 'jenis_ab' => t('JENIS BELANJA PER BULAN (ANGGARAN+BELANJA)'), 	
			 'jenis_kb' => t('JENIS BELANJA PER BULAN (BELANJA SAJA)'), 	
			 'sumberdana_ab' => t('BELANJA PER SUMBER DANA PER BULAN (ANGGARAN+BELANJA)'), 	
			 'sumberdana_kb' => t('BELANJA PER SUMBER DANA PER BULAN (BELANJA SAJA)'), 	
			 'sumberdana_tot' => t('BELANJA PER SUMBER DANA DALAM SETAHUN'), 	
			 'jenis_tot' => t('BELANJA SETAHUN'), 	
			 'fungsi_tot' => t('BELANJA PER FUNGSI DALAM SETAHUN'), 	
			 'urusan_tot' => t('BELANJA PER URUSAN DALAM SETAHUN'), 	
		   ),
	);		
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Tampilkan'),
	);
	
	if (($showchart == 'jenis_ab') or ($showchart == 'jenis_kb')) {
		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_jenis_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart),		//$selected,

		);	
		
		if ($jenis=='LANGSUNG') {
			//MODAL
			$form['chartsub4'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart($bulan, $kodeuk, $sumberdana, $showchart),		//$selected,

			);	
		}
			
	} else if (($showchart == 'total_ab') or ($showchart == 'total_kb')) {
		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_total_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart),		//$selected,

		);	
		
		if ($jenis=='SEMUA') {
			
			//KELOMPOK
			$form['chartsub1'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_kelompok_chart($bulan, $kodeuk, $sumberdana, $showchart),		//$selected,

			);	
			
			
			//BTL
			$form['chartsub2'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart($bulan, $kodeuk, $sumberdana, 'TIDAK LANGSUNG', $showchart),		//$selected,

			);	
			
			
			//BL
			$form['chartsub3'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart($bulan, $kodeuk, $sumberdana, 'LANGSUNG', $showchart),		//$selected,

			);	
			
			
			//MODAL
			$form['chartsub4'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart($bulan, $kodeuk, $sumberdana, $showchart),		//$selected,

			);	
			
			
		} else if ($jenis=='LANGSUNG') {
			//MODAL
			$form['chartsub4'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart($bulan, $kodeuk, $sumberdana, $showchart),		//$selected,

			);	
		}
		
		
	} else if (($showchart == 'sumberdana_ab') or ($showchart == 'sumberdana_kb')) {
		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_sumberdana_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart),		//$selected,

		);	
	} else if ($showchart == 'sumberdana_tot') {
		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_sumberdana_chart_final($bulan, $kodeuk, $jenis, false),		//$selected,

		);	
		$form['chartsub1'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_sumberdana_chart_final($bulan, $kodeuk, $jenis, true),		//$selected,

		);	
	} else if ($showchart == 'jenis_tot') {
		
		if ($jenis=='SEMUA') {
			$form['chart'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_kelompok_chart_final($bulan, $kodeuk, $sumberdana, $jenis, false),		//$selected,

			);	 
			$form['chartpersen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_kelompok_chart_final($bulan, $kodeuk, $sumberdana, $jenis, true),		//$selected,

			);	 
		
			$form['chartsub1'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'TIDAK LANGSUNG', false),		//$selected,

			);	 
			$form['chartsub1persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'TIDAK LANGSUNG', true),		//$selected,

			);	 

			$form['chartsub2'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'LANGSUNG', false),		//$selected,

			);	 
			$form['chartsub2persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'LANGSUNG', true),		//$selected,

			);	 

			//MODAL
			$form['chartsub3'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart_final($bulan, $kodeuk, $sumberdana, false),		//$selected,

			);	 
			$form['chartsub3persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart_final($bulan, $kodeuk, $sumberdana, true),		//$selected,

			);	 
			
			
		} else if (($jenis=='GAJI') or ($jenis=='PPKD')) {
			$form['chartsub1'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, $jenis, false),		//$selected,

			);	 
			$form['chartsub1persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, $jenis, true),		//$selected,

			);	 

			
		} else if ($jenis=='LANGSUNG') {
			$form['chartsub1'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'LANGSUNG', false),		//$selected,

			);	 
			$form['chartsub1persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, 'LANGSUNG', true),		//$selected,

			);	 

			//MODAL
			$form['chartsub2'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart_final($bulan, $kodeuk, $sumberdana, false),		//$selected,

			);	 
			$form['chartsub2persen'] = array(
				'#type' => 'markup',
				'#markup' => belanja_chart_modal_chart_final($bulan, $kodeuk, $sumberdana, true),		//$selected,

			);	 
			
		}		
	} else if ($showchart == 'fungsi_tot') {
		
		if ($jenis=='SEMUA')
			$kelompok = 'ZZ';
		else if ($jenis=='LANGSUNG')
			$kelompok = '52';
		else 
			$kelompok = '51';
		
		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_fungsi_chart_final($bulan, $kelompok, $sumberdana, false),		//$selected,

		);	 
		$form['chartpersen'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_fungsi_chart_final($bulan, $kelompok, $sumberdana, true),		//$selected,

		);	 
		
	} else if ($showchart == 'urusan_tot') {
		if ($jenis=='SEMUA')
			$kelompok = 'ZZ';
		else if ($jenis=='LANGSUNG')
			$kelompok = '52';
		else 
			$kelompok = '51';

		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_urusan_chart_final($bulan, '1', $kelompok, 'ZZ', $sumberdana,  false),		//$selected,

		);	
		$form['chartpersen'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_urusan_chart_final($bulan, '1', $kelompok, 'ZZ', $sumberdana,  true),		//$selected,

		);	

		$form['chart1'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_urusan_chart_final($bulan, '2', $kelompok, 'ZZ', $sumberdana,  false),		//$selected,

		);	
		$form['chart1persen'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_urusan_chart_final($bulan, '2', $kelompok, 'ZZ', $sumberdana,  true),		//$selected,

		);	
		
	} else if ($showchart == 'rekeningberjalan') {

		$form['chart'] = array(
			'#type' => 'markup',
			'#markup' => belanja_chart_rekening_berjalan($bulan, $param1, $kodeuk, false),		//$selected,

		);	
	} else {
		$form['chart'] = array(
			'#type' => 'hidden',
			//'#markup' = > belanja_chart_sumberdana_chart_final($bulan, $kodeuk, $jenis),		//$selected,
			//'#markup' => belanja_chart_sumberdana_chart_final($bulan, $kodeuk, $jenis, true),		//$selected,
			
			//'#markup' => belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana,$jenis, true),
			
			'#markup' => '',

		);	
		
	}
	return $form;
}

function belanja_chart_jenis_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_sp2d_511 = array();
$arr_sp2d_513 = array();
$arr_sp2d_514 = array();
$arr_sp2d_515 = array();
$arr_sp2d_516 = array();
$arr_sp2d_517 = array();
$arr_sp2d_518 = array();

$arr_sp2d_521 = array();
$arr_sp2d_522 = array();
$arr_sp2d_523 = array();

$arr_agg_511 = array();
$arr_agg_513 = array();
$arr_agg_514 = array();
$arr_agg_515 = array();
$arr_agg_516 = array();
$arr_agg_517 = array();
$arr_agg_518 = array();

$arr_agg_521 = array();
$arr_agg_522 = array();
$arr_agg_523 = array();	

//SP2D	
$arr_sp2d_511 = array();
$arr_sp2d_513 = array();
$arr_sp2d_514 = array();
$arr_sp2d_515 = array();
$arr_sp2d_516 = array();
$arr_sp2d_517 = array();
$arr_sp2d_518 = array();

$arr_sp2d_521 = array();
$arr_sp2d_522 = array();
$arr_sp2d_523 = array();

//SP2D KUM
$arr_kum_511 = array();
$arr_kum_513 = array();
$arr_kum_514 = array();
$arr_kum_515 = array();
$arr_kum_516 = array();
$arr_kum_517 = array();
$arr_kum_518 = array();

$arr_kum_521 = array();
$arr_kum_522 = array();
$arr_kum_523 = array();	

$ada511 = false; 
$ada513  = false; 
$ada514  = false; 
$ada515  = false; 
$ada516  = false; 
$ada517  = false; 
$ada518  = false; 

$ada521 = false; 
$ada522  = false; 
$ada523  = false; 

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

if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
} else if ($jenis=='TIDAK LANGSUNG') {
	$query->condition('a.kodekelompok', '51', '=');	
}						 

$query->groupBy('a.kodejenis');
$results = $query->execute();

foreach ($results as $datas) {

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
 		case "511":
			$ada511 = true;
			$arr_agg_511 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_511 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_511 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "513":
			$ada513 = true;
			$arr_agg_513 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_513 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_513 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "514":
			$ada514 = true;
			$arr_agg_514 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_514 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_514 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "515":
			$ada515 = true;
			$arr_agg_515 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_515 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_515 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "516":
			$ada516 = true;
			$arr_agg_516 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_516 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_516 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "517":
			$ada517 = true;
			$arr_agg_517 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_518 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_518 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "518":
			$ada518 = true;
			$arr_agg_518 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_519 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_519 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;

			
		case "521":
			$ada521 = true;
			$arr_agg_521 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_521 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_521 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "522":
			$ada522 = true;
			$arr_agg_522 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_522 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_522 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "523":
			$ada523 = true;
			$arr_agg_523 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_523 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_5 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
	}	
}	//foreach ($results as $datas)


if ($jenis=='SEMUA')
	$jenis_lbl = '';
else
	$jenis_lbl = ' ' . $jenis;

$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('REALISASI BELANJA' . $jenis_lbl . ' TAHUN 2016'),
);


if (($ada511 or $ada513 or $ada514 or $ada515 or $ada516 or $ada517 or $ada518)==false) {
	if (($jenis=='GAJI') or ($jenis=='PPKD') or ($jenis=='TIDAK LANGSUNG') or ($jenis=='SEMUA')) $ada511=true;
}
if (($ada521 or $ada522 or $ada523)==false) {
	if (($jenis=='LANGSUNG') or ($jenis=='SEMUA')) $ada522=true;
}

if ($ada511) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran511'] = array(
			'#type' => 'chart_data',
			'#title' => t('Gaji (Anggaran)'),
			'#data' => $arr_agg_511,
		);
	}
	$chart['kum511'] = array(
		'#type' => 'chart_data',
		'#title' => t('Gaji (Kumulatif)'),
		'#data' => $arr_kum_511,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d511'] = array(
			'#type' => 'chart_data',
			'#title' => t('Gaji (Bulanan)'),
			'#data' => $arr_sp2d_511,
		);
	}
}

if ($ada513) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran513'] = array(
			'#type' => 'chart_data',
			'#title' => t('Subsidi (Anggaran)'),
			'#data' => $arr_agg_513,
		);
	}
	$chart['kum513'] = array(
		'#type' => 'chart_data',
		'#title' => t('Subsidi (Kumulatif)'),
		'#data' => $arr_kum_513,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d513'] = array(
			'#type' => 'chart_data',
			'#title' => t('Subsidi (Bulanan)'),
			'#data' => $arr_sp2d_513,
		);
	}
}	
if ($ada514) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran514'] = array(
			'#type' => 'chart_data',
			'#title' => t('Hibah (Anggaran)'),
			'#data' => $arr_agg_514,
		);
	}
	$chart['kum514'] = array(
		'#type' => 'chart_data',
		'#title' => t('Hibah (Kumulatif)'),
		'#data' => $arr_kum_514,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d514'] = array(
			'#type' => 'chart_data',
			'#title' => t('Hibah (Bulanan)'),
			'#data' => $arr_sp2d_514,
		);
	}
}	
if ($ada515) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran515'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bansos (Anggaran)'),
			'#data' => $arr_agg_515,
		);
	}
	$chart['kum515'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bansos (Kumulatif)'),
		'#data' => $arr_kum_515,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d515'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bansos (Bulanan)'),
			'#data' => $arr_sp2d_515,
		);
	}
}	
if ($ada516) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran516'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bagsil (Anggaran)'),
			'#data' => $arr_agg_516,
		);
	}
	$chart['kum516'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bagsil (Kumulatif)'),
		'#data' => $arr_kum_516,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d516'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bagsil (Bulanan)'),
			'#data' => $arr_sp2d_516,
		);
	}
}	
if ($ada517) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran517'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bankeu (Anggaran)'),
			'#data' => $arr_agg_517,
		);
	}
	$chart['kum517'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bankeu (Kumulatif)'),
		'#data' => $arr_kum_517,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d517'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bankeu (Bulanan)'),
			'#data' => $arr_sp2d_517,
		);
	}
}	
if ($ada518) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran518'] = array(
			'#type' => 'chart_data',
			'#title' => t('BTT (Anggaran)'),
			'#data' => $arr_agg_518,
		);
	}
	$chart['kum518'] = array(
		'#type' => 'chart_data',
		'#title' => t('BTT (Kumulatif)'),
		'#data' => $arr_kum_518,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d518'] = array(
			'#type' => 'chart_data',
			'#title' => t('BTT (Bulanan)'),
			'#data' => $arr_sp2d_518,
		);
	}
}	

if ($ada521) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran521'] = array(
			'#type' => 'chart_data',
			'#title' => t('Pegawai (Anggaran)'),
			'#data' => $arr_agg_521,
		);
	}
	$chart['kum521'] = array(
		'#type' => 'chart_data',
		'#title' => t('Pegawai (Kumulatif)'),
		'#data' => $arr_kum_521,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d521'] = array(
			'#type' => 'chart_data',
			'#title' => t('Pegawai (Bulanan)'),
			'#data' => $arr_sp2d_521,
		);
	}
}	
if ($ada522) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran522'] = array(
			'#type' => 'chart_data',
			'#title' => t('B&J (Anggaran)'),
			'#data' => $arr_agg_522,
		);
	}
	$chart['kum522'] = array(
		'#type' => 'chart_data',
		'#title' => t('B&J (Kumulatif)'),
		'#data' => $arr_kum_522,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d522'] = array(
			'#type' => 'chart_data',
			'#title' => t('B&J (Bulanan)'),
			'#data' => $arr_sp2d_522,
		);
	}
}	
if ($ada523) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran523'] = array(
			'#type' => 'chart_data',
			'#title' => t('Modal (Anggaran)'),
			'#data' => $arr_agg_523,
		);
	}
	$chart['kum523'] = array(
		'#type' => 'chart_data',
		'#title' => t('Modal (Kumulatif)'),
		'#data' => $arr_kum_523,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d523'] = array(
			'#type' => 'chart_data',
			'#title' => t('Modal (Bulanan)'),
			'#data' => $arr_sp2d_523,
		);
	}
}	


$chart_register['realisasi_sp2d_skpd_jenis'] = $chart;

return drupal_render($chart_register);
	
}



function belanja_chart_total_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_sp2d = array();
$arr_kum = array();

$arr_agg = array();


$query = db_select('apbdrekap', 'a');
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

if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
}

$results = $query->execute();
foreach ($results as $datas) {
	$arr_agg = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
				array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
				array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
				array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
				array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
				array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));

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
				
	$arr_sp2d = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4), 
				array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8), 
				array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
	$arr_kum = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4), 
				array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8), 
				array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));

}	//foreach ($results as $datas)


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('REALISASI BELANJA TAHUN 2016'),
);
if ($showchart=='total_ab') {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		'#data' => $arr_agg,
	);
}
$chart['kum'] = array(
	'#type' => 'chart_data',
	'#title' => t('Bel (Kumulatif)'),
	'#data' => $arr_kum,
);
if ($showchart=='total_kb') {
	$chart['sp2d'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bel (Bulanan)'),
		'#data' => $arr_sp2d,
	);
}

$chart_register['realisasi_sp2d_skpd_total'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_chart_kelompok_chart($bulan, $kodeuk, $sumberdana, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

//Anggaran
$arr_agg_51 = array();
$arr_agg_52 = array();

	
//SP2D	
$arr_sp2d_51 = array();
$arr_sp2d_52 = array();

//SP2D KUM
$arr_kum_51 = array();
$arr_kum_52 = array();
					 
//REALISASI SP2D
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

if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

$query->groupBy('a.kodekelompok');

$results = $query->execute();
foreach ($results as $datas) {

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
		case '51':
			$arr_agg_51 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
							array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
							array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
							array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
							array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
							array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_51 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_51 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));		
			break;
			
		case '52':
			$arr_agg_52 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
							array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
							array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
							array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
							array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
							array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));		
			break;
	}

}	//foreach ($results as $datas)

$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Belanja per Kelompok Tahun ' . $bulan),
);
if ($showchart=='total_ab') {
	$chart['anggaran51'] = array(
		'#type' => 'chart_data',
		'#title' => t('5.1-BTL (Anggaran)'),
		'#data' => $arr_agg_51,
	);
}
$chart['kum51'] = array(
	'#type' => 'chart_data',
	'#title' => t('5.1-BTL (Kumulatif)'),
	'#data' => $arr_kum_51,
);
if ($showchart=='total_kb') {
	$chart['sp2d51'] = array(
		'#type' => 'chart_data',
		'#title' => t('5.1-BTL (Bulanan)'),
		'#data' => $arr_sp2d_51,
	);
}


if ($showchart=='total_ab') {
	$chart['anggaran52'] = array(
		'#type' => 'chart_data',
		'#title' => t('5.1-BL (Anggaran)'),
		'#data' => $arr_agg_52,
	);
}
$chart['kum52'] = array(
	'#type' => 'chart_data',
	'#title' => t('5.1-BL (Kumulatif)'),
	'#data' => $arr_kum_52,
);
if ($showchart=='total_kb') {
	$chart['sp2d52'] = array(
		'#type' => 'chart_data',
		'#title' => t('5.1-BL (Bulanan)'),
		'#data' => $arr_sp2d_52,
	);
}	

$chart_register['belanja_chart_kelompok_chart'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_chart_modal_chart($bulan, $kodeuk, $sumberdana, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_agg_52301 = array();
$arr_agg_52302 = array();
$arr_agg_52303 = array();
$arr_agg_52304 = array();
$arr_agg_52305 = array();
						 
$arr_sp2d_52301 = array();
$arr_sp2d_52302 = array();
$arr_sp2d_52303 = array();
$arr_sp2d_52304 = array();
$arr_sp2d_52305 = array();

$arr_kum_52301 = array();
$arr_kum_52302 = array();
$arr_kum_52303 = array();
$arr_kum_52304 = array();
$arr_kum_52305 = array();


$ada52301 = false; $ada52302 = false; $ada52303 = false;
$ada52304 = false; $ada52305 = false; 

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodeobyek'));
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
$query->condition('a.kodejenis', '523', '=');

if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

$query->groupBy('a.kodeobyek');

$results = $query->execute();

foreach ($results as $datas) {

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

	switch ($datas->kodeobyek) {
 		case "52301":
			$ada52301 = true;
			$arr_agg_52301 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52301 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52301 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "52302":
			$ada52302 = true;
			$arr_agg_52302 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52302 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52302 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "52303":
			$ada52303 = true;
			$arr_agg_52303 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52303 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52303 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "52304":
			$ada52304 = true;
			$arr_agg_52304 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52304 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52304 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;

		case "52305":
			$ada52305 = true;
			$arr_agg_52305 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_52305 = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_52305 = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;			

	}	
}	//foreach ($results as $datas)

$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('REALISASI BELANJA MODAL TAHUN 2016'),
);


if (($ada52301 or $ada52302 or $ada52303 or $ada52304 or $ada52305)==false) {
	$ada52302=true;
}

if ($ada52301) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran52301'] = array(
			'#type' => 'chart_data',
			'#title' => t('01-Tanah (Anggaran)'),
			'#data' => $arr_agg_52301,
		);
	}
	$chart['kum52301'] = array(
		'#type' => 'chart_data',
		'#title' => t('01-Tanah (Kumulatif)'),
		'#data' => $arr_kum_52301,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d52301'] = array(
			'#type' => 'chart_data',
			'#title' => t('01-Tanah (Bulanan)'),
			'#data' => $arr_sp2d_52301,
		);
	}
}

if ($ada52302) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran52302'] = array(
			'#type' => 'chart_data',
			'#title' => t('02-Alat Mesin (Anggaran)'),
			'#data' => $arr_agg_52302,
		);
	}
	$chart['kum52302'] = array(
		'#type' => 'chart_data',
		'#title' => t('02-Alat Mesin (Kumulatif)'),
		'#data' => $arr_kum_52302,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d52302'] = array(
			'#type' => 'chart_data',
			'#title' => t('02-Alat Mesin (Bulanan)'),
			'#data' => $arr_sp2d_52302,
		);
	}
}	
if ($ada52303) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran52303'] = array(
			'#type' => 'chart_data',
			'#title' => t('03-Gedung (Anggaran)'),
			'#data' => $arr_agg_52303,
		);
	}
	$chart['kum52303'] = array(
		'#type' => 'chart_data',
		'#title' => t('03-Gedung (Kumulatif)'),
		'#data' => $arr_kum_52303,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d52303'] = array(
			'#type' => 'chart_data',
			'#title' => t('03-Gedung (Bulanan)'),
			'#data' => $arr_sp2d_52303,
		);
	}
}	
if ($ada52304) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran52304'] = array(
			'#type' => 'chart_data',
			'#title' => t('04-Jaringan (Anggaran)'),
			'#data' => $arr_agg_52304,
		);
	}
	$chart['kum52304'] = array(
		'#type' => 'chart_data',
		'#title' => t('04-Jaringan (Kumulatif)'),
		'#data' => $arr_kum_52304,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d52304'] = array(
			'#type' => 'chart_data',
			'#title' => t('04-Jaringan (Bulanan)'),
			'#data' => $arr_sp2d_52304,
		);
	}
}	
if ($ada52305) {
	if (substr($showchart,-2)=='ab') {
		$chart['anggaran52305'] = array(
			'#type' => 'chart_data',
			'#title' => t('05-ATL (Anggaran)'),
			'#data' => $arr_agg_52305,
		);
	}
	$chart['kum52305'] = array(
		'#type' => 'chart_data',
		'#title' => t('05-ATL (Kumulatif)'),
		'#data' => $arr_kum_52305,
	);
	if (substr($showchart,-2)=='kb') {
		$chart['sp2d52305'] = array(
			'#type' => 'chart_data',
			'#title' => t('05-ATL (Bulanan)'),
			'#data' => $arr_sp2d_52305,
		);
	}
}	

$chart_register['belanja_chart_modal_chart'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_chart_sumberdana_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_agg_DAU = array();
$arr_agg_DAK = array();
$arr_agg_BANPROV = array();
$arr_agg_DBH = array();
$arr_agg_DBHCHT = array();
$arr_agg_LLP = array();
$arr_agg_PD = array();
$arr_agg_BK = array();
$arr_agg_BLUD = array();

$arr_sp2d_DAU = array();
$arr_sp2d_DAK = array();
$arr_sp2d_BANPROV = array();
$arr_sp2d_DBH = array();
$arr_sp2d_DBHCHT = array();
$arr_sp2d_LLP = array();
$arr_sp2d_PD = array();
$arr_sp2d_BK = array();
$arr_sp2d_BLUD = array();

$arr_kum_DAU = array();
$arr_kum_DAK = array();
$arr_kum_BANPROV = array();
$arr_kum_DBH = array();
$arr_kum_DBHCHT = array();
$arr_kum_LLP = array();
$arr_kum_PD = array();
$arr_kum_BK = array();
$arr_kum_BLUD = array();

$adaDAU = false;
$adaDAK = false;
$adaBANPROV = false;
$adaDBH = false;
$adaDBHCHT = false;
$adaLLP = false;
$adaPD = false;
$adaBK = false;
$adaBLUD = false;

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('sumberdana'));
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

if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
} else if ($jenis=='TIDAK LANGSUNG') {
	$query->condition('a.kodekelompok', '51', '=');	
}

$query->groupBy('a.sumberdana');

$results = $query->execute();

foreach ($results as $datas) {

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

	switch ($datas->sumberdana) {
 		case "DAU":
			$adaDAU = true;
			$arr_agg_DAU = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_DAU = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_DAU = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "DAK":
			$adaDAK = true;
			$arr_agg_DAK = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_DAK = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_DAK = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "BANPROV":
			$adaBANPROV = true;
			$arr_agg_BANPROV = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_BANPROV = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_BANPROV = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;
			
		case "DBH CHT":
			$adaDBHCHT = true;
			$arr_agg_DBHCHT = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_DBHCHT = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_DBHCHT = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;

		case "LAIN-LAIN PENDAPATAN":
			$adaLLP = true;
			$arr_agg_LLP = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_LLP = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_LLP = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;			

		case "PINJAMAN DAERAH":
			$adaPD = true;
			$arr_agg_PD = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_PD = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_PD = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 
			break;

		case "BANTUAN KHUSUS":
			$adaBK = true;
			$arr_agg_BK = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_BK = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_BK = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 			break;
		case "BLUD":
			$adaBLUD = true;
			$arr_agg_BLUD = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
								 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
								 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
								 array(7, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
								 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
								 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
			$arr_sp2d_BLUD = array(array(1, $b1), array(2, $b2), array(3, $b3), array(4, $b4),			
								 array(5, $b5), array(6, $b6), array(7, $b7), array(8, $b8),
								 array(9, $b9), array(10, $b10), array(11, $b11), array(12, $b12));
			$arr_kum_BLUD = array(array(1, $k1), array(2, $k2), array(3, $k3), array(4, $k4),			
								 array(5, $k5), array(6, $k6), array(7, $k7), array(8, $k8),
								 array(9, $k9), array(10, $k10), array(11, $k11), array(12, $k12));										 			break; 			

	}	
}	//foreach ($results as $datas)

$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Belanja Berdasarkan Sumber Dana'),
);
if ($adaDAU) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDAU'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAU (Anggaran)'),
			'#data' => $arr_agg_DAU,
		);
	}
	$chart['kumDAU'] = array(
		'#type' => 'chart_data',
		'#title' => t('DAU (Kumulatif)'),
		'#data' => $arr_kum_DAU,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dDAU'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAU (Bulanan)'),
			'#data' => $arr_sp2d_DAU,
		);
	}
}

if ($adaDAK) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDAK'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAK (Anggaran)'),
			'#data' => $arr_agg_DAK,
		);
	}
	$chart['kumDAK'] = array(
		'#type' => 'chart_data',
		'#title' => t('DAK (Kumulatif)'),
		'#data' => $arr_kum_DAK,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dDAK'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAK (Bulanan)'),
			'#data' => $arr_sp2d_DAK,
		);
	}
}	
if ($adaBANPROV) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBANPROV'] = array(
			'#type' => 'chart_data',
			'#title' => t('BANPROV (Anggaran)'),
			'#data' => $arr_agg_BANPROV,
		);
	}
	$chart['kumBANPROV'] = array(
		'#type' => 'chart_data',
		'#title' => t('BANPROV (Kumulatif)'),
		'#data' => $arr_kum_BANPROV,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dBANPROV'] = array(
			'#type' => 'chart_data',
			'#title' => t('BANPROV (Bulanan)'),
			'#data' => $arr_sp2d_BANPROV,
		);
	}
}	
if ($adaDBH) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDBH'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBH (Anggaran)'),
			'#data' => $arr_agg_DBH,
		);
	}
	$chart['kumDBH'] = array(
		'#type' => 'chart_data',
		'#title' => t('DBH (Kumulatif)'),
		'#data' => $arr_kum_DBH,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dDBH'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBH (Bulanan)'),
			'#data' => $arr_sp2d_DBH,
		);
	}
}	
if ($adaDBHCHT) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDBHCHT'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBHCHT (Anggaran)'),
			'#data' => $arr_agg_DBHCHT,
		);
	}
	$chart['kumDBHCHT'] = array(
		'#type' => 'chart_data',
		'#title' => t('DBHCHT (Kumulatif)'),
		'#data' => $arr_kum_DBHCHT,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dDBHCHT'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBHCHT (Bulanan)'),
			'#data' => $arr_sp2d_DBHCHT,
		);
	}
}	
if ($adaLLP) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranLLP'] = array(
			'#type' => 'chart_data',
			'#title' => t('LLP (Anggaran)'),
			'#data' => $arr_agg_LLP,
		);
	}
	$chart['kumLLP'] = array(
		'#type' => 'chart_data',
		'#title' => t('LLP (Kumulatif)'),
		'#data' => $arr_kum_LLP,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dLLP'] = array(
			'#type' => 'chart_data',
			'#title' => t('LLP (Bulanan)'),
			'#data' => $arr_sp2d_LLP,
		);
	}
}	
if ($adaPD) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranPD'] = array(
			'#type' => 'chart_data',
			'#title' => t('PD (Anggaran)'),
			'#data' => $arr_agg_PD,
		);
	}
	$chart['kumPD'] = array(
		'#type' => 'chart_data',
		'#title' => t('PD (Kumulatif)'),
		'#data' => $arr_kum_PD,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dPD'] = array(
			'#type' => 'chart_data',
			'#title' => t('PD (Bulanan)'),
			'#data' => $arr_sp2d_PD,
		);
	}
}	

if ($adaBK) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBK'] = array(
			'#type' => 'chart_data',
			'#title' => t('BK (Anggaran)'),
			'#data' => $arr_agg_BK,
		);
	}
	$chart['kumBK'] = array(
		'#type' => 'chart_data',
		'#title' => t('BK (Kumulatif)'),
		'#data' => $arr_kum_BK,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dBK'] = array(
			'#type' => 'chart_data',
			'#title' => t('BK (Bulanan)'),
			'#data' => $arr_sp2d_BK,
		);
	}
}	
if ($adaBLUD) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBLUD'] = array(
			'#type' => 'chart_data',
			'#title' => t('BLUD (Anggaran)'),
			'#data' => $arr_agg_BLUD,
		);
	}
	$chart['kumBLUD'] = array(
		'#type' => 'chart_data',
		'#title' => t('BLUD (Kumulatif)'),
		'#data' => $arr_kum_BLUD,
	);
	if ($showchart=='sumberdana_kb') {
		$chart['sp2dBLUD'] = array(
			'#type' => 'chart_data',
			'#title' => t('BLUD (Bulanan)'),
			'#data' => $arr_sp2d_BLUD,
		);
	}
}	

$chart_register['realisasi_sp2d_skpd_sumberdana'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_chart_sumberdana_chart_final($bulan, $kodeuk, $jenis, $inpersen) {

$arr_sumberdana = array();
$arr_anggaran = array();
$arr_realisasi = array();


$query = db_select('apbdrekap', 'a');
$query->fields('a', array('sumberdana'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodeakun', '5', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
} else if ($jenis=='TIDAK LANGSUNG') {
	$query->condition('a.kodekelompok', '51', '=');	
}

$query->groupBy('a.sumberdana');
$results = $query->execute();

$x = -1;
foreach ($results as $datas) {

	$x++;
	
	$arr_sumberdana[$x] = $datas->sumberdana;
	$arr_anggaran[$x]= apbd_get_dbvalue($datas->anggaran);
	$arr_realisasi[$x]= apbd_get_dbvalue($datas->realisasi);
}	//foreach ($results as $datas)

$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('REALISASI BELANJA BERDASARKAN SUMBER DANA TAHUN 2016'),
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
		'#suffix' => ' Jt',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Jt',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_sumberdana,
);

$apbdbelanja['belanja_chart_sumberdana_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}


function belanja_chart_kelompok_chart_final($bulan, $kodeuk, $sumberdana, $jenis, $inpersen) {
	
$arr_kelompok = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodekelompok', 'namakelompok'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodeakun', '5', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
} else if ($jenis=='TIDAK LANGSUNG') {
	$query->condition('a.kodekelompok', '51', '=');	
}

$query->groupBy('a.kodekelompok');
$query->groupBy('a.namakelompok');
$results = $query->execute();

$x = -1;
foreach ($results as $datas) {

	$x++;
	
	$arr_kelompok[$x] = $datas->kodekelompok . '-' . $datas->namakelompok;
	$arr_anggaran[$x]= apbd_get_dbvalue($datas->anggaran);
	$arr_realisasi[$x]= apbd_get_dbvalue($datas->realisasi);
}	//foreach ($results as $datas)



$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('REALISASI BELANJA TAHUN 2016'),
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
		'#suffix' => ' Ribu',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Ribu',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_kelompok,
);

$apbdbelanja['belanja_chart_kelompok_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_chart_jenis_chart_final($bulan, $kodeuk, $sumberdana, $jenis, $inpersen) {
	
$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodejenis', 'namajenis'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodeakun', '5', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

if ($jenis=='GAJI') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '0', '=');	
} else if ($jenis=='PPKD') {
	$query->condition('a.kodekelompok', '51', '=');	
	$query->condition('a.isppkd', '1', '=');	
} else if ($jenis=='LANGSUNG') {
	$query->condition('a.kodekelompok', '52', '=');	
} else if ($jenis=='TIDAK LANGSUNG') {
	$query->condition('a.kodekelompok', '51', '=');	
}

$query->groupBy('a.kodejenis');
$query->groupBy('a.namajenis');
$results = $query->execute();

$x = -1;
foreach ($results as $datas) {

	$x++;
	
	$arr_jenis[$x] = $datas->kodejenis . '-' . $datas->namajenis;
	$arr_anggaran[$x]= apbd_get_dbvalue($datas->anggaran);
	$arr_realisasi[$x]= apbd_get_dbvalue($datas->realisasi);
}	//foreach ($results as $datas)



if ($jenis=='SEMUA')
	$jenis_lbl = '';
else
	$jenis_lbl = ' ' . $jenis;

$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('REALISASI BELANJA'. $jenis_lbl  . ' TAHUN 2016'),
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
		'#suffix' => ' %',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => ' Ribu',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Ribu',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_jenis,
);

$apbdbelanja['belanja_chart_jenis_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}


function belanja_chart_modal_chart_final($bulan, $kodeuk, $sumberdana, $inpersen) {
	
//$arr_jenis_rek = array(array('52301', 'Tanah'), array('52302', 'Alat Mesin'), array('52303', 'Gedung'), array('52304', 'Jaringan'), array('52305', 'ATL'));	
$arr_jenis_rek = array('52301'=>'Tanah', '52302'=>'Alat Mesin', '52303'=>'Gedung', '52304'=>'Jaringan', '52305'=>'ATL');	
	
$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodeobyek', 'namaobyek'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodejenis', '523', '=');
if ($kodeuk !='ZZ') $query->condition('a.kodeskpd', $kodeuk, '=');
if ($sumberdana!='SEMUA') $query->condition('a.sumberdana', $sumberdana, '=');

$query->groupBy('a.kodeobyek');
$query->groupBy('a.namaobyek');
$results = $query->execute();

$x = -1;
foreach ($results as $datas) {

	$x++;
	
	//$arr_jenis[$x] = $datas->kodeobyek . '-' . $datas->namaobyek;
	$arr_jenis[$x] = $arr_jenis_rek[$datas->kodeobyek];
	$arr_anggaran[$x]= apbd_get_dbvalue($datas->anggaran);
	$arr_realisasi[$x]= apbd_get_dbvalue($datas->realisasi);
}	//foreach ($results as $datas)



$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Belanja Modal Tahun ' . $bulan),
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
		'#suffix' => ' %',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => ' Ribu',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Ribu',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_jenis,
);

$apbdbelanja['belanja_chart_modal_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}


function belanja_chart_fungsi_chart_final($bulan, $kelompok, $sumberdana, $inpersen) {

$arr_fungsi_rek = array('01'=>'Pelayanan Umum', '02'=>'Pertanahan', '03'=>'Ketertiban', '04'=>'Ekonomi', 
						'05'=>'Lingkungan', '06'=>'Perumahan', '07'=>'Kesehatan', '08'=>'Pariwisata', 
						'09'=>'Agama', '10'=>'Pendidikan', '11'=>'Sosial');	

$arr_fungsi = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap', 'r');
$query->fields('r', array('kodefungsi','namafungsi'));
$query->addExpression('SUM(r.anggaran2/1000000)', 'jmlanggaran');
$query->addExpression('SUM(r.realisasikum' . $bulan . '/1000000)', 'jmlrealisasi');
$query->condition('r.kodeakun', '5', '=');
if ($kelompok !='ZZ') $query->condition('r.kodekelompok', $kelompok, '=');
if ($sumberdana!='SEMUA') $query->condition('r.sumberdana', $sumberdana, '=');
$query->groupBy('kodefungsi','namafungsi');

//drupal_set_message($query);

$results = $query->execute();
foreach ($results as $datas) {	
	
	$arr_fungsi[] = $arr_fungsi_rek[$datas->kodefungsi];
	$arr_anggaran[]= (real)$datas->jmlanggaran;
	$arr_realisasi[]= (real)$datas->jmlrealisasi;
	
}	


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Anggaran berdasarkan Fungsi'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<$x; $i++){
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
		'#suffix' => ' Jt',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Jt',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_fungsi,
);

$apbdbelanja['belanja_chart_fungsi_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_chart_urusan_chart_final($bulan, $sifat, $kelompok, $kodefungsi, $sumberdana, $inpersen) {

$arr_urusan = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap', 'r');
$query->fields('r', array('kodeurusan','urusansingkat'));
$query->addExpression('SUM(r.anggaran2/1000000)', 'jmlanggaran');
$query->addExpression('SUM(r.realisasikum' . $bulan . '/1000000)', 'jmlrealisasi');
$query->condition('r.kodeakun', '5', '=');
if ($sifat !='ZZ') $query->condition('r.kodeurusan', db_like($sifat) . '%', 'LIKE');
if ($kelompok !='ZZ') $query->condition('r.kodekelompok', $kelompok, '=');
if ($kodefungsi !='ZZ') $query->condition('r.kodefungsi', $kodefungsi, '=');
if ($sumberdana!='SEMUA') $query->condition('r.sumberdana', $sumberdana, '=');
$query->groupBy('kodeurusan','urusansingkat');

//drupal_set_message('x' . $query);

$results = $query->execute();
foreach ($results as $datas) {	
	
	$arr_urusan[] = $datas->urusansingkat;
	$arr_anggaran[]= (real)$datas->jmlanggaran;
	$arr_realisasi[]= (real)$datas->jmlrealisasi;
	
}	


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('REALISASI ANGGARAN BERDASARKAN URUSAN TAHUN 2016'),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<$x; $i++){
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
		'#suffix' => ' Jt',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Jt',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_urusan,
);

$apbdbelanja['belanja_chart_urusan_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_chart_rekening_berjalan($bulan, $koderincian, $kodeskpd, $inpersen) {

$bulanakhir = $bulan-4;
if ($bulanakhir<2008) $bulanakhir = 2008;

$arrtahun = array();
$arr_anggaran = array();
$arr_realisasi = array();

for ($i=$bulanakhir; $i<=$bulan; $i++) {
	drupal_set_message($i);
	
	
	$query = db_select('apbdrekap' . $i, 'r');
	$query->addExpression('SUM(r.anggaran2/1000)', 'jmlanggaran');
	$query->addExpression('SUM(r.realisasikum' . $bulan . '/1000)', 'jmlrealisasi');
	$query->condition('r.koderincian', $koderincian, '=');
	if ($kodeskpd!='ZZ') $query->condition('r.kodeskpd', $kodeskpd, '=');

	drupal_set_message($query);

	$results = $query->execute();
	foreach ($results as $datas) {	
		
		$arrtahun[] = $i;
		$arr_anggaran[]= apbd_get_dbvalue($datas->jmlanggaran);
		$arr_realisasi[]= apbd_get_dbvalue($datas->jmlrealisasi);
		
	}	

}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Rekening ' . $koderincian),
	'#legend_position' => 'right',
	'#data_labels' => TRUE,
	'#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=$bulan; $i<=2008; $i--) {
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

$apbdbelanja['belanja_chart_rekening_berjalan'] = $chart;

return drupal_render($apbdbelanja);

}

?>
