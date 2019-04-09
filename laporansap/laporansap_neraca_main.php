<?php
function laporansap_neraca_main($arg=NULL, $nama=NULL) {
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
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat, false);
		$output_form = drupal_get_form('laporansap_neraca_main_form');	
		
		
		$btn = l('Cetak', 'laporanneraca/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporanneraca/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporansap_neraca_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];

	
	$uri = 'laporanneraca/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri;
	drupal_goto($uri);
	
}


function laporansap_neraca_main_form($form, &$form_state) {
	
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

$aset_total = 0; $kewajiban_total = 0; $ekuitas_total = 0;

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Jumlah', 'width' => '90px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
);
$rows = array();

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));
if ($kodeuk=='ZZ') {
	//$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
	$tanggal_awal = apbd_tahun() . '-04-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}

// * ASET * //
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '1', '='); 
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
	
	$aset_total = $datas->realisasi;
	
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
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
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
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
				$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					

					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/bukusap/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					$detil = l('Detil', '/laporansapdetillo/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
					
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

// * KEWAJIBAN * //
$adakewajiban = false;
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '2', '='); 
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
	
	$kewajiban_total = $datas->realisasi;
	$adakewajiban = true;
	
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

if ($adakewajiban==false) {
	$rows[] = array(
		array('data' => '<strong>2</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>KEWAJIBAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>0</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
	);	
}

// * EKUITAS * //
$delta_ekuitas = 0;
$adaekuitas = false;
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '3', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	if ($aset_total < ($kewajiban_total+$datas->realisasi)) {
		$delta_ekuitas = $aset_total - ($kewajiban_total+$datas->realisasi);
	} else {
		$delta_ekuitas = ($kewajiban_total+$datas->realisasi)-$aset_total;
	}
	
	$ekuitas_total = $datas->realisasi + $delta_ekuitas;
	$adaekuitas = true;
	
	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporansapdetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($ekuitas_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
	);
	

	
	
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
			array('data' => '', 'valign'=>'top'),
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
if ($adaekuitas==false) {
	
	if ($aset_total < $kewajiban_total) {
		$delta_ekuitas = $aset_total - $kewajiban_total;
	} else {
		$delta_ekuitas = $kewajiban_total-$aset_total;
	}
	$ekuitas_total = $delta_ekuitas;
	
	$rows[] = array(
		array('data' => '<strong>3</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>EKUITAS</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($ekuitas_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
	);	
}

if ($delta_ekuitas!=0) {
	$rows[] = array(
				array('data' => '319', 'align' => 'left', 'valign'=>'top'),
				array('data' => 'Konsolidasi', 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($delta_ekuitas), 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'width' => '10px', 'valign'=>'top'),
				array('data' => '', 'width' => '10px', 'valign'=>'top'),
			);
			
	if ($tingkat>3) {
		$rows[] = array(
			array('data' => '31901', 'align' => 'left', 'valign'=>'top'),
			array('data' => 'Konsolidasi', 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($delta_ekuitas), 'align' => 'right', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
		);
		
		if ($tingkat==5) {
			$rows[] = array(
				array('data' => '31901001', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>Konsolidasi</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>' . apbd_fn($delta_ekuitas) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'width' => '10px', 'valign'=>'top'),
				array('data' => '', 'width' => '10px', 'valign'=>'top'),
			);
		}
	}
}
		
//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

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
	array('data' => '<strong>LAPORAN NERACA</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
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
if ($cetakpdf == 'excel'){
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

$aset_total = 0; $kewajiban_total = 0; $ekuitas_total = 0;

// * ASET * //
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '1', '='); 
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
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
	$aset_total = $datas->realisasi;
	
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
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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


// * KEWAJIBAN * //
$adakewajiban = false;
$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '2', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
	$kewajiban_total = $datas->realisasi;
	$adakewajiban = true;
	
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
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
			
			
			//OBYEK
			if ($tingkat>'3') {
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
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	
}	//foreach ($results as $datas)

if ($adakewajiban==false) {
	$rows[] = array(
		array('data' => '<strong>2</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>KEWAJIBAN</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn(0) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);


// * EKUITAS * //
$delta_ekuitas = 0;
$adaekuitas = false;

$query = db_select('anggaransap', 'a');
$query->innerJoin('jurnalitemlo' . $sufixjurnal, 'ji', 'left(ji.kodero,1)=a.kodea');
$query->innerJoin('jurnal' . $sufixjurnal, 'j', 'ji.jurnalid=j.jurnalid');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
$query->condition('a.kodea', '3', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	if ($aset_total < ($kewajiban_total+$datas->realisasi)) {
		$delta_ekuitas = $aset_total - ($kewajiban_total+$datas->realisasi);
	} else {
		$delta_ekuitas = ($kewajiban_total+$datas->realisasi)-$aset_total;
	}
	
	$ekuitas_total = $datas->realisasi + $delta_ekuitas;
	$adaekuitas = true;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($ekuitas_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
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
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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

if ($adaekuitas==false) {

	if ($aset_total < $kewajiban_total) {
		$delta_ekuitas = $aset_total - $kewajiban_total;
	} else {
		$delta_ekuitas = $kewajiban_total-$aset_total;
	}
	$ekuitas_total = $delta_ekuitas;
	
	$rows[] = array(
		array('data' => '<strong>3</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>EKUITAS</strong>', 'width' => '325px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($ekuitas_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
}

if ($delta_ekuitas!=0) {
	$rows[] = array(
		array('data' => '319', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $bold_start . 'Konsolidasi' . $bold_end, 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $bold_start . apbd_fn($delta_ekuitas) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);		
	if ($tingkat>3) {
		$rows[] = array(
			array('data' => '319.01', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => 'Konsolidasi', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($delta_ekuitas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
		if ($tingkat==5) {
			$rows[] = array(
				array('data' => '319.01.001', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '<em>Konsolidasi</em>', 'width' => '325px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '<em>'. apbd_fn($delta_ekuitas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
		}
	}	
}

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
	
}else {
	$rows[] = array(
		array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
}

	
//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

return $tabel_data;

}


?>
