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
}
