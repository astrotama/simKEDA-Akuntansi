<?php

function umum_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $jurnalid = arg(2);
	if (isUserSKPD()) {
		$suffix = 'uk';
	} else {
		$suffix = '';
	} 
	
	//if (!isSuperuser()) $jurnalid = '123456';
	
	
    if (isset($jurnalid)) {
		$query = db_select('jurnal' . $suffix, 'j');
		$query->fields('j', array('jurnalid', 'nobukti', 'kodeuk', 'nobuktilain', 'tanggal', 'keterangan', 'total'));	
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('j.jurnalid', $jurnalid, '=');

		$ada = false;	
		# execute the query
		$results = $query->execute();
		foreach ($results as $data) {
			$jurnalid = $data->jurnalid;
			
			$keterangan = $data->keterangan;
			$kodeuk = $data->kodeuk;

			$tanggal= apbd_format_tanggal_pendek($data->tanggal);		
			$nobukti = $data->nobukti;
			$nobuktilain = $data->nobuktilain;
			$total = $data->total;
			
			//drupal_set_message ($jurnalid);
			//drupal_set_message ($kodeuk);
			//drupal_set_message ($transid);
			
			$ada = true;
		}
		
	 
        if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan Jurnal',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			$form['formdata']['jurnalid'] = array('#type' => 'value', '#value' => $jurnalid);
			//$form['formdata']['transid'] = array('#type' => 'value', '#value' => $transid);
			$form['formdata']['nobuktilain'] = array('#type' => 'value', '#value' => $nobuktilain);
			$form['formdata']['nomor'] = array (
						'#type' => 'item',
						'#title' =>  t('Nomor/Tanggal'),
						'#markup' => '<p>' . $nobukti . ' ' . $tanggal  . '<p>',
					);
			$form['formdata']['keterangan'] = array (
						'#type' => 'item',
						'#title' =>  t('keperluan'),
						'#markup' => '<p>' . $keterangan . '</p>',
					);
			$form['formdata']['total'] = array (
						'#type' => 'item',
						'#title' =>  t('total'),
						'#markup' => '<p>' . $total . '</p>',
					);
					
					

			
			//FORM NAVIGATION	
			$current_url = url(current_path(), array('absolute' => TRUE));
			$referer = $_SERVER['HTTP_REFERER'];
			
			 
			return confirm_form($form,
								'Anda yakin menghapus Jurnal Nomor/Tanggal : ' .  $nobukti . ', ' . $tanggal,
								$referer,
								'PERHATIAN : Jurnal yang dihapus tidak bisa dikembalikan lagi.',
								//'<button type="button" class="btn btn-danger">Hapus</button>',
								//'<em class="btn btn-danger">Hapus</em>',
								//'<input class="btn btn-danger" type="button" value="Hapus">',
								'Hapus',
								'Batal');
        }
    }
}
function umum_delete_form_validate($form, &$form_state) {
}

function umum_delete_form_submit($form, &$form_state) {
	
	if (isUserSKPD()) {
		$suffix = 'uk';
	} else {
		$suffix = '';
	} 
    if ($form_state['values']['confirm']) {
        $jurnalid = $form_state['values']['jurnalid'];
		
		drupal_set_message("jurnal id = ".$jurnalid);
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {
			
			//Delete Jurnal
			db_delete('jurnal' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();

			//Delete Jurnal Item APBD
			db_delete('jurnalitem' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LRA
			db_delete('jurnalitemlra' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LO
			db_delete('jurnalitemlo' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
				
			
				
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-delete-' . $jurnalid, $e);
		}
		

        if ($num) {
			
           drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('umum');
        }// else {
			//drupal_set_message('Error');
		//}
        
    }
}
?>