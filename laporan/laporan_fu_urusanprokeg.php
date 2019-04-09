<?php
function laporan_fu_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 125px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	$margin =0;
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$margin =arg(3);
				$tanggal =arg(4);
				$cetakpdf = arg(5);
				$ttdlaporan = arg(6);
				$index = arg(7);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$margin = '15';
		$tanggal = date('j F Y');
		$ttdlaporan = 2;
		$index = 1;
	}
	
	//drupal_set_title('BELANJA');
	 if ($cetakpdf=='pdf_uspk') {
		//$output = 'abc';
		$index = arg(7);
		if ($index=='') $index = '1';
		$output = gen_report_realisasi_print_urusan_program_kegiatan($tanggal,  $ttdlaporan, $index);
		apbd_ExportPDF_L($output, $margin, "LAP");
		//return $output;
		
	} elseif ($cetakpdf=='xls_uspk') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Fungsi Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_urusan_program_kegiatan_print_xls($tanggal,  $ttdlaporan, $index);
		echo $output;
				
	} elseif ($cetakpdf=='fus') {
		$output = gen_report_realisasi_print_fungsi_urusan_skpd($tanggal,  $ttdlaporan, $index);
		//$output = '';
		$output_form = drupal_get_form('laporan_fu_main_form');	

		$btn = '<a href = "/laporanfu/filter/'.$bulan.'/'.$margin.'/'.urlencode($tanggal).'/pdf_uspk/'.$ttdlaporan.'/'.$index.'"><button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle"> Cetak </button></a>';		
		$btn .= ' <a href = "/laporanfu/filter/'.$bulan.'/'.$margin.'/'.urlencode($tanggal).'/xls_uspk/'.$ttdlaporan.'/'.$index.'"><button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle"> Excel </button></a>';
				
		return drupal_render($output_form) . $btn . $output	;
		
	}else {
		
		
		$output = gen_report_realisasi_print_urusan_program_kegiatan($tanggal,  $ttdlaporan, $index);
		//$output = gen_report_realisasi_print_fungsi_urusan_skpd($tanggal,  $ttdlaporan, $index);
		//$output = '';
		$output_form = drupal_get_form('laporan_fu_main_form');	

		$btn = '<a href = "/laporanfu/filter/'.$bulan.'/'.$margin.'/'.urlencode($tanggal).'/pdf_uspk/'.$ttdlaporan.'/'.$index.'"><button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle"> Cetak </button></a>';		
		$btn .= ' <a href = "/laporanfu/filter/'.$bulan.'/'.$margin.'/'.urlencode($tanggal).'/xls_uspk/'.$ttdlaporan.'/'.$index.'"><button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle"> Excel </button></a>';
				
		return drupal_render($output_form) . $btn . $output	;
		
	}	
	
}

function laporan_fu_main_form($form, &$form_state) {
	
	$bulan = date('n');
	$margin = 15;
	$tanggal = date('j F Y');
	$ttdlaporan = '2';
	$index = 1;
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		$ttdlaporan = arg(6);
		$index = arg(7);
	}
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PILIHAN DATA<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
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
	$form['formdata']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => $margin,
	);
	$form['formdata']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' => $tanggal ,
	);
	
	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');
	$form['formdata']['ttdlaporan']= array(
		'#type'         => 'select', 
		'#title' =>  t('PENANDA TANGAN LAPORAN'),
		'#options' => $penandatangan,
		'#default_value'=> $ttdlaporan,
	);
	
	$form['formdata']['index'] = array(
		'#type' => 'select',
		'#title' => 'Index',
		'#default_value' => $index,	
		'#options' => array(1=>1, 2=>2, 3=>3, 4=>4),
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>Tampilkan Urusan/OPD/Program/Kegiatan',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	$form['formdata']['submitf'] = array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>Tampilkan Fungsi/Urusan/OPD',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function laporan_fu_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$margin = $form_state['values']['margin'];
	$tanggal = $form_state['values']['tanggal'];
	$ttdlaporan= $form_state['values']['ttdlaporan'];
	$index= $form_state['values']['index'];
	$cetakpdf = '0';
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) 
		$cetakpdf = '0';
	else
		$cetakpdf = 'fus';
	
	$uri = 'laporanfu/filter/' . $bulan . '/' . $margin. '/' . $tanggal . '/' . $cetakpdf . '/' . $ttdlaporan . '/' . $index;
		
	drupal_goto($uri);
	
}

function gen_report_realisasi_print_urusan_program_kegiatan($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARA';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN - OPD - PROGRAM - KEGIATAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:2px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN','width' => '150px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '285px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold;'),
);
	
$header[] = array (
	array('data' => 'PROGRAM/KEGIATAN', 'width' => '150px', 'style'=>'border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
	
	
);

$rows = array();
$agg_521_t = 0; $agg_522_t = 0; $agg_523_t = 0; 
$rea_521_t = 0; $rea_522_t = 0; $rea_523_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->condition('f.sifat', $index, '='); 
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_521)', 'agg_521');
	$sql->addExpression('SUM(k.agg_522)', 'agg_522');
	$sql->addExpression('SUM(k.agg_523)', 'agg_523');

	$sql->addExpression('SUM(k.rea_521)', 'rea_521');
	$sql->addExpression('SUM(k.rea_522)', 'rea_522');
	$sql->addExpression('SUM(k.rea_523)', 'rea_523');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_521 = $data->agg_521;
		$agg_522 = $data->agg_522;
		$agg_523 = $data->agg_523;

		$rea_521 = $data->rea_521;
		$rea_522 = $data->rea_522;
		$rea_523 = $data->rea_523;
		
	}
	
	$agg = $agg_521 + $agg_522 + $agg_523;
	$rea = $rea_521 + $rea_522 + $rea_523;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_521_t += $agg_521; $agg_522_t += $agg_522; $agg_523_t += $agg_523; 
	$rea_521_t += $rea_521; $rea_522_t += $rea_522; $rea_523_t += $rea_523; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
		$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_521)', 'agg_521');
		$sql->addExpression('SUM(k.agg_522)', 'agg_522');
		$sql->addExpression('SUM(k.agg_523)', 'agg_523');

		$sql->addExpression('SUM(k.rea_521)', 'rea_521');
		$sql->addExpression('SUM(k.rea_522)', 'rea_522');
		$sql->addExpression('SUM(k.rea_523)', 'rea_523');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_521 = $data->agg_521;
			$agg_522 = $data->agg_522;
			$agg_523 = $data->agg_523;

			$rea_521 = $data->rea_521;
			$rea_522 = $data->rea_522;
			$rea_523 = $data->rea_523;
		}
		$agg = $agg_521 + $agg_522 + $agg_523;
		$rea = $rea_521 + $rea_522 + $rea_523;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_521 + $data_skpd->agg_522 + $data_skpd->agg_523;
			$rea = $data_skpd->rea_521 + $data_skpd->rea_522 + $data_skpd->rea_523;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				
			);			

				//PROGRAM
				$res_program = db_query('SELECT program.kodepro, program.program, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu and kegiatan_bl.kodeuk=:kodeuk group by program.kodepro, program.program order by program.kodepro', array(':kodeu'=>$data_u->kodeu, ':kodeuk'=>$data_skpd->kodeuk));	
				foreach ($res_program as $data_program) {
					
					$agg = $data_program->agg_521 + $data_program->agg_522 + $data_program->agg_523;
					$rea = $data_program->rea_521 + $data_program->rea_522 + $data_program->rea_523;

					//space
					$rows[] = array(
						array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:15%;'),

						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),		
					);						
					$rows[] = array(
						array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
						array('data' => strtoupper($data_program->program ) , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						array('data' => apbd_fn($data_program->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),

						array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						
					);			
					
					//KEGIATAN
					$res_keg = db_query('SELECT kodepro, kodekeg, kegiatan, agg_521, agg_522, agg_523, rea_521, rea_522, rea_523 from kegiatan_bl where (agg_521+agg_522+agg_523)>0 and kodepro=:kodepro and kodeuk=:kodeuk order by kodekeg', array(':kodepro'=>$data_program->kodepro, ':kodeuk'=>$data_skpd->kodeuk));	
					foreach ($res_keg as $data_keg) {

						$agg = $data_keg->agg_521 + $data_keg->agg_522 + $data_keg->agg_523;
						$rea = $data_keg->rea_521 + $data_keg->rea_522 + $data_keg->rea_523;
						
						//font-style: italic
						$rows[] = array(
							array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro . '.' . substr($data_keg->kodekeg, -3), 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data_keg->kegiatan  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

							array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							
						);			
						
						
						
					}	//KEGAITAN					
					
				}	//PROGRAM
				
				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

if ($index=='4') {

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

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_521_t = $data->agg_521;
		$agg_522_t = $data->agg_522;
		$agg_523_t = $data->agg_523;

		$rea_521_t = $data->rea_521;
		$rea_522_t = $data->rea_522;
		$rea_523_t = $data->rea_523;
		
	}	
	$agg_t = $agg_521_t + $agg_522_t + $agg_523_t;
	$rea_t = $rea_521_t + $rea_522_t + $rea_523_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '','colspan'=>12,  'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal , 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					 
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'NIP. ' . $pimpinannip, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {

	//NEXT
	$index++;
	$query = db_select('urusansifat', 'f');
	$query->fields('f', array('sifat', 'uraian'));
	$query->condition('f.sifat', $index, '='); 
	$query->orderBy('f.sifat');
	$results = $query->execute();

	foreach ($results as $datas) {
		$rows[] = array(
			array('data' => 'Selanjutnya : ' . $datas->sifat . '. '. $datas->uraian . '...', 'colspan'=>12, 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
			
		);
	}		
	$rows[] = array(
		array('data' => '', 'colspan'=>12, 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_fungsi_urusan_skpd($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARA';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI - URUSAN - OPD</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>3,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:2px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI','width' => '150px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>13, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>14, 'width' => '285px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'rowspan'=>2,'width' => '70px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold;'),
);

$header[] = array (
	array('data' => 'URUSAN','width' => '150px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TIDAK LANGSUNG','colspan'=>8, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA LANGSUNG','colspan'=>4, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL','rowspan'=>2, 'width' => '65px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'BELANJA TIDAK LANGSUNG','colspan'=>8, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'), 
	array('data' => 'BELANJA LANGSUNG','colspan'=>4, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL','rowspan'=>2, 'width' => '65px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => '%','rowspan'=>2, 'width' => '20px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);	

$header[] = array (
	array('data' => 'SKPD', 'width' => '150px', 'style'=>'border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	//ANGGARAN
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SUBSIDI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'HIBAH', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BANSOS', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BAGI HSL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BANKEU', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TDK TERDUGA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	
	//REALISASI	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SUBSIDI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'HIBAH', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BANSOS', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BAGI HSL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BANKEU', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TDK TERDUGA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	//array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	//array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
	
	
);

$rows = array();
$agg_521_t = 0; $agg_522_t = 0; $agg_523_t = 0; 
$rea_521_t = 0; $rea_522_t = 0; $rea_523_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//FUNGSI
$query = db_select('fungsi', 'f');
$query->fields('f', array('kodef', 'fungsi'));
$query->condition('f.sifat', $index, '=');
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_gaji = 0; $agg_subsidi = 0; $agg_hibah = 0; $agg_bansos = 0; $agg_bagihasil = 0; $agg_bankeu = 0; $agg_ttdg = 0; 
	$rea_gaji = 0; $rea_subsidi = 0; $rea_hibah = 0; $rea_bansos = 0; $rea_bagihasil = 0; $rea_bankeu = 0; $rea_ttdg = 0; 

	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_521)', 'agg_521');
	$sql->addExpression('SUM(k.agg_522)', 'agg_522');
	$sql->addExpression('SUM(k.agg_523)', 'agg_523');

	$sql->addExpression('SUM(k.rea_521)', 'rea_521');
	$sql->addExpression('SUM(k.rea_522)', 'rea_522');
	$sql->addExpression('SUM(k.rea_523)', 'rea_523');
	
	$sql->condition('u.kodef', $datas->kodef, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_521 = $data->agg_521;
		$agg_522 = $data->agg_522;
		$agg_523 = $data->agg_523;

		$rea_521 = $data->rea_521;
		$rea_522 = $data->rea_522;
		$rea_523 = $data->rea_523;
		
	}
	
	$agg = $agg_521 + $agg_522 + $agg_523;
	$rea = $rea_521 + $rea_522 + $rea_523;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
	
		//agg btl
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//agg bl
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//agg total
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//rea btl
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
		
		//rea bl
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//rea total
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//rea %
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		//sisa agg
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->kodef , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->fungsi, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		

		array('data' => apbd_fn($agg_gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_subsidi) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_hibah) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' => apbd_fn($agg_bansos) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_bagihasil) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_ttdg) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_btl) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_bl) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px 
		solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		
		//realisasi
		array('data' => apbd_fn($rea_gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_subsidi) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_hibah) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' => apbd_fn($rea_bansos) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_bagihasil) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_ttdg) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_btl) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_bl) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_521_t += $agg_521; $agg_522_t += $agg_522; $agg_523_t += $agg_523; 
	$rea_521_t += $rea_521; $rea_522_t += $rea_522; $rea_523_t += $rea_523; 
	
	$agg_t += $agg; $rea_t += $rea;
	

}	//foreach ($results as $datas)

if ($index=='4') {

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

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_521_t = $data->agg_521;
		$agg_522_t = $data->agg_522;
		$agg_523_t = $data->agg_523;

		$rea_521_t = $data->rea_521;
		$rea_522_t = $data->rea_522;
		$rea_523_t = $data->rea_523;
		
	}	
	$agg_t = $agg_521_t + $agg_522_t + $agg_523_t;
	$rea_t = $rea_521_t + $rea_522_t + $rea_523_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '','colspan'=>12,  'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal , 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					 
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'NIP. ' . $pimpinannip, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {


	$rows[] = array(
		array('data' => '', 'colspan'=>12, 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_fungsi_urusan_skpd_bl($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARA';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI - URUSAN - OPD</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:2px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI','width' => '150px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '285px', 'style'=>'border-top:2px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'style'=>'border-top:2px solid black;border-right:1px solid black;font-weight: bold;'),
);
	
$header[] = array (
	array('data' => 'URUSAN/SKPD', 'width' => '150px', 'style'=>'border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold;'),
	
	
);

$rows = array();
$agg_521_t = 0; $agg_522_t = 0; $agg_523_t = 0; 
$rea_521_t = 0; $rea_522_t = 0; $rea_523_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//FUNGSI
$query = db_select('fungsi', 'f');
$query->fields('f', array('kodef', 'fungsi'));
if ($index=='1') {
	$query->condition('f.kodef', '03', '<='); 

} elseif ($index=='2') {
	$query->condition('f.kodef', '04', '>='); 
	$query->condition('f.kodef', '06', '<='); 
	
} elseif ($index=='3') {
	$query->condition('f.kodef', '07', '>='); 
	$query->condition('f.kodef', '09', '<='); 

} else {
	$query->condition('f.kodef', '10', '>='); 
	$query->condition('f.kodef', '12', '<='); 
	
} 
 
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_521)', 'agg_521');
	$sql->addExpression('SUM(k.agg_522)', 'agg_522');
	$sql->addExpression('SUM(k.agg_523)', 'agg_523');

	$sql->addExpression('SUM(k.rea_521)', 'rea_521');
	$sql->addExpression('SUM(k.rea_522)', 'rea_522');
	$sql->addExpression('SUM(k.rea_523)', 'rea_523');
	
	$sql->condition('u.kodef', $datas->kodef, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_521 = $data->agg_521;
		$agg_522 = $data->agg_522;
		$agg_523 = $data->agg_523;

		$rea_521 = $data->rea_521;
		$rea_522 = $data->rea_522;
		$rea_523 = $data->rea_523;
		
	}
	
	$agg = $agg_521 + $agg_522 + $agg_523;
	$rea = $rea_521 + $rea_522 + $rea_523;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->kodef , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->fungsi, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_521_t += $agg_521; $agg_522_t += $agg_522; $agg_523_t += $agg_523; 
	$rea_521_t += $rea_521; $rea_522_t += $rea_522; $rea_523_t += $rea_523; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.kodef', $datas->kodef, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
		$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_521)', 'agg_521');
		$sql->addExpression('SUM(k.agg_522)', 'agg_522');
		$sql->addExpression('SUM(k.agg_523)', 'agg_523');

		$sql->addExpression('SUM(k.rea_521)', 'rea_521');
		$sql->addExpression('SUM(k.rea_522)', 'rea_522');
		$sql->addExpression('SUM(k.rea_523)', 'rea_523');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_521 = $data->agg_521;
			$agg_522 = $data->agg_522;
			$agg_523 = $data->agg_523;

			$rea_521 = $data->rea_521;
			$rea_522 = $data->rea_522;
			$rea_523 = $data->rea_523;
		}
		$agg = $agg_521 + $agg_522 + $agg_523;
		$rea = $rea_521 + $rea_522 + $rea_523;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $datas->kodef . '.' . $data_u->kodeu , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_521 + $data_skpd->agg_522 + $data_skpd->agg_523;
			$rea = $data_skpd->rea_521 + $data_skpd->rea_522 + $data_skpd->rea_523;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $datas->kodef . '.' . $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
				
				
			);			


				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

if ($index=='4') {

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

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_521_t = $data->agg_521;
		$agg_522_t = $data->agg_522;
		$agg_523_t = $data->agg_523;

		$rea_521_t = $data->rea_521;
		$rea_522_t = $data->rea_522;
		$rea_523_t = $data->rea_523;
		
	}	
	$agg_t = $agg_521_t + $agg_522_t + $agg_523_t;
	$rea_t = $rea_521_t + $rea_522_t + $rea_523_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '','colspan'=>12,  'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal , 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					 
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'NIP. ' . $pimpinannip, 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),					
			);
	$rows[] = array(
				array('data' => '', 'colspan'=>6, 'width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'colspan'=>6, 'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {


	$rows[] = array(
		array('data' => '', 'colspan'=>12, 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}
 

function gen_report_realisasi_print_urusan_program_kegiatan_print($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARAX';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN - OPD - PROGRAM - KEGIATAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN','width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '285px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'PROGRAM/KEGIATAN', 'width' => '150px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	
	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();
$agg_521_t = 0; $agg_522_t = 0; $agg_523_t = 0; 
$rea_521_t = 0; $rea_522_t = 0; $rea_523_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->condition('f.sifat', $index, '='); 
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_521)', 'agg_521');
	$sql->addExpression('SUM(k.agg_522)', 'agg_522');
	$sql->addExpression('SUM(k.agg_523)', 'agg_523');

	$sql->addExpression('SUM(k.rea_521)', 'rea_521');
	$sql->addExpression('SUM(k.rea_522)', 'rea_522');
	$sql->addExpression('SUM(k.rea_523)', 'rea_523');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_521 = $data->agg_521;
		$agg_522 = $data->agg_522;
		$agg_523 = $data->agg_523;

		$rea_521 = $data->rea_521;
		$rea_522 = $data->rea_522;
		$rea_523 = $data->rea_523;
		
	}
	
	$agg = $agg_521 + $agg_522 + $agg_523;
	$rea = $rea_521 + $rea_522 + $rea_523;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_521_t += $agg_521; $agg_522_t += $agg_522; $agg_523_t += $agg_523; 
	$rea_521_t += $rea_521; $rea_522_t += $rea_522; $rea_523_t += $rea_523; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
		$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_521)', 'agg_521');
		$sql->addExpression('SUM(k.agg_522)', 'agg_522');
		$sql->addExpression('SUM(k.agg_523)', 'agg_523');

		$sql->addExpression('SUM(k.rea_521)', 'rea_521');
		$sql->addExpression('SUM(k.rea_522)', 'rea_522');
		$sql->addExpression('SUM(k.rea_523)', 'rea_523');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_521 = $data->agg_521;
			$agg_522 = $data->agg_522;
			$agg_523 = $data->agg_523;

			$rea_521 = $data->rea_521;
			$rea_522 = $data->rea_522;
			$rea_523 = $data->rea_523;
		}
		$agg = $agg_521 + $agg_522 + $agg_523;
		$rea = $rea_521 + $rea_522 + $rea_523;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_521 + $data_skpd->agg_522 + $data_skpd->agg_523;
			$rea = $data_skpd->rea_521 + $data_skpd->rea_522 + $data_skpd->rea_523;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				
			);			

				//PROGRAM
				$res_program = db_query('SELECT program.kodepro, program.program, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu and kegiatan_bl.kodeuk=:kodeuk group by program.kodepro, program.program order by program.kodepro', array(':kodeu'=>$data_u->kodeu, ':kodeuk'=>$data_skpd->kodeuk));	
				foreach ($res_program as $data_program) {
					
					$agg = $data_program->agg_521 + $data_program->agg_522 + $data_program->agg_523;
					$rea = $data_program->rea_521 + $data_program->rea_522 + $data_program->rea_523;

					//space
					$rows[] = array(
						array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:15%;'),

						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),		
					);						
					$rows[] = array(
						array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
						array('data' => strtoupper($data_program->program ) , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						array('data' => apbd_fn($data_program->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),

						array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						
					);			
					
					//KEGIATAN
					$res_keg = db_query('SELECT kodepro, kodekeg, kegiatan, agg_521, agg_522, agg_523, rea_521, rea_522, rea_523 from kegiatan_bl where (agg_521+agg_522+agg_523)>0 and kodepro=:kodepro and kodeuk=:kodeuk order by kodekeg', array(':kodepro'=>$data_program->kodepro, ':kodeuk'=>$data_skpd->kodeuk));	
					foreach ($res_keg as $data_keg) {

						$agg = $data_keg->agg_521 + $data_keg->agg_522 + $data_keg->agg_523;
						$rea = $data_keg->rea_521 + $data_keg->rea_522 + $data_keg->rea_523;
						
						//font-style: italic
						$rows[] = array(
							array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro . '.' . substr($data_keg->kodekeg, -3), 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data_keg->kegiatan  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

							array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							
						);			
						
						
						
					}	//KEGAITAN					
					
				}	//PROGRAM
				
				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

if ($index=='4') {

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

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_521_t = $data->agg_521;
		$agg_522_t = $data->agg_522;
		$agg_523_t = $data->agg_523;

		$rea_521_t = $data->rea_521;
		$rea_522_t = $data->rea_522;
		$rea_523_t = $data->rea_523;
		
	}	
	$agg_t = $agg_521_t + $agg_522_t + $agg_523_t;
	$rea_t = $rea_521_t + $rea_522_t + $rea_523_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	/*
	$rows[] = array(
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	*/
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {
	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_urusan_program_kegiatan_print_xls($tanggal,  $penandatangan) {

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

$skpd = 'KABUPATEN JEPARA';


$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN - OPD - PROGRAM - KEGIATAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>11, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '50px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN /PROGRAM /KEGAITAN','rowspan'=>2,'width' => '150px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '265px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BRG JS', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BRG JS', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	
	array('data' => 'RUPIAH', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();

//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_521)', 'agg_521');
	$sql->addExpression('SUM(k.agg_522)', 'agg_522');
	$sql->addExpression('SUM(k.agg_523)', 'agg_523');

	$sql->addExpression('SUM(k.rea_521)', 'rea_521');
	$sql->addExpression('SUM(k.rea_522)', 'rea_522');
	$sql->addExpression('SUM(k.rea_523)', 'rea_523');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_521 = $data->agg_521;
		$agg_522 = $data->agg_522;
		$agg_523 = $data->agg_523;

		$rea_521 = $data->rea_521;
		$rea_522 = $data->rea_522;
		$rea_523 = $data->rea_523;
		
	}
	
	$agg = $agg_521 + $agg_522 + $agg_523;
	$rea = $rea_521 + $rea_522 + $rea_523;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:2px solid black;font-weight: bold'),
		
	);
	
	$agg_521_t += $agg_521; $agg_522_t += $agg_522; $agg_523_t += $agg_523; 
	$rea_521_t += $rea_521; $rea_522_t += $rea_522; $rea_523_t += $rea_523; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
		$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_521)', 'agg_521');
		$sql->addExpression('SUM(k.agg_522)', 'agg_522');
		$sql->addExpression('SUM(k.agg_523)', 'agg_523');

		$sql->addExpression('SUM(k.rea_521)', 'rea_521');
		$sql->addExpression('SUM(k.rea_522)', 'rea_522');
		$sql->addExpression('SUM(k.rea_523)', 'rea_523');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_521 = $data->agg_521;
			$agg_522 = $data->agg_522;
			$agg_523 = $data->agg_523;

			$rea_521 = $data->rea_521;
			$rea_522 = $data->rea_522;
			$rea_523 = $data->rea_523;
		}
		$agg = $agg_521 + $agg_522 + $agg_523;
		$rea = $rea_521 + $rea_522 + $rea_523;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_521 + $data_skpd->agg_522 + $data_skpd->agg_523;
			$rea = $data_skpd->rea_521 + $data_skpd->rea_522 + $data_skpd->rea_523;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($data_skpd->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($data_skpd->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($data_skpd->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($data_skpd->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-weight: bold;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				
			);			

				//PROGRAM
				$res_program = db_query('SELECT program.kodepro, program.program, sum(kegiatan_bl.agg_521) agg_521, sum(kegiatan_bl.agg_522) agg_522, sum(kegiatan_bl.agg_523) agg_523, sum(kegiatan_bl.rea_521) rea_521, sum(kegiatan_bl.rea_522) rea_522, sum(kegiatan_bl.rea_523) rea_523 from kegiatan_bl inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu and kegiatan_bl.kodeuk=:kodeuk group by program.kodepro, program.program order by program.kodepro', array(':kodeu'=>$data_u->kodeu, ':kodeuk'=>$data_skpd->kodeuk));	
				foreach ($res_program as $data_program) {
					
					$agg = $data_program->agg_521 + $data_program->agg_522 + $data_program->agg_523;
					$rea = $data_program->rea_521 + $data_program->rea_522 + $data_program->rea_523;

					//space
					$rows[] = array(
						array('data' => '', 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:15%;'),

						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),		
					);						
					$rows[] = array(
						array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro, 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
						array('data' => strtoupper($data_program->program ) , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
						array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

						array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
						
						
					);			
					
					//KEGIATAN
					$res_keg = db_query('SELECT kodepro, kodekeg, kegiatan, agg_521, agg_522, agg_523, rea_521, rea_522, rea_523 from kegiatan_bl where (agg_521+agg_522+agg_523)>0 and kodepro=:kodepro and kodeuk=:kodeuk order by kodekeg', array(':kodepro'=>$data_program->kodepro, ':kodeuk'=>$data_skpd->kodeuk));	
					foreach ($res_keg as $data_keg) {

						$agg = $data_keg->agg_521 + $data_keg->agg_522 + $data_keg->agg_523;
						$rea = $data_keg->rea_521 + $data_keg->rea_522 + $data_keg->rea_523;
					
						$rows[] = array(
							array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro . '.' . substr($data_keg->kodekeg, -3), 'align' => 'left', 'width' => '50px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data_keg->kegiatan  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-style: italic'),
							
							array('data' => apbd_fn($data_keg->agg_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($data_keg->agg_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($data_keg->agg_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->rea_521) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($data_keg->rea_522) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($data_keg->rea_523) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-style: italic'),
							array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-style: italic'),

							array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-style: italic'),
							
							
						);			
						
						
						
					}	//KEGAITAN					
					
				}	//PROGRAM
				
				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

//TOTAL
$rows[] = array(
	array('data' => '' , 'align' => 'left', 'width' => '50px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($agg_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($agg_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($agg_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
	array('data' => apbd_fn($rea_521_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($rea_522_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($rea_523_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),

	array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
);


//ttd
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	
);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
		);
/*
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
*/
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}
?>


