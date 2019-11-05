<?php
function get_book_list(){
	global $wpdb;
	$table_name = $wpdb->prefix . "books"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name" );
	return $getresults;
}

function get_book_by_id($bookId){
	global $wpdb;
	$table_name = $wpdb->prefix . "books"; 
	$getbook = $wpdb->get_row( "SELECT * FROM $table_name WHERE Id =".$bookId );
	return $getbook;
}

function base64url_encode($plainText) {
    $base64 = base64_encode($plainText);
    $base64url = strtr($base64, '+/=', '-_,');
    return $base64url;
}

function base64url_decode($plainText) {
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;
}
?>