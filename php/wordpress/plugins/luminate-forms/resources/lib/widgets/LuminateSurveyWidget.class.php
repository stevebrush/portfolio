<?php
class LuminateSurveyWidget extends WP_Widget {

	private $app,
		$constituent,
		$page;

	public function LuminateSurveyWidget() {
		$this->app = getLoApp();
		$this->constituent = getLoConst();
		$this->page = getLoPage();
		parent::WP_Widget(false, $name = __("Luminate Survey", "LuminateSurveyWidget") );
	}
	
	public function form( $instance ) {
		$title = ($instance) ? esc_attr($instance["title"]) : "";
		$survey_id = ($instance) ? esc_attr($instance["survey_id"]) : "";
		?>
		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>"><?php _e("Widget Title", "LuminateSurveyWidget"); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("survey_id"); ?>"><?php _e("Survey ID:", "LuminateSurveyWidget"); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id("survey_id"); ?>" name="<?php echo $this->get_field_name("survey_id"); ?>" type="text" value="<?php echo $survey_id; ?>" />
		</p>
		<?php
	}
	
	public function update($newInstance, $oldInstance) {
		$instance = $oldInstance;
		$instance["title"] = strip_tags($newInstance["title"]);
		$instance["survey_id"] = strip_tags($newInstance["survey_id"]);
		return $instance;
	}
	
	public function widget($args, $instance) {
	echo "Hello";
		extract($args);
		
		$title = apply_filters("widget_title", $instance["title"]);
		$form_id = $instance["survey_id"];
		
		echo $before_widget;
		
		$formTitle = "";
		if ($title) {
			$formTitle = $before_title . $title . $after_title;
		}
		
		if ($form_id) {
			$loPage = $this->page;
			$loApp = $this->app;
			$loConst = $this->constituent;
			$loPage->doLoadScripts = true;
			ob_start();
			include LO_FORM_PATH."survey.form.php";
			$contents = ob_get_contents();
			ob_end_clean();
			echo $contents;
		}
		
		echo $after_widget;
	}
}
