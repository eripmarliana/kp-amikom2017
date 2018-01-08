<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_issue_review	 extends CI_Migration {

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();
		
	}

	public function up() {
		// Drop table 'groups' if it exists
		$this->dbforge->drop_table('issue_review', TRUE);

		// Table structure for table 'groups'
		$this->dbforge->add_field(array(
			'id_review' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'auto_increment' => TRUE
			),
			'id_issue' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'null' => TRUE
			),
			'pesan_review' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'constraint'     => '8',
				'null' => TRUE
			),
			'nilai_review' => array(
				'type' => 'DECIMAL',
				'constraint' => '50,2',
				'null' => TRUE
			),
			'waktu_dibuat' => array(
				'type' 		=> 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
				'null'		=> TRUE,
			)
		));
		$this->dbforge->add_key('id_review', TRUE);
		$this->dbforge->create_table('issue_review');

	}

	public function down() {
		$this->dbforge->drop_table('issue_review', TRUE);
	}
}
