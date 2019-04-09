<?php
function jurnal_main($arg=NULL, $nama=NULL) {
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
				//$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$tahun = arg(2);
				$kodeuk = arg(3);
				$bulan = arg(4);
				$jenisdokumen = arg(5);
				$keyword = arg(6);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodeuk = 'ZZ';
		$bulan = '0';
		$jenisdokumen='';
		$keyword = 'ZZ';
	}
	
	drupal_set_title('Daftar Jurnal ');
	
	//drupal_set_message($keyword);
	drupal_set_message($jenisdokumen);
	
	$output_form = drupal_get_form('jurnal_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'kodeuk', 'width'=>'50px'),
		array('data' => 'Keterangan','width' => '80px','field'=> 'nobukti', 'valign'=>'top'),
		array('data' => 'No. Bukti','width' => '80px','field'=> 'nobukti', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('jurnal', 'k')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'k.kodeuk=u.kodeuk');

	# get the desired fields from the database
	$query->fields('k', array('dokid','kodeuk','keterangan', 'nobukti','tanggal',  'total'));
	$query->fields('u', array('namasingkat'));
	
	//keyword
	/*
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('k.kegiatan', '%' . db_like($keyword) . '%', 'LIKE');
		$db_or->condition('k.keperluan', '%' . db_like($keyword) . '%', 'LIKE');		
		$db_or->condition('k.penerimanama', '%' . db_like($keyword) . '%', 'LIKE');
		
		$query->condition($db_or);
	}
	*/
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	//if ($bulan !='0') $query->condition('k.bulan', $bulan, '=');
	//if ($jenisdokumen !='ZZ') $query->condition('k.jenisdokumen', $jenisdokumen, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('k.tanggal', 'ASC');
	$query->limit($limit);
		
	drupal_set_message($query);
	
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
		
		
		$editlink = apbd_button_jurnalitem('jurnal/edit/'.$tahun.'/'.$data->dokid);
		

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),

						array('data' => $data->nobukti, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
						//array('data' => $data->kegiatan, 'align' => 'left', 'valign'=>'top'),
						//array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
						//array('data' => $data->penerimanama, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
						//array('data' => $terjurnal,'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	$btn = apbd_button_print('/penata/filter/'.$tahun.'/'.$kodeuk.'/'.$bulan.'/'.$jenisdokumen.'/'.$keyword.'/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($tahun,$kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function getData($tahun,$kodeuk,$bulan,$jenisdokumen,$keyword){
	
	$header = array (
		array('data' => 'No','height'=>'20px','width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'SKPD','height'=>'20px','width' => '160px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Type Dok.','height'=>'20px','width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'No SP2D','height'=>'20px','width' => '60px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Tgl SP2D','height'=>'20px','width' => '90px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Kegiatan','height'=>'20px', 'width' => '150px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keperluan','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Penerima','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
	);
	$query = db_select('jurnal', 'k');//->extend('PagerDefault')->extend('TableSort');
	# get the desired fields from the database
	$query->fields('k', array('dokid','kodeuk', 'nobukti','tanggal',  'total'));
	
	//keyword
	if ($keyword!='ZZ') {
		$db_or = db_or();
		//$db_or->condition('k.kegiatan', '%' . db_like($keyword) . '%', 'LIKE');
		//$db_or->condition('k.keperluan', '%' . db_like($keyword) . '%', 'LIKE');		
		//$db_or->condition('k.penerimanama', '%' . db_like($keyword) . '%', 'LIKE');
		
		$query->condition($db_or);
	}
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->condition('k.bulan', $bulan, '=');
	//if ($jenisdokumen !='ZZ') $query->condition('k.jenisdokumen', $jenisdokumen, '=');
	
	//$query->orderByHeader($header);
	$query->orderBy('k.tanggal', 'ASC');
	//$query->limit($limit);
		
	//drupal_set_message($query);
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		$query = db_select('unitkerja', 'k')->extend('PagerDefault')->extend('TableSort');
		$query->fields('k', array('namasingkat'));
		$query->condition('kodeuk', $data->kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $datas){
			$namasingkat=$datas->namasingkat;
		}
		
		$query = db_select('dokumentipe', 'd')->extend('PagerDefault')->extend('TableSort');
		$query->fields('d', array('uraian'));
		//$query->condition('kode', $data->jenisdokumen, '=');
		$results = $query->execute();
		foreach ($results as $datas){
			$doktipe=$datas->uraian;
		}
		$rows[] = array(
						array('data' => $no,'width' => '40px', 'align' => 'center', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $namasingkat,'width' => '160px',  'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $doktipe,'width' => '40px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->nobukti,'width' => '60px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),'width' => '90px',  'align' => 'center', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						//array('data' => $data->kegiatan,'width' => '150px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						//array('data' => $data->keperluan,'width' => '120px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						//('data' => $data->penerimanama,'width' => '120px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn($data->total),'width' => '120px','align' => 'right', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					
	}
	$rows[] = array(
						array('data' => '','width' => '900px', 'align' => 'center', 'valign'=>'top','style'=>'border-top:1px solid black;'),
						
					);

	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function jurnal_main_form_submit($form, &$form_state) {
	$tahun= 2016;
	$kodeuk = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	
	
	/*drupal_set_message($skpd); 
	$kodeuk = 'ZZ';
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('namasingkat',$skpd,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$kodeuk = $data->kodeuk;
		}
	}*/
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	
	$uri = 'jurnal/filter/' . $tahun.'/'.$kodeuk.'/'.$bulan ;
	drupal_goto($uri);
	
}


function jurnal_main_form($form, &$form_state) {
	
	$bulanoption=array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	
	$kodeuk = 'ZZ';
	$namasingkat = '|SELURUH SKPD';
	$tahun = 2015;
	$bulan = '0';
	$jenisdokumen = '';
	$keyword = '';
	
	if(arg(2)!=null){
		
		$tahun = arg(2);
		$kodeuk = arg(3);
		$bulan=arg(4);
		$jenisdokumen = arg(5);
		$keyword = arg(6);

	}

	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk', $kodeuk, '=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat = '|' . $data->namasingkat;
			}
		}
	}

	//POPULATE JENIS DOKUMEN
	$opt_jenisdokumen['ZZ'] ='SEMUA';
	$query = db_select('dokumentipe', 'dt');
	$query->fields('dt', array('kode','uraian'));
	$query->orderBy('kode', 'ASC');;
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$opt_jenisdokumen[$data->kode] = $data->uraian;
		}
	}	
	
	$bulan_label = '|' . strtoupper($bulanoption[$bulan]);
	if ($jenisdokumen=='')
		$jenisdokumen_label = '|SEMUA SP2D';
	else
		$jenisdokumen_label = '|' . strtoupper($opt_jenisdokumen[$jenisdokumen]);
	
	// Get the list of options to populate the first dropdown.
	//$option_tahun = _ajax_get_tahun_dropdown();
	// If we have a value for the first dropdown from $form_state['values'] we use
	// this both as the default value for the first dropdown and also as a
	// parameter to pass to the function that retrieves the options for the
	// second dropdown.
  
	$selected_tahun = isset($form_state['values']['tahun']) ? $form_state['values']['tahun'] : $tahun;
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $tahun . $namasingkat . $jenisdokumen_label . $bulan_label,
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	/*$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' => 'Tahun',
		'#options' => $option_tahun,
		'#default_value' => $tahun,		//$selected,
		// Bind an ajax callback to the change event (which is the default for the
		// select form type) of the first dropdown. It will replace the second
		// dropdown when rebuilt.
		'#ajax' => array(
		  // When 'event' occurs, Drupal will perform an ajax request in the
		  // background. Usually the default value is sufficient (eg. change for
		  // select elements), but valid values include any jQuery event,
		  // most notably 'mousedown', 'blur', and 'submit'.
		  // 'event' => 'change',
			'callback' => 'jurnal_main_form_callback',
			'wrapper' => 'skpd-replace',
		),
	);*/
	
	$query = db_select('unitkerja', 'p');

		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');

		# execute the query
		$results = $query->execute();
		
			
		# build the table fields
		$option['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $option[$data->kodeuk] = $data->namasingkat; 
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
		'#options' => $option,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		//'#default_value' => $namasingkat,
	);
	
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $bulanoption,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	
	
	$form['formdata']['submit3'] = array(
		'#type' => 'submit',
		'#value' => 'Submit3',
		'#attributes' => array('class' => array('btn btn-success')),
		'#suffix' => '<input class="btn btn-success form-submit" type="submit" id="edit-submit2" name="op" value="Submit"/>',
	);
	$form['formdata']['submit2']= array(
		'#type' => 'submit',
		'#value' => 'Submittt',
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */
function jurnal_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */

/**
 * Helper function to populate the second dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @param string $key
 *   This will determine which set of options is returned.
 *
 * @return array
 *   Dropdown options
 */


?>
