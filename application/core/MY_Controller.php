<?php
/**
* 
*/
class MY_Controller extends CI_Controller
{

	/**
	 * [$logged_in : menyimpan session logged_in agar lebih singkat untuk dipanggil lagi pada controller turunan]
	 * @var array
	 */
	public $logged_in;

	/**
	 * [__construct inisialisasi varible dan memanggil library yang akan dipakai disemua controller turunannya]
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->load->library(array('glib','template'));
		//run migration
		$this->glib->do_migration();

		
    	$this->load->helper(array('glite','tools'));
		
		$this->lang->load(array('ion_auth_lang','auth_lang'));
		/**
		 * jika pengguna belum masuk maka default template adalah ui_medium.
		 */
		if (is_null($this->session->userdata('user_id'))) {
			$this->logged_in = false;
			$this->glib->layout ='ui_mendium';
		}else{
			$this->logged_in = $this->session->userdata();
			// set default variable view yang menampilkan informasi akun yang sedang aktif.
			$this->set_addtional_logged_in(); 
			$this->set_extra_views();
			$this->glib->layout = 'ui_dashboard'; // default template untuk menampung view content.
		}
		
		
	}

	public function set_extra_views()
	{
		$controller_name 	= $this->router->fetch_class();
		$controller_action	= $this->router->fetch_method();
		$data 				= array(
			'my' 		=> $this->logged_in,
			'title' 	=> $controller_name.' | '.$controller_action ,
			//'brand' 	=> $this->config->item('brand') ,
			'my_model' 	=> $this->last_segment_uri() ,
			'current' 	=> $this->router->fetch_method()
			//'rest_url'	=> $this->config->item('base_url')//$this->config->item('rest_url') // set default url untuk pemanggilan rest API.
		);
		$this->glib->set_args($data);
	}
	/**
	 * [$args description]
	 * @var array
	 */
	public $args= array();
	/**
	 * [index description]
	 * @param  boolean $id [description]
	 * @return [type]      [description]
	 */
	public function index()
	{
		$this->glib->layout = 'ui_launcher';
		$this->glib->tampilkan('pages/home');
	}
	/**
	 * [set_addtional_logged_in : 
	 * penambahan index baru, untuk melengkapi informasi sesi pengguna yang masuk.
	 * ]
	 */
	private function set_addtional_logged_in(){
		$this->load->model(array('ion_auth_model','users'));
		$user_id 			  	= $this->logged_in['user_id'];
		$group					= $this->ion_auth_model->get_users_groups($user_id)->row();
		$account 			  	= $this->users->get($user_id);
		$this->logged_in['is']	= $group->name;
		$this->logged_in 		= array_merge($this->logged_in,$account);
	}

	
	/**
	 * [last_segment_uri mengambil segment ]
	 * @return [type] [description]
	 */
	public function last_segment_uri()
	{
		$list_uri 		= $this->uri->segment_array();
		$last_uri 		= end($list_uri); // mengambil segment terakhir.
		$parts 			= explode('_', $last_uri); //memecah segment dengan underscore.
		return (isset($parts[1]))? $parts[1] : false; //jika index ke-1 ada maka kirim element index ke-1
	}
	/**
	 * [only_grup melindungi class dari pengguna yang tidak memiliki hak masuk]
	 * pengembalian 
	 * @param  [type] $name [nama class]
	 * 
	 */

	public function only_grup($name)
	{

		$name 		= strtolower($name); // mengubah nama class menjadi huruf kecil
		$current 	= strtolower($this->logged_in['is']); // class yang sesuai dengan is akun aktif.
		if ($current !== $name) {
			redirect($current,'refresh');
		}
	}

	/**
	 * [privasi menampilkan halaman profile akun yang sedang aktif]
	 * @return [type] [description]
	 */
	public function privasi()
	{
		$this->load->model(array('users','ion_auth_model')); //memanggil dua model 
		//ambil user_id dari session akun yang sedang aktif.
		$user_id 					= $this->logged_in['user_id']; 
		//ambil informasi pengguna dari session user_id
		$info 						= $this->users->get($user_id); 
		$info['is']					= ucwords($this->logged_in['is']);
		$this->glib->args['info'] 	= $info;
		$this->glib->layout 		='ui_form';
		$this->glib->tampilkan('pages/detail_pribadi');
	}	

	public function update_privasi($id='')
	{
		if ($id == $this->logged_in['user_id'] && !is_null($this->logged_in['user_id'])) {
			$this->load->model('users');
			if (!is_null($this->input->post(NULL,true))) {
				$data = $this->input->post(NULL,true);
				$user_id = $this->logged_in['user_id'];
				if (!$this->users->update($user_id,$data)) {
					$this->session->set_flashdata('message','update profile gagal :(');
				}
			}
			redirect($this->logged_in['is'].'/privasi','refresh');
		}elseif (isset($this->logged_in['is'])) {
			redirect($this->logged_in['is'],'refresh');
		}else{
			redirect('auth/index','refresh');
		}
	}
	/**
	 * [profile menampilkan halaman profile akun yang lain]
	 * @param  [integer] $id [id dari tabel users]
	 * @return [type]     [description]
	 */
	public function profile($id)
	{
		$this->load->model(array('users','ion_auth_model')); //memanggil dua model 
		$this->glib->layout ='ui_form'; // gunakan layout ui_form
		$group 				= $this->ion_auth_model->get_users_groups($id)->row(); //ambil group
		if ($group) {
			$info 						= $this->users->get($id); // ambil detail akun yang sedang aktif
			$info['is']			= ucwords($group->name);
			$this->glib->args['info'] 	= $info;
			$this->glib->tampilkan('pages/detail_user');
		}else{
			$this->glib->tampilkan('pages/404');
		}
	}

	/**
	 * [reset_password mengubah password melalui detail data pribadi akun yang sedang aktif]
	 * @return [type] [description]
	 */
	public function reset_password()
	{
		
		if ($this->form_validation->run('reset_password')) {
			$this->load->library('ion_auth'); //memanggil libraray ion_auth /application/libraries/Ion_auth.php
			$this->load->model('ion_auth_model');//memangil model ion_auth_model
			$identity 	= $this->logged_in['email'];
			$password 	= $this->input->post('password');
			$change  	= $this->ion_auth->reset_password($identity, $password);
			if ($change) {
				$this->session->set_flashdata('message', $this->ion_auth->messages());
			}
			$this->logout();
		}else{
			$this->session->set_flashdata('message', validation_errors());
		}
		$redirect = $this->logged_in['is'].'/privasi';
		redirect($redirect,'refresh');
	}

	/**
	 * [logout mengakhiri session userdata]
	 * @return [type] [description]
	 */
	public function logout()
	{
		$this->load->library(array('ion_auth','notif'));
		if ($this->ion_auth->logout()) {
			//session_destroy();
			if (function_exists('clear_notifikasi_ini')){
				$notif = $this->notif->notifikasi;
				if (isset($notif['to'])) {
					if ($this->logged_in['is'] == $notif['to']) {
						clear_notifikasi_ini();
					}
				}
			}
			//$this->session->sess_destroy();
			$this->logged_in = NULL;
			redirect('auth','refresh');
		}
	}

	public function json_notification()
	{
		$this->load->library('notif');
		$data = $this->notif->notifikasi;
		echo json_encode($data);
	}

	public function notification_clicked()
	{
		$this->form_validation->set_rules('from','dari ','required|trim');
		if ($this->form_validation->run()) {
			if (function_exists('clear_notifikasi_ini')){
				clear_notifikasi_ini();
				//$this->session->unset_userdata('notif');
			}
		}
	}

	public function kirim_proyek()
	{
		$controller 			= $this->router->fetch_class();
		if ($this->form_validation->run('kirim_proyek')) {
			$this->load->model('proyek');
			$data 					= $this->input->post(NULL,true);
			
			$data['proyek_dari']	= $this->logged_in['user_id'];
			
			if ($_FILES['berkas']['size'] > 0 && $_FILES['berkas']['error'] == 0){
				$data['id_berkas']	= $this->glib->unggah_berkas('proyek','proyek/multimedia');	
			}

			if (isset($data['judul_proyek'])) {
				$trim = trim($data['judul_proyek']);
				if (empty($trim) && strlen($trim) == 0) {
					$data['judul_proyek'] = $data['kategori_proyek'];
				}
			}
			
			if (isset($data['id_produk'])) {
				if ($data['id_produk'] > 0) {
					$this->load->model('produk');
					$produk = $this->produk->get($data['id_produk']);
					$data['proyek_produk_id']= $data['id_produk'];
				}				
				$data['judul_proyek'] .=' '.$produk['deskripsi_produk'];
				unset($data['id_produk']);
			}


			$data['status_permintaan']='pending';
			if (!$this->proyek->insert($data)) {
				$this->session->set_flashdata('message','gagal menyimpan proyek');
			}
			redirect($controller.'/daftar_proyek','refresh');
		}else{
			$this->session->set_flashdata('message',validation_errors());
			$this->form_proyek();
		}
		
		//$this->daftar_proyek();
	}

	public function paksa_unduh($id_berkas=false)
	{
		$controller = $this->router->fetch_class();
		$this->load->model('berkas');
		if ($id_berkas && is_numeric($id_berkas)) {
			if ($berkas = $this->berkas->get($id_berkas)) {
				if (isset($berkas['tautan_berkas'])) {

					if (strpos($berkas['tautan_berkas'], 'unggahan')) {
						push_to_download($berkas['path_berkas']);		
					}else{
						echo "<script>alert('tautan berkas bukan dari server');</script>";
					}	
				}
			}
		}
		echo "<script>window.history.back();</script>";
	}
	
	public function detail_berkas($id)
	{
		if ($id && is_numeric($id)) {
			$this->glib->layout ='ui_form';
			$this->load->model(array('berkas','proyek'));	
			$this->glib->custom = array(
				'info' => $this->berkas->get($id),
				'proyek'=> $this->proyek->get_by_file($id)
			);
			$this->glib->tampilkan('pages/detail_berkas');
		}else{
			$this->glib->tampilkan('pages/404');
		}
	}

	public function detail_proyek($id=false)
	{
		if ($id && is_numeric($id)) {

			$this->load->model(array('proyek','berkas'));	
			$info 		= $this->proyek->get($id);
			$this->glib->custom = array(
				'info' => $info,
				'berkas_pemohon' => $this->berkas->get($info['id_berkas']) ,
				'berkas_hasil'=> $this->berkas->get($info['id_berkas_hasil'])
			);
			$this->glib->layout ='ui_form';
			$this->glib->tampilkan('pages/detail_proyek');
		}else{
			$this->glib->tampilkan('pages/404');
		}
	}
	
	public function daftar_proyek()
	{
		
		$fields 		= array('id_proyek','proyek_ditanggapi_oleh','status_permintaan','proyek_jatuh_tempo','judul_proyek','kategori_proyek');
		$this->glib->kolom_tabel($fields);
		$this->glib->layout = 'ui_gridlist';
		$this->glib->custom = array(
			'grid_source' => 'grid/list_proyek/from_user/'.$this->logged_in['user_id']
			,'add' 		  => array('text' => 'Buat proyek', 'url'=>'form_proyek')
		);
		$options = array('action_success','action_pending','action_process','action_default');
		$this->glib->aksi_multi_template('opsi_proyek',$options);
		$this->glib->tampilkan('pages/grid_jquery');
	}

	public function form_proyek()
	{
		$this->load->model('proyek');
		$this->glib->layout 					= 'ui_form';
		$this->glib->custom['project_category'] = $this->proyek->get_project_category();
		$this->glib->tampilkan('form/request_project');
	}

	public function tutup_proyek($id=false)
	{
		if ($id && is_numeric($id) && $this->logged_in['is'] !== 'multimedia') {
			$this->load->model('proyek');
			if ($info = $this->proyek->get($id)) {
				if (isset($info['status_permintaan']) && $info['status_permintaan'] == 'selesai') {
					if (!$this->proyek->update($id,array('status_permintaan'=>'tutup'))) {
						$this->session->set_flashdata('message','gagal menyetujui hasil proyek');
					}
				}
			}
			$this->daftar_proyek();
		}else{
			$this->glib->tampilkan('pages/404');
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