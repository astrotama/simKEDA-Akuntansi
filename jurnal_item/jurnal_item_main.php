<?php
function jurnal_item_main($arg=NULL, $nama=NULL) {
	$perintah = arg(1);
	if($perintah == 'reset'){
		hapus_semua();
		$output_form = drupal_get_form('jurnal_item_main_form');
		return drupal_render($output_form);// . $output;
	}else{
		$output_form = drupal_get_form('jurnal_item_main_form');
		return drupal_render($output_form);// . $output;
	}
}

function jurnal_item_main_form($form, &$form_state){
	//REKENING
	$form['formrekening'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JURNAL ITEM<em class="text-info pull-right">' . '' . '</em>',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	$form['formrekening']['table']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">NO</th><th width="90px">KODE</th><th width="600px">URAIAN</th><th width="130px">DEBET</th><th width="130px">KREDIT</th></tr>',
		 '#suffix' => '</table></div>',
	);	
	
	$i = 0;
	$results = db_query("SELECT * FROM {jurnalitem}");

	foreach ($results as $data) {
		$i++;			
			// $form['formrekening']['table']['koderoapbd' . $i]= array(
					// '#type' => 'value',
					// '#value' => $kodero,
			// ); 
			
			$form['formrekening']['table']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['debet' . $i]= array(
				//'#type'         => 'textfield',
				'#type'         => 'textfield', 
				'#default_value'=> $data->debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			); 
			$form['formrekening']['table']['kredit' . $i]= array(
				//'#type'         => 'textfield', 
				'#type'         => 'textfield', 
				'#default_value'=> $data->kredit, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);	
	}
	
	$form['formdata']['tambah']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formdata']['reset']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function jurnal_item_main_form_submit($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['tambah']) {
		drupal_goto('jurnal_item/tambah');
	}else if($form_state['clicked_button']['#value'] == $form_state['values']['reset']) {
		drupal_goto('jurnal_item/reset');
	}
	drupal_set_message('test');
}

function hapus_semua(){
	$results = db_query("delete from jurnalitem");
}
?>