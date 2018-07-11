<?php

class Greenmart_Tbay_Woo_Carousel extends Tbay_Widget {
    public function __construct() {
        parent::__construct(
            'tbay_woo_carousel',
            esc_html__('Tbay woocommerce Carousel Widget', 'greenmart'),
            array( 'description' => esc_html__( 'Show list product', 'greenmart' ), )
        );
        $this->widgetName = 'woo_carousel';
    }

    public function getTemplate() {
        $this->template = 'woo-carousel.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        } else {
            $title = esc_html__( 'Title', 'greenmart' );
        }        
		
		if ( isset( $instance[ 'sub_title' ] ) ) {
            $sub_title = $instance[ 'sub_title' ];
        } else {
            $sub_title = esc_html__( 'Sub Title', 'greenmart' );
        }
   

        if(isset($instance[ 'categories' ])){
            $categories = $instance[ 'categories' ];
        } else {
            $categories ='';
        }        

        if(isset($instance[ 'types' ])){
            $types = $instance[ 'types' ];
        } else {
            $types ='';
        }       
 
        if(isset($instance[ 'numbers' ])){
            $numbers = $instance[ 'numbers' ];
        } else {
            $numbers = 4;
        }        

        if(isset($instance[ 'columns' ])){
            $columns = $instance[ 'columns' ];
        } else {
            $columns = 4;
        }        

        if(isset($instance[ 'columns_destsmall' ])){
            $columns_destsmall = $instance[ 'columns_destsmall' ];
        } else {
            $columns_destsmall = 3;
        }        

        if(isset($instance[ 'columns_tablet' ])){
            $columns_tablet = $instance[ 'columns_tablet' ];
        } else {
            $columns_tablet = 2;
        }        

        if(isset($instance[ 'columns_mobile' ])){
            $columns_mobile = $instance[ 'columns_mobile' ];
        } else {
            $columns_mobile = 1;
        }

        if(isset($instance[ 'rows' ])){
            $rows = $instance[ 'rows' ];
        } else {
            $rows = 1;
        }

        if(isset($instance[ 'navigations' ])){
            $navigations = $instance[ 'navigations' ];
        } else {
            $navigations = 'no';
        }        

        if(isset($instance[ 'paginations' ])){
            $paginations = $instance[ 'paginations' ];
        } else {
            $paginations = 'no';
        }

        $alltypes = array(
            'Best Selling' => 'best_selling',
            'Featured Products' => 'featured_product',
            'Recent Products' => 'recent_product',
            'On Sale' => 'on_sale',
            'Random products' => 'rand'
        );

        $allcolumns = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            6 => 6
        );

        $allrows    = array(
            1 => 1,
            2 => 2,
            3 => 3
        );

        $allnavigations  = array(
                'No' => 'no',
                'Yes' => 'yes'
        );        

        $allpaginations  = array(
                'No' => 'no',
                'Yes' => 'yes'
        );

        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'greenmart' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>        
		
		<p>
            <label for="<?php echo esc_attr($this->get_field_id( 'sub_title' )); ?>"><?php esc_html_e( 'Sub Title:', 'greenmart' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'sub_title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sub_title' )); ?>" type="text" value="<?php echo esc_attr( $sub_title ); ?>" />
        </p>

        <p>

            <?php 
            $taxonomy     = 'product_cat';
            $orderby      = 'name';  
            $show_count   = 1;      // 1 for yes, 0 for no
            $pad_counts   = 0;      // 1 for yes, 0 for no
            $hierarchical = 1;      // 1 for yes, 0 for no  
            $title        = '';  
            $empty        = 0;

            $args = array(
                'taxonomy'     => $taxonomy,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
            );

            $all_categories = get_categories( $args );

            ?>
            <label for="<?php echo esc_attr($this->get_field_id( 'categories' )); ?>"><?php esc_html_e( 'Please select category to show:', 'greenmart' ); ?></label>


            <?php if(!empty($all_categories)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('categories')); ?>" name="<?php echo esc_attr($this->get_field_name('categories')); ?>">
                <?php
                foreach ($all_categories as $cat) {


                if($cat->category_parent == 0) {

                    $category_slug = $cat->slug;


                    printf(

                        '<option value="%s" %s>%s (%s)</option>',

                        esc_attr($category_slug),

                        ( $category_slug == $categories ) ? 'selected="selected"' : '',

                        esc_html($cat->name),

                        esc_html($cat->count)

                    );


                    }

                }
            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No woocommerce category found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>        

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'numbers' )); ?>"><?php esc_html_e( 'Number of products to show:', 'greenmart' ); ?></label>

            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'numbers' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'numbers' )); ?>" type="text" value="<?php echo  esc_attr( $numbers ); ?>" 
        </p>        

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'types' )); ?>"><?php esc_html_e( 'Type Products:', 'greenmart' ); ?></label>


            <?php if(!empty($alltypes)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('types')); ?>" name="<?php echo esc_attr($this->get_field_name('types')); ?>">
                <?php 

                foreach ($alltypes as $key => $type) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($type),

                        ( $type == $types ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose type product found ','greenmart'); ?>

            <?php endif; ?>

        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'columns' )); ?>"><?php esc_html_e( 'Columns:', 'greenmart' ); ?></label>


            <?php if(!empty($allcolumns)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
                <?php 

                foreach ($allcolumns as $key => $column) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($column),

                        ( $column == $columns ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose columns product found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>          

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'columns_destsmall' )); ?>"><?php esc_html_e( 'Columns screen desktop small:', 'greenmart' ); ?></label>


            <?php if(!empty($allcolumns)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('columns_destsmall')); ?>" name="<?php echo esc_attr($this->get_field_name('columns_destsmall')); ?>">
                <?php 

                foreach ($allcolumns as $key => $column) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($column),

                        ( $column == $columns_destsmall ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose columns desktop small product found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>   

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'columns_tablet' )); ?>"><?php esc_html_e( 'Columns screen tablet:', 'greenmart' ); ?></label>


            <?php if(!empty($allcolumns)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('columns_tablet')); ?>" name="<?php echo esc_attr($this->get_field_name('columns_tablet')); ?>">
                <?php 

                foreach ($allcolumns as $key => $column) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($column),

                        ( $column == $columns_tablet ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose columns table product found ','greenmart'); ?>

            <?php endif; ?>

        </p>           

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'columns_mobile' )); ?>"><?php esc_html_e( 'Columns screen mobile:', 'greenmart' ); ?></label>


            <?php if(!empty($allcolumns)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('columns_mobile')); ?>" name="<?php echo esc_attr($this->get_field_name('columns_mobile')); ?>">
                <?php 

                foreach ($allcolumns as $key => $column) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($column),

                        ( $column == $columns_mobile ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose columns table product found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>   

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'rows' )); ?>"><?php esc_html_e( 'Rows:', 'greenmart' ); ?></label>


            <?php if(!empty($allrows)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('rows')); ?>" name="<?php echo esc_attr($this->get_field_name('rows')); ?>">
                <?php 

                foreach ($allrows as $key => $row) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($row),

                        ( $row == $rows ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose rows product found ','greenmart'); ?>

            <?php endif; ?>

        </p>       

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'navigations' )); ?>"><?php esc_html_e( 'Navigation:', 'greenmart' ); ?></label>


            <?php if(!empty($allnavigations)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('navigations')); ?>" name="<?php echo esc_attr($this->get_field_name('navigations')); ?>">
                <?php 

                foreach ($allnavigations as $key => $navi) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($navi),

                        ( $navi == $navigations ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose navigation product found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>        

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'paginations' )); ?>"><?php esc_html_e( 'Pagination:', 'greenmart' ); ?></label>


            <?php if(!empty($allnavigations)) :  ?>

            <select id="<?php echo esc_attr($this->get_field_id('paginations')); ?>" name="<?php echo esc_attr($this->get_field_name('paginations')); ?>">
                <?php 

                foreach ($allpaginations as $key => $pagi) {
                     printf(

                        '<option value="%s" %s>%s</option>',

                        esc_attr($pagi),

                        ( $pagi == $paginations ) ? 'selected="selected"' : '',

                        esc_html($key)

                    );

                    }

            ?>
            </select>

            <?php else: ?>

                <?php echo esc_html__('No choose pagination product found ', 'greenmart'); ?>

            <?php endif; ?>

        </p>

<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
        $instance['sub_title']      = ( ! empty( $new_instance['sub_title'] ) ) ? strip_tags( $new_instance['sub_title'] ) : '';

        $instance['categories'] = ( ! empty( $new_instance['categories'] ) ) ? strip_tags( $new_instance['categories'] ) : '';

        $instance['types']      = ( ! empty( $new_instance['types'] ) ) ? strip_tags( $new_instance['types'] ) : '';

        $instance['numbers']    = ( ! empty( $new_instance['numbers'] ) ) ? strip_tags( $new_instance['numbers'] ) : '';

        $instance['columns']    = ( ! empty( $new_instance['columns'] ) ) ? strip_tags( $new_instance['columns'] ) : '';

        $instance['columns_destsmall']    = ( ! empty( $new_instance['columns_destsmall'] ) ) ? strip_tags( $new_instance['columns_destsmall'] ) : '';       

        $instance['columns_tablet']    = ( ! empty( $new_instance['columns_tablet'] ) ) ? strip_tags( $new_instance['columns_tablet'] ) : '';        

        $instance['columns_mobile']    = ( ! empty( $new_instance['columns_mobile'] ) ) ? strip_tags( $new_instance['columns_mobile'] ) : '';

        $instance['rows']       = ( ! empty( $new_instance['rows'] ) ) ? strip_tags( $new_instance['rows'] ) : '';

        $instance['navigations']  = ( ! empty( $new_instance['navigations'] ) ) ? strip_tags( $new_instance['navigations'] ) : '';

        $instance['paginations']  = ( ! empty( $new_instance['paginations'] ) ) ? strip_tags( $new_instance['paginations'] ) : '';


        return $instance;
    }
}

register_widget( 'Greenmart_Tbay_Woo_Carousel' );