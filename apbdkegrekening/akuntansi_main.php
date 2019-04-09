<?php
function akuntansi_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	/*if(arg(1)=="cek"){
		echo "CEK";
	}*/
	echo
	'<script 
		src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js">
	</script>';
	
	drupal_add_js('files/js/akuntansi.js');
	
	drupal_set_title('Akuntansi ');
	
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	$btn='<div id="artist">Click Here</div>
<div id="jungle"><span>Random Text</span></div>';
 
	$btn2='';//'<a id="pilih" class="form-submit">PILIH</a></div>';
	$output_form = drupal_get_form('akuntansi_main_form');
	//$output_form = drupal_get_form('formtable_form_table_form');
	if(arg(1)=='pdf'){
			  
			  $output = getTable();
			  print_pdf_p($output);
		}
	else{
		return $btn2.drupal_render($output_form);
	}
	
	
	
}


function akuntansi_main_form($form, &$form_state) {
	$options_first = _ajax_example_get_first_dropdown();
  // If we have a value for the first dropdown from $form_state['values'] we use
  // this both as the default value for the first dropdown and also as a
  // parameter to pass to the function that retrieves the options for the
  // second dropdown.
  //drupal_set_tittle('cek');
  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : key($options_first);

  $form['dropdown_first'] = array(
    '#type' => 'select',
    '#title' => 'Jenis',
    '#options' => $options_first,
    '#default_value' => $selected,
    // Bind an ajax callback to the change event (which is the default for the
    // select form type) of the first dropdown. It will replace the second
    // dropdown when rebuilt.
    '#ajax' => array(
      // When 'event' occurs, Drupal will perform an ajax request in the
      // background. Usually the default value is sufficient (eg. change for
      // select elements), but valid values include any jQuery event,
      // most notably 'mousedown', 'blur', and 'submit'.
      // 'event' => 'change',
      'callback' => 'ajax_example_dependent_dropdown_callback',
      'wrapper' => 'dropdown-second-replace',
    ),
  );

  $form['dropdown_second'] = array(
    '#type' => 'select',
    '#title' => t('Obyek'),
    // The entire enclosing div created here gets replaced when dropdown_first
    // is changed.
    '#prefix' => '<div id="dropdown-second-replace">',
    '#suffix' => '</div>',
    // When the form is rebuilt during ajax processing, the $selected variable
    // will now have the new value and so the options will change.
    '#options' => _ajax_example_get_second_dropdown($selected),
    '#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : '',
	'#ajax' => array(
      // When 'event' occurs, Drupal will perform an ajax request in the
      // background. Usually the default value is sufficient (eg. change for
      // select elements), but valid values include any jQuery event,
      // most notably 'mousedown', 'blur', and 'submit'.
      // 'event' => 'change',
      'callback' => 'ajax_example_dependent_dropdown_callback2',
      'wrapper' => 'dropdown-third-replace',
    ),
  );
  isset($form_state['values']['dropdown_second']) ? $second=$form_state['values']['dropdown_second'] : $second='';
  $form['dropdown_third'] = array(
    '#type' => 'select',
    '#title' => t('Rincian Obyek'),
	'#prefix' => '<div id="dropdown-third-replace">',
	'#suffix' => '</div>',
    //'#suffix' => '<a id="pilih" class="form-submit">PILIH</a></div>',
	'#options' => _ajax_example_get_third_dropdown_options($second),
    // The entire enclosing div created here gets replaced when dropdown_first
    // is changed.
    // When the form is rebuilt during ajax processing, the $selected variable
    // will now have the new value and so the options will change.
    //'#options' => array(1,2,3),
    '#default_value' => isset($form_state['values']['dropdown_third']) ? $form_state['values']['dropdown_third'] : '',
  );
	$form['formdata']['pil']=array(
		'#type' => 'markup',
		'#value' =>'<a id="pilih" class="form-submit">PILIH</a>',
	);
	$form['formdata']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi rekening dengan memilih menggunakan tombol Pilih</font></span>",
	);	
	 
	
	$id=5;
//TABLE . . . . . . .
	$form['pilih']= array(
		'#markup' =>'<a id="pilih" class="form-submit">SUBMIT</a>&nbsp<a href="?q=akuntansi/pdf">CETAK</a>',
	);
	$form['table']= array(
		'#prefix' => '<div id="names-fieldset-wrapper"><table><tr><th width="10%">Edit</th><th width="30%">Rekening</th><th width="60%">Uraian</th></tr>',
		 '#suffix' => '<td>',
	);
	
	$query = db_select('jurnalitem', 'n')
    ->fields('n',array('jurnalid','uraian', 'kodero','debet','kredit','keterangan'));
	$results = $query->execute();$i=0;
    foreach ($results as $data) {
		//$id+=4;
		//if($i<2){
		
			$form['table']['jurnalid'.$i]= array(
				'#type'         => 'hidden', 
				'#default_value'=> $data->jurnalid, 
				
			); 
			$form['table']['editrekening'.$i]= array(
				//'#prefix' => '<tr><td>',
				'#markup' => '<tr><td><input type="checkbox" id="edit'.$i.'" ></td>',
				//'#suffix' => '</td><td>',
			); 
			$form['table']['rekening'.$i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $data->kodero, 
				'#prefix' => '<td>',
				'#suffix' => '</td><td>',
			); 
			 $form['table']['uraian'.$i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $data->uraian, 
				//'#prefix' => '<table><tr><th>Rekening</th><th>Uraian</th></tr>',
				'#suffix' => '</td></tr>',//.$table,
			); 
		//}
         

		 $i++;
	       
    }
	$form['table']['row']= array(
		'#type'	=> 'hidden', 
		'#value'=> $i, 
				
	); 
	if (empty($form_state['num_names'])) {
		$form_state['num_names'] = 1;
	}
	//$form['#tree'] = TRUE;
	/*$form['names_fieldset'] = array(
		'#type' => 'fieldset',
		//'#title' => t('People coming to the picnic'),
		// Set up the wrapper so that AJAX will be able to replace the fieldset.
		'#prefix' => '<div id="names-fieldset-wrapper">',
		'#suffix' => '</div>',
	 );*/
	for ($n = 0; $n < $form_state['num_names']; $n++) {
		if($n==$form_state['num_names']-1){
			$table='</table></div>';
		}
		else{
			$table='';
		}
		$edit=$i+$n;
		$form['table']['editrekening'.$i+$n]= array(
				//'#prefix' => '<tr><td>',
				'#markup' => '<tr><td><input type="checkbox" id="edit'.$edit.'" ></td>',
				//'#suffix' => '</td><td>',
		);
		$form['table']['rekening'.$edit]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '', 
				'#prefix' => '<td>',
				'#suffix' => '</td><td>',
		); 
		 $form['table']['uraian'.$edit]= array(
			'#type'         => 'textfield', 
			'#default_value'=> '', 
			//'#prefix' => '<table><tr><th>Rekening</th><th>Uraian</th></tr>',
			'#suffix' => '</td></tr>'.$table,
		);
		/*$form['names_fieldset']['name'][$i] = array(
		  '#type' => 'textfield',
		  '#title' => t('Name'),
		);*/
	}
	$form['tambah'] = array(
		'#type' => 'submit',
		'#value' => t('Tambah'),
		'#submit' => array('add_field'),
		// See the examples in ajax_example.module for more details on the
		// properties of #ajax.
		'#ajax' => array(
		  'callback' => 'akuntansi_main_form_callback',
		  'wrapper' => 'names-fieldset-wrapper',
		),
	 );
	
 
	$form['maxdetil']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> '', 
	); 
 
	

	
		
		
		
		$form['submitnewdetil'] = array (
			'#type' => 'submit',
			'#value' => 'Simpan',
			//'#weight' => 6,
		);
	
	
//FORMTABLE.............

		
	
    return $form;
}

function akuntansi_main_form_submit($form, &$form_state) {
	$row=$form_state['values']['row'];
	for($n=0;$n<$row;$n++){
		$kodero= $form_state['values']['rekening'.$n];
		$uraian= $form_state['values']['uraian'.$n];
		$jurnalid= $form_state['values']['jurnalid'.$n];
		$new=$form_state['num_names'];
		//if(isset($kodero))drupal_set_message($kodero);
		if($uraian!=null){
			$qupdated = db_update('jurnalitem')
			->fields(array('kodero' => $kodero,'uraian' => $uraian,))
			->condition ('jurnalid', $jurnalid)
			->execute();
			//drupal_set_message($new+$row);
			/*db_insert('jurnalitem')
			->fields(array('kodero', 'uraian'))
			->values(array(
					'kodero' => $kodero,
					'uraian' => $uraian,
					
					))
			->execute();*/
		}
		
	}
	for($n=$row;$n<($new+$row);$n++){
		
			$kodero= $form_state['values']['rekening'.$n];
			$uraian= $form_state['values']['uraian'.$n];
		if(isset($kodero) and $kodero!=''){
			db_insert('jurnalitem')
				->fields(array('kodero', 'uraian'))
				->values(array(
						'kodero' => $kodero,
						'uraian' => $uraian,
						
						))
				->execute();
		}
		else{
			drupal_set_message("Null");
		}
	}
	
	
}

function formtable_form_table_form($form = array(), &$form_state) {
   $sample_data = array
  (
      array('id' => 1, 'first_name' => 'jay', 'last_name' => 'chris'),
      array('id' => 2, 'first_name' => 'clo', 'last_name' => 'jason'),
      array('id' => 3, 'first_name' => 'len', 'last_name' => 'ken'),
  );

  $sample_header = array
  (
    'first_name' => t('First Name'),
    'last_name' => t('Last Name'),
  );
  $options = array();
foreach($sample_data as $data)
{
  $options[$data['id']] = array 
  (
    'first_name' => $data['first_name'], // 'first_name' was the key used in the header
    'last_name' => $data['last_name'], // 'last_Name' was the key used in the header
  );
}

  $form['table'] = array
  (
    '#type' => 'tableselect',
    '#header' => $sample_header,
    '#options' => $options,
  );

  $form['submit'] = array
  (
    '#type' => 'submit',
    '#value' => t('Submit'),
  );
  return $form;
}

/**
 * Theme callback for the form table.
 */
function theme_formtable_form_table(&$variables) {
  // Get the userful values.
  $form = $variables['form'];
  $rows = $form['rows'];
  $header = $form['#header'];

  // Setup the structure to be rendered and returned.
  $content = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => array(),
  );

  // Traverse each row.  @see element_chidren().
  foreach (element_children($rows) as $row_index) {
    $row = array();
    // Traverse each column in the row.  @see element_children().
    foreach (element_children($rows[$row_index]) as $col_index) {
      // Render the column form element.
      $row[] = drupal_render($rows[$row_index][$col_index]);
    }
    // Add the row to the table.
    $content['#rows'][] = $row;
  }

  // Redner the table and return.
  return drupal_render($content);
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


/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */
function _ajax_get_tahun_dropdown() {
  // drupal_map_assoc() just makes an array('String' => 'String'...).
  return drupal_map_assoc(
    array(
	  t('2015'),
	  t('2014'),
	  t('2013'),
	  t('2012'),
      t('2011'),
      t('2010'),
      t('2009'),
      t('2008'),
    )
  );
}

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
function _ajax_get_skpd_dropdown($key = '') {
	$row = array();
	for($n=2015;$n>=2008;$n--){
		$query = db_select('unitkerja'.$n, 'p');

		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');

		# execute the query
		$results = $query->execute();
		
			
		# build the table fields
		$row[$n]['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $row[$n][$data->kodeuk] = $data->namasingkat; 
			}
		}
	}
	
	$options = array(
		t('2008') => drupal_map_assoc(
			$row[2008]
		),
		t('2009') => drupal_map_assoc(
			$row[2009]
		),
		t('2010') => drupal_map_assoc(
			$row[2010]
		),
		t('2011') => drupal_map_assoc(
			$row[2011]
		),
		t('2012') => drupal_map_assoc(
			$row[2012]
		),
		t('2013') => drupal_map_assoc(
			$row[2013]
		),
		t('2014') => drupal_map_assoc(
			$row[2014]
		),
		t('2015') => drupal_map_assoc(
			$row[2015]
		),
	);
	
	if (isset($options[$key])) {
		return $options[$key];
	} else {
		return array();
	}
}
function add_field($form, &$form_state) {
  $form_state['num_names']++;
  $form_state['rebuild'] = TRUE;
}
function akuntansi_main_form_callback($form, $form_state) {
  return $form['table'];
}
function getTable(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Uraian', 'width' => '240px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '120px','align'=>'center','style'=>$styleheader),
		
		
	);
	
	$query = db_query('select distinct j.kodej, j.uraian from {jenis} as j inner join {jurnalitem} as ji on mid(ji.kodero,1,3)=j.kodej');
	$resultsi = $query->fetchAll();
	foreach ($resultsi as $dataj) {
		$rows[] = array(
						array('data' => 1,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;'.$style),
						array('data' => $dataj->kodej, 'width' => '80px','align'=>'left','style'=>$style),
						array('data' => $dataj->uraian, 'width' => '240px', 'align'=>'left','style'=>'font-weight: bold;'),
						array('data' => '-', 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
						array('data' => '-', 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
		);
		//Obyek .............
		$queryo = db_query('select distinct o.kodeo, o.uraian from {obyek} as o  inner join {jurnalitem} as ji on mid(ji.kodero,1,5) = o.kodeo and mid(o.kodeo,1,3)='.$dataj->kodej);
		$resultso = $queryo->fetchAll();
		foreach ($resultso as $datao) {
		$rows[] = array(
						array('data' => 1,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;'.$style),
						array('data' => $datao->kodeo, 'width' => '80px','align'=>'left','style'=>$style),
						array('data' => $datao->uraian, 'width' => '240px', 'align'=>'left','style'=>'font-size: 120%;'),
						array('data' => '-', 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
						array('data' => '-', 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
		);	
		//Rincian Obyek/JurnalItem........
				$queryi = db_select('jurnalitem', 'k')->extend('TableSort');
				$queryi->fields('k', array('uraian', 'kodero','debet','kredit','keterangan'));
				$queryi->orderByHeader($header);
				$queryi->orderBy('k.kodero', 'ASC');
				$queryi->condition('k.kodero', $datao->kodeo.'%', 'like');
				$resultsi = $queryi->execute();
					
				# build the table fields
				$no=0;


				
				$total=0;$total2=0;		
				
				foreach ($resultsi as $data) {
					$no++;  
					
					
					
					$rows[] = array(
								array('data' => $no,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;'.$style),
								array('data' => $data->kodero, 'width' => '80px','align'=>'left','style'=>$style),
								array('data' => ' -'.$data->uraian, 'width' => '240px', 'align'=>'left','style'=>$style.' font-style: italic;'),
								array('data' => apbd_fn($data->debet), 'width' => '120px','align'=>'right','style'=>$style),
								array('data' => apbd_fn($data->kredit), 'width' => '120px','align'=>'right','style'=>$style),
								//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
									
								);
								$total+=$data->debet;
								$total2+=$data->kredit;
				}
		}
		//END Rincian Obyek		
	}
			//$tahun='2015';
			
			
			$rows[] = array(
								array('data' => 'JUMLAHf', 'width' => '360px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
								array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
								array('data' => apbd_fn($total2), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
								
								//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
								
							);
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
}
?>
