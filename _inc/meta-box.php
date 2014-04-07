<?php

/*-------------------------------------------
	Custom Meta Box
---------------------------------------------*/
add_action( 'add_meta_boxes', 'cr_custom_meta_box_add' );
add_action( 'save_post', 'cr_custom_meta_box_save' );

/* Adds a box to the main column on the Post and Page edit screens */
function cr_custom_meta_box_add() {

  add_meta_box(
    "cr_review_information",
    __( "Review Information", "customer-reviews" ),
    "cr_custom_meta_general_information",
    "cust-review",
    "normal",
    "high"
  );

}

/* When the post is saved, saves our custom data */
function cr_custom_meta_box_save( $post_id ) {

	// Check if its an autosave, if so, do nothing
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	  return;

	// Verify this came from the our screen and with proper authorization,
	if ( isset($_POST['cr_noncename']) && !wp_verify_nonce( $_POST['cr_noncename'], basename( __FILE__ ) ) )
	  return;

	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
    return;

	// Review Information
	$cr_about      = (isset($_POST['cr_about']) ? $_POST['cr_about'] : "");
	$cr_source     = (isset($_POST['cr_source']) ? $_POST['cr_source'] : "");
	$cr_type       = (isset($_POST['cr_type']) ? $_POST['cr_type'] : "");
	$cr_date       = (isset($_POST['cr_date']) ? $_POST['cr_date'] : "");
	$cr_date_hide  = (isset($_POST['cr_date_hide']) ? $_POST['cr_date_hide'] : "");
	update_post_meta($post_id, 'cr_about',     $cr_about);
	update_post_meta($post_id, 'cr_source',    $cr_source);
	update_post_meta($post_id, 'cr_type',      $cr_type);
	update_post_meta($post_id, 'cr_date',      $cr_date);
	update_post_meta($post_id, 'cr_date_hide', $cr_date_hide);

  // Return
	return;

}

/* Prints the box content */
function cr_custom_meta_general_information( $post ) {

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'cr_noncename' );

	// Fields
	?>
	<div style="float:left;" class="cr_form">

		<table>
			<tr>
				<td width="100" style="padding: 5px 15px" valign="middle">
					<label>Shortcode</label>
				</td>
				<td colspan="3">
          <span style="color:#6E6E6E">[cust-reviews id="<?= $post->ID; ?>"]</span>
				</td>
			</tr>
			<tr>
				<td width="100" style="padding: 5px 15px" valign="middle">
					<label>Room Type</label>
				</td>
				<td colspan="3">
				  <?php
				  // Type field
				  $cr_about = get_post_meta($post->ID, 'cr_about', true);
				  ?>
				  <input type="text" id="cr_about" name="cr_about" style="width:200px;" placeholder="Bed and Breakfast" value="<?= (!empty($cr_about) ? $cr_about : ""); ?>" />
				</td>
			</tr>
			<tr>
				<td width="100" style="padding: 5px 15px" valign="middle">
					<label>Source</label>
				</td>
				<td colspan="3">
				  <?php
				  // Source field
				  $cr_source = get_post_meta($post->ID, 'cr_source', true);
				  ?>
					<select id="cr_source" name="cr_source" style="width:200px;">
					  <option value="BedAndBreakfast" <?= ($cr_source == "BedAndBreakfast" ? "selected" : ""); ?>>BedAndBreakfast.com</option>
					  <option value="GooglePlus" <?= ($cr_source == "GooglePlus" ? "selected" : ""); ?>>GooglePlus</option>
					  <option value="TripAdvisor" <?= ($cr_source == "TripAdvisor" ? "selected" : ""); ?>>TripAdvisor</option>
					  <option value="Yelp" <?= ($cr_source == "Yelp" ? "selected" : ""); ?>>Yelp</option>
					  <option value="BNBFinder" <?= ($cr_source == "BNBFinder" ? "selected" : ""); ?>>BNBFinder.com</option>
					  <option value="Expedia" <?= ($cr_source == "Expedia" ? "selected" : ""); ?>>Expedia</option>
					  <option value="Travelocity" <?= ($cr_source == "Travelocity" ? "selected" : ""); ?>>Travelocity</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="100" style="padding: 5px 15px" valign="middle">
					<label>Type of Trip</label>
				</td>
				<td colspan="3">
				  <?php
				  // Type field
				  $cr_type = get_post_meta($post->ID, 'cr_type', true);
				  ?>
					<select id="cr_type" name="cr_type" style="width:200px;">
            <option value="Business" <?= ($cr_type == "Business" ? "selected" : ""); ?>>Business</option>
            <option value="Couple" <?= ($cr_type == "Couple" ? "selected" : ""); ?>>Couple</option>
            <option value="Family" <?= ($cr_type == "Family" ? "selected" : ""); ?>>Family</option>
            <option value="Solo" <?= ($cr_type == "Solo" ? "selected" : ""); ?>>Solo</option>
            <option value="Everyone" <?= ($cr_type == "Everyone" ? "selected" : ""); ?>>Everyone</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="100" style="padding: 5px 15px" valign="middle">
					<label>Date of Review</label>
				</td>
				<td colspan="3">
				  <?php
				  // Type field
				  $cr_date = get_post_meta($post->ID, 'cr_date', true);
				  $cr_date_hide = get_post_meta($post->ID, 'cr_date_hide', true);
				  ?>
				  <input type="text" id="cr_date" name="cr_date" style="width:200px;" value="<?= (!empty($cr_date) ? $cr_date : ""); ?>" />
				  <input type="checkbox" id="cr_date_hide" name="cr_date_hide" value="true" <?= ($cr_date_hide == "true" ? "checked" : ""); ?> /> Hide Date?
				</td>
			</tr>
		</table>

	</div>
	<div style="clear:both;"></div>
	<?php
}