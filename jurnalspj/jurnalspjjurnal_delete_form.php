<?php

function jurnalspjjurnal_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $jurnalid = arg(2);
	$suffixjurnal = apbd_getsuffixjurnal();	
	
	
    if (isset($jurnalid)) {
		$query = db_select('jurnal' . $suffixjurnal, 'j');
		$query->fields('j', array('jurnalid', 'refid', 'kodeuk', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'));	
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('j.jurnalid', $jurnalid, '=');

		$ada = false;	
		# execute the query
		$results = $query->execute();
		foreach ($results as $data) {
			
			$transid = $data->refid;
			$jurnalid = $data->jurnalid;
			
			
			$keterangan = $data->keterangan;
			$kodeuk = $data->kodeuk;

			$tanggal= apbd_format_tanggal_pendek($data->tanggal);		
			$nobukti = $data->nobukti;
			$nobuktilain = $data->nobuktilain;
			$jumlah = $data->total;
			
			$ada = true;
		}
		
	 
        if ($ada) {
            
			//drupal_set_message('x');		
			$form['formdata'] = array (
				'#type' => 'fieldset',
				'#title'=> 'Konfirmasi Penghapusan Jurnal Pendapatan',
				'#collapsible' => TRUE,
				'#collapsed' => FALSE,        
			);
			
			
			$form['formdata']['jurnalid'] = array('#type' => 'value', '#value' => $jurnalid);
			$form['formdata']['transid'] = array('#type' => 'value', '#value' => $transid);
			$form['formdata']['nomor'] = array (
						'#type' => 'item',
						'#title' =>  t('Nomor/Tanggal'),
						'#markup' => '<p>' . $nobukti . ', ' . $tanggal  . '<p>',
					);
			$form['formdata']['noref'] = array (
						'#type' => 'item',
						'#title' =>  t('No. Referensi'),
						'#markup' => '<p>' . $transid  . '</p>',
					);
			$form['formdata']['keterangan'] = array (
						'#type' => 'item',
						'#title' =>  t('Uraian'),
						'#markup' => '<p>' . $keterangan . '</p>',
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
function jurnalspjjurnal_delete_form_validate($form, &$form_state) {
}

function jurnalspjjurnal_delete_form_submit($form, &$form_state) {
	$suffixjurnal = apbd_getsuffixjurnal();	
	
    if ($form_state['values']['confirm']) {
        $jurnalid = $form_state['values']['jurnalid'];
		$transid = $form_state['values']['transid'];
		
		//drupal_set_message("jurnal id = ".$jurnalid);
		//drupal_set_message("trans id = " . $transid);
		
		//BEGIN TRANSACTION
		//$transaction = db_transaction();
		
		//try {
			
			//Delete Jurnal
			db_delete('jurnal' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();

			//Delete Jurnal Item APBD
			db_delete('jurnalitem' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LRA
			db_delete('jurnalitemlra' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LO
			db_delete('jurnalitemlo' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Reset Dokumen
			
			db_set_active('penatausahaan');
			$query = db_update('dokumen')
			->fields(
					array(
						'jurnalsudah' . $suffixjurnal => 0,
						'jurnalidspj' . $suffixjurnal => '',
					)
				);
			$query->condition('dokid', $transid, '=');
			$res = $query->execute();
			db_set_active();
			
				
		//}
		//	catch (Exception $e) {
		//	$transaction->rollback();
		//	watchdog_exception('jurnal-delete-' . $jurnalid, $e);
		//}
		

        //if ($num) {
			
        //   drupal_set_message('Penghapusan berhasil dilakukan');
			
            drupal_goto('jurnalspjjurnal');
        //} else {
		//	drupal_set_message('Error');
		//}
        
    }
}
?>