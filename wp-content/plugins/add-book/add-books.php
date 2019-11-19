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
        add_menu_page( 'Books', 'Books', 'edit_posts', 'books-management-plugin', 'add_books_management_init', 'dashicons-book-alt' );
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
    <div class="container videos-form" style="display:none;">
        <div class="form-group row">
            <label for="booktitle" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="booktitle" value="">
                <input type="hidden" id="bookId">
            </div>
        </div>
        <div class="form-group row">
            <label for="author" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Author</label>
            <div class="col-sm-10">
                <select id="author" class="form-control-lg w-100">
                    <option value="0">--Choose Author--</option>
                    <?php
                        $authList = get_author_list();
                        if(count($authList) > 0){
                            foreach($authList as &$value){
                                ?>
                                    <option style="text-transform: capitalize;" value="<?php echo $value -> Id ?>"><?php echo $value -> AuthorName ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="bookCate" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Category</label>
            <div class="col-sm-10">
                <select id="bookCate" class="form-control-lg w-100">
                    <option value="0">--Choose book category--</option>
                    <?php
                        $cateList = get_book_category_list();
                        if(count($cateList) > 0){
                            foreach($cateList as &$value){
                                ?>
                                    <option style="text-transform: capitalize;" value="<?php echo $value -> Id ?>"><?php echo $value -> Category ?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="linkUrl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Link/Url</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="linkUrl" value="">
            </div>
        </div>
        <div class="form-group row">
            <label for="PdfUrl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">PDF link</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="PdfUrl" value="">
            </div>
        </div>
        <div class="form-group row">
            <label for="AudioUrl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Audio link</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="audioUrl" value="">
            </div>
        </div>
        <div class="form-group row">
            <label for="videoUrl" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Video Link</label>
            <div class="col-sm-10">
                <input type="text" class="form-control-lg w-100" id="videoUrl" value="">
            </div>
        </div>
        <!--<div class="form-group row">
            <label for="publishdate" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Publish date</label>
            <div class="col-sm-10">
                <input type="date" keypress="false" class="form-control-lg w-100" id="publishdate">
            </div>
        </div>-->
        <div class="form-group row">
            <label for="publishdate" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Description</label>
            <div class="col-sm-10">
                <textarea id="bookDescription" rows="4" class="form-control-lg w-100">At w3schools.com you will learn how to make a website. We offer free tutorials in all web development technologies.
                </textarea>
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
            <label for="bookcontent" class="col-sm-2 col-form-label d-flex justify-content-end form-label">Content</label>
            <div class="col-sm-10">
	<?php
			$settings = array(
				'textarea_name' => 'bookcontent',
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
                    <button class="btn btn-success" onClick="getBookInfo()">Save</button>
                    <div class="display-post"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container video-list">
        <div class="form-group row">
            <div class="col-12">
                <div class="content-widget d-flex justify-content-start">
                    <button class="btn btn-success" onClick="AddNewBook()">Add new</button>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <table class="table" id="BookList">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Active</th>
                            <th>Url</th>
                            <th>Description</th>
                            <!--<th>Publish date</th>-->
                            <th>Update date</th>
                            <th>Tools</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $bookList = get_book_list_controller();
                            if(count($bookList) > 0){
                                $countNo = 1;
                                foreach($bookList as &$value){
                                    ?>
                                        <tr>
                                            <td scope="row"><?php echo $countNo ?></td>
                                            <td style="width:200px;"><?php echo $value -> Title ?></td>
                                            <td><?php if($value -> IsActive == 1){echo "active";}else{echo "deactive";} ?></td>
                                            <td><a href="<?php echo $value -> OriginalLink ?>"><?php echo substr($value -> OriginalLink, 0,30) . "..." ?></a></td>
                                            <td><?php echo substr($value -> Description, 0,30) . "..." ?></td>
                                            <td><?php echo date_format(date_create($value -> UpdateDate),"d/m/Y") ?></td>
                                            <td style="width:130px; text-align:center;">
                                                <button class="btn btn-sm btn-warning" onClick="GetVideoForEdit(<?php echo $value -> Id ?>)">Edit</button>
                                                <button class="btn btn-sm btn-danger" onClick="DeleteVideo(<?php echo $value -> Id ?>)">Delete</button>
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

function Insert_book($data){
    //echo "videos name is ". $data['content'] ." and I am ".$data['title']  ." year old." ;
    //print_r(json_encode($data));
    $book = "(
        '" . $data['title'] . "'
        ,'" . $data['description'] . "'
        ,'" . $data['content'] . "'
        ,'" . $data['bookUrl'] . "'
        ,'" . $data['pdfUrl'] . "'
        ,'" . $data['pdfUrl'] . "'
        ,'" . $data['audioUrl'] . "'
        ,'" . $data['videoUrl'] . "'
        ,'" . $data['authorId'] . "'
        ,'" . $data['authorName'] . "'
        ,'" .current_time('mysql'). "'
        ,'" .current_time('mysql'). "'
        ,'" . $data['cateId'] . "'
        ,'" .NULL. "'
        ,'" .NULL. "'
        ,'" .NULL. "'
        ,'" . 1 . "'
        ,'" . 1 . "'
    )";
    
    $result = insert_book_controller($book);
    if($result == 1 || $result == "1"){
        LoadBookList();
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
        //echo "Save result is ". $result ;
        LoadBookList();
    }else{
        print_r(0);
    }
}

function LoadBookList(){
    $bookList = get_book_list_controller();
    if(count($bookList) > 0){
        $countNo = 1;
        foreach($bookList as &$value){
            ?>
                <tr>
                    <td scope="row"><?php echo $countNo ?></td>
                    <td><?php echo $value -> Title ?></td>
                    <td><?php if($value -> IsActive == 1){echo "active";}else{echo "deactive";} ?></td>
                    <td><?php echo $value -> OriginalLink ?></td>
                    <td><?php echo substr($value -> Description, 0,100) . "..." ?></td>
                    <td><?php echo date_format(date_create($value -> UpdateDate),"d/m/Y") ?></td>
                    <td style="min-width:120px; text-align:center;">
                        <button class="btn btn-sm btn-warning" onClick="GetVideoForEdit(<?php echo $value -> Id ?>)">Edit</button>
                        <button class="btn btn-sm btn-danger" onClick="DeleteVideo(<?php echo $value -> Id ?>)">Delete</button>
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
}

?>