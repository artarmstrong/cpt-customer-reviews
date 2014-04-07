<?php

/*-------------------------------------------
	Activate / Deactivate
---------------------------------------------*/

function cr_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cr_activate' );

function cr_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cr_deactivate' );