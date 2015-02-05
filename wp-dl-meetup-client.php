<?php
/*
Plugin Name: Meetup Datalook
Plugin URI: http://blog.datalook.io/
Description: Given a set of groups Ids print a plain HTML table with the following fields (name of the group, link, number of members,number of past events) from Meetup.com
Version: 1.0.0
Author: Vicens Fayos
Author URI: http://vicensfayos.com
License: GPL
*/

/*

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php 

if(!class_exists('DatalookMeetupClient'))

{
  class DatalookMeetupClient {

    private $_datalookMeetupAPi;

    public function activate() {}
    public function deactivate() {}


    public function __construct() {

      // Initialize Settings
      require_once(sprintf("%s/wp-dl-settings.php", dirname(__FILE__)));
      $dataLookMeetupSettings = new DataLookMeetupSettings();

      #add_action('init', array(&$this, 'log'));
      require_once(sprintf("%s/wp-dl-meetup-api.php", dirname(__FILE__)));
      $this->_datalookMeetupAPi = new DatalookMeetupAPi();
      #$this->_datalookMeetupAPi->renderTable();

      $plugin = plugin_basename(__FILE__);
      add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
    }

    function log() {
      echo sprintf("%s/wp-dl-meetup-api.php", dirname(__FILE__));
    }

    // Add the settings link to the plugins page
    function plugin_settings_link($links)
    {
      $settings_link = '<a href="options-general.php?page=wp_plugin_data_look_template">Settings</a>';
      array_unshift($links, $settings_link);
      return $links;
    }

  }
}

if(class_exists('DatalookMeetupClient'))
{
  // Installation and uninstallation hooks
  register_activation_hook(__FILE__, array('DatalookMeetupClient', 'activate'));
  register_deactivation_hook(__FILE__, array('DatalookMeetupClient', 'deactivate'));

  // instantiate the plugin class
  $datalookMeetupClient = new DatalookMeetupClient();

}

?>