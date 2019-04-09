<?php
function persediaan_edit_main($arg=NULL, $nama=NULL) {
	
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
		$output_form = drupal_get_form('persediaan_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$transid){

}

function persediaan_edit_main_form($form, &$form_state) {

	$tanggal = mktime(11, 14, 54, 8, 12, 2016);
	$kodeuk = '81';
	$keterangan = '';

	$nobukti = '';
	$nobuktilain = '';

	$tipe = arg(2);
	$jurnalid = arg(3);
	
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
		
		
		$keterangan = $data->keterangan;
		$kodeuk = $data->kodeuk;

		$tanggal= strtotime($data->tanggal);		
		$nobukti = $data->nobukti;
		$nobuktilain = $data->nobuktilain;
	}
	
	if ($tipe=='') {
		$tipe = '0';
		$title = 'Jurnal Penerimaan Persediaan';	
	} else if ($tipe=='1') 
		$title = 'Jurnal Pemakaian Persediaan';	
	else
		$title = 'Jurnal Pengeluaran Persediaan';	
	
	drupal_set_title($title);
	

	$form['jurnalid'] = array(
		'#type' => 'value',
		'#value' => $jurnalid,
	);	

	$form['tanggal'] = array(
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
		
	);


	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'hidden',
			'#title' =>  t('SKPD'),
			'#default_value' => $kodeuk,
		);
		
	} else {
		//SKPD
		$query = db_select('unitkerja', 'p');
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
	}
	
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


	//ITEM APBD
	$form['formapbd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'REKENING PERSEDIAAN',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	
		$form['formapbd']['table']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th></tr>',
			 '#suffix' => '</table>',
		);	
		
		//ITEM APBD
		//condition('a.keterangan', '%' . db_like($keyword) . '%', 'LIKE');
		$query = db_select('rincianobyeksap', 'r');
		$query->fields('r', array('kodero','uraian'));
		$query->condition('r.kodero', db_like('117') . '%', 'LIKE');
		$results = $query->execute();
		$i = 0;
		foreach ($results as $data) {
			
			$i++;
			
			//APBD
			$form['formapbd']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['formapbd']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $data->uraian,
			); 
			
			
			$form['formapbd']['table']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['debet' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '0', 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
			$i = $i;
		}
		

		
		

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formdata']['submithapus']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus',
		'#attributes' => array('class' => array('btn btn-danger btn-sm')),
	);
	
	return $form;
}

function persediaan_edit_main_form_submit($form, &$form_state) {

	drupal_goto('persediaan');
}


?>
