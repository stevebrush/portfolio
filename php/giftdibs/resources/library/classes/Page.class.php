<?php
class Page {
	
	private $app,
		$slug,
		$meta,
		$title,
		$template,
		$content,
		$announcements;
	
	public function __construct(Application $app) {
		$this->app = $app;
	}
	
	public function setSlug($val) {
		$this->slug = $val;
		return $this;
	}
	
	public function setMeta($vals = array()) {
		foreach ($vals as $k => $v) {
			$this->meta[$k] = $v;
		}
		return $this;
	}
	
	public function setTitle($val) {
		$this->title = $val;
		return $this;
	}
	
	public function setTemplate($type) {
		switch ($type) {
			case "form":
				$this->template = TEMPLATE_PATH . "form.template.php";
				break;
			case "profile":
				$this->template = TEMPLATE_PATH . "profile.template.php";
				break;
			case "wiki":
				$this->template = TEMPLATE_PATH . "wiki.template.php";
				break;
			case "home":
				$this->template = TEMPLATE_PATH . "home.template.php";
				break;
			case "main":
			default:
				$this->template = TEMPLATE_PATH . "main.template.php";
				break;
		}
		return $this;
	}
	
	public function setContent($path = "", $location = "primary") {
		if (!isset($this->content[$location])) {
			$this->content[$location] = array();
		}
		array_push($this->content[$location], $path);
		return $this;
	}
	
	public function setAnnouncement(Array $arr) {
		$this->announcement = $arr;
		return $this;
	}
	
	public function getSlug() {
		return $this->slug;
	}
	
	public function getMeta($key = "") {
		if ($this->meta[$key]) {
			return $this->meta[$key];
		}
		return false;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getContent($location = "main") {
		return (isset($this->content[$location])) ? $this->content[$location] : false;
	}
	
	public function getAnnouncements() {
		return $this->createAnnouncements();
	}
	
	public function printAnnouncements() {
		print $this->getAnnouncements();
	}

	public function hasAnnouncements() {
		return (count($this->announcements) > 0) ? true : false;
	}
	
	public function addAnnouncement(Array $arr) {
		$this->announcements[] = $arr;
		return $this;
	}
	
	public function rendering() {
		return $this->template;
	}
	
	private function createAnnouncements() {
		$length = count($this->announcements);
		if ($length > 0) {
			$html = "<div class=\"announcement\">";
			foreach ($this->announcements as $alert) {
				$class = "";
				if (isset($alert['type'])) {
					switch ($alert['type']) {
						case "success":
							$class = " alert-success";
							break;
						case "error":
						case "danger":
							$class = " alert-danger";
							break;
						case "info":
							$class = " alert-info";
							break;
						default:
							$class = " alert-warning";
							break;
					}
				} else {
					$class = " alert-warning";
				}
				$html .= "<div class=\"alert{$class}\">{$alert['html']}</div>";
			}
			$html .= "</div>";
			return $html;
		}
		return "";
	}
	
}