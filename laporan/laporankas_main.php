<?php
function laporankas_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
	$cetakpdf = '';
    
	if (arg(2)!=null) {
		switch($arg) {
			case 'filter':
				$jenisbelanja = arg(2);
				$kelompok = arg(3);			
				$bulan = arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$jenisbelanja = '5';	
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kelompok = 'SEMUA';
	}

	if ($jenisbelanja=='') $jenisbelanja = 'SEMUA';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($jenisbelanja, $kelompok, $bulan);
		print_pdf_p($output);

	} else {
		$output_form = drupal_get_form('laporankas_main_form');
		$output = gen_report_realisasi($jenisbelanja, $kelompok, $bulan);
		
		$btn = l('Cetak', 'laporankas/filter/' . $jenisbelanja . '/' . $kelompok . '/' . $bulan . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}

function laporankas_main_form_submit($form, &$form_state) {
	$jenisbelanja= $form_state['values']['jenisbelanja'];
	$bulan= $form_state['values']['bulan'];
	$kelompok= $form_state['values']['kelompok'];
	
	$uri = 'laporankas/filter/' . $jenisbelanja . '/' . $kelompok . '/' . $bulan;
	drupal_goto($uri);
	
}


function laporankas_main_form($form, &$form_state) {
	
	$jenisbelanja = 'SEMUA';
	$bulan = date('m');
	$kelompok = 'SEMUA';

	
	if(arg(2)!=null){
		$jenisbelanja = arg(2);
		$kelompok = arg(3);			
		$bulan = arg(4);
	}

	if ($jenisbelanja=='') $akun = 'SEMUA';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . '|' . $kelompok . '|' . $jenisbelanja . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	


	$form['formdata']['kelompok']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kelompok,
		
		'#options' => array('SEMUA'=>'SEMUA', 
							'0'=>'DINAS/BADAN/KANTOR',
							'1'=>'KECAMATAN',
							'2'=>'PUSKESMAS',
							'3'=>'SEKOLAH',
							'4'=>'UPT DIKPORA'),	
	);		

	$form['formdata']['jenisbelanja']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Belanja'), 
		'#default_value' => $jenisbelanja,
		
		'#options' => array('SEMUA'=>'SEMUA', 'TIDAK LANGSUNG'=>'TIDAK LANGSUNG', 'LANGSUNG'=>'LANGSUNG'),
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

function gen_report_realisasi($jenisbelanja, $kelompok, $bulan) {
	
$total_anggaran = 0;
$total_realisasi = 0;

$header = array (
	array('data' => 'No','width' => '10px', 'valign'=>'top'),
	array('data' => '', 'width' => '5px','valign'=>'top'), 
	array('data' => 'SKPD', 'field'=> 'namauk', 'valign'=>'top'), 
	array('data' => 'Rencana', 'field'=> 'anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'field'=> 'realisasi', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Persen', 'field'=> 'persen', 'width' => '10px', 'valign'=>'top'),
	array('data' => '', 'width' => '20px', 'valign'=>'top'),
);


$query = db_select('progressrea', 'p')->extend('TableSort');
$query->innerJoin('unitkerja', 'u', 'p.kodeuk=u.kodeuk');

# get the desired fields from the database
$query->fields('u', array('kodeuk','kodedinas','namauk'));

if ($jenisbelanja=='LANGSUNG') { 
	$query->addExpression('p.blrea/1000', 'realisasi');
	$query->addExpression('p.blagg/1000', 'anggaran');
	$query->addExpression('p.blpersen', 'persen');
	
} else if ($jenisbelanja=='TIDAK LANGSUNG') { 
	$query->addExpression('p.btlrea/1000', 'realisasi');
	$query->addExpression('p.btlagg/1000', 'anggaran');
	$query->addExpression('p.btlpersen', 'persen');
	
} else {
	$query->addExpression('p.totalrea/1000', 'realisasi');
	$query->addExpression('p.totalagg/1000', 'anggaran');
	$query->addExpression('p.totalpersen', 'persen');
	
}

$query->condition('p.bulan', $bulan, '=');
if ($kelompok!='SEMUA') { 
	$query->condition('u.kelompok', $kelompok, '=');
}

$query->orderByHeader($header);
$query->orderBy('persen', 'ASC');

//dpq($query)	;
# execute the query
$results = $query->execute();
	
# build the table fields
$no=0;


if (isset($_GET['page'])) {
	$page = $_GET['page'];
	$no = $page * $limit;
} else {
	$no = 0;
} 


$uri = 'public://';
$path= file_create_url($uri);	
	
$rows = array();
foreach ($results as $data) {
	$no++;  

	$total_anggaran += $data->anggaran;
	$total_realisasi += $data->realisasi;
	

	if ($data->realisasi <= $data->anggaran) {
		
		if ($data->persen <= 20)
			if ($data->anggaran==0)
				$imgstatus = "<img src='" . $path . "/icon/progress00.png'>";
			else
				$imgstatus = "<img src='" . $path . "/icon/progress20.png'>";
			
		else if ($data->persen <= 40)
			$imgstatus = "<img src='" . $path . "/icon/progress40.png'>";
		else if ($data->persen <= 60)
			$imgstatus = "<img src='" . $path . "/icon/progress60.png'>";
		else if ($data->persen <= 80)
			$imgstatus = "<img src='" . $path . "/icon/progress80.png'>";
		else
			$imgstatus = "<img src='" . $path . "/icon/progress100.png'>";
		
	} else {
		//$imgstatus = "<img src='/files/icon/progress-ex.png'>";
		$imgstatus = "<img src='" . $path . "/icon/progress-ex.png'>";
	}	
	
	$skpd = l($data->namauk, 'kaskegiatan/filter/' . $data->kodeuk . '/SEMUA/' . $bulan  . '/SEMUA', array ('html' => true));
	
	$rows[] = array(
					array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
					array('data' => $imgstatus, 'align' => 'left', 'valign'=>'top'),
					array('data' => $skpd, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1($data->persen), 'align' => 'right', 'valign'=>'top'),
				);
}

$persen = apbd_hitungpersen($total_anggaran, $total_realisasi);
if ($total_realisasi <= $total_anggaran) {
	 
	if ($persen <= 20)
		if ($datas->anggaran==0)
			$imgstatus = "<img src='" . $path . "/icon/progress00.png'>";
		else
			$imgstatus = "<img src='" . $path . "/icon/progress20.png'>";
		
	else if ($persen <= 40)
		$imgstatus = "<img src='" . $path . "/icon/progress40.png'>";
	else if ($persen <= 60)
		$imgstatus = "<img src='" . $path . "/icon/progress60.png'>";
	else if ($persen <= 80)
		$imgstatus = "<img src='" . $path . "/icon/progress80.png'>";
	else
		$imgstatus = "<img src='" . $path . "/icon/progress100.png'>";
	
} else {
	//$imgstatus = "<img src='/files/icon/progress-ex.png'>";
	$imgstatus = "<img src='" . $path . "/icon/progress-ex.png'>";
}

$rows[] = array(
				array('data' => '', 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
				array('data' => $imgstatus, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($total_anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($total_realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1($persen) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);
				
//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;


}

function gen_report_realisasi_print($jenisbelanja, $kelompok, $bulan) {
	
$total_anggaran = 0;
$total_realisasi = 0;

$header = array (
	array('data' => 'NO','width' => '30px', 'valign'=>'top'),
	array('data' => 'SKPD', 'width' => '350px',  'valign'=>'top'), 
	array('data' => 'RENCANA', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'REALISASI', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'PERSEN', 'width' => '40px', 'valign'=>'top'),
);


$query = db_select('progressrea', 'p')->extend('TableSort');
$query->innerJoin('unitkerja', 'u', 'p.kodeuk=u.kodeuk');

# get the desired fields from the database
$query->fields('u', array('kodeuk','kodedinas','namauk'));

if ($jenisbelanja=='LANGSUNG') { 
	$query->addExpression('p.blrea/1000', 'realisasi');
	$query->addExpression('p.blagg/1000', 'anggaran');
	$query->addExpression('p.blpersen', 'persen');
	
} else if ($jenisbelanja=='TIDAK LANGSUNG') { 
	$query->addExpression('p.btlrea/1000', 'realisasi');
	$query->addExpression('p.btlagg/1000', 'anggaran');
	$query->addExpression('p.btlpersen', 'persen');
	
} else {
	$query->addExpression('p.totalrea/1000', 'realisasi');
	$query->addExpression('p.totalagg/1000', 'anggaran');
	$query->addExpression('p.totalpersen', 'persen');
	
}

$query->condition('p.bulan', $bulan, '=');
if ($kelompok!='SEMUA') { 
	$query->condition('u.kelompok', $kelompok, '=');
}

$query->orderByHeader($header);
$query->orderBy('persen', 'ASC');

//dpq($query)	;
# execute the query
$results = $query->execute();
	
# build the table fields
$no=0;


if (isset($_GET['page'])) {
	$page = $_GET['page'];
	$no = $page * $limit;
} else {
	$no = 0;
} 


$rows = array();
foreach ($results as $data) {
	$no++;  

	$total_anggaran += $data->anggaran;
	$total_realisasi += $data->realisasi;

	
	$rows[] = array(
					array('data' => $no . '.', 'width' => '30px', 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->namauk, 'width' => '350px', 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->anggaran), 'width' => '90px', 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->realisasi), 'width' => '90px', 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1($data->persen), 'width' => '45px', 'align' => 'right', 'valign'=>'top'),
				);
}

$persen = apbd_hitungpersen($total_anggaran, $total_realisasi);

$rows[] = array(
				array('data' => '',  'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($total_anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($total_realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1($persen) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);
				
//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

$top[] = array (
				array('data' => '<strong>LAPORAN REALISASI KAS PER SKPD</strong>','width' => '575px', 'align'=>'center'),
				);
$top[] = array ( 
				array('data' => 'BULAN ' . $bulan . ' TAHUN 2018','width' => '575px', 'align'=>'center'),
				);
$top[] = array (
				array('data' => '','width' => '575px', 'align'=>'center'),
				);

$headertop = array ();
$output_top = theme('table', array('header' => $headertop, 'rows' => $top ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $output_top . $tabel_data;


}


?>


