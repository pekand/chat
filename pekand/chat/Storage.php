<?php

namespace pekand\Chat;

class Storage
{
	private $data = [];
	
	private $storagePath = null;
	
	public function __construct($storagePath, $subDir) {
           $this->storagePath = $storagePath . DIRECTORY_SEPARATOR . $subDir;
    }

    public function uid($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $bytes = openssl_random_pseudo_bytes($length);
        $string = "";

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[ord($bytes[$i]) % $charactersLength];
        }

        return $string;
    }

    public function isUid($length = 32) {
        return true;
    }
    
    public function get($name) {
    	
    	if(isset($this->data[$name])) {
    		return $this->data[$name];
    	}
    	    	
    	$item =  $this->open($name);
    	
    	$this->data[$name] = $item;

    	return $item;
    }
    
    public function set($name, $data) {
    	$data[$name] = $data;
    }
    
    public function setAndSave($name, $data) {
    	$this->data[$name] = $data;
    	
    	$this->save($name, $data);
    }

	public function openAll() {
		foreach (glob($this->storagePath.DIRECTORY_SEPARATOR."*.json") as $file) {
		  $name = pathinfo($file, PATHINFO_FILENAME);
		  $data[$name] = $this->open($name);
		}
	}
	
	public function saveAll() {
		foreach ($this->data as $name => $value) {
			$this->save($name, $value);
		}
	}

    public function open($name) {
    	
    	if(!file_exists($this->storagePath.DIRECTORY_SEPARATOR.$name.".json")){
    		return null;
    	}

    	return  json_decode(file_get_contents($this->storagePath.DIRECTORY_SEPARATOR.$name.".json"), true);
    }
    
    public function save($name, $data) {    	
    	return  file_put_contents($this->storagePath.DIRECTORY_SEPARATOR.$name.".json", json_encode($data));
    }
    
}
