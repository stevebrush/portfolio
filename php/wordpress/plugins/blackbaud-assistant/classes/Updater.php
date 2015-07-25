<?php
namespace blackbaud;
class Updater extends Core
{
    protected $defaults = array(
        "endpoint" => "https://api.blackbaud.com/services/wordpress/updater/",
        "force_update" => false
    );
    protected $endpoint;
    protected $force_update;
    private $plugin_basename;
    private $plugin_file;
    private $plugin_alias;

    protected function start()
    {
        if (is_admin())
        {
            # Determine proxy.
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
            $this->endpoint = $protocol . $this->endpoint;

            # Enable update check on every request(uncomment once, for testing).
            if ($this->force_update)
            {
                set_site_transient('update_plugins', null);
            }
            $this->plugin_file = $this->app->get("plugin_file");
            $this->plugin_basename = $this->app->get("plugin_basename");
            $basenameArray = explode("/", $this->plugin_basename);
            $this->plugin_alias = $basenameArray[0];

            # Start the update check.
            add_filter("pre_set_site_transient_update_plugins", array($this, "check_updates"));
            add_filter("plugins_api", array($this, "plugin_api_call"), 10, 3);
        }
    }

    /**
     * Checks for an update and alerts WordPress if there is one.
     */
    public function check_updates($transient)
    {
        # If we've already checked the plugin data before, don't check it again.
        if (empty($transient->checked) || empty($this->plugin_file))
        {
            return $transient;
        }

        # Get the plugin data.
        $data = get_plugin_data($this->plugin_file);

        # Get the plugin repo data.
        $response = $this->get_remote_data("basic_check", $transient->checked[$this->plugin_basename]);

        # Feed the update data into WP Updater.
        if (is_object($response) && ! empty($response) && !$response->errors)
        {
            $transient->response[$this->plugin_basename] = $response;
        }

        return $transient;

    }

    public function plugin_api_call($def, $action, $data)
    {
        # Push in plugin version information to display in the details lightbox.
        if (empty($data->slug) ||($data->slug != $this->plugin_basename))
        {
            return false;
        }
        return $this->get_remote_data($action, "0.0.0");
    }

    private function get_remote_data($action, $version)
    {
        if (! isset($version))
        {
            return false;
        }
        global $wp_version;
        $url = get_bloginfo("url");

        # Check for an update.
        $response = wp_remote_post($this->endpoint, array(
            "body" => array(
                "action"      => $action,
                "request"     => serialize(array(
                    "slug"    => $this->plugin_alias,
                    "version" => $version
                )),
                "api-key" => md5($url)
            ),
            "user-agent"  => "WordPress/" . $wp_version . "; " . $url
        ));

        # There is an error in the response.
        if (! is_wp_error($response) &&($response["response"]["code"] == 200) && ! empty($response["body"]))
        {
            $response = unserialize($response["body"]);
        }
        return $response;
    }
}
