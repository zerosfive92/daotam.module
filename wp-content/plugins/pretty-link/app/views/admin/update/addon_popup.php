<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="prli-admin-notice prli-auto-open mfp-hide prli-white-popup prli-popup-leftalign">
  <h2 class="prli_error" style="text-align: center !important; padding-bottom: 15px !important; padding-top: 15px !important"><i class="mp-icon-attention"> </i> <?php _e('ACTION REQUIRED', 'pretty-link'); ?></h2>
  <p><?php printf(__('The %s features in your Pretty Link options have been moved from Pretty Link into a separate plugin.', 'pretty-link'), $a->name); ?></p>
  <p><?php __('Why you ask? Well, it\'s to streamline and increase the performance of Pretty Link for you.', 'pretty-link'); ?></p>

  <?php if($installed): ?>
    <p><?php printf(__('And good for you, it looks like you\'ve already got the %1$s Add-on installed. Just click the "Activate %2$s Add-on" button below and you\'ll get all these features back now.', 'pretty-link'), $a->name, $a->name); ?></p>
  <?php else: ?>
    <p><?php printf(__('Luckily it\'s easy to get these features back now. Just click the "Install %s Add-on" button below.', 'pretty-link'), $a->name); ?></p>
    <p><?php printf(__('If you have problems with the auto-install please refer to %1$sthe user manual%2$s for manual install instructions.', 'pretty-link'), '<a href="https://prettylinks.com/pl/addon-popup/um/manual-installation" target="_blank">', '</a>'); ?></p>
  <?php endif; ?>
  <br/>
  <center>
    <div data-addon="<?php echo $k; ?>">
      <?php if($installed): ?>
        <button data-href="<?php echo MeprAddonsHelper::activate_url("pretty-link-{$k}/main.php"); ?>" class="prli-btn prli-left-margin prli-addon-activate"><?php printf(__('Activate %s Add-on', 'pretty-link'), $a->name); ?></button>
      <?php else: ?>
        <button data-href="<?php echo MeprAddonsHelper::install_url('pretty-link-'.$k); ?>" class="prli-btn prli-left-margin prli-addon-install"><?php printf(__('Install %s Add-on', 'pretty-link'), $a->name); ?></button>
      <?php endif; ?>
      <button class="prli-btn prli-left-margin prli-addon-stop-notices"><?php _e('Don\'t Show This Message Again', 'pretty-link'); ?></button>
    </div>
  </center>
  <br/>
</div>

