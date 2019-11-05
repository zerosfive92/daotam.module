<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

//require_once('models.inc.php');

class PrliUtils {
  /** Okay I realize that Percentagize isn't really a word but
    * this is so that the values we have will work with google
    * charts.
    */
  public function percentagizeArray($data,$max_value) {
    $new_data = array();
    foreach($data as $point) {
      if( $max_value > 0 ) {
        $new_data[] = $point / $max_value * 100;
      }
      else {
        $new_data[] = 0;
      }
    }
    return $new_data;
  }

  public function getTopValue($values_array) {
    rsort($values_array);
    return $values_array[0];
  }

  public function getFirstClickDate() {
    global $wpdb;

    $clicks_table = $wpdb->prefix . "prli_clicks";
    $query = "SELECT created_at FROM $clicks_table ORDER BY created_at LIMIT 1";
    $first_click = $wpdb->get_var($query);

    if(isset($first_click)) {
      return strtotime($first_click);
    }
    else {
      return null;
    }
  }

  public function getMonthsArray() {
    global $wpdb;
    global $prli_click;

    $months = array();
    $year = date("Y");
    $month = date("m");
    $current_timestamp = time();
    $current_month_timestamp = mktime(0, 0, 0, date("m", $current_timestamp), 1, date("Y", $current_timestamp));

    $clicks_table = $prli_click->tableName();
    $first_click = $wpdb->get_var("SELECT created_at FROM $clicks_table ORDER BY created_at LIMIT 1;");
    $first_timestamp = ((empty($first_click))?$current_timestamp:strtotime($first_click));
    $first_date = mktime(0, 0, 0, date("m", $first_timestamp), 1, date("Y", $first_timestamp));

    while($current_month_timestamp >= $first_date) {
      $months[] = $current_month_timestamp;
      if(date("m") == 1) {
        $current_month_timestamp = mktime(0, 0, 0, 12, 1, date("Y", $current_month_timestamp)-1);
      }
      else {
        $current_month_timestamp = mktime(0, 0, 0, date("m", $current_month_timestamp)-1, 1, date("Y", $current_month_timestamp));
      }
    }
    return $months;
  }

  // For Pagination
  public function getLastRecordNum($r_count,$current_p,$p_size) {
    return (($r_count < ($current_p * $p_size))?$r_count:($current_p * $p_size));
  }

  // For Pagination
  public function getFirstRecordNum($r_count,$current_p,$p_size) {
    if($current_p == 1) {
      return 1;
    }
    else {
      return ($this->getLastRecordNum($r_count,($current_p - 1),$p_size) + 1);
    }
  }

  public static function get_url_from_slug( $slug ) {
    global $prli_blogurl;
    return $prli_blogurl . self::get_permalink_pre_slug_uri() . $slug;
  }

  public static function debug_log($message) {
    if(defined('WP_DEBUG') && WP_DEBUG) {
      error_log(sprintf(__('*** Pretty Links Debug: %s', 'pretty-link'), $message));
    }
  }

  public static function slugIsAvailable( $slug, $id = '' ) {
    global $wp_rewrite, $prli_link;
    $url = self::get_url_from_slug($slug);

    if(empty($slug)) {
      self::debug_log('SlugIsAvailable: $slug was empty');
      return false;
    }

    // Check Filepaths
    $filepath = ABSPATH . $slug;
    if( file_exists($filepath) ) {
      self::debug_log('SlugIsAvailable: There\'s already a file at: ' . $filepath);
      return false;
    }

    // Check other Pretty Links
    if( empty($id) ) {
      $this_link = $prli_link->getOneFromSlug( $slug );
      if( $this_link != false ) {
        self::debug_log('SlugIsAvailable: There\'s already pretty link for slug: ' . $slug);
        return false;
      }
    }
    else {
      $this_link = $prli_link->getOne( $id );
      $slug_link = $prli_link->getOneFromSlug( $slug );

      // check to see if the link is just being updated with the same slug
      if( !empty($this_link) && !empty($slug_link) && $this_link->id === $slug_link->id ) {
        return true;
      }
    }

    // Check Rewrite Rules
    $rules = $wp_rewrite->wp_rewrite_rules();
    //self::debug_log('SlugIsAvailable: Rewrite Rules: ' . self::object_to_string($rules));
    //unset($rules['([^/]+)(/[0-9]+)?/?$']); //Pretty sure this may nullify all the work below, but it's causing too many false positives somehow

    foreach( $rules as $pattern => $query ) {
      // just looking for the beginning of the pattern
      if(isset($pattern[0]) && $pattern[0] != '^') { $pattern = '^' . $pattern; }

      if( preg_match( "!{$pattern}!", $slug, $matches ) ) {
        // Got a match.
        // Trim the query of everything up to the '?'. (copied from url_to_postid)
        $query = preg_replace("!^.+\?!", '', $query);

        // Substitute the substring matches into the query. (copied from url_to_postid)
        $query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

        // Filter out non-public query vars (copied from url_to_postid)
        global $wp;
        parse_str( $query, $query_vars );
        $query = array();

        foreach ( (array) $query_vars as $key => $value ) {
          if ( in_array( $key, $wp->public_query_vars ) ) {
            $query[$key] = $value;
            if ( isset( $post_type_query_vars[$key] ) ) {
              $query['post_type'] = $post_type_query_vars[$key];
              $query['name'] = $value;
            }
          }
        }

        // Do this query bro
        $wpq = new WP_Query( $query );

        // If we have a resolved post (applicable for tag, category & author pages too) then it's taken
        if( isset($wpq->post) && isset($wpq->posts) && !empty($wpq->posts) ) {
          self::debug_log('SlugIsAvailable: The path resolved to a post in wp rewrite rules for pattern: /'.$pattern.'/ query: '.self::object_to_string($query));
          self::debug_log(self::object_to_string($wpq->post));
          return false;
        }
      }
    }

    return true;
  }

  public static function object_to_string($obj) {
    ob_start();
    print_r($obj);
    return ob_get_clean();
  }

  public function php_get_browsercap_ini() {
    // Since it's a fairly expensive proposition to load the ini file
    // let's make sure we only do it once
    static $browsecap_ini;

    if(!isset($browsecap_ini)) {
      if( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {
        $browsecap_ini = parse_ini_file( PRLI_VENDOR_LIB_PATH . '/browscap/php_browscap.ini', true, INI_SCANNER_RAW );
      }
      else {
        $browsecap_ini = parse_ini_file( PRLI_VENDOR_LIB_PATH . '/browscap/php_browscap.ini', true );
      }
    }

    return $browsecap_ini;
  }

  /* Needed because we don't know if the target uesr will have a browsercap file installed
     on their server ... particularly in a shared hosting environment this is difficult
  */
  public function php_get_browser($agent = NULL) {
    $agent=$agent?$agent:$_SERVER['HTTP_USER_AGENT'];
    $yu=array();
    $q_s=array("#\.#","#\*#","#\?#");
    $q_r=array("\.",".*",".?");
    $brows = $this->php_get_browsercap_ini();

    if(empty($agent)) { return array(); }

    //Do a bit of caching here
    static $hu;
    if(!isset($hu)) {
      $hu = array();
    }
    else {
      return $hu;
    }

    if(!empty($brows) and $brows and is_array($brows)) {
      foreach($brows as $k=>$t) {
        if(fnmatch($k,$agent)) {
          $yu['browser_name_pattern']=$k;
          $pat=preg_replace($q_s,$q_r,$k);
          $yu['browser_name_regex']=strtolower("^$pat$");
          foreach($brows as $g=>$r) {
            if($t['Parent']==$g) {
              foreach($brows as $a=>$b) {
                if(isset($r['Parent']) && $r['Parent']==$a) {
                  $yu=array_merge($yu,$b,$r,$t);
                  foreach($yu as $d=>$z) {
                    $l=strtolower($d);
                    $hu[$l]=$z;
                  }
                }
              }
            }
          }

          break;
        }
      }
    }

    return $hu;
  }

  // This is where the magic happens!
  public function track_link($slug,$values) {
    global $wpdb, $prli_click, $prli_options, $prli_link, $plp_update;

    $query = "SELECT * FROM ".$prli_link->table_name." WHERE slug='$slug' LIMIT 1";
    $pretty_link = $wpdb->get_row($query);
    $pretty_link_target = apply_filters( 'prli_target_url', array( 'url' => $pretty_link->url, 'link_id' => $pretty_link->id, 'redirect_type' => $pretty_link->redirect_type ) );
    $prli_edition = ucwords(preg_replace('/-/', ' ', PRLI_EDITION));

    // Error out when url is blank
    if($pretty_link->redirect_type != 'pixel' && (!isset($pretty_link_target['url']) || empty($pretty_link_target['url']))) {
      return false;
    }

    $pretty_link_url = $pretty_link_target['url'];
    $track_me = apply_filters('prli_track_link', $pretty_link->track_me);

    if(isset($track_me) and !empty($track_me) and $track_me) {
      $first_click = 0;
      $click_ip =         $this->get_current_client_ip();
      $click_referer =    isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
      $click_uri =        isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
      $click_user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';

      //Set Cookie if it doesn't exist
      $cookie_name = 'prli_click_' . $pretty_link->id;

      //Used for unique click tracking
      $cookie_expire_time = time()+60*60*24*30; // Expire in 30 days

      if(!isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name,$slug,$cookie_expire_time,'/');
        $first_click = 1;
      }

      // Set the visitor cookie now
      $visitor_cookie = 'prli_visitor';
      //Used for visitor activity
      $visitor_cookie_expire_time = time()+60*60*24*365; // Expire in 1 year

      // Retrieve / Generate visitor id
      if(!isset($_COOKIE[$visitor_cookie])) {
        $visitor_uid = $prli_click->generateUniqueVisitorId();
        setcookie($visitor_cookie,$visitor_uid,$visitor_cookie_expire_time,'/');
      }
      else {
        $visitor_uid = $_COOKIE[$visitor_cookie];
      }

      if(isset($prli_options->extended_tracking) and $prli_options->extended_tracking == 'extended') {
        $click_browser = $this->php_get_browser();
        $click_host = gethostbyaddr($click_ip);
      }
      else {
        $click_browser = array( 'browser' => '', 'version' => '', 'platform' => '', 'crawler' => '' );
        $click_host = '';
      }

      // If this is flagged as a dup then don't track this link
      // This is to prevent duplicate clicks being recorded due to things like
      // Browser pre-fetching especially, which is no longer detectable using
      // HTTP headers so we have to resort to this not-as-accurate approach

      $visitor_uid_store_key = "{$visitor_cookie}_{$pretty_link->id}_{$visitor_uid}";
      $visitor_uid_store_time = 10; // 10 seconds
      if(!($visitor_uid_store = get_transient($visitor_uid_store_key))) {
        set_transient($visitor_uid_store_key, $visitor_uid, $visitor_uid_store_time);

        if($prli_options->extended_tracking != 'count') {
          //Record Click in DB
          $insert_str = "INSERT INTO {$prli_click->table_name} (link_id,vuid,ip,browser,btype,bversion,os,referer,uri,host,first_click,robot,created_at) VALUES (%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%d,%d,NOW())";
          $insert = $wpdb->prepare(
            $insert_str,
            $pretty_link->id,
            $visitor_uid,
            $click_ip,
            $click_user_agent,
            (isset($click_browser['browser']) ? $click_browser['browser'] : ''),
            (isset($click_browser['version']) ? $click_browser['version'] : ''),
            (isset($click_browser['platform']) ? $click_browser['platform'] : ''),
            $click_referer,
            $click_uri,
            $click_host,
            $first_click,
            $this->this_is_a_robot($click_user_agent,$click_browser)
          );

          $results = $wpdb->query( $insert );

          do_action('prli_record_click',array('link_id' => $pretty_link->id, 'click_id' => $wpdb->insert_id, 'url' => $pretty_link_url));
        }
        else {
          global $prli_link_meta;
          $exclude_ips = explode(",", $prli_options->prli_exclude_ips);
          if(!in_array($click_ip, $exclude_ips) and !$this->this_is_a_robot($click_user_agent,$click_browser)) {
            $clicks  = $prli_link_meta->get_link_meta($pretty_link->id, 'static-clicks', true);
            $clicks = (empty($clicks) or $clicks === false)?0:$clicks;
            $prli_link_meta->update_link_meta($pretty_link->id, 'static-clicks', $clicks+1);

            if($first_click) {
              $uniques  = $prli_link_meta->get_link_meta($pretty_link->id, 'static-uniques', true);
              $uniques = (empty($uniques) or $uniques === false)?0:$uniques;
              $prli_link_meta->update_link_meta($pretty_link->id, 'static-uniques', $uniques+1);
            }
          }
        }
      }
    }

    $param_string = '';
    if( isset($pretty_link->param_forwarding) && !empty($pretty_link->param_forwarding) &&
        $pretty_link->param_forwarding!='off' && isset( $values ) && count( $values ) >= 1 ) {
      $parray = explode( '?', $_SERVER['REQUEST_URI'] );

      if(isset($parray[1])) {
        $param_string = (preg_match("#\?#", $pretty_link_url)?"&":"?") . $parray[1];
      }

      $param_string = preg_replace( array("#%5B#i","#%5D#i"), array("[","]"), $param_string );

      $param_string = apply_filters('prli_redirect_params', $param_string);
    }

    if(isset($pretty_link->nofollow) and $pretty_link->nofollow) {
      header("X-Robots-Tag: noindex, nofollow", true);
    }

    //This action replaces custom variable parameters
    do_action_ref_array('prli_before_redirect', array(&$pretty_link_url, &$param_string, $_GET));

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 07 Jul 1777 07:07:07 GMT"); // Battle of Hubbardton
    header("X-Redirect-Powered-By: {$prli_edition} " . PRLI_VERSION . " http://prettylink.com");

    self::include_pluggables('wp_redirect');
    switch($pretty_link->redirect_type) {
      case '301':
        wp_redirect("{$pretty_link_url}{$param_string}", 301);
        exit;
      case '307':
        wp_redirect("{$pretty_link_url}{$param_string}", 307);
        exit;
      default:
        if($pretty_link->redirect_type == '302' || !$plp_update->is_installed()) {
          wp_redirect("{$pretty_link_url}{$param_string}", 302);
          exit;
        }
        else {
          do_action('prli_issue_cloaked_redirect', $pretty_link->redirect_type, $pretty_link, $pretty_link_url, $param_string);
        }
    }

    return true;
  }

  public function get_current_client_ip() {
    $ipaddress = (isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:'';

    if(isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '127.0.0.1') {
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1') {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'] != '127.0.0.1') {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    }
    elseif(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'] != '127.0.0.1') {
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    elseif(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'] != '127.0.0.1') {
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    }

    $ips = explode(',', $ipaddress);
    if(isset($ips[1])) {
      $ipaddress = $ips[0]; //Fix for flywheel
    }

    return $ipaddress;
  }

  public function get_custom_forwarding_rule($param_struct) {
    $param_struct = preg_replace('#%.*?%#','(.*?)',$param_struct);
    return preg_replace('#\(\.\*\?\)$#','(.*)',$param_struct); // replace the last one with a greedy operator
  }

  public function get_custom_forwarding_params($param_struct, $start_index = 1) {
    preg_match_all('#%(.*?)%#', $param_struct, $matches);

    $param_string = '';
    $match_index = $start_index;

    for($i = 0; $i < count($matches[1]); $i++) {
      if($i == 0 and $start_index == 1)
        $param_string .= "?";
      else
        $param_string .= "&";

      $param_string .= $matches[1][$i] . "=$$match_index";
      $match_index++;
    }

    return $param_string;
  }

  public function decode_custom_param_str($param_struct, $uri_string) {
    // Get the structure matches (param names)
    preg_match_all('#%(.*?)%#', $param_struct, $struct_matches);

    // Get the uri matches (param values)
    $match_str = '#'.$this->get_custom_forwarding_rule($param_struct).'#';
    preg_match($match_str, $uri_string, $uri_matches);

    $param_array = array();
    for($i = 0; $i < count($struct_matches[1]); $i++) {
      $param_array[$struct_matches[1][$i]] = $uri_matches[$i+1];
    }

    return $param_array;
  }

  // Detects whether an array is a true numerical array or an
  // associative array (or hash).
  public function prli_array_type($item) {
    $array_type = 'unknown';

    if(is_array($item)) {
      $array_type = 'array';

      foreach($item as $key => $value) {
        if(!is_numeric($key)) {
          $array_type = 'hash';
          break;
        }
      }
    }

    return $array_type;
  }

  // Get the timestamp of the start date
  public function get_start_date($values,$min_date = '') {
    // set default to 30 days ago
    if(empty($min_date)) {
      $min_date = 30;
    }

    if(!empty($values['sdate'])) {
      $sdate = explode("-",$values['sdate']);
      $start_timestamp = mktime(0,0,0,$sdate[1],$sdate[2],$sdate[0]);
    }
    else {
      $start_timestamp = time()-60*60*24*(int)$min_date;
    }

    return $start_timestamp;
  }

  // Get the timestamp of the end date
  public function get_end_date($values)
  {
    if(!empty($values['edate']))
    {
      $edate = explode("-",$values['edate']);
      $end_timestamp = mktime(0,0,0,$edate[1],$edate[2],$edate[0]);
    }
    else
      $end_timestamp = time();

    return $end_timestamp;
  }

  public function prepend_and_or_where( $starts_with = ' WHERE', $where = '' )
  {
    return (( $where == '' )?'':$starts_with . $where);
  }

  public function uninstall_pro()
  {
    $plp_path = PRLI_PATH . '/pro';

    // unlink pro directory
    $this->delete_dir($plp_path);

    delete_option( 'prlipro_activated' );
    delete_option( 'prlipro_username' );
    delete_option( 'prlipro_password' );
    delete_option( 'prlipro-credentials' );

    // Yah- I just leave the pro database tables & data hanging
    // around in case you want to re-install it at some point
  }

  public function should_install_pro_db() {
    global $plp_db_version;
    $old_pro_db_version = get_option('prlipro_db_version');

    if($plp_db_version != $old_pro_db_version) { return true; }

    return false;
  }

  public function install_pro_db() {
    global $wpdb, $plp_db_version;

    $upgrade_path = ABSPATH . 'wp-admin/includes/upgrade.php';
    require_once($upgrade_path);

    // Pretty Links Pro Tables
    $keywords_table         = "{$wpdb->prefix}prli_keywords";
    $post_keywords_table    = "{$wpdb->prefix}prli_post_keywords";
    $post_urls_table        = "{$wpdb->prefix}prli_post_urls";
    $reports_table          = "{$wpdb->prefix}prli_reports";
    $report_links_table     = "{$wpdb->prefix}prli_report_links";
    $link_rotations_table   = "{$wpdb->prefix}prli_link_rotations";
    $clicks_rotations_table = "{$wpdb->prefix}prli_clicks_rotations";

    // This was introduced in WordPress 3.5
    // $char_col = $wpdb->get_charset_collate(); //This doesn't work for most non english setups
    $char_col = "";
    $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

    if(isset($collation->Collation)) {
      $charset = explode('_', $collation->Collation);

      if(is_array($charset) && count($charset) > 1) {
        $charset = $charset[0]; //Get the charset from the collation
        $char_col = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
      }
    }

    //Fine we'll try it your way this time
    if(empty($char_col)) { $char_col = $wpdb->get_charset_collate(); }

    //Fix for large indexes
    $wpdb->query("SET GLOBAL innodb_large_prefix=1");

    /* Create/Upgrade Keywords Table */
    $sql = "
      CREATE TABLE {$keywords_table} (
        id int(11) NOT NULL auto_increment,
        text varchar(255) NOT NULL,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY link_id (link_id),
        KEY text (text)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Keywords Table */
    $sql = "
      CREATE TABLE {$post_keywords_table} (
        id int(11) NOT NULL auto_increment,
        keyword_id int(11) NOT NULL,
        post_id int(11) NOT NULL,
        PRIMARY KEY  (id),
        KEY keyword_id (keyword_id),
        KEY post_id (post_id),
        UNIQUE KEY post_keyword_index (keyword_id,post_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade URLs Table */
    $sql = "
      CREATE TABLE {$post_urls_table} (
        id int(11) NOT NULL auto_increment,
        url_id int(11) NOT NULL,
        post_id int(11) NOT NULL,
        PRIMARY KEY  (id),
        KEY url_id (url_id),
        KEY post_id (post_id),
        UNIQUE KEY post_url_index (url_id,post_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Reports Table */
    $sql = "
      CREATE TABLE {$reports_table} (
        id int(11) NOT NULL auto_increment,
        name varchar(255) NOT NULL,
        goal_link_id int(11) default NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY goal_link_id (goal_link_id),
        KEY name (name)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Reports Table */
    $sql = "
      CREATE TABLE {$report_links_table} (
        id int(11) NOT NULL auto_increment,
        report_id int(11) NOT NULL,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY report_id (report_id),
        KEY link_id (link_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Link Rotations Table */
    $sql = "
      CREATE TABLE {$link_rotations_table} (
        id int(11) NOT NULL auto_increment,
        url varchar(255) default NULL,
        weight int(11) default 0,
        r_index int(11) default 0,
        link_id int(11) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY link_id (link_id),
        KEY url (url),
        KEY weight (weight),
        KEY r_index (r_index)
      ) {$char_col};
    ";

    dbDelta($sql);

    /* Create/Upgrade Clicks / Rotations Table */
    $sql = "
      CREATE TABLE {$clicks_rotations_table} (
        id int(11) NOT NULL auto_increment,
        click_id int(11) NOT NULL,
        link_id int(11) NOT NULL,
        url text NOT NULL,
        PRIMARY KEY  (id),
        KEY click_id (click_id),
        KEY link_id (link_id)
      ) {$char_col};
    ";

    dbDelta($sql);

    /***** SAVE DB VERSION *****/
    update_option('prlipro_db_version', $plp_db_version);
  }

  // be careful with this one -- I use it to forceably reinstall pretty link pro
  public function delete_dir($dir) {
    if (!file_exists($dir)) {
      return true;
    }

    if (!is_dir($dir)) {
      return unlink($dir);
    }

    foreach (scandir($dir) as $item)
    {
      if ($item == '.' || $item == '..')
        continue;

      if (!$this->delete_dir($dir.DIRECTORY_SEPARATOR.$item))
        return false;
    }

    return rmdir($dir);
  }

  // Used in the install procedure to migrate database columns
  public function migrate_before_db_upgrade() {
    global $prli_options, $prli_link, $prli_click, $wpdb;
    $db_version = (int)get_option('prli_db_version');

    if(!$db_version)
      return;

    // Migration for version 1 of the database
    if($db_version and $db_version < 1)
    {
      $query = "SELECT * from {$prli_link->table_name}";
      $links = $wpdb->get_results($query);
      $query_str = "UPDATE {$prli_link->table_name} SET redirect_type=%s WHERE id=%d";

      foreach($links as $link)
      {
        if(isset($link->track_as_img) and $link->track_as_img)
        {
          $query = $wpdb->prepare($query_str, 'pixel', $link->id);
          $wpdb->query($query);
        }
        else if(isset($link->use_prettybar) and $link->use_prettybar)
        {
          $query = $wpdb->prepare($query_str, 'prettybar', $link->id);
          $wpdb->query($query);
        }
        else if(isset($link->use_ultra_cloak) and $link->use_ultra_cloak)
        {
          $query = $wpdb->prepare($query_str, 'cloak', $link->id);
          $wpdb->query($query);
        }
      }

      $query = "ALTER TABLE {$prli_link->table_name} DROP COLUMN track_as_img, DROP COLUMN use_prettybar, DROP COLUMN use_ultra_cloak, DROP COLUMN gorder";
      $wpdb->query($query);
    }

    if($db_version and $db_version < 2)
    {
      unset($prli_options->prli_exclude_ips);
      unset($prli_options->prettybar_image_url);
      unset($prli_options->prettybar_background_image_url);
      unset($prli_options->prettybar_color);
      unset($prli_options->prettybar_text_color);
      unset($prli_options->prettybar_link_color);
      unset($prli_options->prettybar_hover_color);
      unset($prli_options->prettybar_visited_color);
      unset($prli_options->prettybar_title_limit);
      unset($prli_options->prettybar_desc_limit);
      unset($prli_options->prettybar_link_limit);

      // Save the posted value in the database
      //update_option( 'prli_options', $prli_options );
      $prli_options->store();
    }

    // Modify the tables so they're UTF-8
    if($db_version and $db_version < 3)
    {
      $char_col = '';
      if( $wpdb->has_cap( 'collation' ) )
      {
        if( !empty($wpdb->charset) )
          $char_col = "CONVERT TO CHARACTER SET $wpdb->charset";
        if( !empty($wpdb->collate) )
          $char_col .= " COLLATE $wpdb->collate";
      }

      if(!empty($char_col))
      {
        $prli_table_names = array( "{$wpdb->prefix}prli_groups",
                                   "{$wpdb->prefix}prli_clicks",
                                   "{$wpdb->prefix}prli_links",
                                   "{$wpdb->prefix}prli_link_metas",
                                   "{$wpdb->prefix}prli_tweets",
                                   "{$wpdb->prefix}prli_keywords",
                                   "{$wpdb->prefix}prli_reports",
                                   "{$wpdb->prefix}prli_report_links",
                                   "{$wpdb->prefix}prli_link_rotations",
                                   "{$wpdb->prefix}prli_clicks_rotations" );

        foreach($prli_table_names as $prli_table_name)
        {
          $query = "ALTER TABLE {$prli_table_name} {$char_col}";
          $wpdb->query($query);
        }
      }
    }

    if($db_version and $db_version < 8)
    {
      // Install / Upgrade Pretty Links Pro
      $plp_username = get_option( 'prlipro_username' );
      $plp_password = get_option( 'prlipro_password' );

      if( !empty($plp_username) and !empty($plp_password) )
      {
        $creds = array('username' => $plp_username,
                       'password' => $plp_password);
        update_option('prlipro-credentials', $creds);
      }
    }

    // Hiding pretty link custom fields
    if($db_version and $db_version < 10) {
      $query_str = "UPDATE {$wpdb->postmeta} SET meta_key=%s WHERE meta_key=%s";

      $query = $wpdb->prepare($query_str, '_pretty-link', 'pretty-link');
      $wpdb->query($query);

      $query = $wpdb->prepare($query_str, '_prli-keyword-cached-content', 'prli-keyword-cached-content');
      $wpdb->query($query);

      $query = $wpdb->prepare($query_str, '_prlipro-post-options', 'prlipro-post-options');
      $wpdb->query($query);
    }
  }

  public function migrate_after_db_upgrade() {
    global $prli_options, $prli_link, $prli_link_meta, $prli_click, $wpdb;
    $db_version = (int)get_option('prli_db_version');

    if(empty($db_version)) { return; }

    if($db_version and $db_version < 5) {
      // Migrate pretty-link-posted-to-twitter
      $query = "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key=%s";
      $query = $wpdb->prepare($query,'pretty-link-posted-to-twitter');
      $posts_posted = $wpdb->get_results($query);

      foreach($posts_posted as $postmeta) {
        if($postmeta->meta_value == '1') {
          $link_id = PrliUtils::get_prli_post_meta($postmeta->post_id,'pretty-link',true);
          $prli_link_meta->update_link_meta($link_id,'pretty-link-posted-to-twitter','1');
        }
      }

      // Cleanup
      $query = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key=%s OR meta_key=%s OR meta_key=%s OR meta_key=%s";
      $query = $wpdb->prepare($query,'pretty-link-posted-to-twitter','pretty-link-tweet-count','pretty-link-tweet-last-update','prli-keyword-replacement-count');
      $results = $wpdb->query($query);

      $query = "DELETE FROM {$prli_link_meta->table_name} WHERE meta_key=%s";
      $query = $wpdb->prepare($query,'prli-url-aliases');
      $results = $wpdb->query($query);
    }
  }

  public function this_is_a_robot($browser_ua,$browsecap,$header='')
  {
    $click = new PrliClick();
    $click->browser = $browser_ua;
    $click->btype = (isset($browsecap['browser']) ? $browsecap['browser'] : '');
    return $this->is_robot($click, $browsecap, $header);
  }

  public function is_robot($click,$browsecap,$header='')
  {
    global $prli_utils, $prli_click, $prli_options;
    $ua_string = trim(rawurldecode($click->browser));
    $btype = trim($click->btype);

    // Yah, if the whole user agent string is missing -- wtf?
    if(empty($ua_string))
      return 1;

    // If we're doing extended tracking and the Browser type
    // was unidentifiable then it's most likely a bot
    if( isset($prli_options->extended_tracking) and
        $prli_options->extended_tracking == 'extended' and
        empty($btype) )
      return 1;

    // Some bots actually say they're bots right up front let's get rid of them asap
    if(preg_match("#(bot|spider|crawl)#", strtolower($ua_string)))
      return 1;

    $crawler = (isset($browsecap['crawler']) && $browsecap['crawler'] == 'true');

    // If php_browsecap tells us its a bot, let's believe him
    if($crawler)
      return 1;

    return 0;
  }

  public static function get_permalink_pre_slug_uri($force=false,$trim=false)
  {
    global $prli_options;

    if($force or $prli_options->link_prefix)
    {
      preg_match('#^([^%]*?)%#', get_option('permalink_structure'), $struct);

      $pre_slug_uri = '';
      if(isset($struct[1])) {
        $pre_slug_uri = $struct[1];
      }

      if($trim)
      {
        $pre_slug_uri = trim($pre_slug_uri);
        $pre_slug_uri = preg_replace('#^/#','',$pre_slug_uri);
        $pre_slug_uri = preg_replace('#/$#','',$pre_slug_uri);
      }

      return $pre_slug_uri;
    }
    else
      return '/';
  }

  public static function get_permalink_pre_slug_regex()
  {
    $pre_slug_uri = PrliUtils::get_permalink_pre_slug_uri(true);

    if(empty($pre_slug_uri))
      return '/';
    else
      return "{$pre_slug_uri}|/";
  }

  public function rewriting_on()
  {
    $permalink_structure = get_option('permalink_structure');

    return ($permalink_structure and !empty($permalink_structure));
  }

  public static function get_prli_post_meta($post_id, $key, $single=false)
  {
    if( isset($post_id) and !empty($post_id) and
        $post_id and is_numeric($post_id) )
      return get_post_meta($post_id, $key, $single);
    else
      return false;
  }

  public static function update_prli_post_meta($post_id, $meta_key, $meta_value)
  {
    if( isset($post_id) and !empty($post_id) and
        $post_id and is_numeric($post_id) )
      return update_post_meta($post_id, $meta_key, $meta_value);
    else
      return false;
  }

  public function delete_prli_post_meta($post_id, $key)
  {
    if( isset($post_id) and !empty($post_id) and
        $post_id and is_numeric($post_id) )
      return delete_post_meta($post_id, $key);
    else
      return false;
  }

  /** Gets rid of any pretty link postmetas created without a post_id **/
  public function clear_unknown_post_metas()
  {
    global $wpdb;

    $query = "SELECT count(*) FROM {$wpdb->postmeta} WHERE ( meta_key LIKE 'prli%' OR meta_key LIKE 'pretty-link%' OR meta_key LIKE '_prli%' OR meta_key LIKE '_pretty-link%' ) AND post_id=0";
    $count = $wpdb->get_var($query);

    if($count)
    {
      $query = "DELETE FROM {$wpdb->postmeta} WHERE ( meta_key LIKE 'prli%' OR meta_key LIKE 'pretty-link%' OR meta_key LIKE '_prli%' OR meta_key LIKE '_pretty-link%' ) AND post_id=0";
      $wpdb->query($query);
    }
  }

  public static function gen_random_string($length = 4)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';
    $max_index = strlen($characters) - 1;

    for($p = 0; $p < $length; $p++)
      $string .= $characters[mt_rand(0, $max_index)];

    return $string;
  }

  public static function get_page_title($url, $slug='') {
    $title = '';
    $wp_http = new WP_Http;
    $result = $wp_http->request( $url, array( 'sslverify' => false ) );

    if(!$result or is_a($result, 'WP_Error') or !isset($result['body'])) {
      return apply_filters('prli-get-page-title-return-slug', $slug, $url);
    }

    $data = $result['body'];

    // Look for <title>(.*?)</title> in the text
    if($data and preg_match('#<title>[\s\n\r]*?(.*?)[\s\n\r]*?</title>#im', $data, $matches)) {
      $title = trim($matches[1]);
    }

    //Attempt to covert cyrillic and other weird shiz to UTF-8 - if it fails we'll just return the slug next
    if(extension_loaded('mbstring') && function_exists('iconv')) {
      $title = iconv(mb_detect_encoding($title, mb_detect_order(), true), "UTF-8", $title);
    }

    if(empty($title) or !$title) {
      return apply_filters('prli-get-page-title-return-slug', $slug, $url);
    }

    return apply_filters('prli-get-page-title', $title, $url, $slug);
  }

  public static function is_date($str) {
    if(!is_string($str)) { return false; }
    $d = strtotime($str);
    return ($d !== false);
  }

  public static function is_url($str) {
    //For now we're not going to validate this - there's too many possible protocols/schemes to validate now
    return true;

    // This uses the @diegoperini URL matching regex adapted for PHP from https://gist.github.com/dperini/729294
    // return preg_match('_^(?::/?/?)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$_iuS', $str);

    //Let's see how PHP's built in validator does instead?
    //return apply_filters('prli_is_valid_url', (filter_var($str, FILTER_VALIDATE_URL) !== FALSE), $str);
  }

  public static function is_email($str) {
    return preg_match('/[\w\d._%+-]+@[\w\d.-]+\.[\w]{2,4}/', $str);
  }

  public static function is_phone($str) {
    return preg_match('/\(?\d{3}\)?[- ]\d{3}-\d{4}/', $str);
  }

  public static function get_plp_permalink($link) {
    global $prli_blogurl;

    $struct = PrliUtils::get_permalink_pre_slug_uri();

    if(isset($link->slug))
      return "{$prli_blogurl}{$struct}{$link->slug}";
    else
      return false;
  }

  public static function browser_image($browser) {
    $browser = strtolower($browser);

    $browser_images = array(
      'android' => 'android_32x32.png',
      'android webview' => 'android_32x32.png',
      'chrome' => 'chrome_32x32.png',
      'chromium' => 'chromium_32x32.png',
      'coast' => 'coast_32x32.png',
      //'default browser' => 'default_browser_32x32.png',
      //'defaultproperties' => 'defaultproperties_32x32.png',
      'edge' => 'edge_32x32.png',
      'fake ie' => 'fake_32x32.png',
      'firefox' => 'firefox_32x32.png',
      'ie' => 'ie_32x32.png',
      'opera' => 'opera_32x32.png',
      'safari' => 'safari_32x32.png',
    );

    if(isset($browser_images[$browser])) {
      return $browser_images[$browser];
    }

    return false;
  }

  public static function os_image($os) {
    $os = strtolower($os);

    $os_images = array(
      'android' => 'android_32x32.png',
      'linux' => 'linux_32x32.png',
      'macosx' => 'macos_32x32.png',
      'win10' => 'win8_32x32.png',
      'win32' => 'winxp_32x32.png',
      'win7' => 'win8_32x32.png',
      'win8' => 'win8_32x32.png',
      'win8.1' => 'win8_32x32.png',
      'winnt' => 'winxp_32x32.png',
      'winvista' => 'winxp_32x32.png',
      'ios' => 'ios_32x32.png',
    );

    if(isset($os_images[$os])) {
      return $os_images[$os];
    }

    return false;
  }

  public static function current_page_url() {
    $pageURL = 'http';
    if( isset($_SERVER["HTTPS"]) ) {
      if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }

  public static function is_logged_in_and_current_user($user_id) {
    $current_user = self::get_currentuserinfo();

    return (self::is_user_logged_in() and (is_object($current_user) && $current_user->ID == $user_id));
  }

  public static function is_logged_in_and_an_admin() {
    return (self::is_user_logged_in() and self::is_admin());
  }

  public static function is_logged_in_and_a_subscriber() {
    return (self::is_user_logged_in() and self::is_subscriber());
  }

  public static function is_admin() {
    return self::current_user_can('manage_options');
  }

  public static function is_subscriber() {
    return self::current_user_can('subscriber');
  }

  // Checks to see that the user is authorized to use Pretty Link based on the minimum role
  public static function is_authorized() {
    global $plp_options;

    $prli_update = new PrliUpdateController();

    $role = 'manage_options';

    if($prli_update->is_installed_and_activated() && isset($plp_options) && isset($plp_options->min_role)) {
      $role = $plp_options->min_role;
    }

    return self::current_user_can($role);
  }

  public static function full_request_url() {
    $current_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $current_url .= $_SERVER["SERVER_NAME"];

    if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
      $current_url .= ":".$_SERVER["SERVER_PORT"];
    }

    $current_url .= $_SERVER["REQUEST_URI"];

    return $current_url;
  }

/* PLUGGABLE FUNCTIONS AS TO NOT STEP ON OTHER PLUGINS' CODE */
  public static function include_pluggables($function_name) {
    if(!function_exists($function_name)) {
      require_once(ABSPATH.WPINC.'/pluggable.php');
    }
  }

  public static function is_user_logged_in() {
    self::include_pluggables('is_user_logged_in');
    return is_user_logged_in();
  }

  public static function check_ajax_referer($slug,$param) {
    self::include_pluggables('check_ajax_referer');
    return check_ajax_referer($slug,$param);
  }

  public static function get_currentuserinfo() {
    self::include_pluggables('wp_get_current_user');
    $current_user = wp_get_current_user();

    if(isset($current_user->ID) && $current_user->ID > 0) {
      return $current_user;
    }
    else {
      return false;
    }
  }

  public static function get_current_user_id() {
    self::include_pluggables('wp_get_current_user');
    return get_current_user_id();
  }

  public static function current_user_can($role) {
    self::include_pluggables('wp_get_current_user');
    return current_user_can($role);
  }

  // Get new messages every 1/2 hour
  public static function get_main_message($message = '',$expiration = 43200) { //43200 = 12 hours
    global $plp_update;

    // Set the default message
    if(empty($message)) {
      $message = sprintf( __( 'Get started by %1$sadding a URL%2$s that you want to turn into a pretty link.<br/>' .
                              'Come back to see how many times it was clicked.' , 'pretty-link' ),
                          '<a href="' . admin_url( 'admin.php?page=add-new-pretty-link' ) . '">', '</a>' );
    }

    //Pro users don't want to be spammed
    if($plp_update->is_installed_and_activated()) { return $message; }

    $messages = get_site_transient('_prli_messages');

    // if the messages array has expired go back to the mothership
    if(!$messages) {
      $message_mothership = "https://s3.amazonaws.com/plpmessages/plp_messages.json";

      if(!class_exists('WP_Http')) {
        include_once(ABSPATH . WPINC . '/class-http.php');
      }

      $http = new WP_Http;
      $response = $http->request( $message_mothership );

      if( isset($response) and
          is_array($response) and // if response is an error then WP_Error will be returned
          isset($response['body']) and
          !empty($response['body'])) {
        $messages = json_decode($response['body']);
      }
      else {
        $messages = array($message);
      }

      set_site_transient("_prli_messages", $messages, $expiration);
    }

    if(empty($messages) or !$messages or !is_array($messages)) {
      return $message;
    }
    else {
      return $messages[array_rand($messages)];
    }
  }

  public static function get_post_content($post_id) {
    $post = get_post($post_id);

    $content = $post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);

    return $content;
  }

  public static function now() {
    return date('Y-m-d H:i:s');
  }

  public static function site_domain() {
    return preg_replace('#^https?://(www\.)?([^\?\/]*)#', '$2', home_url());
  }

  public static function is_prli_admin($user_id=null) {
    $prli_cap = 'install_plugins';

    if(empty($user_id)) {
      return self::current_user_can($prli_cap);
    }
    else {
      return user_can($user_id, $prli_cap);
    }
  }

  public static function http_status_codes() {
    return array(
      100 => 'Continue',
      101 => 'Switching Protocols',
      102 => 'Processing',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      207 => 'Multi-Status',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      306 => 'Switch Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      418 => 'I\'m a teapot',
      422 => 'Unprocessable Entity',
      423 => 'Locked',
      424 => 'Failed Dependency',
      425 => 'Unordered Collection',
      426 => 'Upgrade Required',
      449 => 'Retry With',
      450 => 'Blocked by Windows Parental Controls',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      506 => 'Variant Also Negotiates',
      507 => 'Insufficient Storage',
      509 => 'Bandwidth Limit Exceeded',
      510 => 'Not Extended'
    );
  }

  public static function exit_with_status($status,$message='') {
    $codes = self::http_status_codes();
    header("HTTP/1.1 {$status} {$codes[$status]}", true, $status);
    exit($message);
  }

  //Coupons rely on this be careful changing it
  public static function get_date_from_ts($ts, $format = 'M d, Y') {
    if($ts > 0) {
      return gmdate($format, $ts);
    }
    else {
      return gmdate($format, time());
    }
  }

  public static function db_date_to_ts($mysql_date) {
    return strtotime($mysql_date);
  }

  public static function ts_to_mysql_date($ts, $format='Y-m-d H:i:s') {
    return gmdate($format, $ts);
  }

  public static function db_now($format='Y-m-d H:i:s') {
    return self::ts_to_mysql_date(time(),$format);
  }
}

