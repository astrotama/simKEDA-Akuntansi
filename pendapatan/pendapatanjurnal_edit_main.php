<?php
echo "hello";
function pendapatanjurnal_edit_main($arg=NULL, $nama=NULL) {
	
	$jurnalid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$transid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'pendapatan/edit/' . $jurnalid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('pendapatanjurnal_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$transid){

}

function pendapatanjurnal_edit_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, 'edit') !== false) {
		$referer = $_SESSION["pendapatanjurnallastpage"];
	} else {
		$_SESSION["pendapatanjurnallastpage"] = $referer;
	}
	
	
	//if ($current_url != $referer)
	//	$_SESSION["pendapatanjurnallastpage"] = $referer;
	//else
	//	$referer = $_SESSION["pendapatanjurnallastpage"];
	//drupal_set_message($referer);
	
	/*
	drupal_add_library('system','ui.datepicker');
	drupal_add_js('jQuery(document).ready(function(){jQuery( ".pickadate" ).datepicker({dateFormat: "dd-M-yy", autoSize: false});});', 'inline');
	$form['tanggalx'] = array(
		'#type' => 'textfield',
		'#title' => t('TanggalX'),
		'#size' => 10,
		'#maxlength' => 10,
		'#default_value' => $tanggal,
		'#attributes' => array('class' => array('pickadate')),
	);
	*/
	
	$jurnalid = arg(2);
	
	$query = db_select('jurnal', 'j');
	$query->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('rincianobyek', 'r', 'ji.kodero=r.kodero');
	$query->fields('j', array('jurnalid', 'refid', 'kodeuk', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'));	
	$query->fields('ji', array('koderod'));	
	$query->fields('r', array('kodero', 'uraian'));
	
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('j.jurnalid', $jurnalid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Nomor ' . $data->nobukti . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$transid = $data->refid;
		$jurnalid = $data->jurnalid;
		
		$kodero = $data->kodero;
		$koderod = $data->koderod;
		if ($koderod=='') $koderod = '0';
		
		//$rekening = $data->kodero . ' - ' . $data->uraian;
		
		$keterangan = $data->keterangan;
		$kodeuk = $data->kodeuk;

		//$tanggal= strtotime($data->tanggal);	

		$tanggal = dateapi_convert_timestamp_to_datetime($data->tanggal);
		
		$nobukti = $data->nobukti;
		$nobuktilain = $data->nobuktilain;
		$jumlah = $data->total;
	}
	
	drupal_set_title($title);
	
	$form['jurnalid'] = array(
		'#type' => 'hidden',
		'#default_value' => $jurnalid,
	);	
	$form['transid'] = array(
		'#type' => 'hidden',
		'#default_value' => $transid,
	);
	
	
	/*$form['tanggal'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $tanggal,
		'#default_value'=> array(
			'year' => format_date($tanggal, 'custom', 'Y'),
			'month' => format_date($tanggal, 'custom', 'n'), 
			'day' => format_date($tanggal, 'custom', 'j'), 
		  ), 
		
	);*/
	
	$form['tanggaltitle'] = array( 
	'#markup' => 'tanggal',
	);
	$form['tanggal']= array(
		 '#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		 '#default_value' => $tanggal, 
				
		 //'#default_value'=> array(
		//	'year' => format_date($TANGGAL, 'custom', 'Y'),
		//	'month' => format_date($TANGGAL, 'custom', 'n'), 
		//	'day' => format_date($TANGGAL, 'custom', 'j'), 
		 // ), 
		 
		 '#date_format' => 'd-m-Y',
		 '#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		 '#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		 //'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		 '#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		 //'#description' => 'Tanggal',
	);
	

	//SKPD
	$query = db_select('unitkerja', 'p');
	$query->innerJoin('anggperuk', 'a', 'p.kodeuk=a.kodeuk');
	# get the desired fields from the database
	$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
			->orderBy('kodedinas', 'ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
	if($results){
		foreach($results as $data) {
		  $option_skpd[$data->kodeuk] = $data->namasingkat; 
		}
	}		
	$form['kodeuk'] = array(
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
		'#default_value' => $kodeuk,
	);
	
	$form['nobukti'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $nobukti,
	);
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $nobuktilain,
	);
	$form['keterangan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keterangan,
	);


	//AJAX
	// Rekening dropdown list
	$form['rekening'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => _load_rekening($kodero),
		'#default_value' => $kodero,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekdetil',
			'wrapper' => 'rekdetil-wrapper',
		),
	);

	// Wrapper for rekdetil dropdown list
	$form['wrapperdetil'] = array(
		'#prefix' => '<div id="rekdetil-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Detil -');
	if (isset($form_state['values']['rekening'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_rekdetil($form_state['values']['rekening']);
	} else
		$options = _load_rekdetil($kodero);

	// Detil dropdown list
	$form['wrapperdetil']['rekdetil'] = array(
		'#title' => t('Detil'),
		'#type' => 'select',
		'#options' => $options,
		'#default_value' => $koderod,
		'#validated' => TRUE,
	);

	//END AJAX


	$form['jumlah']= array(
		'#type' => 'textfield',
		'#title' => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#disabled' => true,
		'#default_value' => $jumlah,
	);


  
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formdata']['submithapus']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus',
		'#attributes' => array('class' => array('btn btn-danger btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function pendapatanjurnal_edit_main_form_submit($form, &$form_state) {
	
	/*
	$replacement = array(
		'@rekening' => $form_state['values']['rekening'],
		'@rekdetil' => $form_state['values']['rekdetil'],
	);
	

	drupal_set_message(t('Submitted data: Rekening = @rekening, Detil = @rekdetil.', $replacement));
	*/
	
	
	
	$kodeuk = $form_state['values']['kodeuk'];
	$transid = $form_state['values']['transid'];
	$jurnalid = $form_state['values']['jurnalid'];
	

	if($form_state['clicked_button']['#value'] == $form_state['values']['submithapus']) {
		drupal_goto('pendapatanjurnal/delete/'. $jurnalid);

		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		
		
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {
		
		
			$nobukti = $form_state['values']['nobukti'];
			$nobuktilain = $form_state['values']['nobuktilain'];
			$keterangan = $form_state['values']['keterangan'];
			$jumlah = $form_state['values']['jumlah'];
			
			//$tanggal = $form_state['values']['tanggal'];
			//$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
			
			$tanggalsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tanggal']);
			
			$kodero = $form_state['values']['rekening'];
			$koderod = $form_state['values']['rekdetil'];
			if ($koderod=='0') $koderod = '';
			
			//drupal_set_message($kodero);
			//drupal_set_message($koderod);
			//drupal_set_message($jurnalid);
			
			//JURNAL
			$query = db_update('jurnal')
			->fields( 
					array(
						'keterangan' => $keterangan,
						'kodeuk' => $kodeuk,
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'total' => $jumlah,
					)
				);
			$query->condition('jurnalid', $jurnalid, '=');
			$res = $query->execute();	

			//JURNAL ITEM APBD
			//1
			$query = db_update('jurnalitem')
					->fields(
						array(
							'debet' => $jumlah,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', '1', '=');
			$res = $query->execute();
			//2. 
			$query = db_update('jurnalitem')
					->fields(
						array(
							'kredit' => $jumlah,
							'kodero' => $kodero,
							'koderod' => $koderod,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', '2', '=');
			$res = $query->execute();			

			//JURNAL ITEM LRA
			//1
			$query = db_update('jurnalitemlra')
					->fields(
						array(
							'debet' => $jumlah,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', 1, '=');
			$res = $query->execute();
			//2. 
			$query = db_update('jurnalitemlra')
					->fields(
						array(
							'kredit' => $jumlah,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', 2, '=');
			$res = $query->execute();				

			//JURNAL ITEM LO
			//1
			$query = db_update('jurnalitemlo')
					->fields(
						array(
							'debet' => $jumlah,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', 1, '=');
			$res = $query->execute();
			//2. 
			$query = db_update('jurnalitemlo')
					->fields(
						array(
							'kredit' => $jumlah,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('nomor', 2, '=');
			$res = $query->execute();		
				
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-pendapatan-' . $jurnalid, $e);
		}		
		//if ($res) drupal_goto('pendapatanantrian');
		

		$referer = $_SESSION["pendapatanjurnallastpage"];
		drupal_goto($referer);
		
	}
	
	
	
	
}


function _ajax_rekdetil($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperdetil'];
}

/**
 * Function for populating rekening
 */
function _load_rekening($kodero) {
	$rekening = array('- Pilih Rekening -');

	//$kode = '41';
	$kode = substr($kodero, 0, 2);

	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodeo", db_like($kode) . '%', 'LIKE');
	// Order by name
	$query->orderBy("r.kodero");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$rekening[$row->kodero] = $row->kodero . ' - ' . $row->uraian;
	}

	return $rekening;
}

/**
 * Function for populating rekdetil
 */
function _load_rekdetil($kodero) {
	$rekdetil = array('- Pilih Detil -');

	// Select table
	$query = db_select("rincianobyekdetil", "r");
	// Selected fields
	$query->fields("r", array('koderod', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodero", $kodero, "=");
	// Order by name
	$query->orderBy("r.koderod");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$rekdetil[$row->koderod] = $row->koderod . ' - ' . $row->uraian;
	}

	return $rekdetil;
}
 
?>
