<?php

function angg_delete_form() {
    
    $kodero = arg(2);
	//drupal_set_message($id);
	
    if (isset($kodero)) {
		
		$ada = false;	
		
		$result = db_query('select uraian, kodero from {anggperuk} where kodero=:kodero', array(':kodero'=>$kodero));
		foreach ($result as $data) {
			//$pengujian_id = $data->pengujian_id;
			$uraian = $data->uraian;
			$kodero = $data->kodero;
			
			$ada = true;
		}
	 
        if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['kodero'] = array(
										'#type' => 'value', 
										'#value' => $kodero,
										);
			$form['formdata']['uraian'] = array (
						'#type' => 'item',
						'#title' =>  t('Uraian'),
						'#markup' => '<p>' . $uraian  . '</p>',
					);
			
			//FORM NAVIGATION	
			$referer = $_SERVER['HTTP_REFERER'];
			$_SESSION["foo"] = $referer;
			
			return confirm_form($form,
								'Anda yakin menghapus Anggaran dengan Kode' .  $kodero,
								$referer,
								'PERHATIAN : Data yang dihapus tidak bisa dikembalikan lagi.',
								'Hapus',
								'Batal');
        }
    }
}

function angg_delete_form_validate($form, &$form_state) {
}

function angg_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];

		//delete
		$num = db_delete('anggperuk')
		  ->condition('kodero', $kodero)
		  ->execute();
		  
        if ($num) {
			
            drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('angg/new/'. $kodero);
        }
        
    }
}
?>