<?php
function laporan_prognosiskeg_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	

	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = '81';
	}
	$margin = '10'; 
	$tanggal = date('j F Y');
	$hal1 = '1'; 
	$marginkiri = '20';		
	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodeuk = arg(2);
				$margin =arg(3);
				$tanggal =arg(4);
				$hal1 = arg(5);
				$marginkiri = arg(6);
				$cetakpdf = arg(7);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	}
	
	
	//drupal_set_message(isUserSKPD());
	
	if ($cetakpdf == 'pdf') {
		$output = gen_report_realisasi_kegiatan_print($kodeuk, $tanggal);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	} else if ($cetakpdf == 'pdfp') {
		$output = gen_report_realisasi_kegiatan_print_lengkap($kodeuk, $tanggal);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_L_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	} else if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Prognosis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_kegiatan_print($kodeuk, $tanggal);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else if ($cetakpdf=='excel2') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Prognosis Penundaan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_kegiatan_print_lengkap($kodeuk, $tanggal);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi_kegiatan($kodeuk);
		$output_form = drupal_get_form('laporan_prognosiskeg_main_form');	
		
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf">prognosis standar</a></li>' .
						'<li><a href="/laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdfp">prognosis penundaan</a></li>' .
					'</ul>' .
				'</div>';	
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel">prognosis standar</a></li>' .
						'<li><a href="/laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel2">prognosis penundaan</a></li>' .
					'</ul>' .
				'</div>';	
		//$btn = l('Cetak', 'laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		//$btn .= '&nbsp;' . l('Excel', 'laporanprognosiskeg/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		$btn .= '&nbsp;' . l('Rekening', 'laporanprognosis/filter/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-th-large')));
		$btn .= '&nbsp;' . l('Input Prognosis', 'rekeningkeg/prognosis', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-pencil')));
		if (isSuperuser()) {
			$btn .= '&nbsp;' . l('Cek SKPD', 'laporan/skpd', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-saved')));
		}
		

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_prognosiskeg_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];

	
	$uri = 'laporanprognosiskeg/filter/' . $kodeuk . '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri;
	drupal_goto($uri);
	
}


function laporan_prognosiskeg_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = '81';
	}
	$namasingkat = 'SELURUH SKPD';
	$margin = '10';
	$marginkiri = '20';
	$tanggal = date('j F Y');
	$hal1 = '1';
	
	if(arg(2)!=null){		
		$kodeuk = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		$hal1 = arg(5);
		$marginkiri = arg(6);
	} 
	
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= $data->namasingkat;
			}
		}	
	}
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $namasingkat . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
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

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
} 

function gen_report_realisasi_kegiatan_print($kodeuk, $tanggal) {
$tingkat = 3;

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

$results = db_query('select kodedinas,namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;	
};

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI SEMESTER I PENDAPATAN DAN KEGIATAN SKPD</strong>', 'colspan'=>'7', 'width' => '510px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => '<strong>SERTA PROGNOSIS ENAM (6) BULAN BERIKUTNYA</strong>', 'width' => '510px','colspan'=>'7', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>'7','align'=>'center','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '510px', 'colspan'=>'7','align'=>'center','style'=>'font-size:80%;border:none'),
);

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$prog_pendapata_total = 0;
$prog_belanja_total = 0;
$prog_pembiayaan_netto = 0;

//TABEL
if ($cetakpdf == 'excel'){
	$header = array (
		array('data' => 'KODE', 'valign'=>'top', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'URAIAN', 'valign'=>'top', 'width' => '150px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'ANGGARAN', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'REALISASI', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PRSN', 'width' => '25px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'SISA', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PROGNOSIS', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	);
} else {

	$header[] = array (
		array('data' => 'KODE', 'valign'=>'top', 'width' => '75px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'URAIAN', 'valign'=>'top', 'width' => '150px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'JUMLAH ANGGARAN', 'width' => '65px', 'rowspan'=>2, 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'REALISASI SMSTR I', 'width' => '90px','colspan'=>2,  'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'SISA ANGGARAN', 'width' => '65px', 'rowspan'=>2, 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PROGNOSIS SEMESTER II', 'width' => '65px', 'rowspan'=>2, 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	);
	$header[] = array (
		array('data' => 'RUPIAH', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '%', 'width' => '25px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	);
}

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

$realisasi = 0; $prognosis = 0 ; $sisa = 0;
foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:100%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$prog_pendapata_total = $prognosis;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
		);	
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;

				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
					'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
				);
				
				if ($tingkat>'3') {
					//OBYEK
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}
					}	//obyek			
				
				}	//if tingkat obyek
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
);

//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	 
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:100%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	$prog_belanja_total = $prognosis;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;		
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
			'algn' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					$sisa = $data_oby->anggaran - $realisasi;
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 	=> 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					);
					
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
	
	//KELOMPOK BELANJA LANGSUNG
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
			
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
			);		
			read_realisasi_program($kodeuk, $data_pro->kodepro, $realisasi, $prognosis);
			$sisa = $data_pro->anggaran - $realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$rows[] = array(
				array('data' =>  '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->condition('keg.inaktif', '0', '='); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				read_realisasi_kegiatan($kodeuk, $data_keg->kodekeg, $realisasi, $prognosis);
				$sisa = $data_keg->total - $realisasi;
			
					
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => $kodekeg , 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => $data_keg->kegiatan , 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_keg->total) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) , 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
					'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {

					read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_jen->kodej, $realisasi, $prognosis);
					$sisa = $data_jen->anggaran - $realisasi;
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
								
					
					$rows[] = array(
						array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {

							read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_oby->kodeo, $realisasi, $prognosis);
							$sisa = $data_oby->anggaran - $realisasi;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
							
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
);
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $prog_pendapata_total - $prog_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);	
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:100%;', 'width' => '65px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
		);	
	
		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);	
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;
			
		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);
			
				//OBYEK
				if ($tingkat>'3') {
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
						
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}	
					}	//obyek			
					
				}	//tingkat obyek
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	);
	
}


if (isUserSKPD()) {

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
	
}


//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows); 
//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_kegiatan_print_lengkap($kodeuk, $tanggal) {
$tingkat = 3;

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

$results = db_query('select kodedinas,namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;	
};

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI SEMESTER I PENDAPATAN DAN KEGIATAN SKPD</strong>', 'colspan'=>'7','width' => '750px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => '<strong>SERTA PROGNOSIS ENAM (6) BULAN BERIKUTNYA</strong>', 'colspan'=>'7','width' => '750px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '750px', 'colspan'=>'7','align'=>'center','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(),'colspan'=>'7', 'width' => '750px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$prog_pendapata_total = 0;
$prog_belanja_total = 0;
$prog_pembiayaan_netto = 0;

//TABEL
	$header = array (
		array('data' => 'KODE', 'valign'=>'top', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'URAIAN', 'valign'=>'top', 'width' => '150px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'ANGGARAN', 'width' => '80px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TERSEDIA', 'width' => '80px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'REALISASI', 'width' => '80px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '% AGG', 'width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '% TSD', 'width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'SISA', 'width' => '80px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PROGNOSIS', 'width' => '80px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'KETERANGAN', 'width' => '90px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

$realisasi = 0; $prognosis = 0 ; $sisa = 0; $keterangan = null;
foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:100%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_hitungpersen($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$prog_pendapata_total = $prognosis;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
		);	
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_hitungpersen($data_kel->anggaran) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;

				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
					'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_hitungpersen($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
					'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
					array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
				);
				
				if ($tingkat>'3') {
					//OBYEK
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo'); 
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_hitungpersen($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}
					}	//obyek			
				
				}	//if tingkat obyek
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
);

//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	 
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:100%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_hitungpersen($datas->tersedia) . '</strong>', 'align' => 'right', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:2px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$tsd_belanja_total = $datas->tersedia;
	$rea_belanja_total = $realisasi;
	$prog_belanja_total = $prognosis;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;		
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_hitungpersen($data_kel->tersedia) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;		
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
			'algn' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;',
			'algn' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
				array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					$sisa = $data_oby->anggaran - $realisasi;
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($data_oby->tersedia), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 	=> 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 	=> 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					);
					
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
	
	//KELOMPOK BELANJA LANGSUNG
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
			
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->tersedia, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->addExpression('SUM(keg.anggaran)', 'tersedia');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
				
			);		
			read_realisasi_program($kodeuk, $data_pro->kodepro, $realisasi, $prognosis);
			$sisa = $data_pro->anggaran - $realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$rows[] = array(
				array('data' =>  '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->tersedia) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total', 'anggaran'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->condition('keg.inaktif', '0', '='); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				read_realisasi_kegiatan($kodeuk, $data_keg->kodekeg, $realisasi, $prognosis);
				$sisa = $data_keg->total - $realisasi;
			
					
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => $kodekeg , 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => $data_keg->kegiatan , 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_keg->total) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_keg->anggaran) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'), 
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) , 'width' => '40px',  'align' => 'right', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)) , 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;','align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $keterangan , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {

					read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_jen->kodej, $realisasi, $prognosis);
					$sisa = $data_jen->anggaran - $realisasi;
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
								
					
					$rows[] = array(
						array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;font-style:italic;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
						array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-style:italic;'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	 
						foreach ($results_oby as $data_oby) {

							read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_oby->kodeo, $realisasi, $prognosis);
							$sisa = $data_oby->anggaran - $realisasi;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->tersedia), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;','align'=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;','align'=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
							
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
);
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$tersedia_netto = $agg_pendapata_total - $tsd_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $prog_pendapata_total - $prog_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);	
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:100%;', 'width' => '65px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '80px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top','style'=>'font-size:20%;border-left:1px solid black;border-right:1px solid black;'),
		);	
	
		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);	
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;
			
		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $keterangan, 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);
			
				//OBYEK
				if ($tingkat>'3') {
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
						
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($tersedia) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px',  'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '80px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => $keterangan , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}	
					}	//obyek			
					
				}	//tingkat obyek
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	);
	
}


if (isUserSKPD()) {

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
	
}


//RENDER	
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data .= createT($header, $rows); 
//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_kegiatan1($kodeuk) {
$tingkat = 3;


$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$prog_pendapata_total = 0;
$prog_belanja_total = 0;
$prog_pembiayaan_netto = 0;

//TABEL
$header = array (
	array('data' => 'Kode', 'valign'=>'top', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Uraian', 'valign'=>'top', 'width' => '150px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Anggaran', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Prsn', 'width' => '25px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Sisa', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'Prognosis', 'width' => '65px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-top:1px solid black;border-bottom:1px solid black;'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

$realisasi = 0; $prognosis = 0 ; $sisa = 0;
foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$prog_pendapata_total = $prognosis;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;

				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);
				
				if ($tingkat>'3') {
					//OBYEK
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}
					}	//obyek			
				
				}	//if tingkat obyek
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	 
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	$prog_belanja_total = $prognosis;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;		
	
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'algn' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					$sisa = $data_oby->anggaran - $realisasi;
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 	=> 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					);
					
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
	
	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
			
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			read_realisasi_program($kodeuk, $data_pro->kodepro, $realisasi, $prognosis);
			$sisa = $data_pro->anggaran - $realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$rows[] = array(
				array('data' =>  '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'width' => '25px', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '65px', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				read_realisasi_kegiatan($kodeuk, $data_keg->kodekeg, $realisasi, $prognosis);
				$sisa = $data_keg->total - $realisasi;
			
					
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg .  '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => '<strong>' . $data_keg->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => '<strong>' . apbd_fn($data_keg->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
					'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {

					read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_jen->kodej, $realisasi, $prognosis);
					$sisa = $data_jen->anggaran - $realisasi;
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$uraian = l($data_jen->uraian, '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_jen->kodej . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
					
					$rows[] = array(
						array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'ali	gn' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {

							read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_oby->kodeo, $realisasi, $prognosis);
							$sisa = $data_oby->anggaran - $realisasi;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_oby->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' 			=> 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
							
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $prog_pendapata_total - $prog_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
);


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;', 'width' => '65px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);	
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;
			
		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;',
			'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				);
			
				//OBYEK
				if ($tingkat>'3') {
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
						
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							);
						}	
					}	//obyek			
					
				}	//tingkat obyek
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '75px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top', 'width' => '150px','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '25px',  'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '65px', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	);
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_kegiatan($kodeuk) {
$tingkat = 3;

$results = db_query('select kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
};

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$prog_pendapata_total = 0;
$prog_belanja_total = 0;
$prog_pembiayaan_netto = 0;

//TABEL
$header = array (
	array('data' => 'KODE', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'URAIAN', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'ANGGARAN', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'TERSEDIA', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'REALISASI', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => '%AGG', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => '%TSD', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'SISA', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
	array('data' => 'PROGNOSIS', 'valign'=>'top', 'align'=>'center','style'=>'font-weight:bold;'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

$realisasi = 0; $prognosis = 0 ; $sisa = 0; $tersedia = 0; 
foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_hitungpersen($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$prog_pendapata_total = $prognosis;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>',  
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_hitungpersen($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;

				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top',  
			'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_hitungpersen($data_jen->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
				);
				
				if ($tingkat>'3') {
					//OBYEK
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'align'=> 'right', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_hitungpersen($data_oby->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
							);
						}
					}	//obyek			
				
				}	//if tingkat obyek
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {
	 
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right',  'style'=>''),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$tsd_belanja_total = $datas->tersedia;
	$rea_belanja_total = $realisasi;
	$prog_belanja_total = $prognosis;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;		
	
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'algn' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					$sisa = $data_oby->anggaran - $realisasi;
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->tersedia), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'align'=> 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
					);
					
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
	
	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
			
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->addExpression('SUM(keg.anggaran)', 'tersedia');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			read_realisasi_program($kodeuk, $data_pro->kodepro, $realisasi, $prognosis);
			$sisa = $data_pro->anggaran - $realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$rows[] = array(
				array('data' =>  '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total', 'anggaran'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				read_realisasi_kegiatan($kodeuk, $data_keg->kodekeg, $realisasi, $prognosis);
				$sisa = $data_keg->total - $realisasi;
			
					
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg .  '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . $data_keg->kegiatan . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) . '</strong>',  
					'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {

					read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_jen->kodej, $realisasi, $prognosis);
					$sisa = $data_jen->anggaran - $realisasi;
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$uraian = l(ucwords(strtolower($data_jen->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_jen->kodej . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
					
					$rows[] = array(
						array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top',  
			'ali	gn' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)', 'tersedia');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {

							read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_oby->kodeo, $realisasi, $prognosis);
							$sisa = $data_oby->anggaran - $realisasi;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_oby->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => $data_oby->uraian, 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->tersedia), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
							);
							
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)



//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$tersedia_netto = $agg_pendapata_total - $tsd_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $prog_pendapata_total - $prog_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top',  'style'=>''),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top',  'style'=>''),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);	
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;
			
		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => ucwords(strtolower($data_jen->uraian)), 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top', 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
				);
			
				//OBYEK
				if ($tingkat>'3') {
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
						
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top',  'style'=>''),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top',  'style'=>''),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
							);
						}	
					}	//obyek			
					
				}	//tingkat obyek
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top',  'style'=>''),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top',  'style'=>''),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$tersedia_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top',  'style'=>''),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top',  'style'=>''),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}

if ($isUserSKPD) {

		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right'),
			
		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center'),
								
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => '','width' => '255px', 'align'=>'center'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => '','width' => '255px', 'align'=>'center'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => '','width' => '255px', 'align'=>'center'),
						
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'text-decoration:underline;'),					
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center'),					
				);	
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function read_realisasi($kodeuk, $kodeakun, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	
	
	//REALISASI
	if ((substr($kodeakun,0,1)=='4') or (substr($kodeakun,0,2)=='61')) {

		$res = db_query('SELECT SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	} else {

		$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	}
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	
	$res = db_query('SELECT SUM(prognosis) as prognosisx 
			FROM prognosiskeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
		
	}
	
	
	return $true;
	
}

function read_realisasi_program($kodeuk, $kodepro, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodepro=:kodepro AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodepro'=>$kodepro));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE prognosiskeg.kodeo like :kodeakun AND kegiatanskpd.kodeuk=:kodeuk AND kegiatanskpd.kodepro=:kodepro', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodepro'=>$kodepro));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

function read_realisasi_kegiatan($kodeuk, $kodekeg, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE prognosiskeg.kodeo like :kodeakun AND kegiatanskpd.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

function read_realisasi_kegiatan_rekening($kodeuk, $kodekeg, $kodeakun, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE prognosiskeg.kodeo like :kodeakun AND kegiatanskpd.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}


?>


