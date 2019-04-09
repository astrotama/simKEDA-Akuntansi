<?php
function laporansap_detiluk_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = ''; 
	
	if (arg(1)!=null) {
		switch($arg) {
			case 'filter':
				$akun = arg(2);
				$bulan = arg(3);
				$cetakpdf = arg(4);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$akun = '5';	
		$bulan = date('m');		//variable_get('apbdtahun', 0);
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);

	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($akun, $bulan);
		apbd_ExportPDF_P($output, 10, "LAP.pdf");
		
	} else if ($cetakpdf=='xls') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Belanja SKPD.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");

		$output = gen_report_realisasi_print($akun, $bulan);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";

	} else {

		if (strlen($akun)==1)
			$res = db_query('select uraian from anggaransap where kodea=:akun', array(':akun'=>$akun));
		elseif  (strlen($akun)==2)
			$res = db_query('select uraian from kelompoksap where kodek=:akun', array(':akun'=>$akun));
		elseif  (strlen($akun)==3)
			$res = db_query('select uraian from jenissap where kodej=:akun', array(':akun'=>$akun));
		elseif  (strlen($akun)==5)
			$res = db_query('select uraian from obyeksap where kodeo=:akun', array(':akun'=>$akun));
		else
			$res = db_query('select uraian from rincianobyeksap where kodero=:akun', array(':akun'=>$akun));
			
		foreach ($res as $data) {
			$rekening = $bulan . ' | ' . $akun . ' - ' . $data->uraian;
		}	
		
		drupal_set_title($rekening);
		
		$output = gen_report_realisasi($akun, $bulan);
		
		$btn = l('Cetak', 'laporandetiluk/filter/' . $akun . '/'  . $bulan . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp;' . l('Excel', 'laporandetiluk/filter/' . $akun . '/' . $bulan . '/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		
		
		return $btn . $output . $btn;
		//return 'yyy';
	}	
	
}

function laporansap_detiluk_main_form_submit($form, &$form_state) {
	$akun= $form_state['values']['akun'];
	$bulan= $form_state['values']['bulan'];
	$kelompok= $form_state['values']['kelompok'];
	
	$uri = 'laporandetiluk/filter/' . $akun . '/'  . $bulan;
	drupal_goto($uri);
	
}


function laporansap_detiluk_main_form($form, &$form_state) {
	 
	$akun = '5';
	$bulan = date('m');
	$kelompok = 'SEMUA';
	
	
	if(arg(2)!=null){
		$akun = arg(2);
		$bulan = arg(3);
		
		//drupal_set_message($akun);
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . '|' . $kelompok . $akun_str . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['akun']= array(
		'#type' => 'value',		//'radios', 
		'#value' => $akun,
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

function gen_report_realisasi($akun, $bulan) {

$anggaran_total = 0;
$realisasi_total = 0;

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';

	
//TABEL
$header = array (
	array('data' => 'No','width' => '5px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

if ((substr($akun,0,1)=='4') || (substr($akun,0,1)=='5') || (substr($akun,0,1)=='7'))
	$suffixsap = 'lra';
else
	$suffixsap = 'lo';

$query = db_select('jurnalitem' . $suffixsap, 'ji');
$query->innerJoin('jurnal', 'j', 'ji.jurnalid=j.jurnalid');
$query->innerJoin('unitkerja', 'u', 'u.kodeuk=j.kodeuk');
$query->fields('u', array('kodedinas','kodeuk', 'namasingkat'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
$query->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
$query->condition('ji.kodero', db_like($akun) . '%', 'LIKE'); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->groupBy('u.kodedinas');
$query->orderBy('u.kodedinas');

//dpq($query);

$results = $query->execute();	

$no = 0;
foreach ($results as $datas) {
	
	$no++;
	
	$namasingkat = l($datas->namasingkat, '/akuntansi/bukusap/ZZ/'  . $akun  . '/'  . $datas->kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
	$realisasi = get_sap_value($akun, $datas->debetkredit, $datas->kreditdebet);
	
	$rows[] = array(
		array('data' => $no, 'align' => 'left', 'valign'=>'top'),
		array('data' => $namasingkat, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
	);
	
	$realisasi_total += $realisasi;
	
}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($akun, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI PENDAPATAN', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

if (strlen($akun)==1)
	$res = db_query('select uraian from anggaran where kodea=:akun', array(':akun'=>$akun));
elseif  (strlen($akun)==2)
	$res = db_query('select uraian from kelompok where kodek=:akun', array(':akun'=>$akun));
elseif  (strlen($akun)==3)
	$res = db_query('select uraian from jenis where kodej=:akun', array(':akun'=>$akun));
elseif  (strlen($akun)==5)
	$res = db_query('select uraian from obyek where kodeo=:akun', array(':akun'=>$akun));
else
	$res = db_query('select uraian from rincianobyek where kodero=:akun', array(':akun'=>$akun));
	
foreach ($res as $data) {
	$rekening = $akun . ' - ' . $data->uraian;
}	
		
$rows[] = array(
	array('data' => $rekening, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;

//TABEL
$header = array (
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:90%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SKPD', 'width' => '300px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Anggaran', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realiasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggperuk', 'a');
$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(a.anggaran)', 'anggaran');
$query->condition('a.kodero', db_like($akun) . '%', 'LIKE'); 
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.kodedinas');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($akun) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->namauk, 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->anggaran),'width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)),'width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
	);
	
	$anggaran_total += $datas->anggaran;
	$realisasi_total += $realisasi;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '20px',  'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_total, $realisasi_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);


//RENDER	
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

?>


