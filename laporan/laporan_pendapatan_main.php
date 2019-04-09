<?php
function laporan_pendapatan_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$cetakpdf = arg(3);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
	}
	
	//drupal_set_title('BELANJA');
	
	
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, true, true);
		print_pdf_p($output);
	} else {
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat);
		$output_form = drupal_get_form('laporan_pendapatan_main_form');	
		
		$btn = l('Cetak', 'laporan/filter/' . $bulan . '/'. $kodeuk . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_pendapatan_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];

	$uri = 'laporanpendapatan/filter/' . $bulan ;
	drupal_goto($uri);
	
}


function laporan_pendapatan_main_form($form, &$form_state) {
	
	$bulan = date('m');
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		
	} 
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '0' => t('SETAHUN'), 	
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

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan) {

$agg_pendapata_total = 0;
$rea_pendapata_total = 0;

//TABEL
$header = array (
	array('data' => 'No','width' => '5px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggperuk', 'a');
$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
$query->fields('uk', array('namasingkat'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.namasingkat');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->namasingkat, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->anggaran), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_pendapata_total += $datas->anggaran;
	$rea_pendapata_total += $realisasi;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($agg_pendapata_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_pendapata_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_pendapata_total, $rea_pendapata_total)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($bulan, $kodeuk, $showpersen, $shownumber) {

$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();

//TABEL
$header = array (
	array('data' => '<strong>KODE</strong>','width' => '50px', 'valign'=>'top'),
	array('data' => '<strong>URAIAN</strong>', 'width' => '330px','valign'=>'top'),
	array('data' => '<strong>ANGGARAN</strong>', 'width' => '90px', 'valign'=>'top'),
	array('data' => '<strong>REALISASI</strong>', 'width' => '90px', 'valign'=>'top'),
	array('data' => '<strong>PERSEN</strong>', 'width' => '45px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('apbdrekap', 'a');
$query->fields('a', array('kodeakun', 'namaakun'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->condition('a.kodeakun', '6', '<');
if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
$query->groupBy('a.kodeakun');
$query->orderBy('a.kodeakun');
$results = $query->execute();

$anggaran_netto = 0;
$realisasi_netto = 0;
foreach ($results as $datas) {

	if ($datas->kodeakun=='4') {
		$anggaran_netto = $datas->anggaran;
		$realisasi_netto = $datas->realisasi;
	} else {
		$anggaran_netto -= $datas->anggaran;
		$realisasi_netto -= $datas->realisasi;
	}
		

	$rows[] = array(
		array('data' => $datas->kodeakun, 'width' => '50px', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->namaakun . '</strong>','width' => '330px',  'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>','width' => '90px',  'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $datas->realisasi)) . '</strong>', 'width' => '45px', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap', 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
	$query->condition('a.kodeakun', $datas->kodeakun, '=');
	if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap', 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}

}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);

if ($kodeuk=='ZZ') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '6', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap', 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
	$query->condition('a.kodeakun', '6', '=');
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodekelompok=='61') {
			$anggaran_netto_p = $data_kel->anggaran;
			$realisasi_netto_p = $data_kel->realisasi;
		} else {
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $data_kel->realisasi;
		}	

		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap', 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
} 

//HEADER
if ($kodeuk=='ZZ')
	$skpd= 'KABUPATEN JEPARA';
else {
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namauk','kodeuk'))
		  ->condition('kodeuk',$kodeuk,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$skpd= $data->namauk;
		}
	}	
}

$top[] = array (
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>','width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => $skpd,'width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => 'BULAN ' . $bulan . ' TAHUN 2018','width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => '','width' => '575px', 'align'=>'center'),
	);

$headertop = array ();
$output_top = theme('table', array('header' => $headertop, 'rows' => $top ));

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $output_top . $tabel_data;

}


?>


