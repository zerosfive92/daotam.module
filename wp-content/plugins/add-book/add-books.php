<?php
/*
Plugin Name: add-books
Plugin URI: daotam.info
Description: Plugin Thêm sách mới
Version: 1.0
Author: Lapdc
Author URI: https://daotam.info
License: GPL
*/
////require_once($_SERVER['DOCUMENT_ROOT'] .  $folder . '/wp-content/plugins/plugin-one/controllers/plugin-one-controller.php');
require_once('controllers/add-books-controller.php');

if(!function_exists('plugin_add_books')){
	function plugin_add_books(){
		?>
			<style>
				.entry-header{display:none;}
			</style>
			<div class="row">
				<div class="col-md-3">
				<div class="col-12"><h4>Danh mục</h4></div>
				</div>
				<div class="col-md-9">
					<div class=row>
						<div class="col-12 mb-3"><h4>Nhập thông tin</h4></div>
					</div>
					<div class="row">
						<div class="col-sm-3 align-items-end">
							<span>Tên sách:</span>
						</div>
						<div class="col-sm-9">
							<input id="txtBookName" type="text" class="form-control" placeholder="Nhập tên sách" />
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	
	add_shortcode('shortcode_add_books', 'plugin_add_books');
}

?>
