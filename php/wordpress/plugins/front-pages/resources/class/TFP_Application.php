<?php
class TFP_Application {

	private static $instance;
	private static $config;

	private $currentUrl;
	private $iTodaysRemainingSeconds;
	private $dateFormat = "l, F d, Y";

	private $aDailyStatus = array ();
	private $aDailyStatus_cached = array ();
	private $aTopTen = array ();
	private $bUpdateCache = false;
	private $iDayNumber = 0;
	private $sRefreshedDate = "";

	public function __construct ($config = array ()) {

		$this->currentUrl = "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		self::$instance =& $this;
		self::$config = $config;
		$this->iTodaysRemainingSeconds = strtotime('tomorrow') - time();

		$this->aDailyStatus = $this->fetchFeed($this->config("feed", "daily-status"));
		$this->aDailyStatus = $this->aDailyStatus[0];

		$this->aTopTen = $this->fetchFeed($this->config("feed", "top-ten"));

		$this->aDailyStatus_cached = $this->fetchCache("daily-status");
		$this->aDailyStatus_cached = $this->aDailyStatus_cached[0];

		$this->bUpdateCache = $this->checkCacheDates();

		$this->sRefreshedDate = $this->aDailyStatus["refreshedDate"];
		$this->iDayNumber = $this->aDailyStatus["dayNumber"];
	}

	public function checkCacheDates () {

		/*
		Top 10 and Daily Status should never be cached.
		The Daily Papers should be cached when:
			the daily status cache refreshedDate doesn't equal the feed's refreshed Date
		*/

		if ($this->aDailyStatus ["refreshedDate"] == $this->aDailyStatus_cached ["refreshedDate"]) {
			return false;
		} else {
			return true;
		}
	}

	private function checkTopTen ($arr = array ()) {

		$showTopTen = false;

		if (isset ($this->aTopTen ["top10summary"]) && isset ($this->aTopTen ["top10summary"] [0])) {

			$date  		 = strtotime ($this->aTopTen ["top10summary"] [0] ["top10DateCreated"]);
			$now_year 	 = date ("Y");
			$now_day     = date ("j");
			$topten_year = date ("Y", $date);
			$topten_day  = date ("j", $date);

			if ($now_year === $topten_year && $now_day === $topten_day) {
				$showTopTen = true;
			}
		}

		$arr ["showTopTen"] = $showTopTen;

		return $arr;
	}

	public function config ($key, $value) {
		if (isset(self::$config[$key])) {
			$value = self::$config[$key][$value];
		} else {
			$value = false;
		}
		return $value;
	}

	private function exposeData ($arr) {
		$temp = array ();
		foreach ($arr as $k => $v) {
			$temp [$k] = $v;
		}
		$src = $this->config("url", "js") . "front-pages.js";
		echo "<script>var TFP_DATA = " . json_encode ($temp) . ";</script>";
		echo '<!-- BBI NAMESPACE -->
		<script>
		(function(a,c,d){if(!a.getElementById(c)){
		var b=a.createElement("script");b.src=d;b.id=c;
		a.getElementsByTagName("head")[0].appendChild(b)
		}})(document,"bbi-namespace","//api.blackbaud.com/bbi");
		</script>

		<!-- CUSTOM SCRIPTS -->
		<div class="bbi-script" data-bbi-src="' . $src . '"></div>
		<div data-bbi-app="Newseum" data-bbi-action="TodaysFrontPages"></div>';
	}

	private function fetchCache ($alias) {

		$time = 10000000;
		if ($this->bUpdateCache) {
			$time = 0;
		}

		$cache = new SimpleCache ();
		$cache->cache_path = $this->config ("path", "cache");
		$cache->cache_time = $time;
		$json = $cache->get_data ($alias, $this->config ("feed", $alias));
		$items = json_decode ($json, true);

		return $items;
	}

	private function fetchFeed ($url) {
		if (function_exists ("curl_init")) {
			$ch = curl_init ();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$content = curl_exec ($ch);
			curl_close ($ch);
			return json_decode ($content, true);
		} else {
			return json_decode (file_get_contents ($url), true);
		}
	}

	private function filterByPaperId ($data = array (), $fpId = 0) {

		$temp = array ();
		$counter = 0;

		$topTenArgs = "";
		if (isset ($data ["summary"])) {
			$topTenArgs = "&tfp_display=topten";
		}

		foreach ($data ["papers"] as $paper) {

			if ($paper ["paperId"] === $fpId) {

				$temp = $paper;

				# Find the previous paper's URL
				if (isset ($data ["papers"] [$counter - 1])) {
					$temp ["links"] ["prev"] = $data ["papers"] [$counter - 1] ["links"] ["detail"] . $topTenArgs;
				}

				# Find the next paper's URL
				if (isset ($data ["papers"] [$counter + 1])) {
					$temp ["links"] ["next"] = $data ["papers"] [$counter + 1] ["links"] ["detail"] . $topTenArgs;
				}

				break;

			}

			$counter++;

		}

		return $temp;

	}

	private function filterPapers ($arr) {

		global $wp_query;

		$temp = array ();

		foreach ($arr as $paper) {

			$quit = false;

			// Is Region set?
			if (isset($wp_query->query_vars["tfp_region"])) {
				$region = strtolower($wp_query->query_vars["tfp_region"]);
				switch ($region) {
					default:
					if (strtolower($paper["region"]) !== $region) {
						$quit = true;
					}
					break;
					case "usa":
					if (strtolower($paper["country"]) !== "usa") {
						$quit = true;
					}
					break;
					case "international":
					if (strtolower($paper["country"]) === "usa") {
						$quit = true;
					}
					break;
				}
			}

			if ($quit === false) {
				if (isset($wp_query->query_vars['tfp_title_letter'])) {
					if (mb_substr($paper["sortTitle"], 0, 1) !== $wp_query->query_vars['tfp_title_letter']) {
						$quit = true;
					}
				}
			}

			if ($quit === false) {
				if (isset($wp_query->query_vars['tfp_state_letter'])) {
					if (mb_substr($paper["state"], 0, 1) !== $wp_query->query_vars['tfp_state_letter']) {
						$quit = true;
					}
				}
			}

			if ($quit === false) {
				if (isset($wp_query->query_vars['tfp_country_letter'])) {
					if (mb_substr($paper["country"], 0, 1) !== $wp_query->query_vars['tfp_country_letter']) {
						$quit = true;
					}
				}
			}

			if ($quit === false) {
				$temp[] = $paper;
			}
		}
		return $temp;
	}

	private function getArchiveSummary () {

		$temp = array();
		$archives = $this->fetchCache("archive-summary");

		foreach ($archives["papers"] as $archive) {
			$archive["links"] = array(
				"detail" => add_query_arg(array("tfp_display" => "archive-date", "tfp_archive_id" => $archive["archiveid"]), get_permalink())
			);
			$temp[] = $archive;
		}

		return array(
			"papers" => $temp
		);
	}

	private function getArchivedPapers ($archiveId) {

		$temp = array ();

		$cache = new SimpleCache ();
		$cache->cache_path = $this->config ("path", "cache");
		$cache->cache_time = $this->iTodaysRemainingSeconds;
		$archives = json_decode ($cache->get_data ("archive-date-" . $archiveId, $this->config ("feed", "archive-date") . "?value1=" . $archiveId), true);

		$papers = $archives ["papers"];

		foreach ($papers as $paper) {

			if (strpos ($paper ["website"], "http") === false) {
				$paper ["website"] = "http://" . $paper ["website"];
			}

			$paper ["sortTitle"] = (strpos ($paper ["title"], "The ") === 0) ? str_replace ("The ", "", $paper ["title"]) . ", The" : $paper ["title"];

			$paper ["images"] = array (
				"sm" => $this->thumbnailSrcArchive ("sm", $paper ["paperId"], $archiveId),
				"md" => $this->thumbnailSrcArchive ("md", $paper ["paperId"], $archiveId),
				"lg" => $this->thumbnailSrcArchive ("lg", $paper ["paperId"], $archiveId)
			);

			$paper ["links"] = array (
				"back" => add_query_arg (array ("tfp_display" => "archive-summary"), get_permalink ()),
				"pdf" => $this->pdfSrcArchive ($paper ["paperId"], $archiveId),
				"detail" => add_query_arg (array ("tfp_id" => $paper ["paperId"]), get_permalink ())
			);

			$temp [] = $paper;

		}

		$aDate = str_split ($archiveId, 2);
		$date = "20" . $aDate [2] . "-" . $aDate [0] . "-" . $aDate [1];

		return array (
			"papers" => $temp,
			"date" => date ($this->dateFormat, strtotime ($date)),
			"sort" => $this->getSortData ($temp, $archiveId)
		);

	}

	private function getData () {
		$data = array();
		$data["feed"] = array(
			"tfp_archive_date" => array(
				"label" => "Archive Date"
			),
			"tfp_archive_summary" => array(
				"label" => "Archive Summary"
			),
			"tfp_daily_papers" => array(
				"label" => "Daily Papers"
			),
			"tfp_daily_status" => array(
				"label" => "Daily Status"
			),
			"tfp_top_ten" => array(
				"label" => "Top Ten"
			)
		);
		$data["map"] = array(
			"tfp_microsoft_bing_map_app_key" => array(
				"label" => "App Key"
			)
		);
		return $data;
	}

	private function getPapers () {

		global $wp_query;

		$temp = array ();
		$papers = $this->fetchCache ("daily-papers");

		foreach ($papers as $paper) {

			// Make sure the papers are active
			if ($id = $paper ["paperId"]) {

				if (strpos ($paper ["website"], "http") === false) {
					$paper ["website"] = "http://" . $paper ["website"];
				}

				$paper ["sortTitle"] = (strpos ($paper ["title"], "The ") === 0) ? str_replace ("The ", "", $paper ["title"]) . ", The" : $paper ["title"];

				$paper ["images"] = array (
					"sm" => $this->thumbnailSrc ("sm", $id),
					"md" => $this->thumbnailSrc ("md", $id),
					"lg" => $this->thumbnailSrc ("lg", $id)
				);

				$paper ["links"] = array (
					"back" => get_permalink (),
					"pdf" => $this->pdfSrc ($id),
					"detail" => add_query_arg (array ("tfp_id" => $id), get_permalink ())
				);

				$temp [] = $paper;

			}
		}

		return array(
			"papers" => $temp,
			"date" => date($this->dateFormat, strtotime($this->aDailyStatus["updatedDate"])),
			"sort" => $this->getSortData ($temp)
		);
	}

	private function getSortData ($papers, $archiveId = null) {

		$sort = array (
			"region" => array (),
			"stateFirstLetter" => array (),
			"countryFirstLetter" => array (),
			"titleFirstLetter" => array ()
		);

		$queryArray = array ();

		$isArchive = (isset ($archiveId));

		# Add item-specific values:
		foreach ($papers as $paper) {

			$stateLetter = substr ($paper ["state"], 0, 1);
			$countryLetter = substr ($paper ["country"], 0, 1);
			$titleLetter = (function_exists ("mb_strtoupper")) ? mb_strtoupper (mb_substr ($paper ["sortTitle"], 0, 1)) : strtoupper (substr ($paper ["sortTitle"], 0, 1));
			$queryArray = array ();

			# Create region dropdown.
			if ($paper ["region"] !== "" && in_array ($paper ["region"], $sort ["region"], true) === false) {
				$sort ["region"] [] = $paper ["region"];
			}

			# Create state letter dropdown.
			if ($stateLetter && in_array ($stateLetter, $sort ["stateFirstLetter"], true) === false) {

				$queryArray = array ("tfp_state_letter" => $stateLetter);

				if ($isArchive) {
					$queryArray ["tfp_archive_id"] = $archiveId;
				}

				$sort ["stateFirstLetter"] [$stateLetter] = add_query_arg ($queryArray, $this->currentUrl);
			}

			# Create country letter dropdown.
			if ($countryLetter && in_array ($countryLetter, $sort ["countryFirstLetter"], true) === false) {

				$queryArray = array ("tfp_country_letter" => $countryLetter);

				if ($isArchive) {
					$queryArray ["tfp_archive_id"] = $archiveId;
				}

				$sort ["countryFirstLetter"] [$countryLetter] = add_query_arg ($queryArray, $this->currentUrl);
			}

			# Create paper title letter dropdown.
			if ($titleLetter && in_array ($titleLetter, $sort ["titleFirstLetter"], true) === false) {

				$queryArray = array ("tfp_title_letter" => $titleLetter);

				if ($isArchive) {
					$queryArray ["tfp_archive_id"] = $archiveId;
				}

				$sort ["titleFirstLetter"] [$titleLetter] = add_query_arg ($queryArray, $this->currentUrl);
			}

		}

		# Sort arrays alphabetically
		ksort ($sort ["region"]);
		ksort ($sort ["stateFirstLetter"]);
		ksort ($sort ["countryFirstLetter"]);
		ksort ($sort ["titleFirstLetter"]);

		# Add "show all" to state letter dropdown.
		$queryArray = array ("tfp_sort_by" => "state");
		if ($isArchive) {
			$queryArray ["tfp_archive_id"] = $archiveId;
		}
		$sort ["stateFirstLetter"] = array ("A-Z" => add_query_arg ($queryArray, $this->currentUrl)) + $sort ["stateFirstLetter"];

		# Add "show all" to country letter dropdown.
		$queryArray = array ("tfp_sort_by" => "country");
		if ($isArchive) {
			$queryArray ["tfp_archive_id"] = $archiveId;
		}
		$sort ["countryFirstLetter"] = array ("A-Z" => add_query_arg ($queryArray, $this->currentUrl)) + $sort ["countryFirstLetter"];

		# Add "show all" to title letter dropdown.
		$queryArray = array ("tfp_sort_by" => "title");
		if ($isArchive) {
			$queryArray ["tfp_archive_id"] = $archiveId;
		}
		$sort ["titleFirstLetter"] = array ("A-Z" => add_query_arg ($queryArray, $this->currentUrl)) + $sort ["titleFirstLetter"];

		/*
		# Create sort type for domestic.
		$isDomestic = ($wp_query->query_vars ["tfp_region"] === "USA");
		if ($isDomestic) {
			$sort ["type"] = array (
				"id" => "tfp-sort-state-type",
				"options" => array (
					"state" => array ("State Name", add_query_arg (array ("tfp_display" => $wp_query->query_vars ["display"], "tfp_sort_by" => "state", "tfp_region" => "USA"), get_permalink ())),
					"title" => array ("Paper Name", add_query_arg (array ("tfp_display" => $wp_query->query_vars ["display"], "tfp_sort_by" => "title", "tfp_region" => "USA"), get_permalink ()))
				)
			);
		}

		# Create sort type for international.
		else {
			$sort ["type"] = array (
				"id" => "tfp-sort-country-type",
				"options" => array (
					"country" => array ("Country Name", add_query_arg (array ("tfp_display" => $wp_query->query_vars ["display"], "tfp_sort_by" => "country", "tfp_region" => $wp_query->query_vars ['tfp_region']), get_permalink ())),
					"title" => array ("Paper Name", add_query_arg (array ("tfp_display" => $wp_query->query_vars ["display"], "tfp_sort_by" => "title", "tfp_region" => $wp_query->query_vars ['tfp_region']), get_permalink ()))
				)
			);
		}
		*/

		return $sort;

	}

	private function pdfSrc ($id) {
		return "http://webmedia.newseum.org/newseum-multimedia/dfp/pdf" . $this->iDayNumber . "/" . $id . ".pdf";
	}

	private function pdfSrcArchive ($paperId, $archiveId) {
		$aDate = str_split($archiveId, 2);
		$date = "20" . $aDate[2] . "-" . $aDate[0] . "-" . $aDate[1];
		return "http://webmedia.newseum.org/newseum-multimedia/tfp_archive/" . $date . "/pdf/" . $paperId . ".pdf";
	}

	public function plugin_menu () {
		add_options_page ("Front Pages Settings", "Front Pages", "manage_options", "front_pages_config", array($this, "plugin_menu_content"));
	}

	public function plugin_menu_content () {

		if (!current_user_can ("manage_options")) {
			wp_die (__ ("You do not have sufficient permissions to access this page."));
		}

		$data = $this->getData();

		// Set the values for each feed from WordPress storage...
		$testSlug = "";

		foreach ($data["feed"] as $slug => $arr) {
			$testSlug = $slug;
			$data["feed"][$slug]["value"] = get_option($slug);
		}

		foreach ($data["map"] as $slug => $arr) {
			$testSlug = $slug;
			$data["map"][$slug]["value"] = get_option($slug);
		}

		if (isset($_POST[$testSlug])) {
			foreach ($data["feed"] as $slug => $arr) {
				update_option($slug, $_POST[$slug]);
			}
			foreach ($data["map"] as $slug => $arr) {
				update_option($slug, $_POST[$slug]);
			}
			print("<div class=\"updated\"><p><strong>Settings saved.</strong></p></div>");
		}

		echo $this->view("admin", $data);
	}

	private function prep ($papers) {

		$temp = array();

		foreach ($papers["papers"] as $paper) {
			if (strpos($paper["website"], "http") === false) {
				$paper["website"] = "http://" . $paper["website"];
			}
			$paper["images"] = array(
				"sm" => $this->thumbnailSrc("sm", $paper["paperId"]),
				"md" => $this->thumbnailSrc("md", $paper["paperId"]),
				"lg" => $this->thumbnailSrc("lg", $paper["paperId"])
			);
			$paper["links"] = array(
				"back" => get_permalink(),
				"pdf" => $this->pdfSrc($paper["paperId"]),
				"detail" => add_query_arg(array("tfp_id" => $paper["paperId"]), get_permalink())
			);
			$temp[] = $paper;
		}

		return array(
			"papers" => $temp,
			"date" => date($this->dateFormat, strtotime($this->aDailyStatus["currentDate"]))
		);
	}

	public function printScripts () {
		wp_enqueue_script("tfp_bing");
		//wp_enqueue_script("tfp_javascript");
	}

	public function printStyles () {
		wp_enqueue_style("tfp-styles");
	}

	public function registerScripts () {
		//wp_register_script("tfp_javascript", $this->config("url","js") . "front-pages.js");
		wp_register_script("tfp_bing", "//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0");
	}

	public function registerStyles () {
		wp_register_style("tfp-styles", $this->config("url", "css") . "styles.css?v=" . time());
	}

	public function shortcode_front_pages ($atts) {

		global $wp_query;
		$data = array ();

		# Retrieve the options from the shortcode.
		$options = shortcode_atts (array (
			"view" => "gallery"
		), $atts);

		# What's the display mode?
		if (isset ($wp_query->query_vars ["tfp_display"])) {
			$display = $wp_query->query_vars ["tfp_display"];
		} else {
			$display = "gallery";
		}

		# Fetch all papers as an array.
		switch ($display) {

			default:
			case "gallery":
				$data = $this->getPapers ();
				break;

			case "topten":
				$data = $this->prep ($this->aTopTen);
				$data ["summary"] = $this->aTopTen ["top10summary"] [0];
				$data ["date"] = date ($this->dateFormat, strtotime ($this->aDailyStatus ["updatedDate"]));
				break;

			case "archive-summary":
				$data = $this->getArchiveSummary ();
				break;

			case "archive-date":
				$data = $this->getArchivedPapers ($wp_query->query_vars ["tfp_archive_id"]);
				break;
		}

		# Show the Top Ten link?
		$data = $this->checkTopTen ($data);

		# Is the current page a detail page?
		if (isset ($wp_query->query_vars ["tfp_id"])) {

			$data ["paper"] = $this->filterByPaperId ($data, $wp_query->query_vars ["tfp_id"]);

			unset ($data ["papers"]);
			unset ($data ["sort"]);

			$data ["options"] = array ("display" => "detail-" . $display);

			return $this->view ("paper", $data);

		}

		# Not a detail page.
		else {

			# Filter and sort the papers.
			$data ["papers"] = $this->filterPapers ($data ["papers"]);
			$data ["papers"] = $this->sortPapers ($data ["papers"]);

			# How many papers should appear, per page?
			$show = (isset ($wp_query->query_vars ["tfp_show"])) ? $wp_query->query_vars ["tfp_show"] : "40";

			$totalPapers = count ($data ["papers"]);

			if ($show === "all") {
				$show = $totalPapers;
			}

			# Options.
			$data ["options"] = array (
				"display" => $display,
				"show" => $show,
				"itemsPerRow" => 3,
				"colWidth" => 4
			);

			# Paginator.
			$data ["paginator"] = array (
				"totalItems" => (isset ($data ["papers"])) ? $totalPapers : 1,
				"itemsPerPage" => (int) $show,
				"currentPage" => (isset ($wp_query->query_vars ['tfp_page'])) ? (int) $wp_query->query_vars['tfp_page'] : 1
			);
			$data ["paginator"] ["totalPages"] = ($data ["paginator"] ["itemsPerPage"] > 0) ? (int) ceil ($data ["paginator"] ["totalItems"] / $data ["paginator"] ["itemsPerPage"]) : 0;
			$data ["paginator"] ["startItem"] = ($data ["paginator"] ["currentPage"] - 1) * $data ["paginator"] ["itemsPerPage"] + 1;

			# Map key.
			$data["map"] = get_option ("tfp_microsoft_bing_map_app_key");

			# Print JavaScript objects.
			$this->exposeData ($data);

			# Print the view.
			return $this->view ($display, $data);

		}

	}

	public function shortcode_front_pages_preview ($atts) {
		$data = array();
		$papers = $this->getPapers();
		$today = date($this->dateFormat, strtotime("today"));
		if (isset($papers["papers"]) && isset($papers["papers"][0]) && isset($papers["papers"][0]["images"])) {
			$data["src"] = (isset($papers["papers"][0]["images"]["lg"])) ? $papers["papers"][0]["images"]["lg"] : $this->config("url", "default-preview-thumbnail");
		}
		$data["date"] = (isset($papers["date"])) ? $papers["date"] : $today;
		return $this->view("preview", $data);
	}

	private function sortPapers ($arr) {

		global $wp_query;

		if (isset ($wp_query->query_vars ['tfp_sort_by'])) {

			switch ($wp_query->query_vars ['tfp_sort_by']) {
				case "state":
					usort($arr, function ($a, $b) {
						return strcmp($a["state"], $b["state"]);
					});
					break;
				case "country":
					usort($arr, function ($a, $b) {
						return strcmp($a["country"], $b["country"]);
					});
					break;
				case "title":
					usort($arr, function ($a, $b) {
						return strcmp($a["sortTitle"], $b["sortTitle"]);
					});
					break;
			}

		}
		return $arr;
	}

	private function thumbnailSrc ($size = "lg", $id) {
		switch ($size) {
			case "lg":
				$src = "http://webmedia.newseum.org/newseum-multimedia/dfp/jpg" . $this->iDayNumber . "/lg/" . $id . ".jpg";
				break;
			default:
			case "md":
				$src = "http://webmedia.newseum.org/newseum-multimedia/dfp/jpg" . $this->iDayNumber . "/med/" . $id . ".jpg";
				break;
			case "sm":
				$src = "http://webmedia.newseum.org/newseum-multimedia/dfp/jpg" . $this->iDayNumber . "/sm/" . $id . ".jpg";
				break;
		}
		return $src;
	}

	private function thumbnailSrcArchive ($size = "lg", $paperId, $archiveId) {
		// http://webmedia.newseum.org/newseum-multimedia/tfp_archive/yyyy-mm-dd/lg/AL_DD.jpg
		$aDate = str_split($archiveId, 2);
		$date = "20" . $aDate[2] . "-" . $aDate[0] . "-" . $aDate[1];
		switch ($size) {
			case "lg":
				$src = "http://webmedia.newseum.org/newseum-multimedia/tfp_archive/" . $date . "/lg/" . $paperId . ".jpg";
				break;
			case "md":
				$src = "http://webmedia.newseum.org/newseum-multimedia/tfp_archive/" . $date . "/med/" . $paperId . ".jpg";
				break;
			case "sm":
				$src = "http://webmedia.newseum.org/newseum-multimedia/tfp_archive/" . $date . "/sm/" . $paperId . ".jpg";
				break;
		}
		return $src;
	}

	private function view ($alias, $data = array()) {
		$file = TFP_VIEW_PATH . $alias . ".php";
		if (!file_exists($file)) {
			return "";
		}
		ob_start();
		require($file);
		$contents = ob_get_clean();
		return $contents;
	}
}
