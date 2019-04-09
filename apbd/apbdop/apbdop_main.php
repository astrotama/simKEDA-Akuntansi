<?php
function apbdop_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
	$qlike='';
	$limit = 20;
	
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Username',  'field'=> 'username', 'valign'=>'top'), 
		array('data' => 'Nama', 'field'=> 'nama', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Hak Akses', 'field'=> 'rid', 'valign'=>'top'),
		array('data' => 'Akses Terakhir', 'field'=> 'access', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
		
		$query = db_select('apbdop', 'u')->extend('PagerDefault')->extend('TableSort');
		$query->leftJoin('unitkerja', 'uk', 'u.kodeuk=uk.kodeuk');
		$query->innerJoin('users', 's', 'u.username=s.name');
		$query->innerJoin('users_roles', 'ur', 's.uid=ur.uid');
		
		# get the desired fields from the database
		$query->fields('u', array('username','nama'));
		$query->fields('uk', array('namasingkat'));
		$query->fields('s', array('access'));
		$query->fields('ur', array('rid'));
		
		if (!isSuperuser()) {
			$kodeuk = apbd_getuseruk();
			$query->condition('uk.kodeuk', $kodeuk, '=');	
			

		}
		
		$query->orderByHeader($header);
		$query->orderBy('u.username', 'ASC');
		$query->limit($limit);	
		
			
		# execute the query
		$results = $query->execute();
			
		# build the table fields
		$no=0;

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
			$no = $page * $limit;
		} else {
			$no = 0;
		} 

			
		$rows = array();
		foreach ($results as $data) {
			$no++;  
			
			//$keterangan = l($data->jumlahrekening . ' Rekening <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>' , 'pendapatanrek/filter/' . $bulan . '/' . $data->kodeskpd . '/' . $kodek . '/5' , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));
			
			$username = l($data->username, 'operator/edit/' . $data->username , array ('html' => true));
			
			if ($data->access==0)
				$access = 'Belum Pernah';
			else
				$access = gmdate("d M Y", $data->access);
			
			$editlink =  apbd_button_hapus('operator/delete/' . $data->username);
			
			
			if ($data->rid=='3')
				$ha = 'Administrator';
			else if ($data->rid=='4')	
				$ha = 'Superuser';
			else if ($data->rid=='5')	
				$ha = 'SKPD';
			else if ($data->rid=='6')	
				$ha = 'Bidang';
			else
				$ha = '';
			
			$rows[] = array(
							array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
							array('data' => $username, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->nama, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => $ha, 'align' => 'left', 'valign'=>'top'),
							array('data' => $access, 'align' => 'left', 'valign'=>'top'),
							array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
						);
		}

		//BUTTON
		$btn = apbd_button_baru('operator/edit');
		
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');

		//return drupal_render($output_form) . $btn . $output . $btn;
		return $btn . $output . $btn;
}

function apbdop_main_form_submit($form, &$form_state) {

}

function apbdop_main_form($form, &$form_state) {

}

?>
