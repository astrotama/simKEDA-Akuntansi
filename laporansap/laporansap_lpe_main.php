<?php
function laporansap_lpe_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$kodeuk = arg(3);
				$tingkat = arg(4);
				$margin =arg(5);
				$tanggal =arg(6);
				$hal1 = arg(7);
				$marginkiri = arg(8);
				$cetakpdf = arg(9);
				$ttdlaporan = arg(10);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$tingkat = '3';
		$margin = '10'; 
		$marginkiri = '20';
		$hal1 = '1'; 
		$tanggal = date('j F Y');
		$ttdlaporan = 2;
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = 'ZZ';
		}
		
	}
	
	
	//drupal_set_message($bulan);
	
	if ($cetakpdf == 'pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	} else if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan-Komulatif.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat, false);
		$output_form = drupal_get_form('laporansap_lpe_main_form');	
		
		
		$btn = l('Cetak', 'laporanlpe/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf/' . $ttdlaporan , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporanlpe/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel/' . $ttdlaporan , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporansap_lpe_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];
	$ttdlaporan= $form_state['values']['ttdlaporan'];
	$cetakpdf = 0;

	
	$uri = 'laporanlpe/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri . 
	'/' . $cetakpdf . '/' . $ttdlaporan ;
	drupal_goto($uri);
	
}


function laporansap_lpe_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('n');
	$tingkat = '3';
	$margin = '10';
	$marginkiri = '20';
	$tanggal = date('j F Y');
	$hal1 = '1';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$tingkat = arg(4);
		$margin =arg(5);
		$tanggal =arg(6);
		$hal1 =arg(7);
		$marginkiri =arg(8);
		
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
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . $namasingkat . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
		
	} else {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'));
		$query->orderBy('kodedinas', 'ASC');
		$results = $query->execute();
		$optskpd = array();
		$optskpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $optskpd[$data->kodeuk] = $data->namasingkat; 
			}
		}
		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $optskpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
		);
	}
	
	$opttingkat = array();
	$opttingkat['3'] = 'Jenis';
	$opttingkat['4'] = 'Obyek';
	$opttingkat['5'] = 'Rincian';
	$form['formdata']['tingkat'] = array(
		'#type' => 'select',
		'#title' =>  t('Tingkat'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opttingkat,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $tingkat,
	);
		
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '1' => t('JANUARI'), 	
			 '2' => t('FEBRUARI'),
			 '3' => t('MARET'),	
			 '4' => t('APRIL'),	
			 '5' => t('MEI'),	
			 '6' => t('JUNI'),	
			 '7' => t('JULI'),	
			 '8' => t('AGUSTUS'),	
			 '9' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   ),
	);
	$form['formdata']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin Atas',
		'#default_value' => $margin,
	);
	$form['formdata']['marginkiri']= array(
		'#type' => 'textfield',
		'#title' => 'Margin Kiri',
		'#default_value' => $marginkiri,
	);
	$form['formdata']['hal1']= array(
		'#type' => 'textfield',
		'#title' => 'Halaman #1',
		'#default_value' => $hal1,
	);
	$form['formdata']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' => $tanggal ,
	);
	
	if (isUserSKPD()) {
		$form['formdata']['ttdlaporan']= array(
			'#type'         => 'value', 
			'#value' => '2',
		);				
	} else {
		$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');
		$form['formdata']['ttdlaporan']= array(
			'#type'         => 'select', 
			'#title' =>  t('PENANDA TANGAN LAPORAN'),
			'#options' => $penandatangan,
			'#default_value'=> $ttdlaporan, 
		);				
	}	

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

$rows = array();

$ekuitas_awal = 0; $surplus_defisit = 0; 

$rows[] = array(
	array('data' => '1', 'align' => 'left', 'valign'=>'top'),
	array('data' => 'Ekuitas Awal Tahun', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn($ekuitas_awal), 'align' => 'right', 'valign'=>'top'),
);

			
// * PENDAPATAN * //
$query = db_select('jurnalitemlo' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('8') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {
	
	$surplus_defisit = $datas->realisasi;
	
}	//foreach ($results as $datas)

// * BELANJA * //
$query = db_select('jurnalitemlo' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('9') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {

	
	$surplus_defisit = $surplus_defisit - $datas->realisasi;

}	//foreach ($results as $datas)


$rows[] = array(
	array('data' => '2', 'align' => 'left', 'valign'=>'top'),
	array('data' => 'Surplus (Defisit) Operasional', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn($surplus_defisit), 'align' => 'right', 'valign'=>'top'),
);	

			
// * PENDAPATAN * //
$query = db_select('jurnalitem' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {
	
	$surplus_defisit_rkk = $datas->realisasi;
	
}	//foreach ($results as $datas)

// * BELANJA * //
$query = db_select('jurnalitem' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {

	
	$surplus_defisit_rkk = $surplus_defisit_rkk - $datas->realisasi;

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '3', 'align' => 'left', 'valign'=>'top'),
	array('data' => 'R/K Kasda', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn(-$surplus_defisit_rkk), 'align' => 'right', 'valign'=>'top'),
);	

$rows[] = array(
	array('data' => '4', 'align' => 'left', 'valign'=>'top'),
	array('data' => 'Dampak Kumulatif Perubahan', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn(0), 'align' => 'right', 'valign'=>'top'),
);

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => 'Ekuitas Akhir Tahun', 'align' => 'left', 'valign'=>'top'),
	array('data' => apbd_fn($ekuitas_awal + $surplus_defisit-$surplus_defisit_rkk), 'align' => 'right', 'valign'=>'top'),
);	
		
//RENDER	
$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal) {
$penandatangan = arg(10);
if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	if ($penandatangan=='0') {
		$pimpinanatasnama = '';
		$pimpinannama = variable_get('bupatinama', '');
		$pimpinanjabatan = variable_get('bupatijabatan', '');
		$pimpinannip = '';
	
	} elseif ($penandatangan=='1') {
		$pimpinanatasnama = variable_get('wabupjabatanatasnama', '');
		$pimpinannama = variable_get('wabupnama', '');
		$pimpinanjabatan = variable_get('wabupjabatan', '');
		$pimpinannip = '';
	
	} elseif ($penandatangan=='2') {
		$pimpinanatasnama = variable_get('kepalajabatanatasnama', '');
		$pimpinannama = variable_get('kepalanama', '');
		$pimpinanjabatan = variable_get('kepalajabatan', '');
		$pimpinannip = variable_get('kepalanip', '');
	
	} elseif ($penandatangan=='3') {
		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
		$pimpinannama = variable_get('sekretarisnama', '');
		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
		$pimpinannip = variable_get('sekretarisnip', '');
		
		//drupal_set_message('s . ' . $pimpinannip);
		
	} else {
		$pimpinanatasnama = '';
		$pimpinannama = apbd_bud_nama();
		$pimpinanjabatan = apbd_bud_jabatan();
		$pimpinannip = apbd_bud_nip();
	}

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN PERUBAHAN EKUITAS</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$ekuitas_awal = 0; $surplus_defisit = 0; 

// * PENDAPATAN * //
$query = db_select('jurnalitemlo' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('8') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {
	
	$surplus_defisit = $datas->realisasi;
	
}	//foreach ($results as $datas)

// * BELANJA * //
$query = db_select('jurnalitemlo' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('9') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {

	
	$surplus_defisit = $surplus_defisit - $datas->realisasi;

}	//foreach ($results as $datas)

// * PENDAPATAN * //
$query = db_select('jurnalitem' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {
	
	$surplus_defisit_rkk = $datas->realisasi;
	
}	//foreach ($results as $datas)

// * BELANJA * //
$query = db_select('jurnalitem' . $sufixjurnal, 'ji');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
$results = $query->execute();
foreach ($results as $datas) {

	
	$surplus_defisit_rkk = $surplus_defisit_rkk - $datas->realisasi;

}	//foreach ($results as $datas)	

$rows = null;
$rows = array();

$rows[] = array(
	array('data' => '1.', 'width' => '15px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => 'Ekuitas Awal Tahun', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '', 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => apbd_fn($ekuitas_awal), 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);
$rows[] = array(
	array('data' => '2.', 'width' => '15px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => 'Surplus (Defisit) Operasional', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => apbd_fn($surplus_defisit), 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);
$rows[] = array(
	array('data' => '3.', 'width' => '15px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => 'R/K Kasda', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => apbd_fn(-$surplus_defisit_rkk), 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);
$rows[] = array(
	array('data' => '4.', 'width' => '15px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => 'Dampak Kumulatif Perubahan', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => apbd_fn(0), 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);
$rows[] = array(
	array('data' => '', 'width' => '15px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => 'Ekuitas Akhir Tahun', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => apbd_fn($ekuitas_awal + $surplus_defisit - $surplus_defisit_rkk), 'width' => '85px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);

if (isUserSKPD())
	$cetakttd = true;
else
	$cetakttd = ($kodeuk=='ZZ'? true: false );
	
if($cetakttd) {
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
			
		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
								
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),					
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),					
				);	
	
} else {
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
			
		);	
}
	
//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

return $tabel_data;

}


?>


