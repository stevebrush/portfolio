<?php
class TFP_Application {



    private static $instance;
    private static $config;

    private $currentUrl;
    private $iTodaysRemainingSeconds;
    private $multimediaEndpoint = "http://webmedia.newseum.org/newseum-multimedia/";
    private $dateFormat = "l, F d, Y";

    private $aData = array ();
    private $aDailyStatus = array ();
    private $aDailyStatus_cached = array ();
    private $aTopTen = array ();
    private $bUpdateCache = false;
    private $iDayNumber = 0;
    private $sRefreshedDate = "";



    public function __construct ($config = array ()) {

        # Set common variables.
        $this->currentUrl = "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        self::$instance =& $this;
        self::$config = $config;
        $this->iTodaysRemainingSeconds = strtotime ("tomorrow") - time ();

        # Retrieve all data to be used.

        $this->aDailyStatus = $this->FetchFeed ($this->Config ("feed", "daily-status"));
        $this->aDailyStatus = $this->aDailyStatus [0];

        $this->aTopTen = $this->FetchFeed ($this->Config ("feed", "top-ten"));

        $this->aDailyStatus_cached = $this->FetchCache ($this->Config ("feed", "daily-status"));
        $this->aDailyStatus_cached = $this->aDailyStatus_cached [0];

        $this->bUpdateCache = $this->CheckCacheDates ();

        $this->sRefreshedDate = $this->aDailyStatus ["refreshedDate"];
        $this->iDayNumber = $this->aDailyStatus ["dayNumber"];

    }



	public function AddQueryVariables ($aVars) {

		$aVars[] = "tfp_id";
		$aVars[] = "tfp_page";
		$aVars[] = "tfp_show";
		$aVars[] = "tfp_display";
		$aVars[] = "tfp_region";
		$aVars[] = "tfp_title_letter";
		$aVars[] = "tfp_country_letter";
		$aVars[] = "tfp_state_letter";
		$aVars[] = "tfp_sort_by";
		$aVars[] = "tfp_archive_id";

		return $aVars;

	}



    public function CheckCacheDates () {

        /*
        - Top Ten and Daily Status feeds should never be cached.
        - The Daily Papers should be cached when the Daily Status
          cache refreshedDate doesn't equal the feed's refreshedDate.
        */

        if ($this->aDailyStatus ["refreshedDate"] == $this->aDailyStatus_cached ["refreshedDate"]) {
            return false;
        } else {
            return true;
        }

    }



    private function CheckTopTen ($arr = array ()) {

        $showTopTen = false;

        if (isset ($this->aTopTen ["top10summary"]) && isset ($this->aTopTen ["top10summary"][0])) {

            $date        = strtotime ($this->aTopTen ["top10summary"][0]["top10DateCreated"]);
            $now_year    = date ("Y");
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



    public function Config ($key, $value) {

        # Retrieve values from the global configuration array, based on a key.
        if (isset (self::$config [$key])) {
            $value = self::$config [$key][$value];
        } else {
            $value = false;
        }

        return $value;

    }



    private function DashboardFieldRules () {

        $data = array ();

        $data ["feed"] = array (
            "tfp_archive_date" => array (
                "label" => "Archive Date"
            ),
            "tfp_archive_summary" => array (
                "label" => "Archive Summary"
            ),
            "tfp_daily_papers" => array (
                "label" => "Daily Papers"
            ),
            "tfp_daily_status" => array (
                "label" => "Daily Status"
            ),
            "tfp_top_ten" => array (
                "label" => "Top Ten"
            )
        );

        $data ["rss"] = array (
            "tfp_top_ten_rss" => array (
                "label" => "Top Ten"
            )
        );

        $data ["map"] = array (
            "tfp_microsoft_bing_map_app_key" => array (
                "label" => "App Key"
            )
        );

        return $data;

    }



    public function DashboardMenu () {

        add_options_page ("Front Pages Settings", "Front Pages", "manage_options", "front_pages_config", array ($this, "DashboardMenuContent"));

    }



    public function DashboardMenuContent () {

		# Only administrators can save API options.
        if (!current_user_can ("manage_options")) {
            wp_die (__ ("You do not have sufficient permissions to access this page."));
        }

        $data = $this->DashboardFieldRules ();

        # Set the values for each feed from WordPress storage.
        $testSlug = "";

        foreach ($data ["feed"] as $slug => $arr) {
            $testSlug = $slug;
            $data ["feed"][$slug]["value"] = get_option ($slug);
        }

        foreach ($data ["rss"] as $slug => $arr) {
            $testSlug = $slug;
            $data ["rss"][$slug]["value"] = get_option ($slug);
        }

        foreach ($data ["map"] as $slug => $arr) {
            $testSlug = $slug;
            $data ["map"][$slug]["value"] = get_option ($slug);
        }

        if (isset ($_POST [$testSlug])) {

            foreach ($data ["feed"] as $slug => $arr) {
                update_option ($slug, $_POST [$slug]);
            }

            foreach ($data ["rss"] as $slug => $arr) {
                update_option ($slug, $_POST [$slug]);
            }

            foreach ($data ["map"] as $slug => $arr) {
                update_option ($slug, $_POST [$slug]);
            }

            print ("<div class=\"updated\"><p><strong>Settings saved.</strong></p></div>");

        }

        echo $this->View ("admin", $data);

    }



    private function ExposeData ($arr) {

        $src = $this->Config ("url", "js") . "bbi-newseum.js?v=" . time ();
        $temp = array ();

        foreach ($arr as $k => $v) {
            $temp [$k] = $v;
        }

        echo '
        <script bbi-src="' . $src . '">
        window.TFP_DATA = ' . json_encode ($temp) . ';
        (function(a,c,d){if(a.getElementById(c)){return}
        var b=a.createElement("script");b.src=d;b.id=c;
        a.getElementsByTagName("head")[0].appendChild(b)})
        (document,"bbi-namespace","//api.blackbaud.com/bbi");
        </script>
        <i bbi-app="Newseum" bbi-action="TodaysFrontPages"></i>';

    }



    private function FetchCache ($url, $time = 10000000) {

		$key = substr ($url, -44);
        $data = get_transient ($key);

        if (empty ($data) || $this->bUpdateCache) {
	        $data = $this->FetchFeed ($url);
	        set_transient ($key, $data, $time);
        }

        return $data;

    }



    private function FetchFeed ($url) {

	    # Make sure the curl service exists.
        if (function_exists ("curl_init")) {

            $ch = curl_init ();

            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);

            $content = curl_exec ($ch);

            curl_close ($ch);

            return json_decode ($content, true);

        }

        # If it doesn't, just use file_get_contents.
        else {

            return json_decode (file_get_contents ($url), true);

        }
    }



    private function FilterByPaperId ($data = array (), $fpId = 0) {

        global $wp_query;

        $temp = array ();
        $args = array ();
        $counter = 0;

		# Only use the URL variables that concern this plugin.
        foreach ($wp_query->query_vars as $k => $v) {
            if (strpos ($k, "tfp_") === 0) {
                $args [$k] = $v;
            }
        }

		# Loop through all the papers in the cache,
		# but stop when the ID matches the one in the URL.
        foreach ($data ["papers"] as $paper) {

            if ($paper ["paperId"] === $fpId) {

                $temp = $paper;

                # Set the previous paper's URL
                if (isset ($data ["papers"][$counter - 1])) {
                    $args["tfp_id"] = $data ["papers"][$counter - 1]["paperId"];
                    $temp ["links"]["prev"] = add_query_arg ($args, get_permalink ());
                }

                # Set the next paper's URL
                if (isset ($data ["papers"][$counter + 1])) {
                    $args["tfp_id"] = $data ["papers"][$counter + 1]["paperId"];
                    $temp ["links"]["next"] = add_query_arg ($args, get_permalink ());
                }

                break;

            }

            $counter++;

        }

        return $temp;

    }



    private function FilterPapers ($arr) {

        global $wp_query;

        $temp = array ();

        foreach ($arr as $paper) {

            $quit = false;

            # Does this paper match the region?
            if (isset ($wp_query->query_vars ["tfp_region"])) {

                $region = strtolower ($wp_query->query_vars ["tfp_region"]);

                switch ($region) {

                    default:
	                    if (strtolower ($paper ["region"]) !== $region) {
	                        $quit = true;
	                    }
	                    break;

                    case "usa":
	                    if (strtolower ($paper ["country"]) !== "usa") {
	                        $quit = true;
	                    }
	                    break;

                    case "international":
	                    if (strtolower ($paper ["country"]) === "usa") {
	                        $quit = true;
	                    }
	                    break;

                }

            }

			# Does the first letter of this paper's title match the query?
            if ($quit === false) {
                if (isset ($wp_query->query_vars ["tfp_title_letter"])) {
                    if (mb_substr ($paper ["sortTitle"], 0, 1) !== $wp_query->query_vars ["tfp_title_letter"]) {
                        $quit = true;
                    }
                }
            }

			# Does this paper match the state?
            if ($quit === false) {
                if (isset ($wp_query->query_vars ["tfp_state_letter"])) {
                    if (mb_substr ($paper ["state"], 0, 1) !== $wp_query->query_vars ["tfp_state_letter"]) {
                        $quit = true;
                    }
                }
            }

			# Does this paper match the country?
            if ($quit === false) {
                if (isset ($wp_query->query_vars ["tfp_country_letter"])) {
                    if (mb_substr ($paper ["country"], 0, 1) !== $wp_query->query_vars ["tfp_country_letter"]) {
                        $quit = true;
                    }
                }
            }

			# If every filter above passes, include the paper in the list!
            if ($quit === false) {
                $temp [] = $paper;
            }

        }

        return $temp;

    }



    private function GetArchiveSummary () {

        $temp = array ();
        $archives = $this->FetchCache ($this->Config ("feed", "archive-summary"));
        $permalink = get_permalink ();

        foreach ($archives ["papers"] as $archive) {

            $archive ["links"] = array (
                "detail" => add_query_arg (array (
                	"tfp_display" => "archive-date",
                	"tfp_archive_id" => $archive ["archiveid"]
                ), $permalink)
            );

            $temp [] = $archive;

        }

        return array (
            "papers" => $temp
        );

    }



    private function GetArchivedPapers ($archiveId) {

        $temp = array ();

		$url = $this->Config ("feed", "archive-date") . "?value1=" . $archiveId;

		$archives = $this->FetchCache ($url, $this->iTodaysRemainingSeconds);
        $papers = $archives ["papers"];

        foreach ($papers as $paper) {
			$temp [] = $this->SanitizePaper ($paper, "archive-summary", $archiveId);
        }

        $aDate = str_split ($archiveId, 2);
        $date = "20" . $aDate [2] . "-" . $aDate [0] . "-" . $aDate [1];

        return array (
            "papers" => $temp,
            "date" => date ($this->dateFormat, strtotime ($date)),
            "sort" => $this->GetSortData ($temp, $archiveId)
        );

    }



    private function GetImages ($paperId, $archiveId = null) {

		$endpoint = "";
		$images = array ();

        if (isset ($archiveId)) {

	        $aDate = str_split ($archiveId, 2);
			$date = "20" . $aDate [2] . "-" . $aDate [0] . "-" . $aDate [1];
        	$endpoint = $this->multimediaEndpoint . "tfp_archive/" . $date;

        	$images = array (
				"lg" => $endpoint . "/lg/" . $paperId . ".jpg",
				"md" => $endpoint . "/med/" . $paperId . ".jpg",
				"sm" => $endpoint . "/sm/" . $paperId . ".jpg"
			);

        } else {

	        $endpoint = $this->multimediaEndpoint . "dfp/jpg" . $this->iDayNumber;

			$images = array (
				"lg" => $endpoint . "/lg/" . $paperId . ".jpg",
				"md" => $endpoint . "/med/" . $paperId . ".jpg",
				"sm" => $endpoint . "/sm/" . $paperId . ".jpg"
			);

        }

         return $images;

    }



    private function GetDailyPapers ($display = "gallery") {

        $temp = array ();
        $papers = $this->FetchCache ($this->Config ("feed", "daily-papers"));

        foreach ($papers as $paper) {

            # Make sure the papers are active.
            if ($paper ["paperId"]) {
				$temp [] = $this->SanitizePaper ($paper, $display);
            }

        }

        return array (
            "papers" => $temp,
            "date" => date ($this->dateFormat, strtotime ($this->aDailyStatus ["updatedDate"])),
            "sort" => $this->GetSortData ($temp)
        );
    }



    private function GetPDF ($paperId, $archiveId = null) {

		$src = "";

		# Generate the correct URL for the PDF version of papers.
		if (isset ($archiveId)) {
	        $aDate = str_split ($archiveId, 2);
	        $date = "20" . $aDate [2] . "-" . $aDate [0] . "-" . $aDate [1];
	        $src = $this->multimediaEndpoint . "tfp_archive/" . $date . "/pdf/" . $paperId . ".pdf";
		} else {
			$src = $this->multimediaEndpoint . "dfp/pdf" . $this->iDayNumber . "/" . $paperId . ".pdf";
		}

		return $src;

    }



    private function GetSortData ($papers, $archiveId = null) {

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

            # Create 'region' dropdown.
            if ($paper ["region"] !== "" && in_array ($paper ["region"], $sort ["region"], true) === false) {
                $sort ["region"][] = $paper ["region"];
            }

            # Create 'state letter' dropdown.
            if ($stateLetter && in_array ($stateLetter, $sort ["stateFirstLetter"], true) === false) {

                $queryArray = array ("tfp_state_letter" => $stateLetter);

                if ($isArchive) {
                    $queryArray ["tfp_archive_id"] = $archiveId;
                }

                $sort ["stateFirstLetter"][$stateLetter] = add_query_arg ($queryArray, $this->currentUrl);
            }

            # Create 'country letter' dropdown.
            if ($countryLetter && in_array ($countryLetter, $sort ["countryFirstLetter"], true) === false) {

                $queryArray = array ("tfp_country_letter" => $countryLetter);

                if ($isArchive) {
                    $queryArray ["tfp_archive_id"] = $archiveId;
                }

                $sort ["countryFirstLetter"][$countryLetter] = add_query_arg ($queryArray, $this->currentUrl);
            }

            # Create 'paper title letter' dropdown.
            if ($titleLetter && in_array ($titleLetter, $sort ["titleFirstLetter"], true) === false) {

                $queryArray = array ("tfp_title_letter" => $titleLetter);

                if ($isArchive) {
                    $queryArray ["tfp_archive_id"] = $archiveId;
                }

                $sort ["titleFirstLetter"][$titleLetter] = add_query_arg ($queryArray, $this->currentUrl);
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

        return $sort;

    }



    public function PageTitle () {

		global $post;
		$slug = get_post ($post)->post_name;

		if ($slug != "todaysfrontpages") {
			return;
		}

        $title = "Today's Front Pages | Newseum";

        if (isset ($this->aData ["paper"])) {
            $title = $this->aData ["paper"]["title"];
            echo '<meta property="og:image" content="' . $this->aData ["paper"]["images"]["lg"] . '">';
        }

        $protocol = (!empty ($_SERVER ["HTTPS"]) && $_SERVER ["HTTPS"] !== "off" || $_SERVER ["SERVER_PORT"] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"];

        echo '<meta property="og:title" content="' . $title . ' | Today\'s Front Pages | Newseum">';
        echo '<meta property="og:site_name" content="Today\'s Front Pages | Newseum">';
        echo '<meta property="og:url" content="' . $url . '">';

    }



    public function PrintScripts () {

        wp_enqueue_script ("tfp_bing");

    }



    public function PrintStyles () {

        wp_enqueue_style ("tfp-styles");

    }



    public function RegisterScripts () {

        wp_register_script ("tfp_bing", "//ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0");

    }



    public function RegisterStyles () {

        wp_register_style ("tfp-styles", $this->Config ("url", "css") . "styles.css?v=" . time ());

    }



    private function SanitizePaper ($paper = array (), $display = "gallery", $archiveId = null) {

	    $permalink = get_permalink ();

	    # Make sure the website begins with "http://".
        if (strpos ($paper ["website"], "http") === false) {
            $paper ["website"] = "http://" . $paper ["website"];
        }

		# If the title start with "The", put it at the end.
		if (strpos ($paper ["title"], "The ") === 0) {
			$paper ["sortTitle"] = str_replace ("The ", "", $paper ["title"]) . ", The";
		} else {
			$paper ["sortTitle"] = $paper ["title"];
		}

		# Set the locations of all three image sizes.
		$paper ["images"] = $this->GetImages ($paper ["paperId"], $archiveId);

		# Set the various links needed for the detail page.
        $paper ["links"] = array (
            "back" => add_query_arg (array ("tfp_display" => $display), $permalink) . "#" . $paper ["paperId"],
            "pdf" => $this->GetPDF ($paper ["paperId"], $archiveId),
            "detail" => add_query_arg (array ("tfp_id" => $paper ["paperId"]), $permalink)
        );

	    return $paper;

    }



    private function SanitizeTopTen ($papers, $display = "gallery") {

        $temp = array ();

        foreach ($papers ["papers"] as $paper) {
            $temp[] = $this->SanitizePaper ($paper, $display);
        }

        return array (
            "papers" => $temp,
            "date" => date ($this->dateFormat, strtotime ($this->aDailyStatus ["refreshedDate"]))
        );

    }



    public function Shortcode () {

		/*
		Shortcode to display the Front Page gallery and detail pages.
		*/

        # Print JavaScript objects.
        $this->ExposeData ($this->aData);

        # Print the page.
        return $this->View ($this->aData ["options"]["view"], $this->aData);

    }



    public function ShortcodePreview ($atts) {

		/*
		Shortcode to display a preview of the Front Pages.
		*/

        $data = array ();
        $papers = $this->GetDailyPapers ();
        $today = date ($this->dateFormat, strtotime ("today"));

        if (isset ($papers ["papers"]) && isset ($papers ["papers"][0]) && isset ($papers ["papers"][0]["images"])) {
            $data ["src"] = (isset ($papers ["papers"][0]["images"]["lg"])) ? $papers ["papers"][0]["images"]["lg"] : $this->Config ("url", "default-preview-thumbnail");
        }

        $data ["date"] = (isset ($papers ["date"])) ? $papers ["date"] : $today;

        return $this->View ("preview", $data);

    }



    private function SortPapers ($arr) {

        global $wp_query;

        if (isset ($wp_query->query_vars ["tfp_sort_by"])) {

            switch ($wp_query->query_vars ["tfp_sort_by"]) {

                case "state":
                    usort ($arr, function ($a, $b) {
                        return strcmp ($a ["state"], $b ["state"]);
                    });
                    break;

                case "country":
                    usort ($arr, function ($a, $b) {
                        return strcmp ($a ["country"], $b ["country"]);
                    });
                    break;

                case "title":
                    usort ($arr, function ($a, $b) {
                        return strcmp ($a ["sortTitle"], $b ["sortTitle"]);
                    });
                    break;

            }

        }

        return $arr;

    }



    public function Start () {

        global $wp_query;

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
                $this->aData = $this->GetDailyPapers ($display);
                break;

            case "topten":
                $this->aData = $this->SanitizeTopTen ($this->aTopTen, $display);
                $this->aData ["summary"] = $this->aTopTen ["top10summary"][0];
                $this->aData ["date"] = date ($this->dateFormat, strtotime ($this->aDailyStatus ["updatedDate"]));
                $this->aData ["rss"] = get_option ("tfp_top_ten_rss");
                break;

            case "archive-summary":
                $this->aData = $this->GetArchiveSummary ();
                break;

            case "archive-date":
                $this->aData = $this->GetArchivedPapers ($wp_query->query_vars ["tfp_archive_id"]);
                break;
        }

        # Show the Top Ten link?
        $this->aData = $this->CheckTopTen ($this->aData);

        # Filter and sort the papers.
        $this->aData ["papers"] = $this->FilterPapers ($this->aData ["papers"]);
        $this->aData ["papers"] = $this->SortPapers ($this->aData ["papers"]);

        # Is the current page a detail page?
        if (isset ($wp_query->query_vars ["tfp_id"])) {

            $this->aData ["paper"] = $this->FilterByPaperId ($this->aData, $wp_query->query_vars ["tfp_id"]);

            unset ($this->aData ["papers"]);
            unset ($this->aData ["sort"]);

            $this->aData ["options"] = array (
                "display" => "detail-" . $display,
                "view" => "paper"
            );

        }

        # Not a detail page.
        else {

            # How many papers should appear, per page?
            $show = (isset ($wp_query->query_vars ["tfp_show"])) ? $wp_query->query_vars ["tfp_show"] : "40";

            $totalPapers = count ($this->aData ["papers"]);

            if ($show === "all") {
                $show = $totalPapers;
            }

            # Options.
            $this->aData ["options"] = array (
                "display" => $display,
                "view" => $display,
                "show" => $show,
                "itemsPerRow" => 4,
                "colWidth" => 3
            );

            # Paginator.
            $this->aData ["paginator"] = array (
                "totalItems" => (isset ($this->aData ["papers"])) ? $totalPapers : 1,
                "itemsPerPage" => (int) $show,
                "currentPage" => (isset ($wp_query->query_vars ["tfp_page"])) ? (int) $wp_query->query_vars["tfp_page"] : 1
            );
            $this->aData ["paginator"]["totalPages"] = ($this->aData ["paginator"]["itemsPerPage"] > 0) ? (int) ceil ($this->aData ["paginator"]["totalItems"] / $this->aData ["paginator"]["itemsPerPage"]) : 0;
            $this->aData ["paginator"]["startItem"] = ($this->aData ["paginator"]["currentPage"] - 1) * $this->aData ["paginator"]["itemsPerPage"] + 1;

            # Map key.
            $this->aData["map"] = get_option ("tfp_microsoft_bing_map_app_key");

        }

    }



    private function View ($alias, $data = array ()) {

        $file = TFP_VIEW_PATH . $alias . ".php";

        if (!file_exists ($file)) {
            return "";
        }

        ob_start ();
        require ($file);
        $contents = ob_get_clean ();
        return $contents;

    }



}
