<?php
function laporansap_detillo_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$kodeuk = arg(3);
				$akun = arg(4);
				$margin =arg(5);
				$marginkiri = arg(6);
				$jenis = arg(7);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n')-1;		//variable_get('apbdtahun', 0);
		$akun = '512';
		$tingkat = '3';
		$margin = '10'; 
		$marginkiri = '20';
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = '81';
		}
		$jenis = 'kum';
		
	}
	
	if (strlen($akun)==3)
		$results = db_query('select uraian from {jenissap} where kodej=:kodej', array(':kodej' => $akun));
	else
		$results = db_query('select uraian from {obyeksap} where kodeo=:kodeo', array(':kodeo' => $akun));
	
	foreach ($results as $datas) {
		$rekening = $bulan . ' | ' . $akun . ' - ' . $datas->uraian;
	};
	
	drupal_set_title($rekening);
	
	if ($jenis == 'kum') {
		if (strlen($akun)==3)
			$output = gen_report_realisasi_print($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek_print($bulan, $kodeuk, $akun);
		
		$_SESSION["hal1"] = 1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	} else if ($jenis=='per') {
		if (strlen($akun)==3)
			$output = gen_report_realisasi_print_periodik($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek_print_periodik($bulan, $kodeuk, $akun);
		
		$_SESSION["hal1"] = 1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
	
	} else if ($cetakpdf=='excelkum') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Komulatif.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		if (strlen($akun)==3)
			$output = gen_report_realisasi_print($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek_print($bulan, $kodeuk, $akun);
		echo $output;
		
	} else if ($cetakpdf=='excelper') {	
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Komulatif.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		if (strlen($akun)==3)
			$output = gen_report_realisasi_print_periodik($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek_print_periodik($bulan, $kodeuk, $akun);
		echo $output;
		
	}	else {
		
		if (strlen($akun)==3)
			$output = gen_report_realisasi($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek($bulan, $kodeuk, $akun);
		
		//$detil = l('Detil', '/laporansapdetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
		
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/kum">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/per">Realisasi Periodik</a></li>' .
					'</ul>' .
				'</div>';		
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/excelkum">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/excelper">Realisasi Periodik</a></li>' .
					'</ul>' .
				'</div>';		
		
		

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;		
	}
	//return $output;
}

function gen_report_realisasi($bulan, $kodeuk, $kodej) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Bulan Ini', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Kumulatif', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
	$tanggal_awal = apbd_tahun() . '-01-01';

// * PENDAPATAN * //
if (substr($kodej, 0,1)=='8') {
	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();		
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
		
		$uraian = l($data_oby->uraian, '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_oby->kodeo . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($bulanan)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		
		//dpq($query);
		
		$results_rek = $query->execute();		
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) { 
				$realisasi = $data->realisasi; 
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}			

			$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
			$rows[] = array(
				array('data' => $data_oby->kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_rek->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);
		
		
		}
	}	//obyek			
			
} else if (substr($kodej, 0,1)=='9') {		
	// * BELANJA * //

	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 							
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();	
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
		
		$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_oby->kodeo . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($bulanan)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('keg.total', '0', '>'); 													
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();	
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}
			
			$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
			$rows[] = array(
				array('data' => $data_oby->kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_rek->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//rek					
			
	}	//obyek			
				
		
} 

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print($bulan, $kodeuk, $kodej) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';

} else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$results = db_query('select kodej,uraian from {jenissap} where kodej=:kodej', array(':kodej' => $kodej));
foreach ($results as $datas) {
	$rekening = $kodej . ' - ' . $datas->uraian;
};

$rows[] = array(
	array('data' => $rekening . ' (LRA-SAP)', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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


$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

if (substr($kodej, 0,1)=='8') {
	// * PENDAPATAN * //
	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();		
	foreach ($results_oby as $data_oby) {
			
		$realisasi = 0; 
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi))  . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();	
		foreach ($results_rek as $data_rek) {
		
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) { 
				$realisasi = $data->realisasi; 
			}
		
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

		}	//rek		
			
	}	//obyek			
		
} elseif (substr($kodej, 0,1)=='9') {
	// * BELANJA * //

	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 							
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi))  . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('keg.total', '0', '>'); 													
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();	
		foreach ($results_rek as $data_rek) {
				
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
		}	//obyek					
			
	}	//obyek			
				
	
} elseif (substr($kodej, 0,1)=='7') {		
	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	
	$results_oby = $query->execute();
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $data->debetkredit);
		}
		

		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran - $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		
		$results_rek = $query->execute();		
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; $bulanan = 0;
			
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $data->debetkredit);
			}
			
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		
		}	//rek					
			
	}	//obyek			
	

}

$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print_periodik($bulan, $kodeuk, $kodej) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';

} else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$results = db_query('select kodej,uraian from {jenissap} where kodej=:kodej', array(':kodej' => $kodej));
foreach ($results as $datas) {
	$rekening = $kodej . ' - ' . $datas->uraian;
};

$rows[] = array(
	array('data' => $rekening . ' (LRA-SAP)', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$rows = null;
//TABEL

//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Bulan Ini', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Kumulatif', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

if (substr($kodej, 0,1)=='8') {
	// * PENDAPATAN * //
	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();	
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}				
		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi))  . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();		
		foreach ($results_rek as $data_rek) {
		
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}						
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		
		
		}
	}	//obyek	
	
} elseif (substr($kodej, 0,1)=='9') {
			
	// * BELANJA * //

	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 							
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	$results_oby = $query->execute();
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}

		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi))  . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('keg.total', '0', '>'); 													
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();		
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}
		
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		
		}	//obyek					
			
	}	//obyek			
				
} else {
	
	//OBYEK
	$query = db_select('obyeksap', 'o');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'o.kodeo=left(rm.koderosap,5)');
	$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('o', array('kodeo', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('o.kodej', $kodej, '='); 
	$query->groupBy('o.kodeo');
	$query->orderBy('o.kodeo');
	
	$results_oby = $query->execute();	
	foreach ($results_oby as $data_oby) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}

		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
			array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_oby->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
		//REKENING
		$query = db_select('rincianobyeksap', 'ro');
		$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
		$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		
		$results_rek = $query->execute();
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
			}
		
			$rows[] = array(
				array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		
		}	//rincian obyek					
			
	}	//obyek			
}

$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);


//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_obyek($bulan, $kodeuk, $kodeo) {



if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Bulan Ini', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Kumulatif', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
	$tanggal_awal = apbd_tahun() . '-01-01';

// * PENDAPATAN * //
if (substr($kodeo, 0,1)=='8') {

	
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	
	//dpq($query);
	
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) { 
			$realisasi = $data->realisasi; 
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}			

		$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
		$rows[] = array(
			array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
			array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_rek->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
	
	
	}
			
} else if (substr($kodeo, 0,1)=='9') {		
	// * BELANJA * //

	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 													
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();	
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
		
		$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
		$rows[] = array(
			array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
			array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_rek->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
	
	}	//rek					
			
		
}

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_obyek_print($bulan, $kodeuk, $kodeo) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';

} else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$results = db_query('select uraian from {obyeksap} where kodeo=:kodeo', array(':kodeo' => $kodeo));
foreach ($results as $datas) {
	$rekening = $kodeo . ' - ' . $datas->uraian;
};

$rows[] = array(
	array('data' => $rekening . ' (LRA-SAP)', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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


$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

if (substr($kodeo, 0,1)=='8') {
		
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();	
	foreach ($results_rek as $data_rek) {
	
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) { 
			$realisasi = $data->realisasi; 
		}
	
		$rows[] = array(
			array('data' => $kodeo . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);

	}	//rek		
			
		
} elseif (substr($kodeo, 0,1)=='9') {
	// * BELANJA * //

	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 													
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();	
	foreach ($results_rek as $data_rek) {
			
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => $kodeo . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
		
	}	//obyek					
			
				
	
} elseif (substr($kodeo, 0,1)=='7') {		
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; $bulanan = 0;
		
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $data->debetkredit);
		}
		
		$rows[] = array(
			array('data' => $kodeo .  '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
	
	}	//rek					
			
	

}

$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_obyek_print_periodik($bulan, $kodeuk, $kodeo) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';

} else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$results = db_query('select uraian from {obyeksap} where kodeo=:kodeo', array(':kodeo' => $kodeo));
foreach ($results as $datas) {
	$rekening = $kodeo . ' - ' . $datas->uraian;
};

$rows[] = array(
	array('data' => $rekening . ' (LRA-SAP)', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$rows = null;
//TABEL

//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Bulan Ini', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Kumulatif', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

if (substr($kodeo, 0,1)=='8') {
	// * PENDAPATAN * //
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperuk', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
	
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}						
		$rows[] = array(
			array('data' => $kodeo .  '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
	
	
	}
	
} elseif (substr($kodeo, 0,1)=='9') {
			
	// * BELANJA * //
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperkeg', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->innerJoin('kegiatanskpd', 'keg', 'ag.kodekeg=keg.kodekeg');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('keg.total', '0', '>'); 													
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
	
		$rows[] = array(
			array('data' => $kodeo .  '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
	
	}	//obyek					
			
				
} else {
	
	//OBYEK
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('rekeningmapsap_apbd', 'rm', 'ro.kodero=rm.koderosap');
	$query->innerJoin('anggperda', 'ag', 'rm.koderoapbd=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('ro.kodeo', $kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	
	$results_rek = $query->execute();
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = (($data_kel->kodek=='71') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}
	
		$rows[] = array(
			array('data' => $kodeo .  '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);
	
	}	//rincian obyek					
			
}

$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);


//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



?>

