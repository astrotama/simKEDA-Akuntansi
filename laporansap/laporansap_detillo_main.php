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
		
	}	else {
		
		if (strlen($akun)==3)
			$output = gen_report_realisasi($bulan, $kodeuk, $akun);
		else
			$output = gen_report_realisasi_obyek($bulan, $kodeuk, $akun);
		
		$btn = l('Cetak', '/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/kum' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .=  '&nbsp;' . l('Excel', '/laporansapdetillo/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $akun . '/' . $margin . '/' . $marginkiri . '/excelkum' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		return drupal_render($output_form) . $btn . $output . $btn;		
	}
	//return $output;
}

function gen_report_realisasi($bulan, $kodeuk, $kodej) {

drupal_set_time_limit(0);

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
	$tanggal_awal = apbd_tahun() . '-01-01';

$query = db_select('obyeksap', 'o');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('o', array('kodeo', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('o.kodej', $kodej, '='); 
$query->groupBy('o.kodeo');
$query->orderBy('o.kodeo');
$results_oby = $query->execute();		
foreach ($results_oby as $data_oby) {
	
	$uraian = l($data_oby->uraian, '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
	
	//$realisasi = ((substr($kodej,0,1)=='8') ? $data_oby->kreditdebet : $data_oby->debetkredit);
	$realisasi = get_sap_value($kodej, $data_oby->debetkredit, $data_oby->kreditdebet);
	
	$rows[] = array(
		array('data' => '<strong>' . $data_oby->kodeo . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
		
	
		$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
		//$realisasi = ((substr($kodej,0,1)=='8') ? $data_rek->kreditdebet : $data_rek->debetkredit);
		$realisasi = get_sap_value($kodej, $data_rek->debetkredit, $data_rek->kreditdebet);
		
		$rows[] = array(
			array('data' => $data_oby->kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
			array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
	
	
	}
}	//obyek			
			


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
	array('data' => $rekening . ' (LO-SAP)', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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
	array('data' => 'KODE','width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '350px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '115px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();

//OBYEK
$query = db_select('obyeksap', 'o');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('o', array('kodeo', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('o.kodej', $kodej, '='); 
$query->groupBy('o.kodeo');
$query->orderBy('o.kodeo');
$results_oby = $query->execute();		
foreach ($results_oby as $data_oby) {
		
	//$realisasi = ((substr($kodej,0,1)=='8') ? $data_oby->kreditdebet : $data_oby->debetkredit);
	$realisasi = get_sap_value($kodej, $data_oby->debetkredit, $data_oby->kreditdebet);
	
	$rows[] = array(
		array('data' => $kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $data_oby->uraian . '</strong>', 'width' => '350px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '115px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	//REKENING
	$query = db_select('rincianobyeksap', 'ro');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();
	foreach ($results_rek as $data_rek) {
	
		//$realisasi = ((substr($kodej,0,1)=='8') ? $data_rek->kreditdebet : $data_rek->debetkredit);
		$realisasi = get_sap_value($kodej, $data_rek->debetkredit, $data_rek->kreditdebet);
	
		$rows[] = array(
			array('data' => $kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '350px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '115px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);

	}	//rek		
		
}	//obyek			


$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '350px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '115px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_obyek($bulan, $kodeuk, $kodeo) {

drupal_set_time_limit(0);

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
	$tanggal_awal = apbd_tahun() . '-01-01';

//REKENING
$query = db_select('rincianobyeksap', 'ro');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('ro', array('kodero', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ro.kodeo', $kodeo, '='); 
$query->groupBy('ro.kodero');
$query->orderBy('ro.kodero');
$results_rek = $query->execute();		
foreach ($results_rek as $data_rek) {
	

	$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
	//$realisasi = ((substr($kodeo,0,1)=='8') ? $data_rek->kreditdebet : $data_rek->debetkredit);
	$realisasi = get_sap_value($kodej, $data_rek->debetkredit, $data_rek->kreditdebet);
	
	$rows[] = array(
		array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
		array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);


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
	array('data' => $rekening . ' (LO-SAP)', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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
	array('data' => 'KODE','width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '350px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '115px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();



//REKENING
$query = db_select('rincianobyeksap', 'ro');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('ro', array('kodero', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('ro.kodeo', $kodeo, '='); 
$query->groupBy('ro.kodero');
$query->orderBy('ro.kodero');
$results_rek = $query->execute();
foreach ($results_rek as $data_rek) {

	//$realisasi = ((substr($kodeo,0,1)=='8') ? $data_rek->kreditdebet : $data_rek->debetkredit);
	$realisasi = get_sap_value($kodej, $data_rek->debetkredit, $data_rek->kreditdebet);

	$rows[] = array(
		array('data' => $kodeo . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '350px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '115px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//rek		
		


$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '350px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '115px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



?>

