<?php
function belanja_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('files/css/kegiatancam.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	$tahun = 2016;
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$ntitle = 'Kegiatan';
				$nntitle ='';
				$tahun = arg(2);
				
				$kodeuk = arg(3);
				drupal_set_title($ntitle);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$tahun = 2016;		//variable_get('apbdtahun', 0);
	}
	
	$output_form = drupal_get_form('belanja_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan','width' => '400px', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Sumberdana', 'valign'=>'top'),
		array('data' => 'Sasaran/Target', 'field'=> 'sasaran', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasi', 'valign'=>'top'),
		array('data' => 'Prsn', 'width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
	 $query = db_select('kegiatan2014', 'p')->extend('PagerDefault')->extend('TableSort');
		if(arg(2)!=null){
			$field='kodeuk';
			$value=arg(2);
		}
		else {
			$field='1';
			$value='1';
		};
	  # get the desired fields from the database
	  $limit = 10;
	  $query->fields('p', array('kegiatan', 'kodeuk','sumberdana', 'sasaran', 'target', 'anggaran1', 'anggaran2','realisasi'))
			//
			->condition($field, $value, '=')
			->orderByHeader($header)
			->orderBy('kegiatan', 'ASC')
			->limit($limit);

	  # execute the query
	  $results = $query->execute();
	  $editlink = l('Rekening', '', array('html'=>TRUE));
	  $editlink .= l('<p>Register</p>', '', array('html'=>TRUE));
	  $editlink .= l('<p>Gambar</p>', '', array('html'=>TRUE));
		
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
		$anggaran = apbd_fn($data->anggaran2);
		
		if ($data->anggaran1 > $data->anggaran2)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1) . '</font></p>';
		else if ($data->anggaran1 < $data->anggaran2)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1) . '</font></p>';
		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->kegiatan, 'width' => '20px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sumberdana, 'width' => '120px', 'align' => 'center', 'valign'=>'top'),
						array('data' => $data->target . '<p><i><font color="orange">' . $data->sasaran . '</font></i></p>', 'width' => '230px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran, 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasi), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2, $data->realisasi)), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						$editlink,
					);
	  }
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form).$output;
	
}


function belanja_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['dropdown_first'];
	$kodeuk = $form_state['values']['dropdown_second'];
	//drupal_set_message($row[2014][1]);
	$query = db_select('unitkerja'.$tahun, 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('namasingkat',$kodeuk,'=');
	$results = $query->execute();
	if($results){
			 foreach($results as $data) {
				 $uri2=$data->kodeuk;
			 }
		 }
	$uri = 'belanja/' . $tahun.'/'.$uri2;
	drupal_goto($uri);
	
}


function belanja_main_form($form, &$form_state) {
	if(arg(2)!=null){
		$query = db_select('unitkerja'.arg(1), 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',arg(2),'=');
		$results = $query->execute();
		if($results){
				 foreach($results as $data) {
					 $namasingkat=$data->namasingkat;
				 }
			 }
			 
	}
	else{
		
		$namasingkat='';
		
	}
	
  // Get the list of options to populate the first dropdown.
  $options_first = _ajax_get_tahun_dropdown();
  // If we have a value for the first dropdown from $form_state['values'] we use
  // this both as the default value for the first dropdown and also as a
  // parameter to pass to the function that retrieves the options for the
  // second dropdown.
  $selected = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : arg(1);
  $selected2 = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : key($options_first);
  $form['dropdown_first'] = array(
    '#type' => 'select',
    '#title' => 'Tahun',
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
      'callback' => 'belanja_main_form_callback',
      'wrapper' => 'dropdown-second-replace',
    ),
  );

  $form['dropdown_second'] = array(
    '#type' => 'select',
    '#title' =>  t('SKPD'),
    // The entire enclosing div created here gets replaced when dropdown_first
    // is changed.
    '#prefix' => '<div id="dropdown-second-replace">',
    '#suffix' => '</div>',
    // When the form is rebuilt during ajax processing, the $selected variable
    // will now have the new value and so the options will change.
    '#options' => _ajax_get_skpd_dropdown($selected2),
    '#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : $namasingkat,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Tampilkan'),
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
function belanja_main_form_callback($form, $form_state) {
  return $form['dropdown_second'];
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
      t('2008'),
      t('2009'),
      t('2010'),
      t('2011'),
	  t('2012'),
	  t('2013'),
	  t('2014'),
	  t('2015'),
	  t('2016'),
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
	for($n=2008;$n<=2016;$n++){
		  $query = db_select('unitkerja'.$n, 'p');

		  # get the desired fields from the database
		  $query->fields('p', array('namasingkat','kodeuk'))
				;

		  # execute the query
		$results = $query->execute();
		
			
		  # build the table fields
		 if($results){
			 foreach($results as $data) {
			  $row[$n][] = $data->namasingkat;
			 
			}
		 }
		 if(!isset($row[$n][0])){
			 $row[$n][0]='Data masih Kosong';
			 
		 }
		  /*foreach ($results as $data) {
			  $row[$n][] = $data->namasingkat;
			  
		  }*/
		   
		//$row[2008][] ='Data masih Kosong';
		//drupal_set_message($row[2010][0]);
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
	t('2016') => drupal_map_assoc(
		$row[2016]
    ),
	
    t('Woodwind') => drupal_map_assoc(
      array(
        t('Flute'),
        t('Clarinet'),
        t('Oboe'),
        t('Bassoon'),
      )
    ),
    t('Brass') => drupal_map_assoc(
      array(
        t('Trumpet'),
        t('Trombone'),
        t('French Horn'),
        t('Euphonium'),
      )
    ),
    t('Percussion') => drupal_map_assoc(
      array(
        t('Bass Drum'),
        t('Timpani'),
        t('Snare Drum'),
        t('Tambourine'),
      )
    ),
  );
  if (isset($options[$key])) {
    return $options[$key];
  }
  else {
    return array();
  }
}


?>
