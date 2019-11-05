<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliPostsController extends PrliBaseController {
  public $opt_fields;

  public function load_hooks() {
    add_action('init', array($this, 'add_tinymce_buttons'));
    add_action('wp_ajax_prli_tinymce_form', array($this, 'display_tinymce_form'));
    add_action('wp_ajax_prli_tinymce_validate_slug', array($this, 'validate_tinymce_slug'));
    add_action('wp_ajax_prli_create_pretty_link', array($this, 'create_pretty_link'));
    add_action('wp_ajax_prli_search_for_links', array($this, 'search_results'));
  }

  // registers the buttons for use
  public function register_buttons($buttons) {
    array_push($buttons, "prli_tinymce_form");
    return $buttons;
  }

  // add the button to the tinyMCE bar
  public function add_tinymce_plugin($plugin_array) {
    $plugin_array['PrliTinyMCE'] = PRLI_JS_URL.'/tinymce_form.js';
    return $plugin_array;
  }

  // filters the tinyMCE buttons and adds our custom buttons
  public function add_tinymce_buttons() {

    // If this isn't a Pretty Link authorized user then bail
    if(!PrliUtils::is_authorized()) { return; }

    // Add only in Rich Editor mode
    if(get_user_option('rich_editing') == 'true') {
      // filter the tinyMCE buttons and add our own
      add_filter("mce_external_plugins", array($this, "add_tinymce_plugin"));
      add_filter('mce_buttons', array($this, 'register_buttons'));
    }
  }

  //AJAX
  public function display_tinymce_form() {
    global $prli_link, $prli_options, $plp_update;

    //Setup some vars for the view
    $home_url = home_url() . '/';
    $random_slug      = $prli_link->generateValidSlug();
    $default_redirect = $prli_options->link_redirect_type;
    $default_nofollow = ($prli_options->link_nofollow)?'enabled':'disabled';
    $default_tracking = ($prli_options->link_track_me)?'enabled':'disabled';

    //Get alternate Base URL
    if($plp_update->is_installed()) {
      global $plp_options;

      if(isset($plp_options) && $plp_options->use_prettylink_url && !empty($plp_options->prettylink_url)) {
        $home_url = stripslashes($plp_options->prettylink_url) . '/';
      }
    }

    require(PRLI_VIEWS_PATH.'/shared/tinymce_form_popup.php');
    die();
  }

  //AJAX
  public function validate_tinymce_slug() {
    if(!isset($_POST['slug']) || empty($_POST['slug'])) {
      echo "false";
      die();
    }

    $slug = trim(stripslashes($_POST['slug']));

    //Can't end in a slash
    if(substr($slug, -1) == '/' || $slug[0] == '/' || preg_match('/\s/', $slug) || !PrliUtils::slugIsAvailable($slug)) {
      echo "false";
      die();
    }

    echo "true";
    die();
  }

  //AJAX
  public function create_pretty_link() {
    $valid_vars = array('target', 'slug', 'redirect', 'nofollow', 'tracking');

    if(!PrliUtils::is_authorized()) {
      echo "invalid_user";
      die();
    }

    if(!isset($_POST) || !($valid_vars == array_intersect($valid_vars, array_keys($_POST)))) {
      echo "invalid_inputs";
      die();
    }

    //Using the local API Yo
    $id = prli_create_pretty_link(
            stripslashes($_POST['target']),
            stripslashes($_POST['slug']),
            '', //Name
            '', //Desc
            0, //Group ID
            (int)($_POST['tracking'] == 'enabled'),
            (int)($_POST['nofollow'] == 'enabled'),
            $_POST['redirect']
          );

    if((int)$id > 0) {
      echo "true";
      die();
    }

    echo "link_failed_to_create";
    die();
  }

  //AJAX
  public function search_results() {
    global $prli_link, $wpdb;

    if(!isset($_GET['term']) || empty($_GET['term'])) { die(''); }

    $return = array();
    $term = '%' . rawurldecode(stripslashes($_GET['term'])) . '%';
    $q = "SELECT * FROM {$prli_link->table_name} WHERE slug LIKE %s OR name LIKE %s OR url LIKE %s LIMIT 20";
    $q = $wpdb->prepare($q, $term, $term, $term);
    $results = $wpdb->get_results($q, ARRAY_A);

    //Prepare the results for JSON
    if(!empty($results)) {
      foreach($results as $result) {
        $result = stripslashes_deep($result);

        if(extension_loaded('mbstring')) {
          $alt_name = (mb_strlen($result['name']) > 55)?mb_substr($result['name'], 0, 55).'...':$result['name'];
        }
        else {
          $alt_name = (strlen($result['name']) > 55)?substr($result['name'], 0, 55).'...':$result['name'];
        }

        $return[] = array(
                      'value'     => (empty($result['name']))?$result['slug']:$alt_name,
                      'slug'      => $result['slug'],
                      'target'    => $result['url'],
                      'title'     => $result['name'], //Not used currently, but we may want this at some point
                      'nofollow'  => (int)$result['nofollow']
                    );
      }

      die(json_encode($return));
    }

    die();
  }
} //End class
