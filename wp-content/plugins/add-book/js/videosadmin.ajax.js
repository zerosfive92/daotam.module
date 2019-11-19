jQuery(document).ready( function(jQuery){
    // Some event will trigger the ajax call, you can push whatever data to the server, 
    // simply passing it to the "data" object in ajax call
    //call_ajax();
    /*jQuery('.call-ajax').click(function(){
        call_ajax();
    });*/
     /* jQuery('#btnGetData').click(function () {
          
      });*/
  });

  function getBookInfo(){
      if(IsValidate_Insert_Book_Form()){
        var pContent = tinymce.activeEditor.getContent();
        var pTitle = $('#booktitle').val();
        var pAuthorId = $('#author option:selected').val();
        var pAuthorName = $('#author option:selected').text();
        var pCateId = $('#bookCate option:selected').val();
        var pCateName = $('#bookCate option:selected').text();
        var pBookUrl = $('#linkUrl').val();
        var pPdfUrl = $('#PdfUrl').val();
        var pAudioUrl = $('#audioUrl').val();
        var pVideoUrl = $('#videoUrl').val();
        //var pPublish = $("#publishdate").val();
        var pBookDescription = $("#bookDescription").val();
        var pId = $("#bookId").val();
        var dataList = {Id:pId
                        , title: pTitle
                        , authorId: pAuthorId
                        , authorName: pAuthorName
                        , cateId: pCateId
                        , cateName: pCateName
                        , bookUrl: pBookUrl
                        , pdfUrl: pPdfUrl
                        , audioUrl: pAudioUrl
                        , videoUrl: pVideoUrl
                        //, publish: pPublish
                        , description: pBookDescription
                        , content: pContent
                      };
        //console.log(dataList);
        if(pId != "" && pId != null){
            console.log(dataList);
            Update_Video(dataList);
        }else{
            Insert_Book(dataList);
        }
      }
  }

  function video_admin_call_ajax(){
    jQuery.ajax({
        url: ajax_object.ajaxurl, // this is the object instantiated in wp_localize_script function
        type: 'POST',
        data:{ 
          action: 'getVideoByIdAction', // this is the function in your functions.php that will be triggered
          name: 'John',
          age: '38'
        },
        success: function( data ){
          //Do something with the result from server
          console.log( data );
        }
      });
  }

  function Insert_Book(dataList){
    jQuery.ajax({
        url: ajax_object.ajaxurl,
        type: 'POST',
        data: { 
          action: 'insertbookadminaction',
          data: dataList
        },
        success: function(data){
            console.log(data);
            if(data == 0 || data == "0"){
                alert("update failed");
            }else{
                $("#BookList").html(data);
                CancelUpdate();
            }
        }
      });
      return false;
  }

  function Update_Video(dataList){
    jQuery.ajax({
        url: ajax_object.ajaxurl,
        type: 'POST',
        data: { 
          action: 'updatevideoaction',
          data: dataList
        },
        success: function(data){
            if(data == 0 || data == "0"){
                alert("update failed");
            }else{
                $("#videoList tbody").html(data);
                CancelUpdate();
            }
        }
      });
      return false;
  }

  function IsValidate_Insert_Book_Form(){
        $isValid = false;

        if($("#booktitle").val() == ""){
            $("#booktitle").focus();
            $("#booktitle").addClass("error-text-form");
        }else{
            $("#booktitle").removeClass("error-text-form");
        }

        if($("#linkUrl").val() == ""){
            $("#linkUrl").focus();
            $("#linkUrl").addClass("error-text-form");
        }else{
            $("#linkUrl").removeClass("error-text-form");
        }

        if($("#booktitle").val() != "" && $("#linkUrl").val() != ""){
            $isValid = true;
        }
        return $isValid;
  }

  function AddNewBook(){
    ResetVideoForm();
    $(".video-list").css("display","none");
    $(".videos-form").fadeIn("fast");
  }

  function CancelUpdate(){
    ResetVideoForm();
    $(".videos-form").css("display","none");
    $(".video-list").fadeIn("fast");
    location.reload(true);
  }

  function GetVideoForEdit(videoId){
    console.log(videoId);
    jQuery.ajax({
        url: ajax_object.ajaxurl,
        type: 'POST',
        data:{ 
          action: 'getVideoByIdAction',
          id: videoId
        },
        success: function( data ){
          var jSonData = $.parseJSON(data);
          if(jSonData != null){
            $('#videoId').val(jSonData.Id);
            $('#videotitle').val(jSonData.Title);
            $('#videourl').val(jSonData.Url);
            $("#publishdate").val(jSonData.PublishDate);
            $("#videocate").val(jSonData.CategoryId);
            tinymce.activeEditor.setContent(jSonData.Description);

            $(".video-list").css("display","none");
            $(".videos-form").fadeIn("fast");
          }else{
            alert("Video not exist");
          }
        }
      });
  }

  function DeleteVideo(videoId){
    if(confirm("Do you want delete videoid : " + videoId)){
        alert("deleted");
    }else{
        alert("deleted");
    }
  }

  function ResetVideoForm(){
    $('#videoId').val('');
    $('#videotitle').val('');
    $('#videourl').val('');
    $("#publishdate").val('');
    $("#videocate").val(0);
    tinymce.activeEditor.setContent('');
  }