<?php
/**
* 
*/

class Glib
{
	/**
	 * [$ci description]
	 * @var [type]
	 */
	private $ci;
	/**
	 * [$links : menyimpan daftar tautan launcher / shortcut dihalaman beranda dashboard. 
	 * Memiliki 3 kunci yaitu :
	 * * ico => nama icon 
	 * @see http://fontawesome.com [icon-icon keren]
	 *  href => isi tautan shortcut
	 *  text => label/ judul shortcut
	 *  ]
	 * @var array
	 */
	public $links= array();

	/**
	 * [$layout : Nama file views yang digunakan sebagai template, menggunakan prefix ui_  ]
	 * @var string
	 */
	public $layout;
		/**
	 * [$args : menyimpan data yang akan ditampilkan pada view ]
	 * @var array
	 */
	public $args = array();

	public $custom = array();

	public $id_berkas;



	function __construct()
	{
		$this->ci = &get_instance();

	}

	protected function kolom_bootgrid(&$item, $key)
	{
		$item = str_replace(array('proyek_','_'), array('',' '), $item);
		$item = strtoupper($item);
	}

	public function do_migration($version =NULL){
    	$this->ci->load->library('migration');
    	if(isset($version) && ($this->ci->migration->version($version) === FALSE)){
      		$this->session->set_flashdata('message',$this->ci->migration->error_string());
      	}elseif(is_null($version) && $this->ci->migration->latest() === FALSE){
      		$this->session->set_flashdata('message',$this->ci->migration->error_string());
    	}
  	}

	public function set_path($model_name)
	{
		$pecah_path 	= explode('_', $model_name);
		$path 			= implode('/', $pecah_path);
		$path 			='unggahan/'.$path.'/';
		$upload_path 	= FCPATH.$path;
		if (!file_exists($upload_path)) {
    		mkdir($upload_path, 0777, true);
		}
		return $path;
	}

	public function set_args($data=array())
	{
		$this->args = array_merge($this->args,$data);
	}

	public function kolom_tabel($data=array())
	{
		$kunci = $data;
		if ($data) {
			array_walk_recursive($data, array($this,'kolom_bootgrid'));
			$data['fields'] = array_combine($kunci, $data);
			$this->set_args($data);
		}
	}

	public function rentang_per_minggu($ke=1)
	{
		$counter 			= 7 * $ke;
		$saturdaytimestamp 	= strtotime("last Saturday");
		$saturdaytimestamp 	= strtotime("+$counter days", $saturdaytimestamp);
		$mondaytimestamp 	= strtotime("-5 days",$saturdaytimestamp);
		$send[]				= date('Y-m-d',$mondaytimestamp);
		$send[]				= date('Y-m-d',$saturdaytimestamp);
		return $send;
	}

	

	/**
	 * [unggah_berkas description]
	 * @param  [type] $model_name [description]
	 * @param  string $path  [description]
	 * @return [type]        [description]
	 */
	public function unggah_berkas($model_name,$filename='')
	{
		$this->ci->load->model('berkas');
		$user_id 						= $this->ci->logged_in['user_id'];
		$last_id						= $this->ci->berkas->last_id();
		$new_file_name 					= $last_id.'_'.$user_id.'_'.time();
		
		if (!empty($filename)) {
			$new_file_name 				= $last_id.'_'.$filename.'_'.$user_id.'_'.time();
		}

		$path 							= $this->set_path($model_name);
		$config							= $this->ci->config->item('format');
		$upload_config					= $config['unggah'];
		$upload_config['upload_path'] 	= FCPATH.$path;
		$upload_config['upload_url']	= base_url($path);
		$upload_config['file_name']		= $new_file_name;
		$exiting_files 					= $this->periksa_berkas_pada($upload_config['upload_path']);
		
    	
		$this->ci->load->library('upload');	
    	$this->ci->upload->initialize($upload_config);

    	//$filename = $_FILES['berkas']['name'];
		//$ext = pathinfo($filename, PATHINFO_EXTENSION);
		
    	
    	$this->ci->upload->do_upload('berkas');
    	$error = $this->ci->upload->display_errors();
    	if(empty($error)){
        	$file_name				= $this->ci->upload->data('file_name');
			$data['nama_berkas'] 	= $file_name;
			$data['jenis_berkas']	= $model_name;
			$data['path_berkas'] 	= $this->ci->upload->data('full_path'); 
			$data['tautan_berkas'] 	= base_url($path.$file_name);
			$data['dibuat_oleh'] 	= $this->ci->logged_in['username'];
			$data['user_id']		= $this->ci->logged_in['user_id'];
			if ($id_berkas  = $this->ci->berkas->insert($data)) {
				return $id_berkas;
			}
        }else{

        	$this->ci->session->set_flashdata('message',$error);
        	return false;
        	
        }
	}

	public function periksa_berkas_pada($path)
	{
		$data = scandir($path);
		if ($data) {
			$unset_list 	= array('.','..','index.html');
			foreach ($data as $key => $value) {
				if (in_array($value, $unset_list)) {
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

	/**
	 * [tampilkan menampilkan halaman]
	 * @param  [type] $page [view yang digunakan sebagai content]
	 * @param  array  $data [variable dari controller ke view content]
	 * @return [type]       [string html]
	 */
	public function tampilkan($page)
	{

		$this->ci->load->library('alert');
		if ($this->custom) {
			$this->set_args($this->custom);
		}
		if ($this->ci->session->flashdata('message')) { //jika ada session message yang berisi pesan.
			$message 				= $this->ci->session->flashdata('message'); 
			$this->args['alert'] 	= $this->ci->alert->render('info',$message,'info');
		}
		$this->args['content'] = $this->ci->load->view($page,$this->args,true);
		$this->ci->load->vars($this->args);
		$this->ci->load->view($this->layout);
	}

	/**
	 * [all_methods menampilkan semua method pada class tertentu]
	 * @param  [string] $class_name [description]
	 * @return [array]             [larik nama methods class tertentu]
	 */
	public function ambil_semua_method($class_name)
	{
		$methods = get_class_methods($class_name);
		return $methods;
	}

	/**
	 * Membuat daftar tautan yang akan ditampilkan pada dashboard sesui groupname
	 * @param [type] $class_name [description]
	 * @see   [application/config/gl_system.php]
	 */
	public function set_menu_beranda($class_name)
	{
		$class_name 	= strtolower($class_name);
		$lists 			= get_class_methods ($class_name);
		foreach ($lists as $key => $value) {
			$prefix = substr($value, 0,7);
			
			if ($prefix == 'daftar_') {
				$config 			  = $this->ci->config->item('ico'); //ambil icon dari konfigurasi file
				$parts 				  = explode('_', $value); 
				$label 				  = $parts[1];
				$data['launcher'][] = array(
					'ico' => (isset($config[$label]))? $config[$label] : 'fa fa-info'
					,'href'=> $class_name.'/'.$value
					,'text'=> ucfirst($label)
				);
				$this->set_args($data);
			}
		}
	}

	public function aksi_multi_template($name,$options=array()){
		if ($options) {
			$data = array();
			foreach ($options as $key => $value) {
				$data[$value] = $this->ci->load->view('buttons/'.$name,null,true);
			}
			$this->set_args($data);
		}
	}

	public function semua_kolom_kecuali($table,$exclude=array())
	{
		$fields = $this->ci->db->list_fields($table);
		if ($exclude) {
			foreach ($exclude as $key => $value) {
				 if ($key = array_search($value, $fields)) {
				 	unset($fields[$key]);
				 }
			}
		}
		return $fields;
	}

	public function send_mail($to,$subject,$message)
	{
		$this->ci->load->helper('email');
		if (valid_email($to)) {
			$config 				 = $this->ci->config->item('smtp');
			$value_config 			 = array_values($config);
			$key_config 			 = array('smtp_host','smtp_user','smtp_pass','smtp_crypto','smtp_port');
			$mail_config 			 = array_combine($key_config, $value_config);
			$mail_config['mailtype'] = 'html';
			$mail_config['charset'] = 'utf-8';
			$mail_config['wordwrap'] = TRUE;
			$this->ci->load->library('email',$mail_config);
			$this->ci->email->set_mailtype("html");
			$this->ci->email->from($mail_config['smtp_user'], 'Admin Glite');
			$this->ci->email->to($to);
			$this->ci->email->subject($subject);
			$this->ci->email->message($message);
			return $this->ci->email->send();
			//$this->ci->email->print_debugger();
		}	
	}

	public function go_back($method='index')
  	{
  		if (isset($_SERVER['HTTP_REFERER'])) {
  			header('Location: ' . $_SERVER['HTTP_REFERER']);
  			exit;
  		}else{
  			$this->go_to($method);
  		}
  	}

  	public function go_to($method='index')
  	{
  		$controller = $this->router->fetch_class();
  		redirect($controller.'/'.$method,'refresh');
  	}
	
}