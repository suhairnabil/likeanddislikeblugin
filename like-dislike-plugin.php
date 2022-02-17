<?php
/*
Plugin Name: Like and Dislike Plugin
Plugin URI: https://tibo.pro/kreo
Description: Like and Dislike Plugin and count its 
Author:  Suhair Abu Nema
Author URI:  https://tibo.pro/kreo
Version:  1.0
Requires at least: 5.2
Requires PHP:      7.2
Tags: Wordpress,Plugins
*/


function likelugin_activation_hook(){
    global $wpdb;
    update_option('like_dislike_activated',1);

    $query="CREATE TABLE IF NOT EXISTS '{$wpdb->prefix}posts_likes' (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `ip` VARCAR(255) NOT NULL ,
        `post_id` INT ,
        PRIMARY KEY (`id`)
        )";
   $wpdb->query($query);
}
register_activation_hook(__FILE__,'likelugin_activation_hook');


function likelugin_deactivation_hook(){
    update_option('like_dislike_activated',0);
}
register_deactivation_hook(__FILE__,'likelugin_deactivation_hook');


function likelugin_unstall_hook(){
    global $wpdb;
    delete_option('like_dislike_activated');
    $query="DROP TABLE '{$wpdb->prefix}posts_likes'";
    $wpdb->query($query);

}
register_uninstall_hook(__FILE__,'likelugin_unstall_hook');

function the_post_likes($post_id=null){
    if($post_id === null){
        $post_id=get_the_ID();
    }
    $views =  get_post_meta($post_id,'_post_likes',true);
    echo '<b>'.$views.'</b>';

}
function the_post_dislikes($post_id=null){
    if($post_id === null){
        $post_id=get_the_ID();
    }
    $views =  get_post_meta($post_id,'_post_dislikes',true);
    echo '<b>'.$views.'</b>';

}

function do_like_post(){
    global $wpdb;
   if(isset($_POST['post_id'])){
        $post_id=$_POST['post_id'];
        $likes =(int) get_post_meta($post_id,'_post_likes',true);
        $likes++;
        update_post_meta($post_id,'_post_likes', $likes );

        $ip = $_SERVER['REMOTE_ADDR'];
        $wpdb->insert("{$wpdb->prefix}posts_likes",[
            'ip'=> $ip,
            'post_id'=> $post_id,
        ]);

       // wp_redirect(get_permalink($post_id));
    }else{
        die('error');
    }
   

}
function do_dislike_post(){   
    global $wpdb;
     if(isset($_POST['post_id'])){
         $post_id=$_POST['post_id'];
         $dislikes =(int) get_post_meta($post_id,'_post_dislikes',true);
         $dislikes++;
         update_post_meta($post_id,'_post_dislikes', $dislikes );
         $ip = $_SERVER['REMOTE_ADDR'];
         $wpdb->insert("{$wpdb->prefix}posts_likes",[
             'ip'=> $ip,
             'post_id'=> $post_id,
         ]);

     //    wp_redirect(get_permalink($post_id));

     }
 }


function post_liking(){
    $post_id=$_POST['post_id'];
    if(!$post_id){
        wp_die('No post Seleted');
    }
    do_like_post();
    wp_redirect(get_permalink($post_id));
}

function post_disliking(){
    $post_id=$_POST['post_id'];
    if(!$post_id){
        wp_die('No post Seleted');
    }
    do_dislike_post();
    wp_redirect(get_permalink($post_id));
}



add_action('admin_post_post_like','post_liking');
add_action('admin_post_nopriv_post_like','post_liking');
add_action('admin_post_post_dislike','post_disliking');
add_action('admin_post_nopriv_post_dislike','post_disliking');


function ajax_liking(){
    $post_id=$_POST['post_id'] ?? 0 ;
    if(!$post_id){
        wp_send_json_error([
            'message'=>'No Post Selected',
        ]);
    }
    do_like_post();

    $likes =(int) get_post_meta($post_id,'_post_likes',true);

   /*
  wp_send_json([
        'likes'=> 'likes',
        'dislikes'=> 'dislikes',
    ]);
*/
    echo $likes;
}
add_action('wp_ajax_post_like','ajax_liking');
add_action('wp_ajax_nopriv_post_like','ajax_liking');

function ajax_disliking(){
    $post_id=$_POST['post_id'] ?? 0 ;
    if(!$post_id){
        wp_send_json_error([
            'message'=>'No Post Selected',
        ]);
    }
    do_dislike_post();

    $dislikes =(int) get_post_meta($post_id,'_post_dislikes',true);
     echo  $dislikes;
}
add_action('wp_ajax_post_dislike','ajax_disliking');
add_action('wp_ajax_nopriv_post_dislike','ajax_disliking');

function like_enequeue_scripts(){
    wp_enqueue_script(
        'like-script',
        plugin_dir_url(__FILE__).'js/likescript.js',
        [],
        false,
        true

    );

wp_localize_script(
    'like-script',
    'post_likes',
    [
        'ajax_url'=> admin_url('admin-ajax.php'),
        'thank_you'=> __('Thank you!'),
        'done'=> __('Like Done'),  
    ]
);

wp_localize_script(
    'like-script',
    'post_dislikes',
    [
        'ajax_url'=> admin_url('admin-ajax.php'),
        'thank_you'=> __('Thank you!'),
        'done'=> __('DisLike Done'),  
    ]
);




}
add_action('wp_enqueue_scripts','like_enequeue_scripts');


function poslike_metaboxes(){
    add_meta_box(
        'postlike_mb',
        __('Like/Dislike post','post-like'),
        'postlike_metaboxes',
        ['post'],
        'side',
        'high'
    );
}
add_action('add_meta_boxes','poslike_metaboxes');

function postlike_metaboxes(WP_Post $post){
   // the_post_likes($post->ID);
    $value=get_post_meta($post->ID,'_allow_likes',true);
    $checked= $value==1 ? "checked":'';
    printf("<input %s type='checkbox' name='post_likes_allow' value='1' >  Allow Like/Dislike  ",$checked);
}

function post_save_allow_like($post_id,$post,$update){
    $value=isset($_POST['post_likes_allow'])?1:0;
    update_post_meta($post_id,'_allow_likes',$value);

}
add_action('save_post','post_save_allow_like',10,3);






require_once __DIR__ .'/settings-admin.php';

?>