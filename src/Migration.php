<?php
namespace App;
use App\Column;
/**
 * Class Migration
 * @author yourname
 */
class Migration
{
	const NAME = 'migration';
	protected $db;
	protected $isExists;

	protected $schema;
	public function __construct($db)
	{
		$this->db = $db;
		$this->isExists = $this->db->hasTable(static::NAME);
	}

	public function getSchema()
	{
		$table = new Table(static::NAME);
		$table->addColumn(new Column('id INT AUTO_INCREMENT PRIMARY_KEY'));
		$table->addColumn(new Column('name VARCHAR(100)'));
		$table->addColumn(new Column('table_name VARCHAR(100)'));
		$table->addColumn(new Column('payload TEXT'));
		$table->addColumn(new Column('hash VARCHAR(32)'));
		$table->addColumn(new Column('created_at DATETIME'));
		return $table;
	}

	public function isExists()
	{
		return $this->isExists;
	}

	public function findByName($name)
	{
		return $this->db->first(['name' => $name]);
	}

	public function create()
	{
		$schema = $this->getSchema();
		$this->db->createTable($schema);
	}

	public function add($data)
	{
		$this->db->insert(static::NAME, $data);
	}

	public function remove($id)
	{
		$this->db->delete(static::NAME, ['id' => $id]);
	}

	public function update($id, $data)
	{
		$this->db->update(static::NAME, ['id' => $id], $data);
	}
}

