<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Storage;
//Class for handling JSON file data
//Implemented by Md. Asimuzzaman

class myJSON {
	private $fileName, $JSON;

	function __construct($filename) {
		$this->fileName = $filename;

		$path = base_path('storage/app/').$this->fileName;

		if (!file_exists($path)) {
    		// file does not exist, create one
    		$temp['images'] = [];
    		Storage::put($this->fileName, json_encode($temp));
		}

		$this->JSON = json_decode(Storage::get($this->fileName), true);
	}

	//takes array as param,converts to JSON and saves to file
	private function save($data) {
		Storage::put($this->fileName, json_encode($data));
	}

	//returns all entries from file
	function all() {
		return collect($this->JSON['images']);
	}

	//searches for relevant item, returns Laravel Collection
	function search($keyword) {
		if($keyword == '') //no keywrod in the search field should return all
			return collect($this->JSON['images']);

		$result = new \Illuminate\Support\Collection;
		$keyword = strtolower($keyword); //for case insensitive searching

		foreach($this->JSON['images'] as $image) {
			if(strpos(strtolower($image['title']), $keyword) !== false) { //'keyword' substring exists, case insensitive
				$result->push($image);
			}
		}

		return $result;
	}

	function find($id) {
		return $this->JSON['images'][$id];
	}

	function delete($id) {
	    unset($this->JSON['images'][$id]);

	    Storage::put($this->fileName, json_encode($this->JSON));
	}

	//inserts new data, returns latest insert ID
	function insert($title, $address) {    
	    if(count($this->JSON['images']) == 0) //empty array
	        $new_key = 1;
	    else
	        $new_key = max(array_keys($this->JSON['images'])) + 1;
	    
	    $this->JSON['images'][$new_key] = ['id' => $new_key, 'title' => $title, 'address' => $address, 'created_at' => time()];
	    
	    Storage::put($this->fileName, json_encode($this->JSON));

	    return $new_key;
	}

}

?>