<?php
/* Adds a simple Profile Menu to a sidebar */
class LuminateProfileMenuWidget extends WP_Widget {

	private $app;
	private $constitent;

	function __construct() {
		$this->app = getLoApp();
		$this->constituent = getLoConst();
		parent::__construct(
			'lo_profile_menu', 
			'Luminate_Profile',
			array(
				"name"=>__('Luminate Profile Menu', 'LuminateProfileMenuWidget'), 
				"description"=>__('This creates a new profile menu.', 'LuminateProfileMenuWidget')
			)
		);
	}
	
	function form($instance) {
		$title = ($instance) ? esc_attr($instance['title']) : '';
		$titleLoggedIn = ($instance) ? esc_attr($instance['title-logged-in']) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (Logged Out)', 'LuminateProfileMenuWidget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title-logged-in'); ?>"><?php _e('Title (Logged In)', 'LuminateProfileMenuWidget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title-logged-in'); ?>" name="<?php echo $this->get_field_name('title-logged-in'); ?>" type="text" value="<?php echo $titleLoggedIn; ?>" />
		</p>
		<?php
	}
	
	function update($newInstance, $oldInstance) {
		$instance = $oldInstance;
		$instance['title'] = strip_tags($newInstance['title']);
		$instance['title-logged-in'] = strip_tags($newInstance['title-logged-in']);
		return $instance;
	}
	
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$titleLoggedIn = apply_filters('widget_title', $instance['title-logged-in']);
		
		echo $before_widget;
			if ($this->constituent->checkLogin()) {
				if ($titleLoggedIn) {
					echo $before_title . $titleLoggedIn . $after_title;
				}
				echo "<ul>";
					echo "<li><a href=\"{$this->app->config('url','profile')}\">My Profile</a></li>";
					echo "<li><a href=\"{$this->app->config('url','logout')}\">Logout</a></li>";
				echo "</ul>";
			} else {
				if ($title) {
					echo $before_title . $title . $after_title;
				}
				echo "<ul>";
					echo "<li><a href=\"{$this->app->config('url','register')}\">Register</a></li>";
					echo "<li><a href=\"{$this->app->config('url','login')}\">Login</a></li>";
				echo "</ul>";
			}
		echo $after_widget;
	}
}
?>