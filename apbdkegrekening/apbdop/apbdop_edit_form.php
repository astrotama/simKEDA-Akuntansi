<?php
    
function apbdop_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit User',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $username = arg(3);
	drupal_set_title('Pengelolaan User');
	//drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    drupal_add_css('files/css/kegiatancam.css');		

	$akses='';
	$lakses = array();
	$takses = array();
	$lakses[''] = '---Tentukan Hak Akses---';
	
	$roles = user_roles(TRUE);
	foreach( array_keys($roles) as $rid) {
		switch ($roles[$rid]) {
			case 'apbd admin' :
				//$lakses[$rid] = 'Super User';
				if (isSuperuser()) {
					$lakses[$rid] = 'Administrator';
					$takses['apbd admin'] = $rid;
				}
				break;
			case 'user kecamatan':
				//$lakses[$rid] = 'User Kecamatan (Musrenbangcam)';
				$lakses[$rid] = 'Bidang';
				$takses['user kecamatan'] = $rid;
				break;
			case 'user skpd non-kecamatan':
				//$lakses[$rid] = 'User SKPD (non Kecamatan)';
				$lakses[$rid] = 'SKPD';
				$takses['user skpd non-kecamatan'] = $rid;
				break;

			case 'user viewer':
				//$lakses[$rid] = 'User SKPD (non Kecamatan)';
				$lakses[$rid] = 'Viewer';
				$takses['user viewer'] = $rid;
				break;
				
		}
		
	}

    $disabled = FALSE;
    if (isset($username))
    {
        if (!user_access('apbdop edit'))
            drupal_access_denied();
			
        $sql = 'select username,nama,kodeuk,kodesuk from {apbdop} where username=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array ($username));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$username = $data->username;
				$nama = $data->nama;
				$kodeuk = $data->kodeuk;
				$kodesuk = $data->kodesuk;
                $disabled =TRUE;
				$user = user_load(array('name' => $username));
				if (in_array('apbd admin', $user->roles)) {
					$akses = $takses['apbd admin'];
				} elseif(in_array('user kecamatan', $user->roles)){
					$akses = $takses['user kecamatan'];
				} elseif(in_array('user skpd non-kecamatan', $user->roles)){
					$akses = $takses['user skpd non-kecamatan'];
				} elseif(in_array('user viewer', $user->roles)){
					$akses = $takses['user viewer'];
				}
				//print_r($user);
			} else {
				$username = '';
			}
        } else {
			$username = '';
		}
    } else {
		if (!user_access('apbdop tambah'))
			drupal_access_denied();
		$username = '';
		$form['formdata']['#title'] = 'Tambah User';
		$akses = $takses['user skpd non-kecamatan'];
	}
    
	
	$form['formdata']['username']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Username', 
		//'#description'  => 'username', 
		'#maxlength'    => 60, 
		'#size'         => 40, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		//'#value'=> '',
		//'#default_value' => $username,
	);
	if ($disabled) {
		$form['formdata']['username']['#value'] = $username;
	} else {
		$form['formdata']['username']['#default_value'] = $username;
	}
	$form['formdata']['upwd']= array(
		'#type'         => 'textfield', 
		'#title'        => t('Password'), 
		//'#description'  => 'username', 
		'#maxlength'    => 32, 
		'#size'         => 40, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> '', 
	); 
	$form['formdata']['nama']= array(
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
		$privquery = sprintf("select kodeuk, namasingkat from {unitkerja} order by namasingkat");
		$privres = db_query($privquery);
		$options = array();
		while ($privrec = db_fetch_object($privres)) {
			$options[$privrec->kodeuk] = $privrec->namasingkat;
		}
		
		$form['formdata']['kodeuk']= array(
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

		$form['formdata']['kodesuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodesuk, 
		);		
		$form['formdata']['akses']= array(
			'#type'         => 'select', 
			'#title'        => 'Hak Akses',
			'#options'		=> $lakses,
			//'#description'  => 'kodeuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $akses, 
		); 
		
	} else {

		$kodeuk = apbd_getuseruk();
		$akses = $takses['user kecamatan'];
		
		$form['formdata']['kodeuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodeuk, 
		);

		//$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}
		
		$form['formdata']['kodesuk']= array(
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
		
		$form['formdata']['akses']= array(
			'#type'         => 'hidden', 
			'#options'		=> $lakses,
			'#default_value'=> $akses, 
		); 
		
	}
		
    $form['formdata']['e_username']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $username, 
    );
	
	
	
//	$form['formdata']['access'] = array (
//        '#type' => 'fieldset',
//        '#title'=> 'Hak Akses',
//        '#collapsible' => TRUE,
//        '#collapsed' => FALSE,
//		
//    );
	//$t = apbd_perm();
	//for ($i=0; $i<count($t); $i++) {
	//	
	//	$form['formdata']['access'][rr($t[$i])] = array (
	//		'#type' => 'checkbox',
	//		'#title' => $t[$i],
	//		'#return_value' => 1, 
	//		'#default_value' => 0
	//	);
	//}

    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/manageuser' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    return $form;
}
function apbdop_edit_form_validate($form, &$form_state) {
	$username = arg(3);
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
	//$form['formdata']['username']['#default_value'] = 'aa';
}
function apbdop_edit_form_submit($form, &$form_state) {
    
    $e_username = $form_state['values']['e_username'];
    $upwd = $form_state['values']['upwd'];
	$username = $form_state['values']['username'];
	$nama = $form_state['values']['nama'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$user = null;
    $akses = $form_state['values']['akses'];
	$roles = array($akses => 'new akses');

	$iroles = user_roles(TRUE);			
	
    if ($e_username=='')
    {
		$user = user_save(null, array('name'=> $username, 'pass'=>$upwd, 'status'=>1, 'roles' => $roles));
		if ($user) {
			if ($iroles[$akses]=='apbd admin')
				$kodeuk='';
			$sql = 'insert into {apbdop} (username,nama,kodeuk,kodesuk) values(\'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($username, strtoupper($nama), strtoupper($kodeuk), strtoupper($kodesuk)));
		}
    } else {
		
		$user = user_load(array('name' => $username));
		if ($user) {
			$param = array ();			
			if (strlen($upwd)>0)
				$param['pass'] = $upwd;
			$param['roles'] = $roles;
			user_save($user, $param);			
		}
		if ($iroles[$akses]=='apbd admin')
			$kodeuk='';

        $sql = 'update {apbdop} set nama=\'%s\', kodeuk=\'%s\', kodesuk=\'%s\' where username=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($nama), strtoupper($kodeuk), strtoupper($kodesuk), $e_username));
    }
	if ($user) {
		//$perm = apbd_perm();
		//print_r($form_state['values']);
		//for ($i=0; $i<count($perm); $i++) {
		//	$tperm = rr($perm[$i]);
		//	$v = $form_state['values'][$tperm]];
		//	if ($v)  {
		//		
		//	}
		//	
		//}
	}
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/manageuser');    
}
function rr($t) {
	return str_replace(' ', '_', $t);
}
?>