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

  function getVideoInfo(){
      if(IsValidate_Insert_Video_Form()){
        var pDescription = tinymce.activeEditor.getContent();
        var pTitle = $('#videotitle').val();
        var pUrl = $('#videourl').val();
        var pPublish = $("#publishdate").val();
        var pCate = $("#videocate").val();
        var pId = $("#videoId").val();
        var dataList = {Id:pId, title: pTitle, url: pUrl, publishDate: pPublish, videoCate: pCate, description: pDescription};
        if(pId != "" && pId != null){
            console.log(dataList);
            Update_Video(dataList);
        }else{
            Insert_Video(dataList);
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

  function Insert_Video(dataList){
    jQuery.ajax({
        url: ajax_object.ajaxurl,
        type: 'POST',
        data: { 
          action: 'insertvideosadminaction',
          data: dataList
        },
        success: function(data){
            if(data == 0 || data == "0"){
                alert("update failed");
            }else{
                $("#videoList").html(data);
                CancelUpdate();
            }
          console.log(data);
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

  function IsValidate_Insert_Video_Form(){
        $isValid = false;

        if($("#videotitle").val() == ""){
            $("#videotitle").focus();
            $("#videotitle").addClass("error-text-form");
        }else{
            $("#videotitle").removeClass("error-text-form");
        }

        if($("#videourl").val() == ""){
            $("#videourl").focus();
            $("#videourl").addClass("error-text-form");
        }else{
            $("#videourl").removeClass("error-text-form");
        }

        if($("#publishdate").val() == ""){
            $("#publishdate").focus();
            $("#publishdate").addClass("error-text-form");
        }else{
            $("#publishdate").removeClass("error-text-form");
        }

        if($("#videotitle").val() != "" && $("#videourl").val() != "" && $("#publishdate").val() != ""){
            $isValid = true;
        }
        return $isValid;
  }

  function AddNewVideo(){
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