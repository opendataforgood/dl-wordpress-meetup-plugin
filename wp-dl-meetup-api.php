<?php

/*

http://api.meetup.com/2/events?status=upcoming&order=time&limited_events=False&group_urlname=DataKind-NYC&desc=false&offset=0&photo-host=public&format=json&page=20&fields=&sig_id=183808390&sig=72895b288f1d8ca6d906d8835fc93e9434364c70

http://api.meetup.com/2/groups?radius=25.0&order=id&group_urlname=DataKind-NYC&desc=false&offset=0&photo-host=public&format=json&page=20&fields=&sig_id=183808390&sig=f78349aa685475e5510e0cef5e88844441ab7b09

*/

if (!class_exists('DatalookMeetupAPi')) {

  /*
   *  TODO
   *
   *  - clean up: create view fo rendering from controller
   *  - clean up: now is very specific for members / events should be more generalist
   *  - downgrade if meetup api limit api reached
   *  - mock up table on client side for allowing reordering
   *  - add custom field on Admin panel for adding group ids
   *  - add custom field on Admin panel for api / key
   *  - Add on github (as plugin)
   *
   *  http://www.meetup.com/DataforGood/ 3926102
      http://www.meetup.com/DataKind-NYC/" 4300032
      http://www.meetup.com/DataKind-UK/" 7975692
      http://www.meetup.com/Data-for-Good-Calgary/" 11057822
      http://www.meetup.com/DataforGood-Montreal/ 11073962
      http://www.meetup.com/DataKind-DUB/ 11120692
      http://www.meetup.com/Brussels-Data-Science-Community-Meetup/ 12977072
      http://www.meetup.com/DataKind-DC/ 16394282
      http://www.meetup.com/DataKind-SG/ 16412132
      http://www.meetup.com/DataKind-Bangalore/ 16412292
      http://www.meetup.com/Data-for-Good-FR/ 18259255
   */
  class DatalookMeetupAPi
  {

    const DOMAIN = "api.meetup.com";
    const METHOD_GROUPS = "2/groups";
    const METHOD_EVENTS = "2/events";

    //@Deprecated use $_key dynamic private attribute instead.
    const KEY = "6172502d70155707241395b42137b45";
    //@Deprecated use $_groups_ids dynamic private attribute instead.
    const ID_GROUPS = "3926102,4300032,7975692,11057822,11073962,11120692,12977072,16394282,16412132,16412292,18259255";

    private $_url;
    private $_results;
    private $_logs;
    private $_key;
    private $_groups_ids;

    public function __construct()
    {
      add_action('init', array(&$this, 'getBoardInfo'));
   #   add_action( 'wp_enqueue_scripts', array(&$this, 'enqueueScript') );
   #   add_action('init', array(&$this, 'log2'));
      add_shortcode('render_table_meetup_datalook', array(&$this, 'renderTable'));

      $this->api_key = get_option("api_key");
      $this->groups_ids = get_option("groups_ids");

    }

    public function log2()
    {
      echo $this->getUrl(SELF::METHOD_GROUPS, "&group_id=" . urlencode($this->groups_ids));
    }

    public function enqueueScript()
    {
      wp_register_script('custom-script', plugins_url( '/js/custom-script.js', __FILE__ ));
      wp_enqueue_script( 'custom-script' );
    }

    private function getUrl($method, $params)
    {
      return "http://" . SELF::DOMAIN . DIRECTORY_SEPARATOR . $method .
      DIRECTORY_SEPARATOR . "?key=" . $this->api_key . $params;
    }

    private function getNumberOfEvents() {

      $url_events = $this->getUrl(SELF::METHOD_EVENTS, "&group_id=" . urlencode($this->groups_ids));
      $info_events = wp_remote_get($url_events);
      $events = json_decode($info_events["body"])->results;

      $result = [];
      foreach ($events as $item) {
        $result[$item->group->id] = $result[$item->group->id] + 1;
      }

      return $result;

    }


    public function getBoardInfo()
    {
      $url_groups = $this->getUrl(SELF::METHOD_GROUPS, "&group_id=" . urlencode($this->groups_ids));
      $info_groups = wp_remote_get($url_groups);
      if ($info_groups["response"][code] != 200) {
        print_r("warning. at least one the Ids on meetup plugin is not valid. nothing will show.");
        return false;
      };
      $groups = json_decode($info_groups["body"])->results;
      $events = $this->getNumberOfEvents();

      foreach ($groups as $item) {

        $tmpArray["groups_name"] = $item->name;
        $tmpArray["groups_link"] = $item->link;
        $tmpArray["groups_members"] = $item->members;

        $tmpArray["events_number"] = 0;
        if ($events[$item->id] != null) {
          $tmpArray["events_number"] =  $events[$item->id];
        }

        $this->results[] = $tmpArray;

      }

    }

    public function renderTable()
    {
      if (!empty($this->results)) {

        $html = "<table><th>Name</th><th>Members</th><th>Events</th>";
        foreach ( $this->results as $item ) {

          $html .= "<tr><td><a href=\"" . $item["groups_link"] . "\" target=\"_blank\">" . $item["groups_name"] . "</td>
          <td>" . $item["groups_members"] . "</td>
          <td> " . $item["events_number"] . "</td></tr>";
        }
        $html = $html . "</table>";
      }

      return $html;

    }
  }

}