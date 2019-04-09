<?php
function pendapatanrek_skpd_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	//$limit = 10;
    
	$pdf = '';
	if ($arg) {
		$bulan = arg(2);
		$kodero = arg(3);
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodero = 'ZZ';
	}
	
	if ($pdf=='pdf') {
	
	} else {
		$query = db_select('apbdrekap', 'r')->extend('PagerDefault')->extend('TableSort');
		$query->fields('r', array('koderincian','namarincian'));
		$query->condition('koderincian',db_like($kodero) . '%','LIKE');
		$query->limit(1);
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title = $kodero . '|' . $data->namarincian;
			}
		}
		
		drupal_set_title($title);

		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'anggaran','width' => '100px', 'valign'=>'top'),
			array('data' => 'Realisasi', 'field'=> 'realisasi','width' => '100px', 'valign'=>'top'),
			array('data' => 'Persen', 'field'=> 'persen','width' => '60px', 'valign'=>'top'),
			array('data' => 'A-R', 'width' => '60px', 'valign'=>'top'),
			array('data' => '', 'width' => '20px', 'valign'=>'top'),
		);	
		
		//drupal_set_message($bulan);
		$query = db_select('apbdrekap', 'r')->extend('TableSort');

		# get the desired fields from the database
		$query->fields('r', array('kodeskpd', 'namaskpd'));
		
		$query->addExpression('SUM(anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(realisasikum' . $bulan . '/1000)', 'realisasi');
		$query->addExpression('SUM(realisasikum' . $bulan . ')/SUM(anggaran2)', 'persen');
		
		//condition
		$query->condition('koderincian',db_like($kodero) . '%','LIKE');

		//grouping
		//$query->groupBy('kodeskpd');
		$query->groupBy('namaskpd');
		
		//$query->orderByHeader($header);
		$query->orderByHeader($header);
		$query->orderBy('namaskpd', 'ASC');
		
		//dpq($query);
		
		//# execute the query
		$results = $query->execute();
		
		//render
		$no=0;
		$rows = array();
		
		$anggarantot = 0;
		$realisasitot = 0;
		foreach ($results as $data) {
			$no++;  
			
			$anggarantot += $data->anggaran;
			$realisasitot += $data->realisasi;

			$editlink = apbd_button_bukubesar('akuntansi/buku/'. $bulan .'/ZZ/'.$kodero . '/' . $data->kodeskpd);
			
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $data->namaskpd, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->anggaran), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->realisasi), 'align' => 'right', 'valign'=>'top'),
							//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran, $data->realisasi)),'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1($data->persen*100),'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->anggaran - $data->realisasi), 'align' => 'right', 'valign'=>'top'),
							$editlink,
						);
		}
		$rows[] = array(
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($anggarantot) . '</strong>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($realisasitot) . '</strong>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggarantot, $realisasitot)) . '</strong>','align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($anggarantot - $realisasitot), 'align' => 'right', 'valign'=>'top'),
						'',
					);		

		//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		$btn = '';
		//$output_form = drupal_get_form('pendapatanrek_skpd_main_form');
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		return  drupal_render($output_form) . $btn . $output . $btn;
	}
}

function getData($bulan,$kodero){
	
	$header = array (
		array('data' => 'No','height'=>'20px', 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Kode','height'=>'20px', 'width' => '70px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'SKPD','height'=>'20px', 'width' => '265px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Anggaran','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Realisasi','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => '%','height'=>'20px', 'width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
	);

	
	//drupal_set_message($bulan);
	$query = db_select('apbdrekap', 'r');

	# get the desired fields from the database
	$query->fields('r', array('kodeskpd', 'namaskpd'));
	
	$query->addExpression('SUM(anggaran2)', 'anggaran');
	$query->addExpression('SUM(realisasikum' . $bulan . ')', 'realisasi');
	
	//condition
	$query->condition('koderincian',db_like($kodero) . '%','LIKE');

	//grouping
	$query->groupBy('kodeskpd');
	$query->groupBy('namaskpd');
	
	//$query->orderByHeader($header);
	$query->orderByHeader($header);
	$query->orderBy('kodeskpd', 'ASC');
	
	//dpq($query);
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	$rows = array();
	foreach ($results as $data) {
		$no++;  



		$rows[] = array(
						array('data' => $no, 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $data->kodeskpd, 'width' => '70px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $data->namaskpd , 'width' => '265px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn($data->realisasi), 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran, $data->realisasi)), 'width' => '40px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						
					);
	
	}
		$rows[] = array(
						array('data' => '', 'width' => '605px','valign'=>'middle', 'align'=>'center','style'=>'border-top:1px solid black;'),
					);
		
		
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
	
}


function pendapatanrek_skpd_main_form($form, &$form_state) {
	
	$bulan = arg(2);
	$kodero = arg(3);
	
	$query = db_select('apbdrekap', 'r');
	$query->fields('r', array('koderincian'));
	$query->addExpression('SUM(r.anggaran2)', 'anggaran');
	$query->addExpression('SUM(r.realisasikum' . $bulan . ')', 'realisasi');	
	$query->condition('koderincian', db_like($kodero) . '%','LIKE');
	$query->groupBy('koderincian');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$anggaran = apbd_fn($data->anggaran);
			$realisasi = apbd_fn($data->realisasi);
			$persen = apbd_fn1(apbd_hitungpersen($data->anggaran, $data->realisasi));
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