<?php

function insert_video_Controller($videoData){
    global $wpdb;
	$table_name = $wpdb->prefix . "videos"; 

    $query = "
        INSERT INTO 
        `".$table_name."`(
             `Title`
            , `Status`
            , `Description`
            , `Url`
            , `CategoryId`
            , `Content`
            , `PublishDate`
            , `CreateDate`
            , `UpdateDate`
            ) VALUES " .$videoData;

    //print_r($query);
    $videos_save = $wpdb->query($query);
    
    return $videos_save;
}

function get_videos_Controller(){
    global $wpdb;
	$table_name = $wpdb->prefix . "videos"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name" );
	return $getresults;
}

function get_video_categories_Controller(){
    global $wpdb;
	$table_name = $wpdb->prefix . "video_categories"; 
	$getresults = $wpdb->get_results( "SELECT * FROM $table_name WHERE Status = 1" );
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