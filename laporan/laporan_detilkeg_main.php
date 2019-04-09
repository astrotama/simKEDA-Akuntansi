<?php
function laporan_detilkeg_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$kodeuk = arg(3);
				$kodekeg = arg(4);
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
		$kodekeg = '000';
		$margin = '10'; 
		$marginkiri = '20';
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = '81';
		}
		$jenis = 'kum';
		
	}

	/*
	$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	foreach ($results as $datas) {
		$kegiatan = $datas->kegiatan;
	};
	
	
	drupal_set_title($kegiatan);
	*/
	
	if ($jenis == 'pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $kodekeg);
		
		$_SESSION["hal1"] = 1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	}	else {
		//$output = 'Halo';
		$output = gen_report_realisasi($bulan, $kodeuk, $kodekeg);

		$btn = l('Cetak', 'laporandetilkeg/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $kodekeg . '/' . $margin . '/' . $marginkiri . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		
		return $btn . $output . $btn;		
	}
	//return $output;
}

function gen_report_realisasi($bulan, $kodeuk, $kodekeg) {



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
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();
 
$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';

//KEGIATAN
$query = db_select('kegiatanskpd', 'k');
$query->innerJoin('anggperkeg', 'ag', 'k.kodekeg=ag.kodekeg');
$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('k.kodekeg', $kodekeg, '='); 

$results_keg = $query->execute();	
foreach ($results_keg as $data_keg) {
	
	$realisasi = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	//$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
	$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}

	
	$uraian = l(strtoupper($data_keg->kegiatan), '/akuntansi/buku/' . $data_keg->kodekeg . '/5/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
	$kodekeg_view = $data_keg->kodepro . '.' . substr($data_keg->kodekeg,-3);
	
	$rows[] = array(
		array('data' => '<strong>' . $kodekeg_view . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($data_keg->anggaran)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	
	//REKENING
	$query = db_select('rincianobyek', 'ro');
	$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
	$query->fields('ag', array('jumlah'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('ag.kodekeg', $data_keg->kodekeg, '='); 
	$results_rek = $query->execute();	
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0; 
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', $data_rek->kodero, '='); 
		$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
		$rows[] = array(
			array('data' => $kodekeg_view . '.' . $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
			array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_rek->jumlah), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->jumlah, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);
	
	}	//rek					
	
	
	
}	//Keg			
				

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print($bulan, $kodeuk, $kodekeg) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}


$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
foreach ($results as $datas) {
	$kegiatan = $datas->kegiatan;
};

$rows[] = array(
	array('data' => $kegiatan, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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
	array('data' => 'KODE','width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '200px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

//KEGATAN
$query = db_select('kegiatanskpd', 'k');
$query->innerJoin('anggperkeg', 'ag', 'k.kodekeg=ag.kodekeg');
$query->fields('k', array('kodekeg', 'kodepro', 'kegiatan'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('k.kodekeg', $kodekeg, '='); 
$data_keg = $query->execute();	
foreach ($data_keg as $data_keg) {
	
	$realisasi = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	//$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
	$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}

	$kodekeg_view = $data_keg->kodepro . '.' . substr($data_keg->kodekeg,-3);
	$rows[] = array(
		array('data' => $kodekeg_view, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $data_keg->kegiatan . '</strong>', 'width' => '200px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($data_keg->anggaran)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi)  . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi))  . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($data_keg->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	//REKENING
	$query = db_select('rincianobyek', 'ro');
	$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
	$query->fields('ag', array('jumlah'));
	$query->fields('ro', array('kodero', 'uraian'));
	$query->condition('ag.kodekeg', $data_keg->kodekeg, '='); 
	$results_rek = $query->execute();		
	foreach ($results_rek as $data_rek) {
			
			$realisasi = 0; 
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', $data_rek->kodero, '='); 
			$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$rows[] = array(
				array('data' => $kodekeg_view . '.' . $data_rek->kodero, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '200px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->jumlah), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->jumlah, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->jumlah- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		
		}	//Rek					
		
}	//Keg			
				


$rows[] = array(
	array('data' => '', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '200px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
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


?>

