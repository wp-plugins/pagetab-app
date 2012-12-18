<?php
/*
Plugin Name: PageTabApp.com Wordpress Sync to Page Tab
Plugin URI: http://www.pagetabapp.com
Description: This plug-in will enable auto sync from Wordpress post to Page Tab.
Author: Patric Chan
Version: 1.10
Author URI: http://www.PageTabApp.com
*/

/*  Copyright 2009

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (!class_exists('cwp_pta')) {
    class cwp_pta {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'cwp_pta_options';

        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "cwp_pta";

        /**
        * @var string $pluginurl The path to this plugin
        */
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';

        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();

        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function cwp_pta(){$this->__construct();}

        /**
        * PHP 5 Constructor
        */
        function __construct(){
            //Language Setup
            /*$locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);*/

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';

            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();

            //Actions
            //add_action("admin_menu", array(&$this,"admin_menu_link"));

            /*
            add_action("wp_head", array(&$this,"add_css"));
            add_action('wp_print_scripts', array(&$this, 'add_js'));
            */

            //Filters
            /*
            add_filter('the_content', array(&$this, 'filter_content'), 0);
            */
            add_filter("publish_post", array(&$this, "publish_post"));
            add_filter("publish_page", array(&$this, "publish_page"));
        }

        function publish_page($post_ID) {
        	global $wpdb;

        	//Check if this is fresh new published post, not edited one
        	if (true) {
        		$secret = get_post_meta($post_ID, 'pagetab_secret_key', true);
        		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $post_ID ) );
        		$content = explode(PHP_EOL . PHP_EOL, $post->post_content);
	            $htmlcontent = '';
    	        foreach($content as $line){
    				$htmlcontent .= '<p>' . str_replace(PHP_EOL, '<br />' , $line) . '</p>';
				}
        		$post_data = array(
        		    'source' => get_permalink($post_ID),
        		    'title' => $post->post_title,
        		    'body' => $htmlcontent,
        		    'wpurl' => get_bloginfo('wpurl'),
        		    'published' => $post->post_date,
        		    'feed_title' => get_bloginfo('name'),
        		    'feed_url' => get_bloginfo('rss2_url'),
        		    'secret' => $secret
        		);
        		$result = $this->post_request('http://www.pagepressapp.com/wp_tabsync', $post_data, get_bloginfo('wpurl'));
        		if ($result['status'] == 'ok'){

        		} else {

        		}
        	}
        }

        function publish_post($post_ID) {
        	global $wpdb;

        	//Check if this is fresh new published post, not edited one
        	if (true) {
        		$secret = get_post_meta($post_ID, 'pagetab_secret_key', true);
        		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d", $post_ID ) );
        		$content = explode(PHP_EOL . PHP_EOL, $post->post_content);
	            $htmlcontent = '';
    	        foreach($content as $line){
    				$htmlcontent .= '<p>' . str_replace(PHP_EOL, '<br />' , $line) . '</p>';
				}
        		$post_data = array(
        		    'source' => get_permalink($post_ID),
        		    'title' => $post->post_title,
        		    'body' => $htmlcontent,
        		    'wpurl' => get_bloginfo('wpurl'),
        		    'published' => $post->post_date,
        		    'feed_title' => get_bloginfo('name'),
        		    'feed_url' => get_bloginfo('rss2_url'),
        		    'secret' => $secret
        		);
        		$result = $this->post_request('http://www.pagepressapp.com/wp_tabsync', $post_data, get_bloginfo('wpurl'));
        		if ($result['status'] == 'ok'){

        		} else {

        		}
        	}
        }


        /**
        * Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array('default'=>'options');
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;

            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }

        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
            //add_options_page('PageTabApp.com', 'PageTabApp.com', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }

        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }

        /**
        * Adds settings/options page
        */
        function admin_options_page() {
            if($_POST['cwp_pta_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'cwp_pta-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.');
                $this->options['cwp_pta_secret'] = $_POST['cwp_pta_secret'];

                $this->saveAdminOptions();

                echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
            }
?>
                <div class="wrap">
                <h2>PagePressApp.com Auto Wall Post</h2>
                <form method="post" id="cwp_pta_options">
                <?php wp_nonce_field('cwp_pta-update-options'); ?>
                    <table width="100%" cellspacing="2" cellpadding="5" class="form-table">
                        <tr valign="top">
                            <th width="33%" scope="row"><?php _e('Secret key :', $this->localizationDomain); ?></th>
                            <td><input name="cwp_pta_secret" type="text" id="cwp_pta_secret" size="45" value="<?php echo $this->options['cwp_pta_secret'] ;?>"/>
                        </td>
                        </tr>
                        <tr>
                            <th colspan=2><input type="submit" name="cwp_pta_save" value="Save" /></th>
                        </tr>
                    </table>
                </form>
                <?php
        }

        function post_request($url, $data, $referer='') {

            // Convert the data array into URL Parameters like a=b&foo=bar etc.
            $data = http_build_query($data);

            // parse the given URL
            $url = parse_url($url);

            if ($url['scheme'] != 'http') {
                die('Error: Only HTTP request are supported !');
            }

            // extract host and path:
            $host = $url['host'];
            $path = $url['path'];

            // open a socket connection on port 80 - timeout: 30 sec
            $fp = fsockopen($host, 80, $errno, $errstr, 30);

            if ($fp){

                // send the request headers:
                fputs($fp, "POST $path HTTP/1.1\r\n");
                fputs($fp, "Host: $host\r\n");

                if ($referer != '')
                    fputs($fp, "Referer: $referer\r\n");

                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                fputs($fp, "Content-length: ". strlen($data) ."\r\n");
                fputs($fp, "Connection: close\r\n\r\n");
                fputs($fp, $data);

                $result = '';
                while(!feof($fp)) {
                    // receive the results of the request
                    $result .= fgets($fp, 128);
                }
            }
            else {
                return array(
                    'status' => 'err',
                    'error' => "$errstr ($errno)"
                );
            }

            // close the socket connection:
            fclose($fp);

            // split the result header from the content
            $result = explode("\r\n\r\n", $result, 2);

            $header = isset($result[0]) ? $result[0] : '';
            $content = isset($result[1]) ? $result[1] : '';

            // return as structured array:
            return array(
                'status' => 'ok',
                'header' => $header,
                'content' => $content
            );
        }

  } //End Class
} //End if class exists statement

//instantiate the class
if (class_exists('cwp_pta')) {
    $cwp_pta_var = new cwp_pta();
}
?>
