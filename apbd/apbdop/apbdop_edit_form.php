<?php
    
function apbdop_edit_form(){

    $username = arg(2);
	
	//drupal_set_message($username);

	$akses_id = '';
	$arr_akses_by_id = array();
	$arr_akses_by_id[''] = '---Tentukan Hak Akses---';
	
	$roles = user_roles(TRUE);
	foreach( array_keys($roles) as $rid) {
		//drupal_set_message($rid . $roles[$rid]);
		switch ($roles[$rid]) {
			
			
			case 'administrator' :
				//$arr_akses_by_id[$rid] = 'Super User';
				if (isAdministrator()) {
					$arr_akses_by_id[$rid] = 'Administrator';
				}
				break;
			case 'superuser' :
				//$arr_akses_by_id[$rid] = 'Super User';
				if (isSuperuser()) {
					$arr_akses_by_id[$rid] = 'Superuser';
				}
				break;
			case 'bidang':
				//$arr_akses_by_id[$rid] = 'User Kecamatan (Musrenbangcam)';
				$arr_akses_by_id[$rid] = 'Bidang';
				break;
			case 'pembantu':
				//$arr_akses_by_id[$rid] = 'User Kecamatan (Musrenbangcam)';
				$arr_akses_by_id[$rid] = 'Bidang';
				break;
			case 'skpd':
				//$arr_akses_by_id[$rid] = 'User SKPD (non Kecamatan)';
				$arr_akses_by_id[$rid] = 'SKPD';
				break;
			case 'ppkd':
				//$arr_akses_by_id[$rid] = 'User Kecamatan (Musrenbangcam)';
				$arr_akses_by_id[$rid] = 'Bendahara PPKD';
				break;
			case 'verifikator':
				if (isSuperuser()) {
					$arr_akses_by_id[$rid] = 'Verifikator';
				}
				break;
			case 'auditor':
				if (isSuperuser()) {
					$arr_akses_by_id[$rid] = 'Auditor';
				}
				break;
		}
		
	}

    $disabled = FALSE;
    if (isset($username)) {
		
		$query = db_select('apbdop', 'o');
		$query->innerJoin('users', 'u', 'o.username=u.name');
		$query->fields('o', array('username','nama','kodeuk','kodesuk'));	
		$query->fields('u', array('uid'));	
		$query->condition('o.username', $username, '=');
		
		//dpq($query);	
			
		# execute the query	

		$results = $query->execute();
		foreach ($results as $data) {
		
				$username = $data->username;
				$nama = $data->nama;
				$kodeuk = $data->kodeuk;
				$kodesuk = $data->kodesuk;
				$uid = $data->uid;
                $disabled =TRUE;
				/*
				$user = user_load(array('name' => $username));
				drupal_set_message('x :' . $user->roles[0]);
				if (in_array('administrator', $user->roles)) {
					$akses_id = $arr_akses_by_name['administrator'];
				} elseif(in_array('bidang', $user->roles)){
					$akses_id = $arr_akses_by_name['bidang'];
				} elseif(in_array('skpd', $user->roles)){
					$akses_id = $arr_akses_by_name['skpd'];
				} elseif(in_array('verifikator', $user->roles)){
					$akses_id = $arr_akses_by_name['verifikator'];					
				} 
				*/
				
				//drupal_set_message($uid);
				
				$user = user_load($uid);
				if(user_has_role('3', $user)) {				//administrator
					$akses_id = '3';
				} else if(user_has_role('4', $user)) {		//superuser
					$akses_id = '4';
				} else if(user_has_role('5', $user)) {		//skpd
					$akses_id = '5';
				} else if(user_has_role('6', $user)) {		//bidang
					$akses_id = '6';
				} else if(user_has_role('7', $user)) {		//verifikator skpd
					$akses_id = '7';
				} else if(user_has_role('8', $user)) {		//ppkd
					$akses_id = '8';
				}
				
				//drupal_set_message($akses_id);
		
		} 
		
		drupal_set_title('Operator ' . $username);

		$form['usernamex']= array(
			'#type'         => 'item', 
			'#title'        => 'Username', 
			//'#description'  => 'username', 
			'#markup' => '<H3>' . $username . '</H3>',
		);
		$form['username']= array(
			'#type'         => 'value', 
			'#value' => $username,
		);
		
		
    } else {
		$username = '';
		
		if (isSuperuser()) 
			$akses_id = '6';
		else
			$akses_id = '5';
		
		drupal_set_title('Operator Baru');

		$form['username']= array(
			'#type'         => 'textfield', 
			'#title'        => t('Username'), 
			//'#description'  => 'username', 
			'#maxlength'    => 32, 
			'#size'         => 40, 
			'#required'     => true, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $username, 
		); 		
	}

	$form['uid']= array(
		'#type'	=> 'value', 
		'#value'=> $uid, 
	);     
	
	$form['upwd']= array(
		'#type'         => 'textfield', 
		'#title'        => t('Password'), 
		//'#description'  => 'username', 
		'#maxlength'    => 32, 
		'#size'         => 40, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> '', 
	); 
	$form['nama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'nama', 
		'#maxlength'    => 100, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nama, 
	);
	
	
	if (isSuperuser()) {
		
		$options = array();
		$options[] = '-SUPERUSER/VERIFIKATOR-';
		$query = db_select('unitkerja', 'p');
		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');
		# execute the query
		$results = $query->execute();
		foreach($results as $data) {
			$options[$data->kodeuk] = $data->namasingkat;
		}		
		
		$form['kodeuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Unit Kerja',
			'#options'		=> $options,
			//'#description'  => 'kodeuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodeuk, 
		);

		$form['kodesuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodesuk, 
		);		
		$form['akses']= array(
			'#type'         => 'select', 
			'#title'        => 'Hak Akses',
			'#options'		=> $arr_akses_by_id,
			//'#description'  => 'kodeuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $akses_id, 
		); 
		
	} else {

		$kodeuk = apbd_getuseruk();
		
		$form['kodeuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodeuk, 
		);

		
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';

		$query = db_select('subunitkerja', 's');
		$query->fields('s', array('kodesuk','namasuk'));	
		$query->condition('s.kodeuk', $kodeuk, '=');	
		$query->orderBy('s.kodeuk', 'ASC');	
		$results = $query->execute();
		foreach ($results as $data) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}
		
		$form['kodesuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Bidang/Bagian',
			'#options'		=> $subskpd,
			//'#description'  => 'kodesuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20,  
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodesuk, 
		); 
		
		$form['akses']= array(
			'#type'         => 'select', 
			'#options'		=> $arr_akses_by_id,
			'#default_value'=> $akses_id, 
		); 
		
	}
		
    $form['e_username']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $username, 
    );
	
	
	
    $form['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/operators' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    return $form;
}

function apbdop_edit_form_validate($form, &$form_state) {
	$username = arg(2);
    if (!isset($username)) {
        if (strlen($form_state['values']['username']) < 3 ) {
            form_set_error('username', 'username harus terdiri atas minimal 5 karakter');
        }
		$user = user_load(array('name' => $form_state['values']['username']));
		if ($user) {
			form_set_error('username', 'username sudah ada, pilih yang lain');
		}
        if (strlen($form_state['values']['upwd']) < 3 ) {
            form_set_error('upwd', 'Password harus terdiri atas minimal 5 karakter');
        }
    }
	if ($form_state['values']['akses'] =='') {
		form_set_error("akses",'Hak Akses User Belum diisi');
	}
	//$form['username']['#default_value'] = 'aa';
}
function apbdop_edit_form_submit($form, &$form_state) {
    
    $e_username = $form_state['values']['e_username'];
    $upwd = $form_state['values']['upwd'];
	$uid = $form_state['values']['uid'];
	$username = $form_state['values']['username'];
	$nama = $form_state['values']['nama'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	if ($kodesuk=='') $kodesuk = '0000';

	$user = null;

    $akses_id = $form_state['values']['akses'];
	
    if ($e_username=='')
    {
		$user = user_save(null, array('name'=> $username, 'pass'=>$upwd, 'status'=>1));
		if ($user) {
			
			//APBD_OP
			$num_deleted = db_delete('apbdop')
				->condition('username', $username)
				->execute();			
			db_insert('apbdop')
				->fields(array('username', 'nama', 'kodeuk', 'kodesuk'))
				->values(array(
					'username'=> $username,
					'nama' => strtoupper($nama),
					'kodeuk' => $kodeuk,
					'kodesuk' => $kodesuk,
				))
			->execute();
			
			//USER_rokes			
			$res = db_insert('users_roles')
				->fields(array('uid', 'rid'))
				->values(array(
					'uid' => $user->uid,
					'rid' => $akses_id,
				))
			->execute();
			
		}
    } else {
		
		$user = user_load($uid);
		if ($user) {
			$param = array ();			
			if (strlen($upwd)>0) {
				$param['pass'] = $upwd;
				user_save($user, $param);			
			}
		}

		//UPDATE ROLES
		$query = db_update('users_roles')
					->fields( 
						array(
							'rid' => $akses_id,
						)
					);
		$query->condition('uid', $user->uid, '=');
		$res = $query->execute();		
		
		//UPDATE OP
		$query = db_update('apbdop')
					->fields( 
						array(
							'nama' => strtoupper($nama),
							'kodeuk' => $kodeuk,
							'kodesuk' => $kodesuk,
						)
					);
		$query->condition('username', $username, '=');
		$res = $query->execute();		
    }

    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('operators');    
}

?>