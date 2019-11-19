<?php

function insert_book_controller($bookInfo){
    global $wpdb;
	$table_name = $wpdb->prefix . "books"; 

    $query = "
        INSERT INTO 
        `".$table_name."`(
            `Title`
            , `Description`
            , `Content`
            , `OriginalLink`
            , `PdfFile`
            , `OriginalPdfFile`
            , `AudioFile`
            , `VideoFile`
            , `Author`
            , `AuthorName`
            , `CreateDate`
            , `UpdateDate`
            , `CategoryId`
            , `KindId`
            , `DomainId`
            , `Type`
            , `Status`
            , `IsActive`
            ) VALUES " .$bookInfo;
    //print_r($query);
    $book_save = $wpdb->query($query);
    
    return $book_save;
}

function get_book_list_controller(){
	global $wpdb;
	$table_name = $wpdb->prefix . "books"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name WHERE Status = 1 AND IsActive = 1 " );
	return $getresults;
}

function get_videos_Controller(){
    global $wpdb;
	$table_name = $wpdb->prefix . "videos"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name" );
	return $getresults;
}

function get_author_list(){
    global $wpdb;
	$table_name = $wpdb->prefix . "authors"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name WHERE Status = 1" );
	return $getresults;
}

function get_book_category_list(){
    global $wpdb;
	$table_name = $wpdb->prefix . "categories"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name" );
	return $getresults;
}

function Get_Video_By_Id_Controller($videoId){
	global $wpdb;
	$table_name = $wpdb->prefix . "videos"; 
	$getVideo = $wpdb->get_row( "SELECT * FROM $table_name WHERE Id =".$videoId );
	return $getVideo;
}

function Update_Video_By_Id_Controller($VideoData){
    global $wpdb;
    $table_name = $wpdb->prefix . "videos";

    $getVideo = $wpdb->get_row( "SELECT * FROM $table_name WHERE Id =".$VideoData['Id'] );

    if($getVideo != null){
        $video_update = $wpdb->update( 
            $table_name, 
            array( 
                'Title' => $VideoData['title'],
                'Status' => '1',
                'Description' => $VideoData['description'],
                'Url' => $VideoData['url'],
                'CategoryId' => $VideoData['videoCate'],
                'Content' => NULL,
                'PublishDate' => $VideoData['publishDate'],
                'UpdateDate' => current_time('mysql')
            ), 
            array( 'Id' => $VideoData['Id'] ) );
        return $video_update;
    }else{
        return 0;
    }
}

?>