<?php

function pendapatanbku_import_form($form, $form_state) {
	$referer = $_SERVER['HTTP_REFERER'];
	
	$form['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa file yang di-upload adalah file csv.</li><li>Pastikan untuk menekan tombol Upload sebelum meng-import data</li><li>Beri centang pada Langsung Jurnalkan bila ingin langsung menjurnalkan data dari bank</li></ul></div>',
	);
	$form['import'] = array(
		'#title' => t('Import'),
		'#type' => 'managed_file',
		'#description' => t('The uploaded csv will be imported and temporarily saved.'),
		'#upload_location' => 'public://tmp/',
		'#upload_validators' => array(
		'file_validate_extensions' => array('csv', 'txt'),
	),
	);
	$form['submit'] = array (
		'#type' => 'submit',
		'#disabled' => isAuditor(),
		'#value' => t('Import'),
		'#suffix' => "&nbsp;<a href='/' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
  return $form;
}

function pendapatanbku_import_form_submit($form, $form_state) {
	// Check to make sure that the file was uploaded to the server properly
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $form_state['input']['import']['fid'],
					)
				)->fetchField();
	
	$i = 0;
	if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
			// Open the csv
			 
			
			$handle = fopen(drupal_realpath($uri), "r");
			// Go through each row in the csv and run a function on it. In this case we are parsing by '|' (pipe) characters.
			// If you want commas are any other character, replace the pipe with it.
			//while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
			while (($data = fgets($handle)) !== false) {	
				
				$str4 = substr($data, 4, 1);
				$str7 = substr($data, 7, 1);
				$str11 = substr($data, 15, 1);
				$str40 = substr($data, 40, 1);
				$str109 = substr($data, 109, 1);
				$str87 = substr($data, 87, 1);
				
				
				if (($str4=='/') and ($str7=='/') and ($str11=='/') and  ($str40!='') and  ($str109!='') and  ($str87==' ')) {
					//drupal_set_message($data);
					//drupal_set_message('Here');

					$tanggal = substr($data, 8,4) . '-' . trim(substr($data, 5,2)) . '-' . trim(substr($data, 2,2));
					$refno = substr($data, 24, 10);
					$uraian = substr($data, 40, 30);

					$jumlah = str_replace(',', '', trim(substr($data, 92, 18)));
					//simpan 
					save2bku($tanggal, $refno, $jumlah, $uraian);

					$operations[] = array('pendapatanbku_import_batch_processing',  array($data));
					
				}
				
				
			}	//end while read data
			//drupal_set_message($i);
			
			// Once everything is gathered and ready to be processed... well... process it!
			
			$batch = array(
				'title' => t('Importing data BKU dari Bank...'),
				'operations' => $operations,  // Runs all of the queued processes from the while loop above.
				'finished' => 'pendapatanbku_import_finished', // Function to run when the import is successful
				'error_message' => t('The installation has encountered an error.'),
				'progress_message' => t('Record ke @current dari @total transaksi.'),
			);
			batch_set($batch);
			//batch_process('user');
			
			
			fclose($handle);    
		}	//end exist 
		
		drupal_set_message('Porting ' . $uri . ' selesai.');
	
	} else {	//end empty
		
		drupal_set_message(t('There was an error uploading your file. Please contact a System administator.'), 'error');
	}
}

function save2bku($tanggal, $refno, $total, $keterangan) {
	
	//drupal_set_message($tanggal . ' | ' . $refno . ' | ' . $total . ' | ' . $keterangan);
	
	db_insert('bkutrans')
		->fields(array('tanggal', 'refno', 'total', 'nobukti', 'keterangan'))
		->values(array(
				'tanggal'=> $tanggal,
				'refno' => $refno,
				'total' => $total,
				'nobukti' => $refno,
				'keterangan' => $keterangan,
				))
		->execute();
	

}

function pendapatanbku_import_batch_processing($data) {
	drupal_set_message('Hai');
}

function pendapatanbku_import_finished() {
  drupal_set_message(t('Import Completed Successfully'));
}

?>