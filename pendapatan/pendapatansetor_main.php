<?php
function pendapatansetor_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				
				$kodeuk = arg(2);
				$bulan = arg(3);
				$statusjurnal = arg(4);
				$keyword = arg(5);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		if (isUserSKPD())
			$kodeuk = apbd_getuseruk();
		else
			$kodeuk = 'ZZ';
		//$bulan = date('m');
		$bulan = '0';
		$statusjurnal = '0';
		$keyword = 'ZZ';
	}
	
	if ($keyword == '') $keyword = 'ZZ';
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('pendapatansetor_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Rekening', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'jumlahkeluar',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('setor', 'a')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('rincianobyek', 'r', 'a.kodero=r.kodero');

	# get the desired fields from the database
	$query->fields('a', array('setorid', 'kodeuk', 'tanggal', 'jurnalsudah', 'keterangan', 'jumlahkeluar', 'jurnalid'));
	$query->fields('r', array('kodero', 'uraian'));
	//$query->condition('a.tipe', 1, '=');
	$query->condition('a.jumlahkeluar', 0, '>');
	
	//keyword
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('a.keterangan', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);	
	}
	
	if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM a.tanggal) = :month', array('month' => $bulan));
	
	if ($statusjurnal !='ZZ') $query->condition('a.jurnalsudah', $statusjurnal, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('a.tanggal', 'ASC');
	$query->limit($limit);
		
	dpq($query);
	
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
		
		
		if($data->jurnalsudah=='1'){
			$jurnalsudah = apbd_icon_jurnal_sudah();
			$editlink = apbd_button_jurnal('pendapatansetor/jurnaledit/' . $data->jurnalid);
		
		} else {
			$jurnalsudah = apbd_icon_jurnal_belum();
			$editlink = apbd_button_jurnalkan('pendapatansetor/jurnal/' . $data->setorid);
		}

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $jurnalsudah,'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
						array('data' => $data->uraian,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlahkeluar),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	$btn = apbd_button_print('/pendapatansetor/filter/' . $kodeuk . '/' . $bulan . '/' . $keyword . '/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		//$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		//print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function pendapatansetor_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	$statusjurnal = $form_state['values']['statusjurnal'];
	$keyword = $form_state['values']['keyword'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$uri = 'pendapatansetor/filter/' . $kodeuk . '/' . $bulan . '/' . $statusjurnal . '/' . $keyword;
	drupal_goto($uri);
	
}


function pendapatansetor_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '9';
	$statusjurnal = 'ZZ';
	$keyword = '';
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$statusjurnal = arg(4);
		$keyword = arg(5);

	}
	drupal_set_message('x');
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		 

	if (isSuperuser()) {
		//SKPD
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		# build the table fields
		$option_skpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $option_skpd[$data->kodeuk] = $data->namasingkat; 
			}
		}		
		$form['formdata']['skpd'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#prefix' => '<div id="skpd-replace">',
			'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			//'#default_value' => $namasingkat,
		);		
	} else {
		
		$kodeuk = apbd_getuseruk();
		$form['formdata']['skpd'] = array(
			'#type' => 'value',
			'#value' =>  $kodeuk,

		);		


	}	
	//BULAN
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	$opt_jurnal['ZZ'] ='SEMUA';
	$opt_jurnal['0'] = 'BELUM JURNAL';
	$opt_jurnal['1'] = 'SUDAH JURNAL';	
	$form['formdata']['statusjurnal'] = array(
		'#type' => 'select',
		'#title' =>  t('Penjurnalan'),
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $statusjurnal,
	);	 
	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari S2PD, bisa nama kegiatan, keterangan, atau nama penerima/pihak ketiga'),
		'#default_value' => $keyword, 
	);	

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}



?>
