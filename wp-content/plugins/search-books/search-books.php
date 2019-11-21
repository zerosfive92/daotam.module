<?php
/*
Plugin Name: search-books
Plugin URI: daotam.info
Description: Plugin tìm kiếm sách trên tù sách đại đạo
Version: 1.0
Author: Lapdc
Author URI: https://daotam.info
License: GPL
*/
////require_once($_SERVER['DOCUMENT_ROOT'] .  $folder . '/wp-content/plugins/plugin-one/controllers/plugin-one-controller.php');
require_once('controllers/search-books-controller.php');

if(!function_exists('plugin_search_books')){
	function plugin_search_books(){
		$pChar = $_GET['char'];
			?>
			<div class="search-book_header col medium-12 text-center mb-3"><a href="<?php echo get_page_link(8).'?char=A' ?>">A</a> | 
			<a href="<?php echo get_page_link(8).'?char=B' ?>">B</a> | 
			<a href="<?php echo get_page_link(8).'?char=C' ?>">C</a> | <a>D</a> | <a>Đ</a> | <a>E</a> | <a>G</a> | <a>H | <a>K</a> | <a>L</a> | <a>M</a> | <a>N</a> | <a>O</a> | <a>P</a> | <a>Q</a> | <a>R</a> | <a>S</a> | <a>T</a> | <a>U</a> | <a>V</a> | <a>X</a> | <a>Y</a></div>
			<?php
			$postlist;
			if(empty($pChar)){
				$postlist = get_book_list();
			}else{
				$char = substr($pChar, 0,1);
				$postlist = get_book_list_by_Char($char);
				?>
				<div class="col medium-12 text-center"><h1><?php echo $char ?></h1></div>
				<?php
			}
			$html = '<div class="col medium-8 small-12 large-8">';
			foreach($postlist as &$value){
				$bookIdEncrypt = encrypt_decrypt('encrypt', $value -> Id);
				$html .= '<div style="margin-bottom:15px; border-bottom: 1px solid gray">';
				$html .= '<div style="font-weight:bold; font-size:16px; margin-bottom:5px;">';
				$html .= '<a style="font-weight:bold; font-size:13px; margin-bottom:5px;" href="'.get_page_link(10).'?book='. $bookIdEncrypt .'">';
				$html .= $value -> Title;
				$html .= '</a>';
				$html .= '</div>';
				$html .= '<div style="font-size:13px; margin-bottom:5px;">';
				$html .= $value -> CreateDate;
				$html .= '</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
			echo $html;
	}
	
	add_shortcode('shortcode_search_books', 'plugin_search_books');
}

if(!function_exists('plugin_get_book_by_id')){
	function plugin_get_book_by_id(){

		?>
			<div class="content-widget">
				<button class="call-ajax">Bài viết ngẫu nhiên</button>
				<div class="display-post"></div>
			</div>
		<?php

		$book = $_GET['book'];
		$bookId = (int) encrypt_decrypt('decrypt', $book);
		$bookInfo = get_book_by_id($bookId);

		$html = '<div style="padding:10px">';
		$html .= '<div style="margin-bottom:15px; border-bottom: 1px solid gray">';
		$html .= '<div style="font-weight:bold; font-size:16px; margin-bottom:5px;">';
		$html .= '<a style="font-weight:bold; font-size:13px; margin-bottom:5px;" href="'.get_page_link(30).'?id='. $bookInfo -> Id .'">';
		$html .= $bookInfo -> Title;
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div style="font-size:13px; margin-bottom:5px;">';
		$html .= $bookInfo -> CreateDate;
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		echo $html;
	}
	
	add_shortcode('shortcode_get_book_by_id', 'plugin_get_book_by_id');
}


function encrypt_decrypt($action, $string) { 
    $output = false;
    $encrypt_method = "aes-128-cbc";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}


?>
