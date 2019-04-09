<?php
function rekening_detil_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('rekening_detil_main_form');
	return drupal_render($output_form);// . $output;
	
}

function rekening_detil_main_form($form, &$form_state) {

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	/*
	if (strpos($referer, 'arsip')>0)
		$_SESSION["spjgajilastpage"] = $referer;
	else
		$referer = $_SESSION["spjgajilastpage"];
	*/
	
	//db_set_active('penatausahaan');
	$kodero = arg(1);
	//drupal_set_message($kodero);

	$form['kodero'] = array(
		'#type' => 'value',
		'#value' => $kodero,
	);
	

	//PAJAK	
	$form['formdetil']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>detil</th></tr>',
		 '#suffix' => '</table>',
	);	 
	
	$i = 0;
	$query = db_query('SELECT koderod,uraian FROM `rincianobyekdetil` WHERE kodero=:kodero order by  LENGTH(koderod), koderod asc', array(':kodero' => $kodero));
	foreach ($query as $data) {
		
		$i++;
		
		$form['formdetil']['e_koderod' . $i]= array(
				'#type' => 'value',
				'#value' => $data->koderod,
		); 
		
		$form['formdetil']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$datarod='';
		if(strlen($data->koderod)>10){
			$datarod=substr($data->koderod,-3);
		}
		else{
			$datarod=substr($data->koderod,-2);
		}
		$form['formdetil']['koderod' . $i]= array(
				'#type'		=> 'textfield', 
				'#prefix' 	=> '<td>',
				'#default_value'=> $datarod, 
				'#size' => 3,
				'#maxlength' => 3,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['uraian' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#default_value'=> $data->uraian, 
			'#suffix' => '</td></tr>',
		); 
		//drupal_set_message(substr($data->koderod,-2));
	}	

	for ($x = 1; $x <= 5; $x++)  {
		
		$i++;
		
		$form['formdetil']['e_koderod' . $i]= array(
				'#type' => 'value',
				'#value' => 'new',
		); 
		
		$form['formdetil']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['koderod' . $i]= array(
				'#type'		=> 'textfield', 
				'#prefix' 	=> '<td>',
				'#default_value'=> '', 
				'#size' => 3,
				'#maxlength' => 3,
				'#suffix' => '</td>',
		); 
		$form['formdetil']['uraian' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#default_value'=> '', 
			'#suffix' => '</td></tr>',
		); 

	}		

	$form['formdetil']['jumlahdetil']= array(
		'#type' => 'value',
		'#value' => $i,
	);	

	$form['formdetil']['referer']= array(
		'#type' => 'value',
		'#value' => $referer,
	);	
	
	//SIMPAN
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	
	
	return $form;
}

function rekening_detil_main_form_validate($form, &$form_state) {

}
	
function rekening_detil_main_form_submit($form, &$form_state) {
$kodero = $form_state['values']['kodero'];
$jumlahdetil = $form_state['values']['jumlahdetil'];
$referer = $form_state['values']['referer'];

for($n=1; $n<=$jumlahdetil; $n++){
	$e_koderod = $form_state['values']['e_koderod' . $n];
	$koderod = $form_state['values']['koderod' . $n];
	$uraian = $form_state['values']['uraian' . $n];
	
	if ($koderod=='') {
		
		if ($e_koderod != 'new') {
			$num_deleted = db_delete('rincianobyekdetil')
				  ->condition('kodero', $kodero)
				  ->condition('koderod', $e_koderod)
				  ->execute();				
				 //drupal_set_message("A");
		}
		 
	} else {
		
		$koderod = $kodero . $koderod;
		if ($e_koderod=='new') {						//old
			$query = db_insert('rincianobyekdetil') // Table name no longer needs {}
					->fields(array(
					  'kodero' => $kodero,
					  'koderod' => $koderod,
					  'uraian' => $uraian,				  
			))
			//dpq($query);
			->execute();
			//drupal_set_message($kodero.'-'.$koderod.'-'.$uraian);
				
			
		} else {									//new
			$query = db_update('rincianobyekdetil') 		// Table name no longer needs {}
			->fields(array(
				'koderod' => $koderod,
				'uraian' => $uraian,
			))
			->condition('kodero', $kodero, '=')
			->condition('koderod', $e_koderod, '=')
			//drupal_set_message("C");
			->execute();
					
		}	
	}

}
	
drupal_goto($referer);
	
}



?>