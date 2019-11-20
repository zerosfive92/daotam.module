<?php

function side_bar_get_author_list(){
    global $wpdb;
	$table_name = $wpdb->prefix . "authors"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name WHERE Status = 1" );
	return $getresults;
}

function side_bar_get_book_category_list(){
    global $wpdb;
	$table_name = $wpdb->prefix . "categories"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name" );
	return $getresults;
}
?>