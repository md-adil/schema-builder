<?php
/**
* 
*/
class Constraint
{
	protected $name;
	protected $type;
	protected $fields;
	protected $comment;

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}

	public function setComment(string $comment)
	{
		$this->comment = $comment;
	}

	public function setRefrenceTable()
	{

	}
	public function setRefrenceField()
	{

	}
}

