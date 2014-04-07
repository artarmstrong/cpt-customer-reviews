<?php

/*-------------------------------------------
	Shortcodes
---------------------------------------------*/

// [bcr-single id="12"]
function cr_single_func( $atts ) {
	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );

	return "foo = {$foo}";
}
add_shortcode( 'cust-reviews-single', 'cr_single_func' );

// [cust-reviews]
function cr_all_func( $atts ) {

  global $post;

  extract( shortcode_atts( array(
		'id' => '',
		'cat' => '',
	), $atts ) );

  // Start our output
  $output = "";
  $output .= "<ul>";

  // Get all review arguments
  if(!empty($id) && is_numeric($id)){
    $args = array( 'post_type' => 'cust-review', 'posts_per_page' => 1, 'p' => $id );
  }elseif(!empty($cat)){
    $term = term_exists($cat, 'cust-review-cat');
    if ($term !== 0 && $term !== null) {
      if(is_numeric($term)){
        $args = array(
          'post_type' => 'cust-review',
          'posts_per_page' => -1,
          'tax_query' => array(
        		array(
        			'taxonomy' => 'cust-review-cat',
        			'field' => 'term_id',
        			'terms' => $term
        		)
        	)
        );
      }elseif(is_array($term)){
        $args = array(
          'post_type' => 'cust-review',
          'posts_per_page' => -1,
          'orderby' => 'rand',
          'tax_query' => array(
        		array(
        			'taxonomy' => 'cust-review-cat',
        			'field' => 'term_id',
        			'terms' => $term['term_id']
        		)
        	)
        );
      }
    }

  }else{
    $args = array( 'post_type' => 'cust-review', 'posts_per_page' => -1, 'orderby' => 'rand' );
  }

  // Get review posts
  if(is_array($args) && !empty($args))
    $reviews = get_posts( $args );
  else
    $reviews = array();
  foreach($reviews as $review):
  setup_postdata($review);

  // Get fields
  $cr_about = get_post_meta($review->ID, 'cr_about', true);
  $cr_source = get_post_meta($review->ID, 'cr_source', true);
  $cr_type = get_post_meta($review->ID, 'cr_type', true);
  $cr_date = get_post_meta($review->ID, 'cr_date', true);
  $cr_date_hide = get_post_meta($review->ID, 'cr_date_hide', true);
  $cr_str_length = 250;
  $cr_title = get_the_title($review->ID);
  $cr_link = get_permalink($review->ID);
  $cr_content = get_the_content();
  $cr_content_first = "";
  $cr_content_second = "";
  if(strlen($cr_content) > $cr_str_length){
    $cr_content_split_pos = strpos($cr_content, " ", $cr_str_length);
    $cr_content_first = substr($cr_content, 0, $cr_content_split_pos);
    $cr_content_second = substr($cr_content, $cr_content_split_pos+1);
  }

  ob_start();
  ?>

  <li>
    <div itemprop="review" itemscope itemtype="http://schema.org/Review">
      <div class="cust-review-source">
        <span class="cust-review-image">
          <img src="<?= plugins_url( '_img', dirname(__FILE__))."/".$cr_source; ?>.png" width="200px" / >
        </span>
        <span class="cust-review-rating">
          <img src="<?= plugins_url( '_img', dirname(__FILE__))."/five_stars.png"; ?>" height="20px" / >
        </span>
        <span class="cust-review-image" itemprop="reviewRating">5</span>
        <span class="cust-review-type">Trip Type: <?= $cr_type; ?></span>
      </div>
      <div class="cust-review-content">
        <span class="cust-review-title">
          <strong><a href="<?= $cr_link; ?>"><span itemprop="name"><?= $cr_title; ?></span></a></strong>
        </span>
        <span itemprop="about"><?= (!empty($cr_about) ? $cr_about : "Bed and Breakfast"); ?></span>
        <?php
        // Check if date is hidden
        if($cr_date_hide != "true"):
        ?>
        <span class="cust-review-date">
          <meta itemprop="datePublished" content="<?= date('Y-m-d', strtotime($cr_date)); ?>"><?= $cr_date; ?>
        </span>
        <?php endif; ?>
        <span class="cust-review-body">
        <span itemprop="reviewBody">
          <?php
          // Check our length
          if(strlen($cr_content) > $cr_str_length){
            echo "$cr_content_first <span id='hidden-content-{$review->ID}' style='display:none;'>$cr_content_second</span>";
          }else{
            echo $cr_content;
          }
          ?>
        </span>
        <?php
        if(strlen($cr_content) > $cr_str_length){
        echo "<span id=\"cust-review-body-more-{$review->ID}\">... </span><a class=\"show-more-content\" title=\"{$review->ID}\">More</a>";
        }?>
        </span>
      </div>
    </div>
  </li>

  <?php
  $output .= ob_get_contents();
  ob_end_clean();
  endforeach;
  wp_reset_postdata();

  // End output
  $output .= "</ul>";

  // Return
  return $output;

}
add_shortcode( 'cust-reviews', 'cr_all_func' );