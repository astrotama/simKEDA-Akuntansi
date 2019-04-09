<?php

function pendapatan_import_form($form, $form_state) {
	$referer = $_SERVER['HTTP_REFERER'];
	
	$form['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Perhatian!<ul><li>Pastikan bahwa file yang di-upload adalah file csv.</li><li>Pastikan untuk menekan tombol Upload sebelum meng-import data</li><li>Beri centang pada Langsung Jurnalkan bila ingin langsung menjurnalkan data dari bank</li></ul></div>',
	);
	$form['autojurnal'] = array(
		'#type' => 'checkbox', 
		'#title' => t('Langsung jurnalkan'),
		'#default_value' => true,
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

function pendapatan_import_form_submit($form, $form_state) {
	// Check to make sure that the file was uploaded to the server properly
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $form_state['input']['import']['fid'],
					)
				)->fetchField();
	
	$autojurnal = $form_state['values']['autojurnal'];

	$i = 0;
	if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
			// Open the csv
			
			$handle = fopen(drupal_realpath($uri), "r");
			// Go through each row in the csv and run a function on it. In this case we are parsing by '|' (pipe) characters.
			// If you want commas are any other character, replace the pipe with it.
			while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
				
				//pal_set_message($data[3]);
				$transid = $data[3];
				
				//drupal_set_message(str_replace($data[3], '/', ''));
				
				
				//read data
				$tanggal = substr($data[7],0,4) . '-' . substr($data[7],4,2) . '-' . substr($data[7],-2);
				$refno = $data[3];
				$reftgl = date('Y') . '-' . date('m') . '-' . date('d');;
				$subtotal = $data[11]; $potongan = 0; $total = $subtotal;
				$nobukti = $data[8];
				
				if (strlen($data[10])==8)
					$kodero = $data[10];
				else
					$kodero = substr($data[10],0,5) . '0' . substr($data[10], -2);
				$keterangan = $data[9];
				
				$key = $data[3];
				
				/*
				drupal_set_message($tanggal);
				drupal_set_message($refno);
				drupal_set_message($reftgl);
				drupal_set_message($subtotal);
				drupal_set_message($nobukti);
				drupal_set_message($kodero);
				drupal_set_message($keterangan);
				*/
				
				
				if ($autojurnal) {		
					
					//drupal_set_message($kodero);
					if (substr($kodero,0,1) == '9') {
							
						//drupal_set_message('a');
						
						//kode 9 -> simpan antrian
						$kodero = '90090900';
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');
						
					} else {
						
						//drupal_set_message('j');
						
						//jurnalkan
						$i++;
						
						//$transid = 'T . ' . str_replace($transid, '/', '');
						save2jurnal($transid, $tanggal, $i, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero);
						
						//tandai sudah jurnal
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '1');
					}
					
				} else {					//end if auto jurnal	
					
					//simpan antrian
					save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');

				}
				
				
				
				$operations[] = array(
									'pendapatan_import_batch_processing',  // The function to run on each row
									array($data),  // The row in the csv
								);
			}	//end while read data
 
			// Once everything is gathered and ready to be processed... well... process it!
			$batch = array(
				'title' => t('Importing data CSV dari Bank...'),
				'operations' => $operations,  // Runs all of the queued processes from the while loop above.
				'finished' => 'pendapatan_import_finished', // Function to run when the import is successful
				'error_message' => t('The installation has encountered an error.'),
				'progress_message' => t('Record ke @current dari @total transaksi.'),
			);
			batch_set($batch);
			//batch_process('user');
			fclose($handle);    
		}	//end exist 
	
	} else {	//end empty
		
		drupal_set_message(t('There was an error uploading your file. Please contact a System administator.'), 'error');
	}
}

function pendapatan_import_form_submit_asli($form, $form_state) {
	// Check to make sure that the file was uploaded to the server properly
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(
					':fid' => $form_state['input']['import']['fid'],
					)
				)->fetchField();
	
	$autojurnal = $form_state['values']['autojurnal'];

	$i = 0;
	if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
			// Open the csv
			
			$handle = fopen(drupal_realpath($uri), "r");
			// Go through each row in the csv and run a function on it. In this case we are parsing by '|' (pipe) characters.
			// If you want commas are any other character, replace the pipe with it.
			while (($data = fgetcsv($handle, 0, ';', '"')) !== FALSE) {
				
				//pal_set_message($data[3]);
				$transid = $data[3];
				
				//drupal_set_message(str_replace($data[3], '/', ''));
				
				
				//read data
				$tanggal = substr($data[7],0,4) . '-' . substr($data[7],4,2) . '-' . substr($data[7],-2);
				$refno = $data[3];
				$reftgl = date('Y') . '-' . date('m') . '-' . date('d');;
				$subtotal = $data[11]; $potongan = 0; $total = $subtotal;
				$nobukti = $data[8];
				
				if (strlen($data[10])==8)
					$kodero = $data[10];
				else
					$kodero = substr($data[10],0,5) . '0' . substr($data[10], -2);
				$keterangan = $data[9];
				
				$key = $data[3];
				
				/*
				drupal_set_message($tanggal);
				drupal_set_message($refno);
				drupal_set_message($reftgl);
				drupal_set_message($subtotal);
				drupal_set_message($nobukti);
				drupal_set_message($kodero);
				drupal_set_message($keterangan);
				*/
				
				
				if ($autojurnal) {		
					
					//drupal_set_message($kodero);
					if (substr($kodero,0,1) == '9') {
							
						//drupal_set_message('a');
						
						//kode 9 -> simpan antrian
						$kodero = '90090900';
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');
						
					} else {
						
						//drupal_set_message('j');
						
						//jurnalkan
						$i++;
						
						//$transid = 'T . ' . str_replace($transid, '/', '');
						save2jurnal($transid, $tanggal, $i, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero);
						
						//tandai sudah jurnal
						save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '1');
					}
					
				} else {					//end if auto jurnal	
					
					//simpan antrian
					save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, '0');

				}
				
				
				
				$operations[] = array(
									'pendapatan_import_batch_processing',  // The function to run on each row
									array($data),  // The row in the csv
								);
			}	//end while read data
 
			// Once everything is gathered and ready to be processed... well... process it!
			$batch = array(
				'title' => t('Importing data CSV dari Bank...'),
				'operations' => $operations,  // Runs all of the queued processes from the while loop above.
				'finished' => 'pendapatan_import_finished', // Function to run when the import is successful
				'error_message' => t('The installation has encountered an error.'),
				'progress_message' => t('Record ke @current dari @total transaksi.'),
			);
			batch_set($batch);
			//batch_process('user');
			fclose($handle);    
		}	//end exist 
	
	} else {	//end empty
		
		drupal_set_message(t('There was an error uploading your file. Please contact a System administator.'), 'error');
	}
}
function save2antrian($key, $tanggal, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero, $jurnalsudah) {
	//2017-04-08
	//$transid = $key . sprintf("%04d", 1) . sprintf("%03d", rand(0, 999));
	
	
	$transid = $key . substr($tanggal, 5,2 ) . substr($tanggal, -2) . sprintf("%03d", rand(0, 999));
	
	//drupal_set_message($key);
	//drupal_set_message($transid);
	
	
	db_insert('apbdtrans')
		->fields(array('transid', 'tanggal', 'refno', 'reftgl', 'subtotal', 'total', 'nobukti', 'keterangan'))
		->values(array(
				'transid'=> $transid,
				'tanggal'=> $tanggal,
				'refno' => $refno,
				'reftgl' => $reftgl,
				'subtotal' => $total,
				'total' => $total,
				'nobukti' => $nobukti,
				'keterangan' => $keterangan,
				'jurnalsudah' => $jurnalsudah,
				))
		->execute();
	
	db_insert('apbdtransitem')
		->fields(array('transid', 'kodero', 'masuk', 'keluar'))
		->values(array(
				'transid'=> $transid,
				'kodero'=> $kodero,
				'masuk' => $total,
				'keluar' => 0,
				))
		->execute();	
	
		
}

function save2jurnal($transid, $tanggal, $nourut, $refno, $reftgl, $total, $nobukti, $keterangan, $kodero) {
	
	
	//CEK
	$tidakada = true;
	$results = db_query('select jurnalid from jurnal where refid=:refid and nobukti=:nobukti and nobuktilain=:nobuktilain and tanggal=:tanggal and total=:total', array(':refid'=>$transid, ':nobukti'=>$nobukti, ':nobuktilain'=>$refno, ':tanggal'=>$tanggal, ':total'=>$total));
	foreach ($results as $data) {
		$tidakada = false;	
	}	
	
	if ($tidakada) {
		$kodeuk = '';
		if ($kodero=='41413003') {
			$kodeuk = '27';
		} else if ($kodero=='41201002') {		//sampah
			$kodeuk = '27';						//Indag
		} else if ($kodero=='41202001') {		//kekayaan daerah
			$kodeuk = '81';						//bpkad
		} else if ($kodero=='41202002') {
			$kodeuk = '28';
		} else if ($kodero=='41202005') {		//khusus parkir
			$kodeuk = '30';						//dishub
		} else if ($kodero=='41203003') {		//gangguan
			$kodeuk = '32';						//dlh
		} else {
			$results = db_query('select kodeuk from anggperuk where kodero=:kodero', array(':kodero'=>$kodero));
			foreach ($results as $data) {
				$kodeuk = $data->kodeuk;	
			}
		}		
		//drupal_set_message($kodeuk);
		if ($kodeuk == '') {
			if (substr($kodero,0,3)=='414')
				$kodeuk = '00';
			else
				$kodeuk = '81';
		}	
		$jurnalid = apbd_getkodejurnal($kodeuk);
		
		/*
		drupal_set_message('jurnalid ' . $jurnalid);
		drupal_set_message('nourut ' . $nourut);
		drupal_set_message('transid ' . $transid);
		drupal_set_message('kodeuk ' . $kodeuk);
		drupal_set_message('pad');
		drupal_set_message('nobukti ' . $nobukti); 
		drupal_set_message('refno ' . $refno);
		drupal_set_message('tanggal ' . $tanggal);
		drupal_set_message('keterangan ' . $keterangan); 
		drupal_set_message('total ' . $total);
		*/
		
		$query = db_insert('jurnal')
				->fields(array('jurnalid', 'nourut', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nourut' => $nourut,
						'refid' => $transid,
						'kodeuk' => $kodeuk,
						'jenis' => 'pad',
						'nobukti' => $nobukti,
						'nobuktilain' => $refno,
						'tanggal' =>$tanggal,
						'keterangan' => $keterangan, 
						'total' => $total,
					)
				);
		//drupal_set_message($query);	
		
		$res = $query->execute();
		
		
		//JURNAL ITEM APBD
		//1
		$query = db_insert('jurnalitem')
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(
					array(
						'jurnalid' => $jurnalid,
						'nomor' => 1,
						'kodero' => apbd_getKodeROAPBD(),
						'debet' => $total,
					)
				); 
		$res = $query->execute();
		//2. 
		$query = db_insert('jurnalitem')
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => 2,
						'kodero' => $kodero,
						'kredit' => $total,
					)
				);
		$res = $query->execute();
		
		 
		//JURNAL ITEM LO
		$query = db_insert('jurnalitemlo')
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => 1,
						'kodero' => apbd_getKodeRORKPPKD(),
						'debet' => $total,
					)
				);
		$res = $query->execute();
		
		//Rek LO
		$koderosap = '81101001';
		$sql = db_select('rekeningmapsap_apbd', 'rm');
		$sql->fields('rm',array('koderosap'));
		$sql->condition('koderoapbd', $kodero, '=');
		$res = $sql->execute();
		foreach ($res as $datamap) {
			$koderosap = $datamap->koderosap;
		}
		$query = db_insert('jurnalitemlo')
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => 2,
						'kodero' => $koderosap,
						'kredit' => $total,
					)
				);
		$res = $query->execute();
		
		
		//JURNAL ITEM LRA
		$query = db_insert('jurnalitemlra')
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => '1',
						'kodero' => apbd_getKodeROSAL(),
						'debet' => $total,
					)
				);
		$res = $query->execute();
		//Rek LRA
		$koderosap = '41101001';
		$sql = db_select('rekeningmaplra_apbd', 'rm');
		$sql->fields('rm',array('koderolra'));
		$sql->condition('koderoapbd', $kodero, '=');
		$res = $sql->execute();
		foreach ($res as $datamap) {
			$koderosap = $datamap->koderolra;
		}
		$query = db_insert('jurnalitemlra')
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => '2',
						'kodero' => $koderosap,
						'kredit' => $total,
					)
				);
		$res = $query->execute();	
		
	
	}
	
}

function pendapatan_import_batch_processing($data) {
	drupal_set_message('Hai');
	
/*	
  // Lets make the variables more readable.
  $title = $data[0];
  $body = $data[1];
  $serial_num = $data[2];
  // Find out if the node already exists by looking up its serial number. Each serial number should be unique. You can use whatever you want.
  $nid = db_query("SELECT DISTINCT n.nid FROM {node} n " . 
    "INNER JOIN {field_data_field_serial_number} s ON s.revision_id = n.vid AND s.entity_id = n.nid " .
    "WHERE field_serial_number_value = :serial", array(
      ':serial' => $serial_num,
    ))->fetchField();
  if(!empty($nid)) {
    // The node exists! Load it.
    $node = node_load($nid);
 
    // Change the values. No need to update the serial number though.
    $node->title = $title;
    $node->body['und'][0]['value'] = $body;
    $node->body['und'][0]['safe_value'] = check_plain($body);
    node_save($node);
  }
  else {
    // The node does not exist! Create it.
    global $user;
    $node = new StdClass();
    $node->type = 'page'; // Choose your type
    $node->status = 1; // Sets to published automatically, 0 will be unpublished
    $node->title = $title;
    $node->uid = $user->uid;		
    $node->body['und'][0]['value'] = $body;
    $node->body['und'][0]['safe_value'] = check_plain($body);
    $node->language = 'und';
 
    $node->field_serial_number['und'][0]['value'] = $serial_num;
    $node->field_serial_number['und'][0]['safe_value'] = check_plain($serial_num);
    node_save($node);
  }
 */ 
}

function pendapatan_import_finished() {
  drupal_set_message(t('Import Completed Successfully'));
}

?>