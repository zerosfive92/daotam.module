<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(isset($errors) && $errors != null && count($errors) > 0): ?>
  <div class="error notice is-dismissible below-h2">
    <ul>
      <?php foreach($errors as $error): ?>
        <li><strong><?php _e('ERROR', 'pretty-link'); ?></strong>: <?php print $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if( isset($message) and !empty($message) ): ?>
  <div class="updated notice notice-success is-dismissible">
    <p><?php echo $message; ?></p>
  </div>
<?php endif; ?>
