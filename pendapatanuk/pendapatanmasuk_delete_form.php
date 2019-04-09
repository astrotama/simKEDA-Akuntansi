<?php

function pendapatanmasuk_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $jurnalid = arg(2);
	
	
	
    if (isset($jurnalid)) {
		$query = db_select('jurnaluk', 'j');
		$query->fields('j', array('jurnalid', 'refid', 'jenis', 'kodeuk', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'));	
		
		//$query->fields('u', array('namasingkat'));
		$query->condition('j.jurnalid', $jurnalid, '=');
		
		//dpq($query);
		
		$ada = false;	
		# execute the query
		$results = $query->execute();
		foreach ($results as $data) {
			
			$transid = $data->refid;
			$jurnalid = $data->jurnalid;
			
			
			$keterangan = $data->keterangan;
			$kodeuk = $data->kodeuk;
			$jenis = $data->jenis;

			$tanggal= apbd_format_tanggal_pendek($data->tanggal);		
			$nobukti = $data->nobukti;
			$nobuktilain = $data->nobuktilain;
			$jumlah = $data->total;
			
			$ada = true;
		}
		
	 	//drupal_set_message($jenis);
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
			$form['formdata']['jenis'] = array (
						'#type' => 'value',
						'#value' => $jenis,
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
function pendapatanmasuk_delete_form_validate($form, &$form_state) {
}

function pendapatanmasuk_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
      $jurnalid = $form_state['values']['jurnalid'];
		$transid = $form_state['values']['transid'];
		$jenis = $form_state['values']['jenis'];

		//BEGIN TRANSACTION
		//$transaction = db_transaction();
		
		//try {
			
			//Delete Jurnal
			$num = db_delete('jurnaluk')
				->condition('jurnalid', $jurnalid)
				->execute();

			//Delete Jurnal Item APBD
			$num = db_delete('jurnalitemuk')
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LRA
			$num = db_delete('jurnalitemlrauk')
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LO
			$num = db_delete('jurnalitemlouk')
				->condition('jurnalid', $jurnalid)
				->execute();
				
			//Reset Dokumen
			db_set_active('pendapatan');
			if ($jenis=='pad-in') {
				$query = db_update('setor')
				->fields(
						array(
							'jurnalsudah' => 0,
							'jurnalid' => '',
						)
					);
				$query->condition('setorid', $transid, '=');
			} else {
				$query = db_update('setoridmaster')
				->fields(
						array(
							'jurnalsudah' => 0,
							'jurnalid' => '',
						)
					);
				$query->condition('id', $transid, '=');			
			}			
			$num = $query->execute();
			
			db_set_active();
				
		
		/*	
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-delete-' . $jurnalid, $e);
		}
		*/

	     drupal_set_message('Penghapusan berhasil dilakukan');
        if ($jenis=='pad-in') {
            drupal_goto('pendapatanjurnaluk');
        } else {
        		drupal_goto('pendapatanjurnaluksetor');
        }
        
    }
}
?>