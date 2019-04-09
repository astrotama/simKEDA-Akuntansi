<?php
function laporan_rekonkeg_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 125px; float: left;}</style>';
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
				$kodeuk = arg(3);
				$tingkat = arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n')-1;		//variable_get('apbdtahun', 0);
		$tingkat = '3';
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = '81';
		}
		
	}
	if ($bulan=='0') $bulan='12';
	//drupal_set_title('BELANJA');
	
	 
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat);
		//$output = gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat);
		apbd_ExportPDF_P($output, 10, "LAP");
		
		//return $output;
				
	} else if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Rekon Kegiatan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		$output = gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat);
		$output_form = drupal_get_form('laporan_rekonkeg_main_form');	
		
		$btn = l('Cetak', 'laporanrekonkeg/filter/' . $bulan . '/'. $kodeuk .'/'.$tingkat. '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporanrekonkeg/filter/' . $bulan . '/'. $kodeuk .'/'.$tingkat. '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_rekonkeg_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];

	$uri = 'laporanrekonkeg/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat;
	drupal_goto($uri);
	
}


function laporan_rekonkeg_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}
	$namasingkat = '|BPKAD';
	$bulan = date('n')-1;
	$tingkat = '3';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$tingkat = arg(4);
		
	} 
	
	if ($bulan=='0') $bulan='12';
	
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

	$arr_bulan = array(	
			 '01' => t('JANUARI'), 	
			 '02' => t('FEBRUARI'),
			 '03' => t('MARET'),	
			 '04' => t('APRIL'),	
			 '05' => t('MEI'),	
			 '06' => t('JUNI'),	
			 '07' => t('JULI'),	
			 '08' => t('AGUSTUS'),	
			 '09' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   );	
	$opttingkat = array();
	$opttingkat['3'] = 'JENIS';
	$opttingkat['4'] = 'OBYEK';
	$opttingkat['5'] = 'RINCIAN';
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $arr_bulan[$bulan] . $namasingkat . '|' . $opttingkat[$tingkat] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => $arr_bulan,
	);
	
	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
		
	} else {
		global $user;
		$username = $user->name;		
		
		$result = db_query('SELECT unitkerja.kodeuk, unitkerja.namasingkat FROM unitkerja INNER JOIN userskpd ON unitkerja.kodeuk=userskpd.kodeuk WHERE userskpd.username=:username ORDER BY unitkerja.namasingkat', array(':username' => $username));	
		$n = 0;		
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
			$n++;
		}
		if ($n==0) {
			$result = db_query('SELECT kodeuk, namasingkat FROM unitkerja ORDER BY namasingkat');	
			while($row = $result->fetchObject()){
				$option_skpd[$row->kodeuk] = $row->namasingkat; 
				$n++;
			}
		}
		
		
		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
		);
	}

	$form['formdata']['tingkat'] = array(
		'#type' => 'select',
		'#title' =>  t('Tingkat'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opttingkat,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $tingkat,
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat) {

$sufixjurnal = 'uk';

$agg_pendapatan_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapatan_total_dinas = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'SKPD', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Pusat', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Selisih', 'width' => '80px', 'valign'=>'top'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasidinas = 0; $realisasipusat = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasidinas = $data->realisasi;
	}
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasipusat = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasipusat - $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_pendapatan_total = $datas->anggaran;
	$rea_pendapatan_total_dinas = $realisasidinas;
	$rea_pendapatan_total_pusat = $realisasipusat;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$sql->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}

		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}
			
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasidinas), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasipusat), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
			);
			
			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}

					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}
					
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasidinas) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
						
						$realisasidinas = 0; $realisasipusat = 0;
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasidinas = $data->realisasi;
						}
					
						$sql = db_select('jurnal', 'j');
						$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasipusat = $data->realisasi;
						}

						$rows[] = array(
							array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($realisasipusat -  $realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
						);
					
					}	//obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasidinas = 0; $realisasipusat = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasidinas = $data->realisasi;
	}
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasipusat = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total_dinas = $realisasidinas;
	$rea_belanja_total_pusat = $realisasipusat;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
			
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}
			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasidinas), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasipusat), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}				
					
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasidinas) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}

							$sql = db_select('jurnal', 'j');
							$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasipusat = $data->realisasi;
							}							
							$rows[] = array(
								array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasipusat -  $realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
			
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}			
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.anggaran)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}

			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}
			
			$kodepro = $data_kel->kodek . '.' . $data_pro->kodepro;
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);				
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'anggaran'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasidinas = 0; $realisasipusat = 0;
				
				$sql = db_select('jurnal' . $sufixjurnal, 'j');
				$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasidinas = $data->realisasi;
				} 
				
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasipusat = $data->realisasi;
				} 

				$kodekeg = $kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . strtoupper($data_keg->kegiatan) . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasipusat -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
					
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));					
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}
				
					$kodej = $kodekeg . '.' . substr($data_jen->kodej,-1);
					$rows[] = array(
						array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasidinas), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}
							$sql = db_select('jurnal', 'j');
							$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasipusat = $data->realisasi;
							}						
							$rows[] = array(
								array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasidinas) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasipusat) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasipusat -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
							);
							
							//REKENING
							if ($tingkat=='5') {
								$query = db_select('rincianobyek', 'ro');
								$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
								$query->fields('ro', array('kodero', 'uraian'));
								$query->addExpression('SUM(ag.anggaran)', 'anggaran');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
								$query->groupBy('ro.kodero');
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									
									$realisasidinas = 0; $realisasipusat = 0;
									$sql = db_select('jurnal' . $sufixjurnal, 'j');
									$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '='); 
									$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasidinas = $data->realisasi;
									}

									$sql = db_select('jurnal', 'j');
									$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '='); 
									$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasipusat = $data->realisasi;
									}									
									$rows[] = array(
										array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasipusat -  $realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
									);
								
								}	//obyek					
								
							}	//rekening
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapatan_total - $agg_belanja_total;
$realisasidinas_netto = $rea_pendapatan_total_dinas - $rea_belanja_total_dinas;
$realisasipusat_netto = $rea_pendapatan_total_pusat - $rea_belanja_total_pusat;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasidinas_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasipusat_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasipusat_netto - $realisasidinas_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


if ($kodeuk=='ZZ') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasidinas_netto_p = 0;

	$rows[] = array(
		array('data' => '6', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran -  $realisasidinas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasidinas_netto_p += $realisasidinas;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasidinas_netto_p -= $realisasidinas;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
		
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasidinas), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
			);
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
				
					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasidinas) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran -  $realisasidinas), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}
						
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran -  $realisasidinas) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);
						
						}	//obyek					
						
					}
				}	//obyek			
				
			}	//tingkat obyek
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasidinas_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasidinas_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasidinas_netto += $realisasidinas_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasidinas_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasidinas_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk, $tingkat) {

$sufixjurnal = 'uk';

if ($kodeuk == 'ZZ')
	$skpd = 'KABUPATEN JEPARA';
else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN', 'width' => '510px',  'colspan'=>6,'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>6, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapatan_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapatan_total_dinas = 0;
$rea_belanja_total_dinas = 0;
$rea_belanja_total_pusat = 0;
$rea_pembiayaan_netto = 0;

//TABEL 
$rows = null;
if (arg(5) == 'excel'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '70px','align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '160px','align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI SKPD', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'REALISASI Pusat', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}else{
$header[] = array (
	array('data' => 'KODE','width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '160px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '140px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'SKPD', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Pusat', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	
	$realisasidinas = 0; $realisasipusat = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasidinas = $data->realisasi;
	}
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasipusat = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasipusat - $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);	
	
	$agg_pendapatan_total = $datas->anggaran;
	$rea_pendapatan_total_dinas = $realisasidinas;
	$rea_pendapatan_total_pusat = $realisasipusat;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$sql->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}				
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat - $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);			
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}			
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$rows[] = array(
				array('data' => $kodej, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat - $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}				
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasidinas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat - $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);					
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
						
						$realisasidinas = 0; $realisasipusat = 0;
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasidinas = $data->realisasi;
						}
						$sql = db_select('jurnal', 'j');
						$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasipusat = $data->realisasi;
						}					

						$rows[] = array(
							array('data' => $kodej . substr($data_rek->kodero,-5), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasipusat- $realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						);						
					
					}	//obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);						

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasidinas = 0; $realisasipusat = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasidinas = $data->realisasi;
	}
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasipusat = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasipusat - $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total_dinas = $realisasidinas;
	$rea_belanja_total_pusat = $realisasipusat;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat - $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
				
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}		
			$rows[] = array(
				array('data' => $kodej, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat - $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}						
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasidinas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat - $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);					
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}				
							$sql = db_select('jurnal', 'j');
							$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasipusat = $data->realisasi;
							}	
							$rows[] = array(
								array('data' => $kodej . substr($data_rek->kodero,-5), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasipusat - $realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);							
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasipusat = $data->realisasi;
		}				
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat- $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);			
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.anggaran)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}

			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasipusat = $data->realisasi;
			}
			
			$kodepro = $data_kel->kodek . '.' . $data_pro->kodepro;		
			$rows[] = array(
				array('data' => $kodepro, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => strtoupper($data_pro->program), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_pro->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat- $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'anggaran'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasidinas = 0; $realisasipusat = 0;
				
				$sql = db_select('jurnal' . $sufixjurnal, 'j');
				$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasidinas = $data->realisasi;
				} 
				
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasipusat = $data->realisasi;
				} 
				

				$kodekeg = $kodepro . '.' . substr($data_keg->kodekeg,-3);		
				$rows[] = array(
					array('data' => $kodekeg, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => strtoupper($data_keg->kegiatan), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasipusat- $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				);					
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej'); 
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}

					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));					
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasipusat = $data->realisasi;
					}
					
					$kodej = $kodekeg . '.' . substr($data_jen->kodej,-1);
					$rows[] = array(
						array('data' => $kodej, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => $data_jen->uraian, 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat- $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);					
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}
							$sql = db_select('jurnal', 'j');
							$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasipusat = $data->realisasi;
							}							
							$rows[] = array(
								array('data' => $kodej . substr($data_oby->kodeo,-2), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasidinas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasipusat) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasipusat - $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);		
							
							//REKENING
							if ($tingkat=='5') {
								$query = db_select('rincianobyek', 'ro');
								$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
								$query->fields('ro', array('kodero', 'uraian'));
								$query->addExpression('SUM(ag.anggaran)', 'anggaran');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
								$query->groupBy('ro.kodero');
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									
									$realisasidinas = 0; $realisasipusat = 0;
									$sql = db_select('jurnal' . $sufixjurnal, 'j');
									$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '='); 
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasidinas = $data->realisasi;
									}
									$sql = db_select('jurnal', 'j');
									$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '='); 
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasipusat = $data->realisasi;
									}									

									$rows[] = array(
										array('data' => $kodej . substr($data_rek->kodero,-5), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
										array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
										array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
										array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
										array('data' => '<em>'. apbd_fn($realisasipusat- $realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
									);									
								
								}	//obyek					
								
							}	//rekening
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapatan_total - $agg_belanja_total;
$realisasidinas_netto = $rea_pendapatan_total_dinas - $rea_belanja_total_dinas;
$realisasipusat_netto = $rea_pendapatan_total_pusat - $rea_belanja_total_pusat;
$rows[] = array(
	array('data' => '', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasidinas_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasipusat_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasipusat_netto - $realisasidinas_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if ($kodeuk=='ZZ') {
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);						

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasidinas_netto_p = 0;


	$rows[] = array(
		array('data' => '6', 'width' => '70px', 'align'=>'left','style'=>'border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '160px', 'align'=>'left','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'border-right:1px solid black;'),
	);
	
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasidinas = 0; $realisasipusat = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasidinas = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasipusat) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasidinas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);			
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasidinas_netto_p += $realisasidinas;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasidinas_netto_p -= $realisasidinas;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasidinas = 0; $realisasipusat = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasidinas = $data->realisasi;
			}
		
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$rows[] = array(
				array('data' => $kodej, 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasipusat), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran- $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasidinas = 0; $realisasipusat = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasidinas = $data->realisasi;
					}
				
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasidinas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasipusat) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran- $realisasidinas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);	
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasidinas = 0; $realisasipusat = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasidinas = $data->realisasi;
							}
						
							$rows[] = array(
								array('data' => $kodej . substr($data_rek->kodero,-5), 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasipusat) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasidinas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);	
						
						}	//obyek					
						
					}
				}	//obyek			
				
			}	//tingkat obyek
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasidinas_netto_p) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasidinas_netto_p) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasidinas_netto += $realisasidinas_netto_p;
	$rows[] = array(
		array('data' => '', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasidinas_netto) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasidinas_netto) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
}
//Batas
$rows[] = array(
	array('data' => '', 'width' => '70px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '160px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);						

$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



?>


