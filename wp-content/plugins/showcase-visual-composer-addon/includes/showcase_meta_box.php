<?php
/*
** Register MetaBox - Showcase
*/

function showcase_meta_box(){
    add_meta_box(
        'showcase_meta_box',
        __('More Info - Showcase', 'showcase-visual-composer-addon'),
        'create_shocase_meta_box',
        'showcases',
        'advanced',
        'high'
    );
};

function create_shocase_meta_box(){
    global $post;

    $meta_element_align = get_post_meta($post->ID, 'custom_element_grid_align_meta_box', true);
    $vc_showcase_facebook = get_post_meta($post->ID, 'vc_showcase_facebook', true);
    $vc_showcase_google_plus = get_post_meta($post->ID, 'vc_showcase_google_plus', true);
    $vc_showcase_linkedin = get_post_meta($post->ID, 'vc_showcase_linkedin', true);
    $vc_showcase_custom_link = get_post_meta($post->ID, 'vc_showcase_custom_link', true);

    ?>

    <dl class="showcase-visual-composer-addon-admin">
        <dd>
            <div class="label-showcase-visual-composer-addon">
                <label><?php _e('Image default align');?></label>
            </div>
            <div class="input-showcase-visual-composer-addon">
                <select name="custom_element_grid_align" id="custom_element_grid_align">
                    <option value="left" <?php selected( $meta_element_align, 'left' ); ?>><?php _e('Left', 'showcase-visual-composer-addon');?></option>
                    <option value="center" <?php selected( $meta_element_align, 'center' ); ?>><?php _e('Center', 'showcase-visual-composer-addon');?></option>
                    <option value="right" <?php selected( $meta_element_align, 'right' ); ?>><?php _e('Right', 'showcase-visual-composer-addon');?></option>
                </select>
            </div>
        </dd>
        <dd>
            <div class="label-showcase-visual-composer-addon">
                <label for="vc-showcase-facebook"><?php _e('Facebook:', 'showcase-visual-composer-addon');?></label>
            </div>
            <div class="input-showcase-visual-composer-addon">
                <input type="text" name="vc_showcase_facebook" id="vc-showcase-facebook" value="<?php echo $vc_showcase_facebook;?>" />
                <em><?php _e("Please, don't forget to add <strong>http://</strong> in your link...", 'showcase-visual-composer-addon');?></em>
            </div>
        </dd>
        <dd>
            <div class="label-showcase-visual-composer-addon">
                <label for="vc-showcase-google-plus"><?php _e('Google Plus:', 'showcase-visual-composer-addon');?></label>
            </div>
            <div class="input-showcase-visual-composer-addon">
                <input type="text" name="vc_showcase_google_plus" id="vc-showcase-google-plus" value="<?php echo $vc_showcase_google_plus;?>" />
                <em><?php _e("Please, don't forget to add <strong>http://</strong> in your link...", 'showcase-visual-composer-addon');?></em>
            </div>
        </dd>
        <dd>
            <div class="label-showcase-visual-composer-addon">
                <label for="vc-showcase-linkedin"><?php _e('LinkedIn:', 'showcase-visual-composer-addon');?></label>
            </div>
            <div class="input-showcase-visual-composer-addon">
                <input type="text" name="vc_showcase_linkedin" id="vc-showcase-linkedin" value="<?php echo $vc_showcase_linkedin;?>" />
                <em><?php _e("Please, don't forget to add <strong>http://</strong> in your link...", 'showcase-visual-composer-addon');?></em>
            </div>
        </dd>
        <dd>
            <div class="label-showcase-visual-composer-addon">
                <label for="vc-showcase-custom-link"><?php _e('Custom Link:', 'showcase-visual-composer-addon');?></label>
            </div>
            <div class="input-showcase-visual-composer-addon">
                <input type="text" name="vc_showcase_custom_link" id="vc-showcase-custom-link" value="<?php echo $vc_showcase_custom_link;?>" />
                <em><?php _e("Please, don't forget to add <strong>http://</strong> in your link...", 'showcase-visual-composer-addon');?></em>
            </div>
        </dd>
    </dl>

<?php
};

add_action( 'save_post', 'showcase_save_post', 1, 2 );

function showcase_save_post( $post_id, $post ) {

    if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( is_int( wp_is_post_revision( $post ) ) ) return;
    if ( is_int( wp_is_post_autosave( $post ) ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( $post->post_type != 'showcases' ) return;

    update_post_meta($post->ID, 'vc_showcase_facebook', $_POST['vc_showcase_facebook']);
    update_post_meta($post->ID, 'vc_showcase_google_plus', $_POST['vc_showcase_google_plus']);
    update_post_meta($post->ID, 'vc_showcase_linkedin', $_POST['vc_showcase_linkedin']);
    update_post_meta($post->ID, 'vc_showcase_custom_link', $_POST['vc_showcase_custom_link']);

    if(isset($_POST["custom_element_grid_align"])){
        $meta_element_align = $_POST['custom_element_grid_align'];
        update_post_meta($post->ID, 'custom_element_grid_align_meta_box', $meta_element_align);
    }

}

