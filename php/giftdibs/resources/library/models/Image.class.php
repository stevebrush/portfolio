<?php
class Image extends DatabaseObject {
	
	protected $tableName = "Image";
	
	protected $tableFields = array(
		"imageId",
		"userId",
		"name",
		"dateCreated",
		"timestamp"
	);
	
	protected $imageId,
		$userId,
		$name,
		$dateCreated,
		$timestamp;
	
	protected $file, // form input, or URL
		$fileSize, // bytes
		$maxFileSize = 1048576, // 1 MB
		$fileExtension; // .jpg, .gif, .png
		
	protected $sizes,
		$uploadUrl = UPLOAD_URL,
		$uploadPath = UPLOAD_PATH,
		$src, // web use, includes uploadUrl and file name
		$path, // server use, includes uploadPath and file name
		$width, 
		$height,
		$class;
		
	protected $dimensions = array(
		array("label"=>"original", "width"=>0, "height"=>0),
		array("label"=>"lg", "width"=>362, "height"=>362),
		array("label"=>"md", "width"=>150, "height"=>150),
		array("label"=>"sm", "width"=>74, "height"=>74),
	);
	
	protected $errors,
		$fillType = "outside",
		$isCreated = false,
		$wideImage;
	
	public function getErrors() {
		if (count($this->errors) > 0) {
			return join("<br>", $this->errors);
		} else {
			return "";
		}
	}
	
	public function size( $size = "original" ) {
		if (isset($this->sizes[ $size ])) {
			return $this->sizes[ $size ]; // lg, md, sm, original
		} else {
			if ($size == "original" && !isset($this->sizes["original"])) {
				return $this->sizes["lg"];
			}
			return $this->sizes["original"];
		}
	}
	
	public function find( $limit = null, $fields = array("*"), $offset = null, $suffix = "" ) {

		if ($img = parent::find($limit, $fields, $offset)) {
		
			$img->uploadUrl = $this->uploadUrl;
			$img->uploadPath = $this->uploadPath;
			$img->fetchFiles();
		
			// make sure image exists before sending...
			if (!@getimagesize( $img->size()->get("src") )) {
				$img->delete();
				return false;
			}
			
			return $img;
		}
		
		return false;
	}
	
	public function create() {
	
		$this->name = $this->generateName();
		$img = parent::create();
		$this->imageId = $img->imageId;
		
		if ($this->fileExists() && 
			$this->isValidFileSize() && 
			$this->isValidExtension() && 
			$this->load() && 
			$this->createFiles()) {
			$this->fetchFiles();
			$this->isCreated = true;
			return $this;
		}

		$this->delete();
		$this->isCreated = false;
		return $this;
		
	}
	
	public function delete() {
		$this->deleteFiles();
		parent::delete();
	}
	
	public function isCreated() {
		return $this->isCreated;
	}
	
	public function fetchFiles() {
		
		foreach ($this->dimensions as $d) {
			$ext = (!isset($this->extension)) ? ".jpg" : "." . $this->extension;
			$src = (!isset($this->src)) ? $this->uploadUrl.$this->name."-".$d['label'].$ext : $this->src;
			$path = (!isset($this->path)) ? $this->uploadPath.$this->name."-".$d['label'].$ext : $this->path;
			
			$width = $d["width"];
			$height = $d["height"];
			
			if (!$width && !$height) $width = $height = "auto";
			
			//$class = ($height < $width) ? "force-width" : "force-height";
			
			$img = new $this($this->db);
			$img->set(array(
				"userId" => $this->userId,
				"name" => $this->name,
				"src" => $src,
				"path" => $path,
				"width" => $width,
				"height" => $height
				//"class" => $class
			));
			
			$this->sizes[ $d["label"] ] = $img;
		}
		
		return $this;
		
	}
	
	private function generateName() {
		$date = time();
		return $this->userId.'-'.randomString().'-'.$date;
	}
	
	private function fileExists() {
		if (isset($this->file['tmp_name']) && file_exists($this->file['tmp_name']) && is_uploaded_file($this->file['tmp_name'])) {
			$this->fileSize = $this->file['size'];
			$type = $this->file['type'];
			$type = explode('/', $type);
			$this->fileExtension = $type[1];
			return true;
		} else if (gettype($this->file) == "string" && strpos($this->file, "facebook") !== false) {
			$this->fileSize = $this->getSizeFromUrl($this->file);
			$this->fileExtension = "jpg";
			return true;
		} else if (!empty($this->file) && gettype($this->file) == "string" && gettype(getimagesize($this->file)) == "array") {
			$this->fileSize = $this->getSizeFromUrl($this->file);
			$this->fileExtension = substr(strrchr($this->file, '.'), 1);
			return true;
		} else {
			$this->errors[] = "The file you attempted to upload does not exist or is not an uploaded file.";
			return false;
		}
	}
	
	private function isValidFileSize() {
		if ((int)$this->fileSize <= (int)$this->maxFileSize) {
			return true;
		} else {
			$this->errors[] = "The image you attempted to upload is larger than one megabyte (1024 kb). Please upload a smaller image.";
			return false;
		}
	}
	
	private function isValidExtension() {
		if ($this->fileExtension == "jpg" || 
			$this->fileExtension == "jpeg" || 
			$this->fileExtension == "png" || 
			$this->fileExtension == "gif") {
			return true;
		} else {
			$this->errors[] = "The image you attempted to upload is not a valid type.  Only JPG, GIF, or PNG are accepted. The file you uploaded had the extension: .{$this->fileExtension}";
			return false;
		}
	}
	
	private function getSizeFromUrl( $url = "" ) {
		$ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_HEADER, 1);
		 curl_setopt($ch, CURLOPT_NOBODY, 1);
		 $data = curl_exec($ch);
		 $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		 curl_close($ch);
		 return $size;
	}
	
	private function load() {
		if (gettype($this->file) == "array") {
			$fileName = $this->file['tmp_name'];
		} else {
			$fileName = $this->file;
		}
		if ($this->wideImage = WideImage::load($fileName)) {
			return true;
		} else {
			$this->errors[] = "The image is either the wrong type or corrupted.";
			return false;
		}
	}
	
	private function createFiles() {
	
		if (isEmpty($this->name)) {
			$this->name = $this->generateName();
		}
	
		$white = $this->wideImage->allocateColor(255, 255, 255);
	
		foreach ($this->dimensions as $size) {
		
			$label = $size["label"];
			$path = $this->uploadPath.$this->name."-".$label.".jpg";
			$width = $size["width"];
			$height = $size['height'];
			
			if (!$width || !$height) {
				$width = "100%";
				$height = "100%";
			}
			
			try {
			
				$resized = $this->wideImage->resize($width, $height, $this->fillType);
				$resized = $resized->resizeCanvas($width, $height, 'center', 'center', $white, 'up', true);
				$resized->saveToFile($path, 100);
				
			} catch (Exception $e) {
			
				$this->errors[] = $e;
				return false;
			}
		}
		return true;
	}
	
	private function deleteFiles() {
	
		if (!isset($this->sizes) || $this->uploadUrl == IMG_URL) return; // using default image, don't delete!
		
		foreach ($this->dimensions as $d) {
			$f = $this->sizes[ $d["label"] ]->path;
			if (@getimagesize( $f )) {
				unlink( $f );
			}
		}
	}
	
}
