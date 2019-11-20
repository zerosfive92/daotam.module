<?php
/*
Plugin Name: side-bar-books
Plugin URI: daotam.info
Description: Danh mục trên tù sách đại đạo
Version: 1.0
Author: Lapdc
Author URI: https://daotam.info
License: GPL
*/

require_once('controllers/side-bar-plugin-controller.php');

if(!function_exists('plugin_side_bar_books')){
	function plugin_side_bar_books(){
        $authList = side_bar_get_author_list();
        ?>
        <div class="col medium-12 small-12 large-12 p-0 mb-2">
            <div class="col medium-12 widget-title p-0 mb-2"><h4>Tác giả</h4></div>
            <?php
            if(count($authList) > 0){
                foreach($authList as &$value){
                    ?>
                        <div style="text-transform: capitalize;" data-value="<?php echo $value -> Id ?>"><?php echo $value -> AuthorName ?></div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
        $cateList = side_bar_get_book_category_list();
        ?>
        <div class="col medium-12 small-12 large-12 p-0 mb-2">
            <div class="col medium-12 widget-title p-0 mb-2"><h4>Danh mục</h4></div>
            <?php
            if(count($cateList) > 0){
                foreach($cateList as &$value){
                    ?>
                        <div style="text-transform: capitalize;" data-value="<?php echo $value -> Id ?>"><?php echo $value -> Category ?></div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
	}
	
	add_shortcode('shortcode_side_bar_books', 'plugin_side_bar_books');
}

?>