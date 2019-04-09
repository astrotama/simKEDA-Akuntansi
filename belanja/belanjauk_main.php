<?php
function belanjauk_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
				
			case 'filter':
				$kelompok = arg(2);
				$bulan = arg(3);
				$kodek = arg(4);
				$keyword = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kelompok = 'SEMUA';
		$kodek = 'SEMUA';
		$keyword ='';
	}
	
	//drupal_set_title('BELANJA SKPD #' . $bulan);
	
	$output_form = drupal_get_form('belanjauk_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD',  'field'=> 'namaskpd', 'valign'=>'top'), 
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran1x', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
		array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen','valign'=>'top'),
		array('data' => 'A-R', 'width' => '50px',  'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
	
	$query = db_select('apbdrekap', 'k')->extend('PagerDefault')->extend('TableSort');
	if ($kelompok!='SEMUA') { 
		$query->innerJoin('unitkerja', 'u', 'k.kodeskpd=u.kodeuk');
		$query->condition('u.kelompok', $kelompok, '=');
	}	

	# get the desired fields from the database
	$query->fields('k', array('kodeskpd','namaskpd'));
	$query->addExpression('SUM(k.anggaran1/1000)', 'anggaran1x');
	$query->addExpression('SUM(k.anggaran2/1000)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasikum' . $bulan . '/1000)', 'realisasix');
	$query->addExpression('SUM(k.realisasikum' . $bulan . ')/SUM(k.anggaran2)', 'persen');
	$query->addExpression('COUNT (DISTINCT k.kodekeg)', 'jumlahkegiatan');
	$query->condition('k.kodeakun', '5', '=');

	
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

	if ($keyword !='') {
		if (substr($keyword,0,1) =='U')
			$query->condition('k.kodeurusan', substr($keyword,1,3), '=');
		else if (substr($keyword,0,1) =='F')
			$query->condition('k.kodefungsi', substr($keyword,1,2), '=');
		else
			$query->condition('k.namarincian', '%' . db_like($keyword) . '%', 'LIKE');
	}
	
	$query->groupBy('kodeskpd');
	$query->groupBy('namaskpd');

	$query->orderByHeader($header);
	$query->orderBy('k.namaskpd', 'ASC');
	$query->limit($limit);	
		
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
		$keterangan = l($data->jumlahkegiatan . ' Kegiatan <span class="glyphicon glyphicon-th-large" 
						aria-hidden="true"></span>' , 'belanja/filter/' . $bulan . '/' . $data->kodeskpd . '/' . $kodek . '/SEMUA' , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));
		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namaskpd . $keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasix), 'align' => 'right', 'valign'=>'top'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1($data->persen*100), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x-$data->realisasix), 'align' => 'right', 'valign'=>'top'),
					);
	}

	//BUTTON
	//$btn = apbd_button_print('');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	//$btn .= apbd_button_chart('pendapatan/chart/' . $bulan.'/ZZ/jenis_rb');
	//$btn .= apbd_button_chart('belanja/chart/' . $bulan.'/ZZ/SEMUA/SEMUA/total_kb');
	
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


function belanjauk_main_form_submit($form, &$form_state) {
	$kelompok = $form_state['values']['kelompok'];
	$bulan= $form_state['values']['bulan'];
	$kodek = $form_state['values']['kodek'];
	
	$uri = 'belanjauk/filter/' . $kelompok . '/' . $bulan.'/' . $kodek ;
	drupal_goto($uri);
	
}


function belanjauk_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$keyword = '';
	$bulan = date('m');
	$kodek = 'SEMUA';
	$kelompok = 'SEMUA';
	
	if(arg(2)!=null){
		
		$kelompok = arg(2);
		$bulan = arg(3);
		$kodek = arg(4);
	}

	$kelompok_str = 'SEMUA';
	if ($kelompok=='0')
		$kelompok_str = 'DINAS/BADAN/KANTOR';
	else if ($kelompok=='1')
		$kelompok_str = 'KECAMATAN';
	else if ($kelompok=='2')
		$kelompok_str = 'PUSKESMAS';	
	else if ($kelompok=='3')
		$kelompok_str = 'SEKOLAH';
	else if ($kelompok=='4')
		$kelompok_str = 'UPT DIKPORA';
	
	$kelompok_str .= '|' . $kodek;

	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=> '<p>' . $bulan . $namasingkat . $kelompok . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		'#title'=> $bulan . '|' . $kelompok_str . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['kelompok']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('SKPD'), 
		'#default_value' => $kelompok,
		
		'#options' => array('SEMUA'=>'SEMUA', 
							'0'=>'DINAS/BADAN/KANTOR',
							'1'=>'KECAMATAN',
							'2'=>'PUSKESMAS',
							'3'=>'SEKOLAH',
							'4'=>'UPT DIKPORA'),	
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

	$form['formdata']['kodek']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		
		'#options' => array(	
			 'SEMUA' => t('SEMUA'), 	
			 'GAJI' => t('GAJI'), 	
			 'LANGSUNG' => t('LANGSUNG'),	
			 'PPKD' => t('PPKD'),
		   ),
	);		

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

?>
