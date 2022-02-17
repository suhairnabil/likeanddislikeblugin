<?php



function likeplugin_settings(){

    register_setting('reading','like_dislike_activated');

    add_settings_section(
        'myplugin_settings_section',
        'Site States',
        'myplugin_settings_section',
        'reading'
    );
    add_settings_field(
        'like_dislike_activated',
        'Like / Dislike post',
        'my_plugin_like_field',
        'reading',
        'myplugin_settings_section',
    );
}
add_action('admin_init','likeplugin_settings');

function my_plugin_like_field(){
   $value= get_option('like_dislike_activated');
   if($value == 1){$sel='checked';}else{$sel='';}
    printf( '<input type=checkbox name="like_dislike_activated" '.$sel.' value="%d" > ',esc_attr($value));
}


?>