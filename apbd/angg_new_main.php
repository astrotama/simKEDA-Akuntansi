<?php
function angg_new_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');

	
	$output_form = drupal_get_form('angg_new_main_form');
	return drupal_render($output_form);
	
}

function angg_new_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (arg(2)==''){
		$kodeuk = '81';
	}else{
		$kodeuk = arg(2);
	}
	//drupal_set_message($kodero);	
	
	$form['pilskpd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilih SKPD',
		'#collapsible' => TRUE,
		'#collapsed' => false,        
	);	
	$query = db_select('unitkerja', 'p');
	$query->innerJoin('anggperuk', 'a', 'a.kodeuk=p.kodeuk');
	# get the desired fields from the database
	$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
			->orderBy('kodedinas', 'ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
	//$option_skpd['ZZ'] = 'SELURUH SKPD'; 
	if($results){
		foreach($results as $data) {
		  $option_skpd[$data->kodeuk] = $data->namasingkat; 
		}
	}		
	$form['pilskpd']['pilkodeuk'] = array(
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
	$form['pilskpd']['submitskpd']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formlra'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Anggaran',
		'#collapsible' => TRUE,
		'#collapsed' => false,        
	);	
		 
		$queryy = db_query('SELECT namauk FROM `unitkerja` WHERE kodeuk=:kodeuk ', array(':kodeuk' => $kodeuk));
		$i = 1;
		foreach ($queryy as $data) {	
		$form['formlra']['skpd'] = array(
			'#markup' =>  '<b>SKPD :   '. $data->namauk .'</b>',			
		);
		$form['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);
		}
		$form['formlra']['table2']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="120px">Kode</th><th>Uraian</th><th>Jumlah</th><th width="20px">  </th></tr>',
			 '#suffix' => '</table>',
		);	
		
		//ITEM APBD
		$queryy = db_query('SELECT kodero,uraian, jumlah FROM `anggperuk` WHERE kodeuk=:kodeuk ', array(':kodeuk' => $kodeuk));
		$i = 1;
		foreach ($queryy as $data) {			
		$i++;
			$form['formlra']['table2']['koderot' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['uraiant' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['jumlaht' . $i]= array(
				//'#type'         => 'textfield', 
				'#markup'=> apbd_fn($data->jumlah), 
				'#attributes' => array('style' => 'align: right'),	
				'#size' => 25,
				'#prefix' => '<td style="text-align: right;">',
				'#suffix' => '</td>',
			);
			if ($data->jumlah == 0){
			$form['formlra']['table2']['hapus' . $i]= array(
				//'#type'         => 'submit', 
				'#markup'=> '<a class="btn btn-danger btn-sm glyphicon glyphicon-trash" href="/angg/delete/' . $data->kodero.'">Hapus</a>', 
				'#attributes' => array('style' => 'align: right'),	
				'#prefix' => '<td style="text-align: right;">',
				'#suffix' => '</td></tr>',
			);
			$form['formlra']['table2']['kosong']= array(
				'#type'         => 'value', 
				'#value'         => $data->kodero, 
			);
			}
			//$i = ;
		}	
	
	
	
	
	//PAJAK	
	$form['formlra']['formdetil']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="120px">Kode</th><th>Uraian</th><th  width="250px">Jumlah</th></tr>',
		 '#suffix' => '</table>',
	);	 
	for ($x = 1; $x <= 3; $x++)  {
		
		
		$form['formlra']['formdetil']['kodero' . $x]= array(
				'#prefix' => '<tr><td>',
				'#type' => 'textfield',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formlra']['formdetil']['uraian' . $x]= array(
				'#type'		=> 'textfield', 
				'#prefix' 	=> '<td>',
				'#default_value'=> '', 
				'#suffix' => '</td>',
		); 
		$form['formlra']['formdetil']['jumlah' . $x]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#attributes'	=> array('style' => 'text-align: right'),
			'#default_value'=> '0', 
			'#suffix' => '</td></tr>',
		); 

	}	
	
	$form['formlra']['formdetil']['referer']= array(
		'#type' => 'value',
		'#value' => $referer,
	);	
	
	//SIMPAN
	
	$form['formlra']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	
	
	return $form;
}

function angg_new_main_form_validate($form, &$form_state) {

}
	
function angg_new_main_form_submit($form, &$form_state) {
$kodeuk =  $form_state['values']['kodeuk'];
$pilkodeuk =  $form_state['values']['pilkodeuk'];
$referer = $form_state['values']['referer'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitskpd']) {
	drupal_goto('angg/new/'. $pilkodeuk);
}else{
	
for($n=1; $n<=3; $n++){
	$kodero = $form_state['values']['kodero' . $n];
	$uraian = $form_state['values']['uraian' . $n];
	$jumlah = $form_state['values']['jumlah' . $n]; 
	
	if ($kodero!='') {
		$query = db_query('SELECT uraian FROM rincianobyek WHERE kodero=:kodero', array(':kodero' => $kodero));
		foreach ($query as $data) {
			$uraian = $data->uraian;
		}
		
		drupal_set_message($uraian);
		drupal_set_message($kodeuk);
		
		
		$query = db_insert('anggperuk')
				->fields(array(
				  'kodero' => $kodero, 
				  'tahun' => apbd_tahun(), 
				  'kodeuk' => $kodeuk,
				  'jumlah' => $jumlah,
				  'uraian' => $uraian,				  
		))
		->execute();
		
	}
			
			
			//dpq($query);
			
					

}
	
}
	
//drupal_goto($referer);
	
}

?>