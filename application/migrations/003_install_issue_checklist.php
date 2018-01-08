<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_issue_checklist	 extends CI_Migration {

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();
		
	}

	public function up() {
		// Drop table 'groups' if it exists
		$this->dbforge->drop_table('issue_checklist', TRUE);

		// Table structure for table 'groups'
		$this->dbforge->add_field(array(
			'id_check' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'auto_increment' => TRUE
			),
			'id_issue' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'null' => TRUE
			),
			'ditanggapi_id' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'constraint'     => '8',
				'null' => TRUE
			),
			'item_checklist' => array(
				'type'           => 'VARCHAR',
				'constraint'     => '125',
				'null' => TRUE
			),
			'deskripsi_checklist' => array(
				'type'      => 'TEXT',
				'null'		=>TRUE,
			),
			'berkas_checklist' => array(
				'type'      => 'TEXT',
				'null'		=>TRUE,
			),
			'status_checklist' => array(
				'type' => 'ENUM("true","false")',
				'default' => "false",
			),
			'waktu_dibuat' => array(
				'type' 		=> 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
				'null'		=> TRUE,
			),
			'waktu_update' => array(
				'type' 		=> 'DATETIME',
				'null'		=> TRUE,
			),
		));
		$this->dbforge->add_key('id_check', TRUE);
		$this->dbforge->create_table('issue_checklist');

	}

	public function down() {
		$this->dbforge->drop_table('issue_checklist', TRUE);
	}
}
