(function($){

  if(disablebtns ==1){
    $(".like_div .likebtn").css('color','rgba(0, 128, 0, 0.5)');
    var btn=$('#like-form').find('button');
    btn.prop('disabled',true);

    $(".like_div .dislikebtn").css('color','rgba(255, 0, 0, 0.5)');
    var btn=$('#dislike-form').find('button');
    btn.prop('disabled',true);
  }
   
    $('#like-form').on('submit',function(e){
        e.preventDefault();
       var data=$(this).serialize();
      $.post(post_likes.ajax_url,data,function(response){
         
          $('#likes_number').html(response);
        //  alert(post_likes.done);
          $('.likeresult').html('<div class="alert alert-success" role="alert"> Liked You ^_^ </div>');
          $(".like_div .likebtn").css('color','rgba(0, 128, 0, 0.5)');
          var btn=$(this).find('button');
          btn.prop('disabled',true);
          $(".like_div .dislikebtn").css('color','rgba(255, 0, 0, 0.5)');
          var btn=$('#dislike-form').find('button');
          btn.prop('disabled',true);

        });
    });

    $('#dislike-form').on('submit',function(e){
      e.preventDefault();
     var data=$(this).serialize();
    $.post(post_dislikes.ajax_url,data,function(response){
       
        $('#dislikes_number').html(response);
        $('.likeresult').html('<div class="alert alert-danger" role="alert"> DisLiked You .... </div>');
        $(".like_div .dislikebtn").css('color','rgba(255, 0, 0, 0.5)');
          var btn=$(this).find('button');
          btn.prop('disabled',true);
          $(".like_div .likebtn").css('color','rgba(0, 128, 0, 0.5)');
          var btn=$('#like-form').find('button');
          btn.prop('disabled',true);


      });
  });


})(jQuery);

