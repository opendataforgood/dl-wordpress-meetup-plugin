<?php
/*
Plugin Name: Meet up event able from meetup.com
Plugin URI: http://blog.datalook.io/
Description: Set of groups and meetups with common interest on Datalook
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
      #add_action('init', array(&$this, 'log'));
      require_once(sprintf("%s/wp-dl-meetup-api.php", dirname(__FILE__)));
      $this->_datalookMeetupAPi = new DatalookMeetupAPi();
      #$this->_datalookMeetupAPi->renderTable();
    }

    function log() {
      echo sprintf("%s/wp-dl-meetup-api.php", dirname(__FILE__));
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