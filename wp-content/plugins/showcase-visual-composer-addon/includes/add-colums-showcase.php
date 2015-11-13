<?php
/**
 * Testimonials columns.
 */
function showcase_posts_edit_columns( $columns ) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'showcase_image' => __( 'Image/Brand', 'showcase-visual-composer-addon' ),
        'title' => __( 'Name', 'showcase-visual-composer-addon' ),
        'showcase_texto' => __( 'Summary Content', 'showcase-visual-composer-addon' ),
        'showcase_url' => __( 'URL', 'showcase-visual-composer-addon' )       
    );
    return $columns;
}
add_filter( 'manage_edit-showcases_columns', 'showcase_posts_edit_columns' );

/**
 * Testimonials custom columns content.
 */
function showcase_posts_columns( $column, $post_id ) {
    global $post;
    switch ( $column ) {
        case 'showcase_image':
            $sc_carousel_thumb = get_the_post_thumbnail(  $post_id, 'thumbnail' );
            echo sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', admin_url( 'post.php?post=' . $post_id . '&action=edit' ), get_the_title(), $sc_carousel_thumb );
            break;
        case 'showcase_texto':
            $sc_carousel_texto = get_post_field('post_content', $post_id); /* or you can use get_the_title() */
            $sc_carousel_getlength = strlen($sc_carousel_texto);
            $sc_carousel_thelength = 120;
            echo substr($sc_carousel_texto, 0, $sc_carousel_thelength);
            if ($sc_carousel_getlength > $sc_carousel_thelength) echo "...";
            break;
        case 'showcase_url':
            $vc_showcase_facebook = get_post_meta( $post_id, 'vc_showcase_facebook', true); 
            $vc_showcase_google_plus = get_post_meta( $post_id, 'vc_showcase_google_plus', true); 
            $vc_showcase_linkedin = get_post_meta( $post_id, 'vc_showcase_linkedin', true); 
            $vc_showcase_custom_link = get_post_meta( $post_id, 'vc_showcase_custom_link', true); 
            echo ! empty( $vc_showcase_facebook ) ? sprintf( '<a href="%1$s" target="_blank">%1$s</a><br />', esc_url( $vc_showcase_facebook ) ) : '';
            echo ! empty( $vc_showcase_google_plus ) ? sprintf( '<a href="%1$s" target="_blank">%1$s</a><br />', esc_url( $vc_showcase_google_plus ) ) : '';
            echo ! empty( $vc_showcase_linkedin ) ? sprintf( '<a href="%1$s" target="_blank">%1$s</a><br />', esc_url( $vc_showcase_linkedin ) ) : '';
            echo ! empty( $vc_showcase_custom_link ) ? sprintf( '<a href="%1$s" target="_blank">%1$s</a><br />', esc_url( $vc_showcase_custom_link ) ) : '';
            break;
    }
}

add_action( 'manage_posts_custom_column', 'showcase_posts_columns', 1, 2 );