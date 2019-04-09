<?php
function belanjaurusan_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
				
			case 'filter':
				$bulan = arg(2);
				$kodek = arg(3);
				$urusan = arg(4);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodek = 'SEMUA';
		$urusan = 'SEMUA';
	}
	
	//drupal_set_title('BELANJA SKPD #' . $bulan);
	
	$output_form = drupal_get_form('belanjaurusan_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kodeurusan',  'width' => '10px',  'valign'=>'top'), 
		array('data' => 'Urusan',  'field'=> 'namaurusan', 'valign'=>'top'), 
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran1x', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
		array('data' => 'Persen', 'width' => '10px','field'=> 'persen', 'valign'=>'top'),
		array('data' => 'A-R', 'width' => '50px',  'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
	
	$query = db_select('apbdrekap', 'k')->extend('PagerDefault')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('kodeurusan','namaurusan'));
	$query->addExpression('SUM(k.anggaran1/1000)', 'anggaran1x');
	$query->addExpression('SUM(k.anggaran2/1000)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasikum' . $bulan . '/1000)', 'realisasix');
	$query->addExpression('SUM(k.realisasikum' . $bulan . ')/SUM(k.anggaran2)', 'persen');
	$query->addExpression('COUNT (DISTINCT k.kodeskpd)', 'jumlahskpd');
	$query->addExpression('COUNT (DISTINCT k.kodekeg)', 'jumlahkegiatan');
	$query->condition('k.kodeakun', '5', '=');

	if ($urusan == 'WAJIB') 
		$query->condition('k.kodeurusan', db_like('1') . '%', 'LIKE');
	else if ($urusan == 'PILIHAN') 
		$query->condition('k.kodeurusan', db_like('2') . '%', 'LIKE');
	
	if ($kodek=='GAJI') {
		$query->condition('k.kodekelompok', '51', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($kodek=='PPKD') {
		$query->condition('k.kodekelompok', '51', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($kodek=='LANGSUNG') {
		$query->condition('k.kodekelompok', '52', '=');	
	} else if ($kodek=='TIDAK LANGSUNG') {
		$query->condition('k.kodekelompok', '51', '=');	
	}						 

	$query->groupBy('kodeurusan');
	$query->groupBy('namaurusan');

	$query->orderByHeader($header);
	$query->orderBy('k.kodeurusan', 'ASC');
	$query->limit($limit);	
	
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
		
		//<font color="red">This is some text!</font>
		$anggaran = apbd_fn($data->anggaran2x);
		
		if ($data->anggaran1x > $data->anggaran2x)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1x) . '</font></p>';
		else if ($data->anggaran1x < $data->anggaran2x)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1x) . '</font></p>';

		//belanja/filter/10/58/SEMUA/SEMUA/
		//$keterangan = l($data->jumlahkegiatan . ' Kegiatan <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>' , 
		//'belanja/filter/' . $bulan . '/' . $data->kodeskpd . '/' . $kodek . '/SEMUA' , 
		//array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));

		$keterangan = l($data->jumlahkegiatan . ' Kegiatan <span class="glyphicon glyphicon-th-large" 
						aria-hidden="true"></span>' , 'belanja/filter/' . $bulan . '/ZZ/' . $kodek . '/SEMUA/U' . $data->kodeurusan , 
						array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));

		//$keterangan .= l($data->jumlahskpd . ' SKPD&nbsp;', 'belanjauk/filter/' . $bulan . '/SEMUA/U' . $data->kodeurusan , 
		//				array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));

		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->kodeurusan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->namaurusan . $keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasix), 'align' => 'right', 'valign'=>'top'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1($data->persen*100), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x - $data->realisasix), 'align' => 'right', 'valign'=>'top'),
						);
	}

	//BUTTON 
	//$btn = apbd_button_print('');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	//$btn .= apbd_button_chart('belanja/chart/' . $bulan.'/ZZ/SEMUA/SEMUA/urusan_tot/single');
	
	$btn = '';
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(6)=='pdf'){
		//$output=getData($bulan,$kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	//return drupal_render($output_form) . $btn . $output . $btn;
}


function belanjaurusan_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$urusan= $form_state['values']['urusan'];
	$kodek = $form_state['values']['kodek'];
	
	$uri = 'belanjaurusan/filter/' . $bulan.'/' . $kodek  . '/' . $urusan;
	drupal_goto($uri);
	
}


function belanjaurusan_main_form($form, &$form_state) {
	
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$kodek = 'SEMUA';
	$urusan = 'SEMUA';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodek = arg(3);
		$urusan = arg(4);
	}
	
	$kelompok = '|' . $kodek;

	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=> '<p>' . $bulan . $namasingkat . $kelompok . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		'#title'=> $bulan . $kelompok . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
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

	$form['formdata']['urusan']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Urusan'), 
		'#default_value' => $urusan,
		
		'#options' => array('SEMUA'=>'SEMUA', 'WAJIB'=>'WAJIB','PILIHAN'=>'PILIHAN'),	
	);		
	
	$form['formdata']['kodek']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		
		'#options' => array('SEMUA'=>'SEMUA', 'GAJI'=>'GAJI', 'LANGSUNG'=>'LANGSUNG', 'PPKD'=>'PPKD'),
	);		

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

?>
