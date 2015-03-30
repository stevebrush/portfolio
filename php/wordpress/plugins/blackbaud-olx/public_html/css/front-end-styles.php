<?php
$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $absolute_path[0] . 'wp-load.php';
require_once($wp_load);
header("Content-type: text/css; charset: UTF-8");
global $BlackbaudOnlineExpress;
$OLX_DATA = $BlackbaudOnlineExpress->GetOptionsData();
?>
<?php if (isset ($OLX_DATA["includeFontAwesome"]) && $OLX_DATA["includeFontAwesome"] == true) : ?>
/* FONT AWESOME */
@import "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css";
<?php endif; ?>
<?php if (isset ($OLX_DATA["includeBootstrap"]) && $OLX_DATA["includeBootstrap"] == true) : ?>
/* BOOTSTRAP */
@import "<?php echo OLXFORMS_CSS_URL; ?>bootstrap.min.css";
<?php endif; ?>
#bbox-root .fa,
#olx-forms-modal .fa {
	margin-right: 7px;
}
#bbox-root .olx-off,
#olx-forms-modal .olx-off {
	display: none;
}
<?php if (isset ($OLX_DATA["includeDefaultStyles"]) && $OLX_DATA["includeDefaultStyles"] == true) : ?>
	#bboxdonation_BBEmbeddedForm.BBFormContainer {
		margin: 0 0 15px;
		padding: 0;
	}
	#olx-forms-modal .btn-social {
		color: #fff;
		text-align: left;
		font-size: 16px;
	}
		#olx-forms-modal .btn-social:hover,
		#olx-forms-modal .btn-social:focus,
		#olx-forms-modal .btn-social:active,
		#olx-forms-modal .btn-social.active,
		#olx-forms-modal .open .dropdown-toggle.btn-social {
			color: #fff;
			border-color: transparent;
		}
	#olx-forms-modal .btn-facebook,
	#olx-forms-modal .btn-facebook:hover,
	#olx-forms-modal .btn-facebook:focus,
	#olx-forms-modal .btn-facebook:active,
	#olx-forms-modal .btn-facebook.active,
	#olx-forms-modal .open .dropdown-toggle.btn-facebook {
		background-color: #3b5999;
	}
	#olx-forms-modal .btn-twitter,
	#olx-forms-modal .btn-twitter:hover,
	#olx-forms-modal .btn-twitter:focus,
	#olx-forms-modal .btn-twitter:active,
	#olx-forms-modal .btn-twitter.active,
	#olx-forms-modal .open .dropdown-toggle.btn-twitter {
		background-color: #55acee;
	}
	#olx-forms-modal .btn-google-plus,
	#olx-forms-modal .btn-google-plus:hover,
	#olx-forms-modal .btn-google-plus:focus,
	#olx-forms-modal .btn-google-plus:active,
	#olx-forms-modal .btn-google-plus.active,
	#olx-forms-modal .open .dropdown-toggle.btn-google-plus {
		background-color: #dd4b39;
	}
	#olx-forms-modal .btn-email,
	#olx-forms-modal .btn-email:hover,
	#olx-forms-modal .btn-email:focus,
	#olx-forms-modal .btn-email:active,
	#olx-forms-modal .btn-email.active,
	#olx-forms-modal .open .dropdown-toggle.btn-email {
		background-color: #333;
	}
<?php endif; ?>
