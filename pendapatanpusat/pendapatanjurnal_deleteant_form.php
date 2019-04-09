<?php

function pendapatanjurnal_deleteant_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $tanggal = arg(2);
	
	
	//drupal_set_message($tanggal);
    if (isset($tanggal)) {
				
		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Konfirmasi Penghapusan Antrian Jurnal',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);
		
		
		$form['formdata']['tanggal'] = array('#type' => 'value', '#value' => $tanggal);
		$form['formdata']['keterangan'] = array (
					'#type' => 'item',
					'#title' =>  t('Uraian'),
					'#markup' => '<p>Penghapusan antrian jurnal harian</p>',
				);
		
		//FORM NAVIGATION	
		$current_url = url(current_path(), array('absolute' => TRUE));
		$referer = $_SERVER['HTTP_REFERER'];
		
		
		return confirm_form($form,
							'Anda yakin menghapus SEMUA ANTRIAN PADA tanggal : ' . apbd_fd($tanggal),
							$referer,
							'PERHATIAN : Jurnal yang dihapus tidak bisa dikembalikan lagi.',
							//'<button type="button" class="btn btn-danger">Hapus</button>',
							//'<em class="btn btn-danger">Hapus</em>',
							//'<input class="btn btn-danger" type="button" value="Hapus">',
							'Hapus',
							'Batal');
        
    }
}
function pendapatanjurnal_deleteant_form_validate($form, &$form_state) {
}

function pendapatanjurnal_deleteant_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $tanggal = $form_state['values']['tanggal'];
		//drupal_set_message($tanggal);
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {
			
			$res = db_query('select transid from {apbdtrans} where tanggal=:tanggal', array(':tanggal'=>$tanggal));
			foreach ($res as $data) {
				
				//drupal_set_message($data->transid);
				
				//Delete Jurnal Item APBD
				$num = db_delete('apbdtransitem')
					->condition('transid', $data->transid)
					->execute();

					
			}	
			
			//Delete Jurnal
			$num = db_delete('apbdtrans')
				->condition('tanggal', $tanggal)
				->execute();
			
				
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('apbdtrans-delete-' . $transid, $e);
		}
		

        if ($num) {
			
            drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('pendapatanantrian');
        }
        
    }
}
?>