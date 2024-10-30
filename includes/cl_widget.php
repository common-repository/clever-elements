<?php
/**
 * @author Panayot Balkandzhiyski
 * @copyright  (c) 2011-2014 Despark Ltd.
 */
class cl_wp_pl_widget extends WP_Widget
{
    protected $ce_from_place_holder = '[CE_FORM_CODE]';
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(

            'cl_el_wp_widget',
            __('Clever Elements', 'clever-elements'),

            array('description' => __('Clever elements form', 'clever-elements'))
        );

        //$this->api = new CE_SOAPAuth(get_option('cl_wp_pl_user_id'), get_option('cl_wp_pl_api_key'), '1.0');
    }

    /**
     * @param array $args
     * @param array $instance
     *
     * @return bool
     */
    public function widget($args, $instance)
    {
        /*if (!$this->api->cewp_is_connected()) {
            return false;
        }*/

        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        echo $this->cewp_form_view();
        echo $args['after_widget'];
    }

    // Widget Backend
    /**
     * @param array $instance
     */
    public function form($instance)
    {
        if (isset($instance[ 'title' ])) {
            $title = $instance[ 'title' ];
        } else {
            $title = __('New title', 'clever-elements');
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo ($this->get_field_id('title'));
            ?>"><?php _e('Title:', 'clever-elements');
                ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));
            ?>" name="<?php echo ($this->get_field_name('title'));
            ?>" type="text" value="<?php echo ($title);
            ?>" />
        </p>
        <?php

    }

    // Updating widget replacing old instances with new
    /**
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

    /**
     * @return string
     */
    public function cewp_form_view()
    {
        $html = '';
        $html .= '<form class="cl_el_widget_from_ajax" method="post">';
        $html .= '<div class="cl_form_message">' . get_option('cl_wp_pl_forms_success_message') . '</div>';
        $html .= '<div class="cl_form_error_message">' . get_option('cl_wp_pl_forms_error_message') . '</div>';
        $html .= stripslashes(get_option('cl_wp_pl_form_html'));
        $html .= '</form>';

        return $html;
    }
    public function ce_get_form_placeholder(){
        return $this->ce_from_place_holder;
    }
} // Class end

/**
 * Register and load the widget.
 */
function cewp_widget_loader()
{
    register_widget('cl_wp_pl_widget');
}

add_action('widgets_init', 'cewp_widget_loader');
