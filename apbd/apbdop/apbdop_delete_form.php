<?php

function apbdop_delete_form() {
    //drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
     
    $username = arg(2);
	
    if (isset($username)) {


		
	 
		//drupal_set_message('x');		
		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Konfirmasi Penghapusan Operator',
			'#collapsible' => TRUE,
			'#collapsed' => FALSE,        
		);
		
		$query = db_select('apbdop', 'o');
		$query->innerJoin('users', 'u', 'o.username=u.name');
		$query->fields('u', array('uid'));
		$query->condition('o.username', $username, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$uid = $data->uid;
		}	
		$form['formdata']['username'] = array('#type' => 'value', '#value' => $username);
		$form['formdata']['uid'] = array('#type' => 'value', '#value' => $uid);
		$form['formdata']['keterangan'] = array (
					//'#type' => 'markup',
					'#markup' => 'Mengahpus operator ' . $username,
					);
		
		//FORM NAVIGATION	
		$current_url = url(current_path(), array('absolute' => TRUE));
		$referer = $_SERVER['HTTP_REFERER'];
		if ($current_url != $referer)
			$_SESSION["apbdoplastpage"] = $referer;
		else
			$referer = $_SESSION["apbdoplastpage"];

		return confirm_form($form,
							'Anda yakin menghapus data operator ' . $username ,
							$referer,
							'PERHATIAN : Data yang dihapus tidak bisa dikembalikan lagi.',
							//'<button type="button" class="btn btn-danger">Hapus</button>',
							//'<em class="btn btn-danger">Hapus</em>',
							//'<input class="btn btn-danger" type="button" value="Hapus">',
							'Hapus',
							'Batal');
    }
}
function apbdop_delete_form_validate($form, &$form_state) {
}
function apbdop_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $username = $form_state['values']['username'];
		$uid = $form_state['values']['uid'];
		
		//user_error
		user_delete($uid);
		
		//delete apbdop
		$num = db_delete('apbdop')
		  ->condition('username', $username)
		  ->execute();
		  
        if ($num) {
			
			//drupal_set_message($username);
            drupal_set_message('Penghapusan berhasil dilakukan');
			
			$referer = $_SESSION["apbdoplastpage"];
            drupal_goto($referer);
        }
        
    }
}
?>