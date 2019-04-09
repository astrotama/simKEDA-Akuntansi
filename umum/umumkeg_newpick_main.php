<?php
function umumkeg_newpick_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	
	
	if (isUserSKPD())
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = arg(2);

	//drupal_set_message(arg(3));
	
	$kegiatan = arg(3);
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kodekeg',  'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	); 

	
	$query = db_select('kegiatanskpd', 'd')->extend('PagerDefault')->extend('TableSort');;

	# get the desired fields from the database
	$query->fields('d', array('kodekeg',  'kodeuk', 'kegiatan', 'anggaran'));
	$query->condition('d.kodeuk', $kodeuk, '=');
	//if ($kodeuk!='00') $query->condition('d.jenis', 2, '=');
	$query->condition('d.inaktif', 0, '=');
	$query->condition('d.anggaran', 0, '>');
	
	if (strlen($kegiatan)> 0) $query->condition('d.kegiatan', '%' . db_like($kegiatan) . '%', 'LIKE');

	$query->orderByHeader($header);
	$query->orderBy('d.jenis', 'ASC');
	$query->orderBy('d.kegiatan', 'ASC');
	$query->limit($limit);

	# execute the query
	$results = $query->execute();
		
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
		
		$editlink = apbd_button_baru_custom_small('umum/newpost/' . $data->kodekeg, 'Jurnal');

		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => substr($data->kodekeg, 6),'align' => 'left', 'valign'=>'top'),
			array('data' => $data->kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
		);			
	}

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	
	$output_form = drupal_get_form('umumkeg_newpick_main_form');
	//drupal_render($output_form) . $btn . $output . $btn;
	
	return drupal_render($output_form) . $output;
	
	
}


function umumkeg_newpick_main_form_submit($form, &$form_state) {

	$kegiatan = $form_state['values']['kegiatan'];
	$kodeuk = $form_state['values']['kodeuk'];
	
	$uri = 'umum/newpick/' . $kodeuk . '/' . $kegiatan;
	drupal_goto($uri);	
}


function umumkeg_newpick_main_form($form, &$form_state) {

	if (isUserSKPD())
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = arg(2);

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'CARI KEGIATAN',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	
	$form['formdata']['kegiatan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);

	$form['formdata']['kodeuk'] = array(
		'#type' => 'hidden',
		'#default_value' => $kodeuk,
	);	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span> Cari',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);


	return $form;
}


?>
