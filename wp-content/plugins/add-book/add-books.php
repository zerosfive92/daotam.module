<?php
/*
Plugin Name:  Videos
Plugin URI: .com.vn
Description: Plugin quản lý videos cho website .com.vn
Version: 1.0
Author: Lapdc
Author URI: https://.com.vn
License: GPL
*/
require_once('controllers/add-books-controller.php');

add_action('admin_menu', 'add_books_admin_plugin');
 
function add_books_admin_plugin(){
        add_menu_page( 'Thêm sách', 'Thêm sách', 'edit_posts', 'books-management-plugin', 'add_books_management_init' );
}


function add_books_management_init(){

    wp_register_style( 'boostrap4css', plugins_url() . '/add-book/lib/bootstrap.min.css' );
    wp_enqueue_style( 'boostrap4css' );

    wp_register_style( 'videosAdminStyle', plugins_url() . '/add-book/css/video_admin_style.css' );
    wp_enqueue_style( 'videosAdminStyle' );

    wp_register_script('boostrap4js', plugins_url() . '/add-book/lib/bootstrap.min.js');
    wp_enqueue_script('boostrap4js');

	//test_handle_post();
	wp_enqueue_script('videos-admin-ajax', plugins_url() . '/add-book/js/videosadmin.ajax.js');
	wp_localize_script('videos-admin-ajax', 'videosAdminAjax', array(
		'pluginsUrl' => plugins_url(),
	));
	wp_localize_script( 'videos-admin-ajax', 'ajax_object', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	));
	?>
	<h3>Thêm sách</h3>
	<!-- Form to handle the upload - The enctype value here is very important -->
    <div class="container videos-form">
        <div class="form-group row">
            <label for="videotitle" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="videotitle" value="">
                <input type="hidden" id="videoId">
            </div>
        </div>
        <div class="form-group row">
            <label for="videocate" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Category</label>
            <div class="col-sm-10">
                <select id="videocate" class="form-control-lg w-100">
                    <option value="0">--Choose video category--</option>
                    <?php
                        $cateList = get_video_categories_Controller();
                        if(count($cateList) > 0){
                            foreach($cateList as &$value){
                                ?>
                                    <option value="<?php echo $value -> Id ?>"><?php echo $value -> Name ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="videourl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Link/Url</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="videourl" value="">
            </div>
        </div>
        <div class="form-group row">
            <label for="publishdate" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Publish date</label>
            <div class="col-sm-10">
                <input type="date" keypress="false" class="form-control-lg w-100" id="publishdate">
            </div>
        </div>
        <!--<div class="form-group row">
            <label for="videourl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Cover image</label>
            <div class="col-sm-10">
                <form  method="post" enctype="multipart/form-data">
                    <input type='file' id='test_upload_pdf' name='test_upload_pdf' />
                    <?php submit_button('Upload') ?>
                </form>
            </div>
        </div>-->
        <div class="form-group row">
            <label for="videodescription" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Short description</label>
            <div class="col-sm-10">
	<?php
			$settings = array(
				'textarea_name' => 'videodescription',
                'media_buttons' => true,
                'editor_height' => 425, // In pixels, takes precedence and has no default value
                'textarea_rows' => 20,
				'tinymce' => array(
						'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
								'bullist,blockquote,|,justifyleft,justifycenter' .
								',justifyright,justifyfull,|,link,unlink,|' .
                                ',spellchecker,wp_fullscreen,wp_adv'
				)
		);
		wp_editor( '', 'content', $settings );
        ?>
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-12">
                <div class="content-widget d-flex justify-content-end">
                    <button class="btn btn-default mr-2" onClick="CancelUpdate()">Cancel</button>
                    <button class="btn btn-success" onClick="getVideoInfo()">Submit</button>
                    <div class="display-post"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container video-list">
        <div class="form-group row">
            <div class="col-12">
                <div class="content-widget d-flex justify-content-start">
                    <button class="btn btn-success" onClick="AddNewVideo()">Add new</button>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <table class="table" id="videoList">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Url</th>
                            <th>Description</th>
                            <!--<th>Publish date</th>-->
                            <th>Update date</th>
                            <th>Tools</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $videosList = get_videos_Controller();
                            if(count($videosList) > 0){
                                $countNo = 1;
                                foreach($videosList as &$value){
                                    ?>
                                        <tr>
                                            <td scope="row"><?php echo $countNo ?></td>
                                            <td><?php echo $value -> Title ?></td>
                                            <td><?php if($value -> Status == 1){echo "active";}else{echo "deactive";} ?></td>
                                            <td><?php echo $value -> Url ?></td>
                                            <td><?php echo substr($value -> Description, 0,100) . "..." ?></td>
                                            <td><?php echo date_format(date_create($value -> UpdateDate),"d/m/Y") ?></td>
                                            <td>
                                                <button class="btn btn-warning" onClick="GetVideoForEdit(<?php echo $value -> Id ?>)">Edit</button>
                                                <button class="btn btn-danger" onClick="DeleteVideo(<?php echo $value -> Id ?>)">Delete</button>
                                            </td>
                                        </tr>
                                    <?php
                                    $countNo ++;
                                }
                            }else{
                                ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Chưa có sách</td>
                                    </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
}


function Insert_videos_test($name, $age){
    echo "videos name is ". $name ." and I am ".$age ." year old." ;
}

function Insert_videos($data){
    print_r($data['publishDate']);

    $video = "(
        '" . $data['title'] . "'
        ,'" . 1 . "'
        ,'" . $data['description'] . "'
        ,'" . $data['url'] . "'
        ,'" . $data['videoCate'] . "'
        ,'" .NULL. "'
        ,'" .$data['publishDate']. "'
        ,'" .current_time('mysql'). "'
        ,'" .current_time('mysql'). "'
    )";
    $result = insert_video_Controller($video);
    if($result == 1 || $result == "1"){
        LoadVideoList();
    }else{
        print_r(0);
    }
}

function Get_Video_By_Id($Id){
    $video = Get_Video_By_Id_Controller($Id);
    print_r(json_encode($video));
}

function UpdateVideo($VideoData){
    $result = Update_Video_By_Id_Controller($VideoData);
    if($result == 1 || $result == "1"){
        LoadVideoList();
    }else{
        print_r(0);
    }
}

function LoadVideoList(){
    $videosList = get_videos_Controller();
    if(count($videosList) > 0){
        $countNo = 1;
        foreach($videosList as &$value){
            ?>
                <tr>
                    <td scope="row"><?php echo $countNo ?></td>
                    <td><?php echo $value -> Title ?></td>
                    <td><?php if($value -> Status == 1){echo "active";}else{echo "deactive";} ?></td>
                    <td><?php echo $value -> Url ?></td>
                    <td><?php echo $value -> Description ?></td>
                    <!--<td><?php /*echo $value -> PublishDate*/ ?></td>-->
                    <td><?php echo date_format(date_create($value -> UpdateDate),"d/m/Y") ?></td>
                    <td>
                        <button class="btn btn-warning" onClick="GetVideoForEdit(<?php echo $value -> Id ?>)">Edit</button>
                        <button class="btn btn-danger" onClick="DeleteVideo(<?php echo $value -> Id ?>)">Delete</button>
                    </td>
                </tr>
            <?php
            $countNo ++;
        }
    }else{
        ?>
            <tr>
                <td colspan="7" style="text-align: center;">No video</td>
            </tr>
        <?php
    }
}

?>