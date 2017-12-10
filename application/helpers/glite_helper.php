<?php


function tautan_berkas($id,$kolom='tautan_berkas',$output=false)
{
	$ci =&get_instance();
	$ci->load->model('berkas');
	$link	= '';
	$data 	= $ci->berkas->get($id);
	if (!is_null($data)) {
		$link ='<a href="'.$data[$kolom].'" target="blank">'.$data[$kolom].'</a>';
	}
	if ($output) {
		return $data[$kolom];
	}else{
		echo $link;
	}
}





function get_audit_value($id_post,$id_seeker)
{
	$ci =&get_instance();
	$where = array(
		'id_post' =>$id_post,
		'id_seeker'=>$id_seeker
	);
	$query = $ci->db->get_where('post_audit',$where);
	if ($query->num_rows() > 0) {
		$data = $query->row();
		if (isset($data->penilaian)) {
			return $data->penilaian;
		}
	}
	return 0;
}


	function set_datatables_column($field='')
	{
    	if (!empty($field)) {
        	$field = str_replace(array('proyek_','quotation_','_'), array('','',' '), $field);
        	$field = strtoupper($field);
        	return $field;
    	}
	}

	function set_datatables_action($action=array(),$_id=0,$value=array())
	{
		$color = array('default','primary','warning','info');
		
		if (isset($action) && $action) {
		 				
		 				$button_action 	= '<div class="btn-group btn-group-xs" role="group" >';
		 				$x 				= 0;

		 				foreach ($action as $label => $url) {

		 					$btn_color  = 'btn btn-';

		 					if (isset($color[$x])) {
		 						$btn_color.=$color[$x];
		 					}else{
		 						$btn_color.=$color[0];
		 					}

		 					if (is_array($url)) {
		 						if (isset($value[$label])) {
		 							$value_condition = $value[$label];
		 							if (isset($url[$value_condition])) {
		 								if (is_array($url[$value_condition])) {
		 									$collection = $url[$value_condition];
		 									foreach ($collection as $index => $data) {
		 										$button_action .= anchor(
		 												$data.$_id
		 												,ucfirst($index)
		 												,array(
		 													'class'=>$btn_color
		 												)
		 											);
		 									}
		 								}
		 							}
		 						}
		 						
		 					}elseif ($label == 'hapus' || $label =='delete') {
		 						$button_action .= anchor(
		 							$url.$_id
		 							,ucfirst($label)
		 							,array('class'=>'btn btn-danger btn-confirm-link')
		 						);
		 					}else{
		 						$button_action .= anchor(
		 							$url.$_id
		 							,ucfirst($label)
		 							,array('class'=>$btn_color)
		 						);
		 					}
		 					$x++;
		 				}

		 				if (isset($value['url_post'])) {
		 					$button_action .= anchor($value['url_post'],'pratinjau ',array(
		 						'target'=>'_blank',
		 						'class'=>'btn btn-xs btn-primary'
		 					));
		 				}
		 				$button_action .='</div>';
		 				echo $button_action;
		}
	}


	function get_controllers_names()
	{
		$files =  get_files_in(APPPATH.'controllers');
		$names = array();
		if ($files) {
			foreach ($files as $key => $name) {
				$names[] = removeFromEnd($name,'.php');
			}
		}
		return $names;
	}

	function get_data_type($model_name='',$column='')
	{
		$ci =&get_instance();
		$models = get_files_in(APPPATH.'models');
		if (in_array($model_name.'.php', $models)) {
			$ci->load->model($model_name);
			if ($data = $ci->$model_name->get_type_data($column)) {
				if (isset($data[0]) && isset($data[0]['DATA_TYPE'])) {
					$type = $data[0]['DATA_TYPE'];
					if ($type == 'enum') {
				return $ci->$model_name->get_enum_values($column);
					}
					return $type;
				}
			}
		}		
	}

	function render_top_navigation($last_uri='',$data=array()){
		$ci =&get_instance();
		$me = $ci->router->fetch_class();
		if (isset($data) && $last_uri ) {
			if ($data) {
				$nav     ='';
				foreach ($data as $key => $value) {
					$explode  = explode('/', $value);
					$method   = $explode[0];
					$controller = $me.'/'.$method;
					if (isset($explode[1])) {
                        $method   = $explode[1];
                        $controller = $explode[0].'/'.$method;

					}
					$label = str_replace('_', ' ', $method);
					$label = ucwords($label);
					$nav   .= '<li > ';
					if ($last_uri == $method) {
                          $nav   .= '<li class="active"> ';
					}
					$nav .= anchor($controller,$label,array('class'=>''));
					$nav .= '</li> ';
                }
                echo $nav;
			}
		}
	}

	function render_label_html($data=array(),$labels=array())
	{
		if ($data) {
			$word = '';
			
			foreach ($data as $key => $value) {
				foreach ($labels as $index => $show) {
					
					if ($key == $show) {
						
						$word .='<h4><small>'.set_datatables_column($show).'</small><br/>';
						if (is_numeric($value)) {
							$word .=show_currency_format($value);
						}else{
							$word .= ucwords($value);
						}
						$word .='</h4>';

					}
				}

			}
			echo $word;
		}
	}

	function get_username($user_id='')
	{
		$ci =&get_instance();
		$ci->load->model('users');
		
		if ($user = $ci->users->get($user_id)) {
			if (isset($user['username'])) {
				return $user['username'];
			}
		}
	}

	function get_symbol_currency($id_currency='')
	{
		$ci =&get_instance();
		$ci->load->model('currency');
		$currency = $ci->currency->get($id_currency);
		if ($currency && isset($currency['symbol'])) {
			return $currency['symbol'];
		}
	}

	function get_details_client($id_client='')
	{
		$ci =&get_instance();
		$ci->load->model('clients');
		$clients = $ci->clients->get($id_client);
		return $clients;
	}

	function get_details_produk($id_produk='')
	{
		$ci =&get_instance();
		$ci->load->model('produk');
		$produk = $ci->produk->get($id_produk);
		return $produk;
	}

	/**
 	* Google shorten link
 	*/

	function assert_equals($is, $should) {
  		if($is != $should) {
    		exit(1);
  		} else {
    	return false;
  		}
	}
	function assert_url($is) {
  		if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $is)) {
    		exit(1);
  	} else {
    	return false;
  	}
}
