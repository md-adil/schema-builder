<?php
namespace App;

class Schema {

	protected $file;
	protected $index;
	protected $filename;
	protected $hash;

	public function __construct($file)
	{
		$this->file = $file;
		$info = pathinfo($file);
		$this->parseFileName($info);
		$this->setHash();
	}

	public function parseFileName($info)
	{
	    $parts = explode('.', $info['filename']);
	    switch (count($parts)) {
	    	case 1:
	    		$this->filename = $parts[0];
	    		break;
	    	case 2:
	    		$this->filename = $parts[1];
	    		$this->index = $parts[0];
	    		break;
	    	case 2:
	    		$this->filename = $parts[1];
	    		$this->index = $parts[0];
	    		break;
	    	default:
	    		break;
	    }
	}

	public function setHash()
	{
	    $this->hash = md5_file($this->file);
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function getFileName()
	{
		return $this->filename;
	}

	public function getIndex()
	{
		return $this->index;
	}

	public function getContent()
	{
		return file_get_contents($this->file);
	}

	public function getLines()
	{
		return explode(PHP_EOL, $this->getContents());
	}

	public static function find(string $path)
	{
		$files = [];
		foreach(glob($path) as $file) {
			$schema = new Static($file);
			$files[$schema->getIndex()] = $schema;
		}
		return $files;
	} 
}

