<?php
function belanjarek_skpd_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	//$limit = 10;
    
	if ($arg) {
		$bulan = arg(2);
		$kodero = arg(3);
		
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodero = 'ZZ';
	}
	$query = db_select('apbdrekap', 'k')->extend('PagerDefault');
	if (strlen($kodero)=='8') {
		$query->fields('k', array('koderincian','namarincian'));
		$query->condition('koderincian',$kodero,'=');
		$query->limit(1);
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title = $kodero . '|' . $data->namarincian;
			}
		}
		
	} else if (strlen($kodero)=='5') {
		$query->fields('k', array('kodeobyek','namaobyek'));
		$query->condition('kodeobyek',$kodero,'=');
		$query->limit(1);
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title = $kodero . '|' . $data->namaobyek;
			}
		}
		
	} else if (strlen($kodero)=='3') {
		$query->fields('k', array('kodejenis','namajenis'));
		$query->condition('kodejenis',$kodero,'=');
		$query->limit(1);
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title = $kodero . '|' . $data->namajenis;
			}
		}
		
	} else {
		$query->fields('k', array('kodekelompok','namakelompok'));
		$query->condition('kodekelompok',$kodero,'=');
		$query->limit(1);
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title = $kodero . '|' . $data->namarincian;
			}
		}
		
	}
	
	drupal_set_title($title);

	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD','field'=> 'namaskpd', 'valign'=>'top'),
		array('data' => 'Anggaran', 'field'=> 'anggaran2x','width' => '100px', 'valign'=>'top'),
		array('data' => 'Realisasi', 'field'=> 'realisasix','width' => '100px', 'valign'=>'top'),
		array('data' => 'Persen', 'field'=> 'persen', 'width' => '60px', 'valign'=>'top'),
		array('data' => 'A-R', 'width' => '60px', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);	
	
	//drupal_set_message($tahun);
	$query = db_select('apbdrekap', 'k')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('kodeskpd', 'namaskpd'));
	
	$query->addExpression('SUM(k.anggaran2/1000)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasikum' . $bulan . '/1000)', 'realisasix');
	$query->addExpression('SUM(k.realisasikum' . $bulan . ')/SUM(k.anggaran2)', 'persen');
	
	//condition 
	$query->condition('k.koderincian', db_like($kodero) . '%','LIKE');

	//grouping
	$query->groupBy('k.kodeskpd');
	$query->groupBy('k.namaskpd');
	
	//$query->orderByHeader($header);
	$query->orderByHeader($header);
	$query->orderBy('k.namaskpd', 'ASC');
	
	//# execute the query
	$results = $query->execute();
	
	//render
	$no=0;
	$rows = array();
	
	$anggarantot = 0;
	$realisasitot = 0;
	foreach ($results as $data) {
		$no++;  
		
		$anggarantot += $data->anggaran2x;
		$realisasitot += $data->realisasix;

		$editlink = apbd_button_bukubesar('akuntansi/buku/'. $bulan .'/ZZ/'.$kodero . '/' . $data->kodeskpd);
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namaskpd, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasix), 'align' => 'right', 'valign'=>'top'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1($data->persen*100),'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x - $data->realisasix), 'align' => 'right', 'valign'=>'top'),
						$editlink
					);
	} 
	$rows[] = array(
					array('data' => '', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($anggarantot) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasitot) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggarantot, $realisasitot)) . '</strong>','align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($anggarantot - $realisasitot) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					
				);		

	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

	$btn = '';
	
	$output_form = drupal_get_form('belanjarek_skpd_main_form');
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	return  drupal_render($output_form) . $btn . $output . $btn;
	
}

function belanjarek_skpd_main_form($form, &$form_state) {
	
	$bulan = arg(2);
	$kodero = arg(3);
	
	$query = db_select('apbdrekap', 'r');
	if (strlen($kodero)=='8') {
		$query->fields('r', array('koderincian'));
		$query->condition('koderincian',$kodero,'=');
		$query->groupBy('koderincian');
		
	} else if (strlen($kodero)=='5') {
		$query->fields('r', array('kodeobyek'));
		$query->condition('kodeobyek',$kodero,'=');
		$query->groupBy('kodeobyek');
		
	} else if (strlen($kodero)=='3') {
		$query->fields('r', array('kodejenis'));
		$query->condition('kodejenis',$kodero,'=');
		$query->groupBy('kodejenis');
		
	} else {
		$query->fields('r', array('kodekelompok'));
		$query->condition('kodekelompok',$kodero,'=');
		$query->groupBy('kodekelompok');
		
	}
	$query->addExpression('SUM(r.anggaran2)', 'anggaran2x');
	$query->addExpression('SUM(r.realisasikum' . $bulan . ')', 'realisasix');	
	
	$results = $query->execute();
	
	if($results){
		foreach($results as $data) {
			$anggaran = apbd_fn($data->anggaran2x);
			$realisasi = apbd_fn($data->realisasix);
			$persen = apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix));
		}
	}

	
	$form['anggaran'] = array(
		'#type' => 'item',
		'#title' =>  t('Anggaran'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-info pull-right">' . $anggaran . '</p>',
	);	
	$form['realisasi'] = array(
		'#type' => 'item',
		'#title' =>  t('Realisasi'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-info pull-right">' . $realisasi . '</p>',
	);	
	$form['persen'] = array(
		'#type' => 'item',
		'#title' =>  t('Prosentase'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-info pull-right">' . $persen . '</p>',
	);	


		
	return $form;
	
}

?>