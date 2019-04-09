<?php

function pendapatanjurnal_deleteday_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $tanggal = arg(2);
	
	
	
    if (isset($tanggal)) {
		//drupal_set_message('x');		
		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Konfirmasi Penghapusan Jurnal Pendapatan',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);
		
		
		$form['formdata']['tanggal'] = array('#type' => 'value', '#value' => $tanggal);
		$form['formdata']['keterangan'] = array (
					'#type' => 'item',
					'#title' =>  t('Uraian'),
					'#markup' => '<p>Penghapusan jurnal harian</p>',
				);
		
		//FORM NAVIGATION	
		$current_url = url(current_path(), array('absolute' => TRUE));
		$referer = $_SERVER['HTTP_REFERER'];
		
		
		return confirm_form($form,
							'Anda yakin menghapus SEMUA JURNAL PADA tanggal : ' . apbd_fd($tanggal),
							$referer,
							'PERHATIAN : Jurnal yang dihapus tidak bisa dikembalikan lagi.',
							//'<button type="button" class="btn btn-danger">Hapus</button>',
							//'<em class="btn btn-danger">Hapus</em>',
							//'<input class="btn btn-danger" type="button" value="Hapus">',
							'Hapus',
							'Batal');
        
    }
}
function pendapatanjurnal_deleteday_form_validate($form, &$form_state) {
}

function pendapatanjurnal_deleteday_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $tanggal = $form_state['values']['tanggal'];
		//drupal_set_message($tanggal);
		
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {
			
			$res = db_query('select jurnalid from {jurnal} where jenis=:jenis and tanggal=:tanggal', array(':jenis'=>'pad', ':tanggal'=>$tanggal));
			foreach ($res as $data) {
				
				//drupal_set_message($data->jurnalid);
				
				//Delete Jurnal Item APBD
				$num = db_delete('jurnalitem')
					->condition('jurnalid', $data->jurnalid)
					->execute();
				//Delete Jurnal Item LRA
				
				$num = db_delete('jurnalitemlra')
					->condition('jurnalid', $data->jurnalid)
					->execute();
				//Delete Jurnal Item LO
				$num = db_delete('jurnalitemlo')
					->condition('jurnalid', $data->jurnalid)
					->execute();			
				
				
				//Reset antrian
				$query = db_update('apbdtrans')
				->fields(
						array(
							'jurnalsudah' => 0,
							'jurnalid' => '',
						)
					);
				$query->condition('jurnalid', $data->jurnalid, '=');
				$res = $query->execute();
					
			}	
			
			//Delete Jurnal
			$num = db_delete('jurnal')
				->condition('tanggal', $tanggal)
				->execute();
			
		
			
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-delete-' . 'x', $e);
		}
		

        if ($num) {
			
            drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('pendapatanjurnal');
        }
        
    }
}
?>