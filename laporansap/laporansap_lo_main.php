<?php
function laporansap_lo_main($arg=NULL, $nama=NULL) {
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
		header( "Content-disposition: attachment; filename=Laporan Komulatif.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat, false);
		$output_form = drupal_get_form('laporansap_lo_main_form');	
		
		
		$btn = l('Cetak', 'laporanlo/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf/' . $ttdlaporan , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporanlo/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel/' . $ttdlaporan , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporansap_lo_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];
	$ttdlaporan= $form_state['values']['ttdlaporan'];
	$cetakpdf = 0;
	
	$uri = 'laporanlo/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri . '/' . $cetakpdf . '/' . $ttdlaporan;
	drupal_goto($uri);
	
}


function laporansap_lo_main_form($form, &$form_state) {
	
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

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

$margin = 10;
$marginkiri = 20;

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

$pendapatan_total = 0; $belanja_total = 0; 

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'width' => '5px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	//$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
	$tanggal_awal = apbd_tahun() . '-04-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '8', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
	);
	
	$pendapatan_total = $datas->realisasi;
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,2)=k.kodek');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	
	foreach ($results_kel as $data_kel) {
		
		$uraian = l($data_kel->uraian, '/akuntansi/bukusap/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'valign'=>'top'),
		);		
		
		
		//JENIS
		$query = db_select('jenissap', 'jen');
		$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,3)=jen.kodej');
		$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
		$query->fields('jen', array('kodej', 'uraian'));
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$query->condition('jen.kodek', $data_kel->kodek, '='); 
		$query->groupBy('jen.kodej');
		$query->orderBy('jen.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$uraian = l($data_jen->uraian, '/akuntansi/bukusap/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$detil = l('Detil', '/laporansapdetillo/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
			);
			
			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyeksap', 'o');
				$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
				$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
				$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					$detil = l('Detil', '/laporansapdetillo/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
					
					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat>='5') {
						$query = db_select('rincianobyeksap', 'ro');
						$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
						$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$skpd = ($kodeuk=='ZZ'? l('<em>SKPD</em>', '/laporansapdetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'width' => '10px', 'valign'=>'top'),
							);
					
						}//rincian obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '9', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	$belanja_total = $datas->realisasi;
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,2)=k.kodek');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	
	foreach ($results_kel as $data_kel) {
		
		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
		$uraian = l($data_kel->uraian, '/akuntansi/bukusap/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'align' => 'right', 'valign'=>'top'),
		);		
		
		
		//JENIS
		$query = db_select('jenissap', 'jen');
		$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,3)=jen.kodej');
		$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
		$query->fields('jen', array('kodej', 'uraian'));
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$query->condition('jen.kodek', $data_kel->kodek, '='); 
		$query->groupBy('jen.kodej');
		$query->orderBy('jen.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
			$uraian = l($data_jen->uraian, '/akuntansi/bukusap/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$detil = l('Detil', '/laporansapdetillo/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
			);
			
			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyeksap', 'o');
				$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
				$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
				$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					$detil = l('Detil', '/laporansapdetillo/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
					
					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat>='5') {
						$query = db_select('rincianobyeksap', 'ro');
						$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
						$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$skpd = ($kodeuk=='ZZ'? l('<em>SKPD</em>', '/laporansapdetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'width' => '10px', 'valign'=>'top'),
							);
					
						}//rincian obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($pendapatan_total - $belanja_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
);		
		
//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf) {
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
	array('data' => '<strong>LAPORAN OPERASIONAL</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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

$pendapatan_total = 0; $belanja_total = 0;

$rows = null;
//TABEL
if($cetakpdf == 'excel'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '325px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}else{
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '325px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH', 'width' => '140px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}
$rows = array();

// * PENDAPATAN * //
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '8', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();


foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
	$pendapatan_total = $datas->realisasi;
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,2)=k.kodek');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {

		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		
		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}		
		$query = db_select('jenissap', 'jen');
		$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,3)=jen.kodej');
		$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
		$query->fields('jen', array('kodej', 'uraian'));
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$query->condition('jen.kodek', $data_kel->kodek, '='); 
		$query->groupBy('jen.kodej');
		$query->orderBy('jen.kodej');
		$results_jen = $query->execute();		
		foreach ($results_jen as $data_jen) {
	
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyeksap', 'o');
				$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
				$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
				$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();		
				foreach ($results_oby as $data_oby) {
					
					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyeksap', 'ro');
						$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
						$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
						
							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);
					
						}	//obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)


// * BELANJA * //
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '9', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
	$belanja_total = $datas->realisasi;
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,2)=k.kodek');
	$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}		
		
		$query = db_select('jenissap', 'jen');
		$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,3)=jen.kodej');
		$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
		$query->fields('jen', array('kodej', 'uraian'));
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$query->condition('jen.kodek', $data_kel->kodek, '='); 
		$query->groupBy('jen.kodej');
		$query->orderBy('jen.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyeksap', 'o');
				$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,5)=o.kodeo');
				$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
				$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();		
				foreach ($results_oby as $data_oby) {
					
					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyeksap', 'ro');
						$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'ji.kodero=ro.kodero');
						$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT) OPERASIONAL</strong>', 'width' => '325px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($pendapatan_total - $belanja_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
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
	
}
	
//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

return $tabel_data;

}


?>


