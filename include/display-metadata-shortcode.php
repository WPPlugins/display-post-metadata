<?php
/**
 * Display_Metadata_shortcode
 */
class Display_Metadata_shortcode{
    /**
     * $shortcode_tag
     * holds the name of the shortcode tag
     * @var string
     */
    public $shortcode_tag = 'metadata';

    /**
     * __construct
     * class constructor will set the needed filter and action hooks
     *
     * @param array $args
     */
    function __construct($args = array()){

        //add shortcode
        add_shortcode( $this->shortcode_tag, array( $this, 'shortcode_handler' ) );

        if ( is_admin() ){
            add_action('admin_head', array( $this, 'admin_head') );
            add_action( 'admin_enqueue_scripts', array($this , 'admin_enqueue_scripts' ) );
        }
    }

    /**
     * shortcode_handler
     * @param  array  $atts shortcode attributes
     * @param  string $content shortcode content
     * @return string
     */
    function shortcode_handler( $atts , $content = null ) {

        $elements = explode( ',', $atts['element'] );

        echo '<div id="dpm-wrap"><ul class="display-post-metadata">';
        foreach( $elements as $element ) {

            switch( $element ) {
                case 'date':
                    echo '<li class="date-meta"><img src="'. plugin_dir_url( dirname( __FILE__ ) ) .'svg/date.svg" alt="date"><span>'. get_the_date() .'</span></li>';
                    break;
                case 'author':
                    echo '<li class="author-meta"><img src="'. plugin_dir_url( dirname( __FILE__ ) ) .'svg/user.svg" alt="user"><span>'. get_the_author() .'</span></li>';
                    break;
                case 'sticky':
                    if ( is_sticky() ) { echo '<li class="sticky-meta"><img src="'. plugin_dir_url( dirname( __FILE__ ) ) .'svg/sticky.svg" alt="sticky"><span>'. __( 'Sticky', 'display-post-metadata') .'</span></li>';  }
                    break;
                case 'views':
                    display_pmd_setPostViews( get_the_ID() );
                    echo '<li class="views-meta"><img src="'. plugin_dir_url( dirname( __FILE__ ) ) .'svg/eye.svg" alt="view"><span>'. display_pmd_getPostViews( get_the_ID() ) .'</span></li>';
                    break;
                case 'comments':
                    echo '<li class="comment-meta"><img src="'. plugin_dir_url( dirname( __FILE__ ) ) .'svg/comment.svg" alt="comment"><span>';
                    comments_number( __( 'No Comments', 'display-post-metadata'), __( 'one Comment', 'display-post-metadata'), '% ' . __( 'Comments', 'display-post-metadata') );
                    echo '</span></li>';
                    break;
            }
        }
        echo '</ul></div>';
    }

    /**
     * admin_head
     * calls your functions into the correct filters
     * @return [type] [description]
     */
    function admin_head() {
        // check user permissions
        if ( ! current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

        // check if WYSIWYG is enabled
        if ( 'true' == get_user_option( 'rich_editing' ) ) {
            add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
            add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
        }
    }

    /**
     * mce_external_plugins
     * Adds our tinymce plugin
     * @param  array $plugin_array
     * @return array
     */
    function mce_external_plugins( $plugin_array ) {
        $plugin_array[$this->shortcode_tag] = plugins_url( 'js/mce-button.js' , dirname( __FILE__ ) );
        return $plugin_array;
    }

    /**
     * mce_buttons
     * Adds our tinymce button
     * @param  array $buttons
     * @return array
     */
    function mce_buttons( $buttons ) {
        array_push( $buttons, $this->shortcode_tag );
        return $buttons;
    }

    /**
     * admin_enqueue_scripts
     * Used to enqueue custom styles
     * @return void
     */
    function admin_enqueue_scripts(){
        wp_enqueue_style('display_metadata_shortcode', plugins_url( 'css/mce-button.css' , dirname( __FILE__ ) ) );
    }

}//end class
