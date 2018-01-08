<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_issue extends CI_Migration {

	public function __construct() {
		parent::__construct();
		$this->load->dbforge();
		
	}

	public function up() {
		// Drop table 'groups' if it exists
		$this->dbforge->drop_table('issue', TRUE);

		// Table structure for table 'groups'
		$this->dbforge->add_field(array(
			'id_issue' => array(
				'type'           => 'BIGINT',
				'constraint'     => '20',
				'auto_increment' => TRUE
			),
			'user_id' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'constraint'     => '8',
				'null' => TRUE
			),
			'group_id' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'constraint'     => '8',
				'null' => TRUE
			),
			'ditanggapi_id' => array(
				'type'           => 'MEDIUMINT',
				'unsigned'       => TRUE,
				'constraint'     => '8',
				'null' => TRUE,
			),
			'judul_issue' => array(
				'type'       => 'VARCHAR',
				'constraint' => '175',
				'null' => TRUE,
			),
			'deskripsi_issue' => array(
				'type'      => 'TEXT',
				'null'		=>TRUE,
			),
			'berkas_issue' => array(
				'type'      => 'TEXT',
				'null'		=>TRUE,
			),
			'status_issue' => array(
				'type' => 'ENUM("pending","proses","review","selesai")',
				'default' => "pending",
			),
			'terarsip' => array(
				'type' => 'ENUM("ya","tidak")',
				'default' => "tidak",
			),
			'waktu_dibuat' => array(
				'type' 		=> 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
				'null'		=> TRUE,
			),
			'waktu_ditanggapi' => array(
				'type' 		=> 'DATETIME',
				'null'		=> TRUE,
			)
		));
		$this->dbforge->add_key('id_issue', TRUE);
		$this->dbforge->create_table('issue');

	}

	public function down() {
		$this->dbforge->drop_table('issue', TRUE);
	}
}
