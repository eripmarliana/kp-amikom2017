<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller 
//CI_Controller 
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();
		
		$this->template->set_layout('bootstrap_navbar_layout');
	}

	public function index()
	{
		$this->template->set_layout('bootstrap_layout');
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/index', $data)->render();
	}

	public function agenda()
	{

		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/agenda', $data)->render();
	}

	public function beranda_arsip()
	{
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/beranda_arsip', $data)->render();
	}

	public function beranda_dikerjakan()
	{
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/beranda_dikerjakan', $data)->render();
	}

	public function beranda_pending()
	{
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/beranda_pending', $data)->render();
	}

	public function beranda_selesai()
	{
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/beranda_selesai', $data)->render();
	}

	public function register()
	{
		//$this->load->view('welcome_message');
		$data['dari_controller'] = 'hello world dari controller';
		$this->template->set_content('welcome/register', $data)->render();
	}

	public function issue(){
		$this->load->library('grocery_CRUD');

					$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('issue');
			$crud->set_subject('issue');
			
			$crud->set_relation('ditanggapi_id','users','username');
			$crud->display_as('ditanggapi_id','Petugas');
			$crud->set_relation('group_id','groups','name');
			//$crud->display_as('group_id','kepada');
			$crud->required_fields('judul_issue');
			
			$crud->columns('waktu_dibuat','status_issue','Petugas','judul_issue','group_id');
			$fields = array('judul_issue','group_id','berkas_issue','deskripsi_issue');
			$crud->fields($fields);
			$crud->set_field_upload('berkas_issue','assets/uploads/files');
			$output = $crud->render();
			$this->template->set_layout('example');

			$this->template->render($output);
	}

	public function groups(){
		$this->load->library('grocery_CRUD');
					$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('groups');
			$crud->set_subject('group');
			$crud->required_fields('name');
			$crud->columns('name','description');

			$output = $crud->render();
			$this->template->set_layout('example');

			$this->template->render($output);
	}

	public function issue_checklist(){
		$this->load->library('grocery_CRUD');
					$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('issue_checklist');
			$crud->set_subject('checklist');
			$crud->required_fields('id_issue');
			$crud->columns('ditanggapi_id','item_checklist','status_checklist','waktu_update');

			$crud->set_relation('ditanggapi_id','users','username');
			$crud->set_relation('id_issue','issue','judul_issue');
			$crud->set_field_upload('berkas_checklist','assets/uploads/files');
			$output = $crud->render();
			$this->template->set_layout('example');

			$this->template->render($output);
		}

		public function issue_review(){
		$this->load->library('grocery_CRUD');
					$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('issue_review');
			$crud->set_subject('review');
			$crud->required_fields('id_issue');
			$crud->columns('id_issue','pesan_review','nilai_review','waktu_dibuat');
			$output = $crud->render();
			$this->template->set_layout('example');

			$this->template->render($output);
		}
}
