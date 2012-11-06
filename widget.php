<?php
/* widget
-------------------------------------------------------------------------------------*/
class JAGMP_Widget extends WP_Widget {

    /*
     * widget fields
     */
    var $fields = array(
        array(
            'label' => 'Title',
            'name' => 'title',
            'input' => 'text',
            'type' => 'string'
        ),
        array(
            'label' => 'Postcode',
            'name' => 'postcode',
            'input' => 'text',
            'type' => 'string'
        ),
        array(
            'label' => 'Marker Text',
            'name' => 'marker',
            'input' => 'text',
            'type' => 'string'
        ),
        array(
            'label' => 'Disable Ui',
            'name' => 'disableui',
            'input' => 'checkbox',
            'type' => 'string'
        ),
    );

    function __construct() {
        $this->WP_Widget('jagmp-widget', 'Google Map (JAGMP)', array(
            'description' => 'Display a google map',
            'classname' => 'map-widget'
        ));
    }

    function widget($args, $instance) {
        extract($args);

        $output = '';

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
        $postcode = $instance['postcode'];
        $marker = $instance['marker'];
        $disableui = ($instance['disableui'] == 'yes') ? "true" : "";

        if(!empty($postcode)){
            $output .= $before_widget;

            if ($title) $output .= $before_title . $title . $after_title;

            $output .= do_shortcode(JAGMP_make_shortcode(array(
                'postcode' => $postcode,
                'disableui' => $disableui,
                'marker' => $marker
            )));

            $output .= $after_widget;

            echo $output;
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        foreach($this->fields as $field){
            $instance[$field['name']] = strip_tags($new_instance[$field['name']]);
        }

        return $instance;
    }

    function form($instance){
        global $post;

        $field_names = array();
        foreach($this->fields as $field){
            $field_names[$field['name']] = '';
        }

        $instance = wp_parse_args(
            (array) $instance,
            $field_names
        );

        foreach($this->fields as $field):
            $field_value = strip_tags($instance[$field['name']]); ?>

            <p>
                <label for="<?php echo $this->get_field_id($field['name']); ?>"><?php _e($field['label'] .':'); ?></label>
                <?php if($field['input'] == 'text'): ?>
                    <input class="widefat" id="<?php echo $this->get_field_id($field['name']); ?>" name="<?php echo $this->get_field_name($field['name']); ?>" type="text" value="<?php echo esc_attr($field_value); ?>" />
                <?php elseif($field['input'] == 'textarea'): ?>
                    <textarea class="widefat" id="<?php echo $this->get_field_id($field['name']); ?>" name="<?php echo $this->get_field_name($field['name']); ?>"><?php echo esc_attr($field_value); ?></textarea>
                <?php elseif($field['input'] == 'checkbox'):
                    $checked_state = ($field_value == 'yes') ? 'checked="checked"' : '';
                    ?>
                    <input id="<?php echo $this->get_field_id($field['name']); ?>" name="<?php echo $this->get_field_name($field['name']); ?>" type="checkbox" value="yes" <?php echo $checked_state ?> />
                <?php endif; ?>
            </p>

        <?php endforeach;

    }

}

add_action('widgets_init', function(){
    register_widget('JAGMP_Widget');
});