<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliToolsController extends PrliBaseController {
  public function load_hooks() {
    // Purely for reverse compatibility
    add_action('init',array($this,'redirect'));
  }

  public function route() {
    global $prli_options;

    $update_message = '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if($action == 'clear_all_clicks') {
      $update_message = $this->clear_clicks();
    }
    else if($action == 'clear_30day_clicks') {
      $update_message = $this->clear_clicks(30);
    }
    else if($action == 'clear_90day_clicks') {
      $update_message = $this->clear_clicks(90);
    }

    $update_message = apply_filters('prli_tools_update_message', $update_message);
    require_once(PRLI_VIEWS_PATH . '/tools/form.php');
  }

  public static function standalone_route() {
    global $prli_options;

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if(isset($_GET['k']) && $_GET['k']==$prli_options->bookmarklet_auth) {
      if( $action == 'prli_bookmarklet' && isset($_GET['target_url']) &&
          PrliUtils::is_url($_GET['target_url']) ) {
        return self::bookmark($_GET['target_url']);
      }
    }
    else {
      wp_redirect(home_url());
      exit;
    }
  }

  public static function bookmark($target_url) {
    global $prli_options, $prli_blogurl, $prli_link, $plp_update;

    $redirect_type = esc_html((isset($_GET['rt']) and $_GET['rt'] != '-1')?$_GET['rt']:'');
    $track = esc_html((isset($_GET['trk']) and $_GET['trk'] != '-1')?$_GET['trk']:'');
    $group = esc_html((isset($_GET['grp']) and $_GET['grp'] != '-1')?$_GET['grp']:'');

    $result = prli_create_pretty_link( esc_url_raw($target_url, array('http','https')), '', '', '', $group, $track, '', $redirect_type );

    $plink = $prli_link->getOne($result);
    $target_url = $plink->url;
    $target_url_title = $plink->name;
    $pretty_link = $prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $plink->slug;

    $twitter_status = substr($target_url_title,0,(114 - strlen($pretty_link))) . ((strlen($target_url_title) > 114)?"...":'') . " | $pretty_link";
    $pretty_link_id = $plink->id;

    require( PRLI_VIEWS_PATH . '/shared/public_link.php' );
  }

  // This is for reverse compatibility ...
  public function redirect() {
    $path = preg_replace( '!'.home_url().'!', '', PRLI_URL.'/prli-bookmarklet.php' );

    if($_SERVER['REQUEST_URI']==$path) {
      $accepted_params = array('k','target_url','action','rt','trk','grp');
      $param_str = '';

      foreach($_GET as $k => $v) {
        if(in_array($k,$accepted_params)) {
          $param_str .= "&{$k}={$v}";
        }
      }

      header("location: /index.php?action=prli_bookmarklet{$param_str}");
      exit;
    }
  }

  private function clear_clicks($days=false) {
    global $prli_click;

    if($days===false) {
      $prli_click->clearAllClicks();
      $update_message = __('Click Database was Cleared.', 'pretty-link');
    }
    else {
      $num_clicks = $prli_click->clear_clicks_by_age_in_days($days);

      if($num_clicks) {
        $update_message = sprintf(__('Clicks older than %1$d days (%2$d Clicks) were deleted' , 'pretty-link'), $days, $num_clicks);
      }
      else {
        $update_message = sprintf(__('No clicks older than %1$d days were found, so nothing was deleted' , 'pretty-link'), $days);
      }
    }

    return $update_message;
  }
}

