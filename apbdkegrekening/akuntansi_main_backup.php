<?php
function akuntansi_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
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
	return $btn2.drupal_render($output_form);
	
}

function akuntansi_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['tabledragrows-4-rek'];
	drupal_set_message($tahun);
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
	 
	$form['tabledragrows'] = array(
	 
	  '#tree' => TRUE,
	  '#prefix' => '<a id="pilih" class="form-submit">PILIH</a>',
	  '#theme' => 'table',
	  '#header' => array('REK',t('Rekening'), t('Uraian')),
	  '#rows' => array(),
	);
	$id=5;
    for ($i=0;$i<5;$i++) {
		$id+=4;
		//if($i<2){
			$rek = array(
			'#id' => 'tabledragrows-'.$i.'-rek',
			'#type' => 'textfield',
			'#value' => $i,
			'#default_value' => 'satu',
			
		  );
		  $ura = array(
			'#id' => 'tabledragrows-' .$i .'-uraian',
			'#type' => 'textfield',
			'#default_value' => 'duwa',
		  );
		//}
         

		  /*$form['tabledragrows'][] = array(
			'fname' => &$fname,
			'sname' => &$sname,
		  );*/
		  
		  

		  $form['tabledragrows']['#rows'][$i] = array(
			$i,
			array('data' => &$rek),
			array('data' => &$ura),
		  );
		
		
       
    }
 
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
function akuntansi_main_form_callback($form, $form_state) {
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


?>
