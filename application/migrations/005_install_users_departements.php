<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_users_departements	 extends CI_Migration {

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();
		
	}

	public function up() {
		// Drop table 'groups' if it exists
		$this->dbforge->drop_table('users_departements', TRUE);

		// Table structure for table 'groups'
		$this->dbforge->add_field(array(
			'id_dept' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'auto_increment' => TRUE
			),
			'group_id' => array(
				'type'           => 'MEDIUMINT',
				'constraint'     => '8',
				'unsigned'       => TRUE,
				'null' => TRUE
			),
			'nama_departement' => array(
				'type'           => 'VARCHAR',
				'constraint'     => '175',
				'null' => TRUE
			),
			'deskripsi_departement' => array(
				'type'           => 'VARCHAR',
				'constraint'     => '225',
				'null' => TRUE
			),
			'waktu_dibuat' => array(
				'type' 		=> 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
				'null'		=> TRUE,
			)
		));
		$this->dbforge->add_key('id_dept', TRUE);
		$this->dbforge->create_table('users_departements');

	}

	public function down() {
		$this->dbforge->drop_table('users_departements', TRUE);
	}
}
