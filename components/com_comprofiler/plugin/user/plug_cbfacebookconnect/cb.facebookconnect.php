<?php
/**
* Facebook CB plugin for Community Builder
* @version $Id: cb.facebookconnect.php 1102 2010-05-06 12:56:04Z kyle $
* @package Community Builder
* @subpackage Facebook CB plugin
* @author Kyle and Beat
* @copyright (C) http://www.joomlapolis.com and various
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) {
	die( 'Direct Access to this location is not allowed.' );
}

global $_PLUGINS;
$_PLUGINS->registerFunction( 'onAfterLoginForm', 'getDisplay', 'cbfacebookconnectPlugin' );
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'getDisplay', 'cbfacebookconnectPlugin' );
$_PLUGINS->registerFunction( 'onBeforeLogout', 'clearLogin', 'cbfacebookconnectPlugin' );
$_PLUGINS->registerFunction( 'onPrepareMenus', 'getMenu','cbfacebookconnectPlugin' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'facebook_userid' => 'cbfacebookconnectField' ) );

class cbfacebookconnectField extends cbFieldHandler {

	/**
	 * Returns a field icon
	 *
	 * @param  cbFieldHandler       $fieldHandler
	 * @param  moscomprofilerFields $field
	 * @param  moscomprofilerUser   $user
	 * @param  string               $output
	 * @param  string               $reason      'profile' for user profile view and edit, 'register' for registration
	 * @param  string               $tag         <tag
	 * @param  string               $type        type="$type"
	 * @param  string               $value       value="$value"
	 * @param  string               $additional  'xxxx="xxx" yy="y"'
	 * @param  string               $allValues
	 * @param  boolean              $displayFieldIcons
	 * @param  boolean              $required
	 * @return string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types ) {
		global $_CB_framework;

		$value						=	$user->get( $field->name );
		$return						=	null;

		switch( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$fieldFormat	=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null );
					$return			=	$this->_fieldSearchModeHtml( $field, $user, $fieldFormat, 'text', $list_compare_types );
				} else {
					if ( $_CB_framework->getUi() == 2 ) {
						$return		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null );
					}
				}
				break;
			case 'html':
			case 'rss':
				if ( $value ) {
					$value			=	'<a href="http://www.facebook.com/profile.php?id=' . htmlspecialchars( urlencode( $value ) ) . '" target="_blank">' . htmlspecialchars( CBTxt::T( 'View Facebook Profile' ) ) . '</a>';
				}

				$return				=	$this->_formatFieldOutput( $field->name, $value, $output, false );
				break;
			default:
				$return				=	$this->_formatFieldOutput( $field->name, $value, $output );
				break;
		}

		return $return;
	}
}

class cbfacebookconnectPlugin extends cbPluginHandler {

	/**
	 * Prepare and display connect or logout based on facebook status
	 *
	 * @return string
	 */
	public function getDisplay( $name_lenght, $pass_lenght, $horizontal, $class_sfx, $params ) {
		global $_CB_framework;

		$plugin					=	cbfacebookconnectClass::getPlugin();
		$facebook				=	cbfacebookconnect::getApi();
		$return					=	null;

		if ( $facebook ) {
			$fb_user			=	cbfacebookconnect::getUser();
			$myId				=	$_CB_framework->myId();

			if ( $fb_user ) {
				$session		=	cbfacebookconnectClass::getSession();

				if ( $session->get( 'fb_logout' ) ) {
					cbfacebookconnect::unsetUser();

					$session->set( 'fb_logout', false );

					$session->session_write_close();

					$fb_user	=	null;
				}
			}

			static $CSS_loaded	=	0;

			if ( ! $CSS_loaded++ ) {
				$_CB_framework->document->addHeadStyleSheet( $plugin->livePath . '/cb.facebookconnect.css' );
			}

			if ( $horizontal ) {
				$class			=	'fbc_button_small';
			} else {
				$class			=	'fbc_button';
			}

			if ( $fb_user ) {
				cbfacebookconnectPlugin::syncUser( $plugin, $fb_user );

				if ( $myId ) {
					$_CB_framework->outputCbJQuery( "$( '#mod_login_logoutform' ).find( ':input[type=submit]' ).remove();" );
				}

				$return			=	'<input class="' . $class . '" onclick="fbc_logout();" type="button" title="' . htmlspecialchars( CBTxt::T( 'Logout of your Facebook account.' ) ) . '" value="' . ( $horizontal ? null : htmlspecialchars( CBTxt::T( 'Sign out' ) ) ) . '" />';
			} else {
				$return			=	'<input class="' . $class . '" onclick="fbc_login();"  type="button" title="' . htmlspecialchars( CBTxt::T( 'Login with your Facebook account.' ) ) . '" value="' . ( $horizontal ? null : htmlspecialchars( CBTxt::T( 'Sign in' ) ) ) . '" />';
			}

			if ( ! $myId ) {
				$return			=	array( 'afterButton' => $return );
			}
		}

		return $return;
	}

	/**
	 * clears facebook login session
	 */
	public function clearLogin() {
		if ( cbfacebookconnect::getUser() ) {
			$session	=	cbfacebookconnectClass::getSession();

			$session->set( 'fb_logout', true );

			$session->session_write_close();
		}
	}

	/**
	 * synchronize facebook user with CB
	 *
	 * @param object $plugin
	 * @param object $fb_user
	 * @param int $user_id
	 * @return boolean
	 */
	private function syncUser( $plugin, $fb_user, $user_id = null ) {
		global $_CB_framework;

		cbimport( 'cb.html' );

		$myId				=	$_CB_framework->myId();

		if ( ! $user_id ) {
			$user_id		=	cbfacebookconnectPlugin::getUser( $fb_user->id );
		}

		if ( $myId ) {
			if ( $user_id ) {
				if ( $user_id != $myId ) {
					cbfacebookconnectPlugin::clearSession( 'Facebook ID already in use or account mismatch!' );

					return false;
				}
			} else {
				$user_id	=	$myId;
			}
		}

		$cbUser				=&	CBuser::getInstance( (int) $user_id );

		if ( ! $cbUser ) {
			$cbUser			=&	CBuser::getInstance( null );
		}

		$user				=&	$cbUser->getUserData();

		if ( $user->id ) {
			if ( ! cbfacebookconnectPlugin::updateUser( $user, $fb_user ) ) {
				cbfacebookconnectPlugin::clearSession( $user->getError() );

				return false;
			}

			if ( ! $myId ) {
				if ( cbfacebookconnectPlugin::loginUser( $plugin, $user, $fb_user ) ) {
					cbfacebookconnectClass::setReload();
				} else {
					cbfacebookconnectPlugin::clearSession( $user->getError(), 'message' );

					return false;
				}
			}
		} else {
			if ( cbfacebookconnectPlugin::registerUser( $plugin, $user, $fb_user ) ) {
				cbfacebookconnectClass::setReload();
			} else {
				cbfacebookconnectPlugin::clearSession( $user->getError() );

				return false;
			}
		}

		return true;
	}

	/**
	 * create new user from Facebook data
	 *
	 * @param object $plugin
	 * @param moscomprofilerUser $user
	 * @param object $fb_user
	 * @return boolean
	 */
	private function registerUser( $plugin, $user, $fb_user ) {
		global $_CB_framework, $_CB_database, $ueConfig;

		$key					=	trim( $plugin->params->get( 'fb_app_id', null ) );
		$approve				=	$plugin->params->get( 'fb_reg_approve', 0 );
		$confirm				=	$plugin->params->get( 'fb_reg_confirm', 0 );
		$approval				=	( $approve == 2 ? $ueConfig['reg_admin_approval'] : $approve );
		$confirmation			=	( $confirm ? $ueConfig['reg_confirmation'] : 0 );
		$username				=	trim( preg_replace( '/\W/', '', str_replace( ' ', '_', $fb_user->name ) ) );

		$dummy_user				=	new moscomprofilerUser( $_CB_database );

		if ( $dummy_user->loadByUsername( $username ) ) {
			$username			=	trim( preg_replace( '/\W/', '', $username . '_' . $fb_user->id ) );

			if ( $dummy_user->loadByUsername( $username ) ) {
				cbfacebookconnectPlugin::clearSession( 'This username is already in use.' );

				return false;
			}
		}

		$middlename_position	=	strpos( $fb_user->name, ' ' );
		$lastname_position		=	strrpos( $fb_user->name, ' ' );

		if ( $lastname_position !== false ) {
			$firstname			=	substr( $fb_user->name, 0, $middlename_position );
			$lastname			=	substr( $fb_user->name, $lastname_position + 1 );

			if ( $middlename_position !== $lastname_position ) {
				$middlename		=	substr( $fb_user->name, $middlename_position + 1, $lastname_position - $middlename_position - 1 );
			} else {
				$middlename		=	null;
			}
		} else {
			$firstname			=	null;
			$middlename			=	null;
			$lastname			=	$fb_user->name;
		}

		$usertype				=	$_CB_framework->getCfg( 'new_usertype' );

		$user->usertype			=	( $usertype ? $usertype : 'Registered' );
		$user->gid				=	$_CB_framework->acl->get_group_id( $user->usertype, 'ARO' );
		$user->sendEmail		=	0;
		$user->registerDate		=	date( 'Y-m-d H:i:s', $_CB_framework->now() );
		$user->username			=	$username;
		$user->firstname		=	$firstname;
		$user->middlename		=	$middlename;
		$user->lastname			=	$lastname;
		$user->name				=	$fb_user->name;
		$user->email			=	$fb_user->email;
		$user->password			=	$user->hashAndSaltPassword( md5( $fb_user->id . $key ) );
		$user->avatar			=	cbfacebookconnectPlugin::setAvatar( $fb_user );
		$user->registeripaddr	=	cbGetIPlist();
		$user->fb_userid		=	$fb_user->id;

		if ( $approval == 0 ) {
			$user->approved		=	1;
		} else {
			$user->approved		=	0;
		}

		if ( $confirmation == 0 ) {
			$user->confirmed	=	1;
		} else {
			$user->confirmed	=	0;
		}

		if ( ( $user->confirmed == 1 ) && ( $user->approved == 1 ) ) {
			$user->block		=	0;
		} else {
			$user->block		=	1;
		}

		if ( $user->store() ) {
			if ( ( $user->confirmed == 0 ) && ( $confirmation != 0 ) ) {
				$user->_setActivationCode();

				if ( ! $user->store() ) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * update link between Facebook and user
	 *
	 * @param moscomprofilerUser $user
	 * @param object $fb_user
	 * @return boolean
	 */
	private function updateUser( &$user, $fb_user ) {
		if ( $user->fb_userid != $fb_user->id ) {
			$user->fb_userid	=	$fb_user->id;

			if ( ! $user->store() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * login facebook user
	 *
	 * @param object $plugin
	 * @param moscomprofilerUser $user
	 * @param object $fb_user
	 * @return boolean
	 */
	private function loginUser( $plugin, $user, $fb_user  ) {
		if ( $user->fb_userid == $fb_user->id ) {
			cbimport( 'cb.authentication' );

			$cbAuthenticate		=	new CBAuthentication();

			$messagesToUser		=	array();
			$alertmessages		=	array();
			$redirect_url		=	cbfacebookconnectClass::setReturnURL( true );
			$resultError		=	$cbAuthenticate->login( $user->username, false, 0, 1, $redirect_url, $messagesToUser, $alertmessages, 0 );

			if ( $resultError ) {
				$user->_error	=	$resultError;
			} else {
				return true;
			}
		}

		return false;
	}

	/**
	 * prepare Facebook avatar
	 *
	 * @param object $fb_user
	 * @return string
	 */
	private function setAvatar( $fb_user ) {
		global $_CB_framework, $ueConfig;

		$avatar_img									=	$fb_user->picture;

		if ( $avatar_img ) {
			$avatar_name							=	trim( preg_replace( '/\W/', '', 'fb_' . $fb_user->id ) );

			cbimport( 'cb.snoopy' );

			$snoopy									=	new CBSnoopy;
			$snoopy->read_timeout					=	30;

			$snoopy->fetch( $avatar_img );

			if ( ! $snoopy->error ) {
				$headers							=	$snoopy->headers;

				if ( $headers ) foreach( $headers as $header ) {
					if ( preg_match( '/^Content-Type:/', $header ) ) {
						if ( preg_match( '/image\/(\w+)/', $header, $matches ) ) {
							if ( isset( $matches[1] ) ) {
								$ext				=	$matches[1];
							}
						}
					}
				}

				if ( isset( $ext ) ) {
					$ext							=	strtolower( $ext );

					if ( ! in_array( $ext, array( 'jpeg', 'jpg', 'png', 'gif' ) ) ) {
						return null;
					}

					cbimport( 'cb.imgtoolbox' );

					$path							=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';
					$allwaysResize					=	( isset( $ueConfig['avatarResizeAlways'] ) ? $ueConfig['avatarResizeAlways'] : 1 );

					$imgToolBox						=	new imgToolBox();
					$imgToolBox->_conversiontype	=	$ueConfig['conversiontype'];
					$imgToolBox->_IM_path			=	$ueConfig['im_path'];
					$imgToolBox->_NETPBM_path		=	$ueConfig['netpbm_path'];
					$imgToolBox->_maxsize			=	$ueConfig['avatarSize'];
					$imgToolBox->_maxwidth			=	$ueConfig['avatarWidth'];
					$imgToolBox->_maxheight			=	$ueConfig['avatarHeight'];
					$imgToolBox->_thumbwidth		=	$ueConfig['thumbWidth'];
					$imgToolBox->_thumbheight		=	$ueConfig['thumbHeight'];
					$imgToolBox->_debug				=	0;

					$image							=	array( 'name' => $avatar_name . '.' . $ext, 'tmp_name' => $snoopy->results );
					$newFileName					=	$imgToolBox->processImage( $image, $avatar_name, $path, 0, 0, 4, $allwaysResize );

					if ( $newFileName ) {
						return $newFileName;
					}
				}
			}
		}

		return null;
	}

	/**
	 * clear Facebook session
	 *
	 * @param mixed $msg
	 * @param string $type
	 */
	public function clearSession( $msg = null, $type = 'error' ) {
		if ( ! cbfacebookconnect::unsetUser() ) {
			cbfacebookconnectPlugin::clearLogin();
		}

		$url	=	cbfacebookconnectClass::setReturnURL( true );

		cbRedirect( $url, $msg, $type );
	}

	/**
	 * obtain CB user from Facebook ID
	 *
	 * @param int $fb_userid
	 * @return int
	 */
	public function getUser( $fb_userid ) {
		global $_CB_database;

		static $cbUids				=	array();

		if ( ! isset( $cbUids[$fb_userid] ) ) {
			if ( $fb_userid && preg_match( '/^\d+$/', $fb_userid ) ) {
				$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'fb_userid' ) . " = " . $fb_userid;
				$_CB_database->setQuery( $query );
				$cbUids[$fb_userid]	=	$_CB_database->loadResult();
			} else {
				$cbUids[$fb_userid]	=	null;
			}
		}

		return $cbUids[$fb_userid];
	}

	/**
	 * prepare CB Menu with Facebook links
	 *
	 * @param moscomprofilerUser $user
	 */
	public function getMenu( &$user ) {
		global $_CB_framework;

		if ( $user->fb_userid ) {
			$fb_user						=	cbfacebookconnect::getUser();

			if ( $fb_user ) {
				if ( $user->fb_userid == $fb_user->id ) {
					$plugin					=	cbfacebookconnectClass::getPlugin();
					$icon					=	$plugin->livePath . '/images/icon.gif';

					$unjoin					=	array();
					$unjoin['arrayPos']		=	array( '_UE_MENU_EDIT' => array( '_UE_MENU_FBC_UNJOIN' => null ) );
					$unjoin['position']		=	'menuBar';
					$unjoin['caption']		=	htmlspecialchars( CBTxt::T( 'Unjoin this site' ) );
					$unjoin['url']			=	"javascript: fbc_unjoin();";
					$unjoin['target']		=	'';
					$unjoin['img']			=	'<img src="' . $icon . '" width="16" height="16" />';
					$unjoin['tooltip']		=	htmlspecialchars( CBTxt::T( 'Unauthorize this site from your Facebook account.' ) );

					$this->addMenu( $unjoin );
				}
			}
		}
	}

	/**
	 * load backend configuration instructions
	 *
	 * @return string
	 */
	public function loadInstructions() {
		global $_CB_framework;

		$return	=	'<div>To begin developing your facebook application to connect with your ' . $_CB_framework->getCfg( 'live_site' ) . ' CB website, you must do the following steps.</div>'
				.	'<ol>'
				.		'<li>Navigate to <a href="http://www.facebook.com/developers/" target="_blank">Developer</a> Application.</li>'
				.		'<li>Click <a href="http://www.facebook.com/developers/createapp.php" target="_blank">Set Up New Application</a>.</li>'
				.		'<li>In <strong>Essential Information</strong> area complete <i>Application Name</i> (e.g., Community Builder FBC for ' . $_CB_framework->getCfg( 'sitename' ) . ') and Agree to Facebook <i>Terms</i> then click on <strong>Create Application</strong> button.</li>'
				.		'<li>Click on the <strong>Web Site</strong> link on the left column (under About).</li>'
				.		'<li>'
				.			'<div>Complete the following:</div>'
				.			'<ol>'
				.				'<li><i>Site URL</i> (e.g. ' . $_CB_framework->getCfg( 'live_site' ) . '/)</li>'
				.			'</ol>'
				.		'</li>'
				.		'<li>Click <strong>Save</strong></li>'
				.		'<li>Copy <strong>API Key</strong>, <strong>Application Secret</strong>, and <strong>Application ID</strong> to their respective locations.</li>'
				.		'<li>Click <strong>Save</strong></li>'
				.		'<li>Locate <strong>CB Login</strong> (mod_cblogin) within <strong>Module Manager</strong>.</li>'
				.		'<li>Set the parameter <i>CB Plugins integration</i> to Yes.</li>'
				.		'<li>Click <strong>Save</strong></li>'
				.	'</ol>';

		return $return;
	}
}

class cbfacebookconnect {

	/**
	 * prepare facebook API object
	 *
	 * @return cbFacebook
	 */
	function getAPI() {
		global $_CB_framework;

		static $api					=	null;

		if ( ! isset( $api ) ) {
			$plugin					=	cbfacebookconnectClass::getPlugin();
			$key					=	trim( $plugin->params->get( 'fb_app_apikey', null ) );
			$secret					=	trim( $plugin->params->get( 'fb_app_secretkey', null ) );
			$id						=	trim( $plugin->params->get( 'fb_app_id', null ) );

			if ( $key && $secret && $id ) {
				static $JS_loaded	=	0;

				if ( ! $JS_loaded++ ) {
					$_CB_framework->outputCbJQuery( "$( 'body' ).append( '<div id=\"fb-root\"></div>' );" );

					$_CB_framework->document->addHeadScriptUrl( 'http://connect.facebook.net/en_US/all.js' );

					$logout_url		=	$_CB_framework->getCfg( 'live_site' ) . '/index.php?option=' . $plugin->option . '&task=logout' . getCBprofileItemid();

					$API_js			=	"if ( window.FB ) {"
									.		"FB.init({"
									.			"appId: '" . addslashes( $id ) . "',"
									.			"status: true,"
									.			"cookie: true,"
									.			"xfbml: true"
									.		"});"
									.		"$( 'html' ).attr( 'xmlns:og', 'http://opengraphprotocol.org/schema/' );"
									.		"$( 'html' ).attr( 'xmlns:fb', 'http://www.facebook.com/2008/fbml' );"
									.	"}";

					$_CB_framework->outputCbJQuery( $API_js );

					$BUTTON_js		=	"function fbc_login() {"
									.		"if ( window.FB ) {"
									.			"FB.login( function( response ) {"
									.				"if ( response.session ) {"
									.					"location.reload();"
									.				"}"
									.			"}, { perms: 'email' });"
									.		"}"
									.	"};"
									.	"function fbc_logout() {"
									.		"if ( window.FB ) {"
									.			"FB.logout( function( response ) {"
									.				"window.location = '" . addslashes( $logout_url ) . "';"
									.			"});"
									.		"}"
									.	"};"
									.	"function fbc_unjoin() {"
									.		"if ( window.FB ) {"
									.			"if ( confirm( '" . addslashes( sprintf( CBTxt::T( 'Are you sure you want to unjoin %s?' ), $_CB_framework->getCfg( 'live_site' ) ) ) . "' ) ) {"
									.				"FB.api({ method: 'Auth.revokeAuthorization' }, function( response ) {"
									.					"location.reload();"
									.				"});"
									.			"}"
									.		"}"
									.	"};";

					$_CB_framework->document->addHeadScriptDeclaration( $BUTTON_js );
				}

				static $API_loaded	=	0;

				if ( ! $API_loaded++ ) {
					include_once( $plugin->absPath . '/api/facebook.php' );
				}

				$api				=	new cbFacebook( $key, $secret, $id );
			}
		}

		return $api;
	}

	/**
	 * get Facebook user object
	 *
	 * @return mixed
	 */
	public function getUser() {
		$facebook							=	cbfacebookconnect::getApi();

		if ( $facebook ) {
			if ( $facebook->getSession() ) {
				try {
					$results				=	$facebook->api( '/me?fields=id,name,email,picture&type=large' );

					if ( isset( $results['id'] ) ) {
						throw new Exception( 1 );
					}
				} catch ( Exception $e ) {
					if ( $e->getMessage() == 1 ) {
						foreach ( $results as $k => $v ) {
							$results[$k]	=	stripslashes( cbGetParam( $results, $k, null ) );
						}

						$fb_user			=	(object) $results;
					}
				}
			}
		}

		return ( isset( $fb_user ) ? $fb_user : false );
	}

	/**
	 * clean Facebook user
	 *
	 * @return boolean
	 */
	public function unsetUser() {
		global $_CB_framework;

		if ( cbfacebookconnect::getUser() ) {
			$_CB_framework->outputCbJQuery( "if ( window.FB ) { FB.logout( function( response ) {} ); }" );

			if ( ! cbfacebookconnect::getUser() ) {
				return true;
			}
		}

		return false;
	}
}

class cbfacebookconnectClass {

	/**
	 * get plugin object
	 *
	 * @return object
	 */
	public static function getPlugin() {
		global $_CB_framework, $_CB_database;

		static $plugin			=	null;

		if ( ! $plugin ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'element' ) . " = " . $_CB_database->Quote( 'cb.facebookconnect' );
			$_CB_database->setQuery( $query );
			$plugin				=	null;
			$_CB_database->loadObject( $plugin );
		}

		if ( $plugin ) {
			if ( ! is_object( $plugin->params ) ) {
				$plugin->params	=	new cbParamsBase( $plugin->params );
			}

			$plugin->option		=	'com_comprofiler';
			$plugin->relPath	=	'components/' . $plugin->option . '/plugin/' . $plugin->type . '/' . $plugin->folder;
			$plugin->livePath	=	$_CB_framework->getCfg( 'live_site' ) . '/' . $plugin->relPath;
			$plugin->absPath	=	$_CB_framework->getCfg( 'absolute_path' ) . '/' . $plugin->relPath;
			$plugin->xml		=	$plugin->absPath . '/' . $plugin->element . '.xml';

			cbimport( 'language.cbteamplugins' );
		}

		return $plugin;
	}

	/**
	 * get plugin item id
	 *
	 * @param boolean $htmlspecialchars
	 * @return mixed
	 */
	public static function getItemid( $htmlspecialchars = false ) {
		global $_CB_framework, $_CB_database;

		static $Itemid	=	null;

		if ( ! isset( $Itemid ) ) {
			$query		=	'SELECT ' . $_CB_database->NameQuote( 'id' )
						.	"\n FROM " . $_CB_database->NameQuote( '#__menu' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'link' ) . " LIKE " . $_CB_database->Quote( 'index.php?option=com_comprofiler&task=pluginclass&plugin=cb.facebookconnect%' )
						.	"\n AND " . $_CB_database->NameQuote( 'published' ) . " = 1"
						.	"\n AND " . $_CB_database->NameQuote( 'access' ) . " <= " . (int) $_CB_framework->myCmsGid();
			$_CB_database->setQuery( $query );
			$Itemid		=	$_CB_database->loadResult();

			if ( ! $Itemid ) {
				$Itemid	=	getCBprofileItemid( null );
			}
		}

		if ( is_bool( $htmlspecialchars ) ) {
			return ( $htmlspecialchars ? '&amp;' : '&' ) . 'Itemid=' . $Itemid;
		} else {
			return $Itemid;
		}
	}

	/**
	 * get plugin frontend or backend url
	 *
	 * @param array $variables
	 * @param string $msg
	 * @param boolean $htmlspecialchars
	 * @param boolean $redirect
	 * @param string $type
	 * @param boolean $return
	 * @param boolean $ajax
	 * @return string
	 */
	public static function getPluginURL( $variables = array(), $msg = null, $htmlspecialchars = true, $redirect = false,  $type = null, $return = false, $ajax = false ) {
		global $_CB_framework;

		$plugin						=	cbfacebookconnectClass::getPlugin();
		$action						=	( isset( $variables[0] ) ? '&action=' . urlencode( $variables[0] ) : null );
		$id							=	( isset( $variables[2] ) ? '&id=' . urlencode( $variables[2] ) : null );
		$set_return					=	( $return ? cbfacebookconnectClass::setReturnURL() : null );

		if ( $_CB_framework->getUi() == 2 ) {
			$function				=	( isset( $variables[1] ) ? '.' . urlencode( $variables[1] ) : null );
			$vars					=	$action . $function . $id . $set_return;
			$format					=	( $ajax ? 'raw' : 'html' );
			$url					=	'index.php?option=' . $plugin->option . '&task=editPlugin&cid=' . $plugin->id . $vars;

			if ( $htmlspecialchars ) {
				$url				=	htmlspecialchars( $url );
			}

			$url					=	$_CB_framework->backendUrl( $url, $htmlspecialchars, $format );
		} else {
			$function				=	( isset( $variables[1] ) ? '&func=' . urlencode( $variables[1] ) : null );
			$vars					=	$action . $function . $id . $set_return . cbfacebookconnectClass::getItemid();
			$format					=	( $ajax ? 'component' : 'html' );
			$url					=	cbSef( 'index.php?option=' . $plugin->option . '&task=pluginclass&plugin=' . $plugin->element . $vars, $htmlspecialchars, $format );
		}

		if ( $msg ) {
			if ( is_array( $msg ) ) {
				$msgParams			=	$msg;
				$msgString			=	array_shift( $msgParams );

				for ( $i = 0, $n = count( $msgParams ); $i < $n; $i++ ) {
					$msgParams[$i]	=	CBTxt::T( $msgParams[$i] );
				}

				$msg				=	vsprintf( CBTxt::T( $msgString ), $msgParams );
			} else {
				$msg				=	CBTxt::T( $msg );
			}

			if ( $redirect ) {
				$is_return			=	cbfacebookconnectClass::getReturnURL();

				cbRedirect( ( $is_return ? $is_return : $url ), $msg, $type );
			} else {
				$url				=	"javascript: if ( confirm( '" . addslashes( $msg ) . "' ) ) { location.href = '" . addslashes( $url ) . "'; }";
			}
		}

		return $url;
	}

	/**
	 * prepare secure return URL
	 *
	 * @param boolean $raw
	 * @return string
	 */
	public static function setReturnURL( $raw = false ) {
		global $_CB_framework;

		$isHttps		=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
		$return			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

		if ( ! empty( $_SERVER['PHP_SELF'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$return		.=	$_SERVER['REQUEST_URI'];
		} else {
			$return		.=	$_SERVER['SCRIPT_NAME'];

			if ( isset( $_SERVER['QUERY_STRING'] ) && ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$return	.=	'?' . $_SERVER['QUERY_STRING'];
			}
		}

		if ( ! preg_match( '!^(?:(?:' . preg_quote( $_CB_framework->getCfg( 'live_site' ), '!' ) . ')|(?:index.php))!', $return ) ) {
			$return		=	'';
		}

		$return			=	preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $return ) ) ) );

		$return			=	cbUnHtmlspecialchars( $return );

		if ( preg_match( '!index\.php\?option=com_comprofiler&task=confirm&confirmCode=|index\.php\?option=com_comprofiler&task=login!', $return ) ) {
			$return		=	'index.php';
		}

		if ( ! $raw ) {
			$return		=	'&return=BR' . base64_encode( $return );
		}

		return $return;
	}

	/**
	 * get stored return URL
	 *
	 * @return string
	 */
	public static function getReturnURL() {
		global $_CB_framework;

		$return				=	trim( stripslashes( cbGetParam( $_GET, 'return', null ) ) );

		if ( preg_match( '!^BR!', $return ) ) {
			$return			=	base64_decode( substr( $return, 2 ) );
			$arrToClean		=	array( 'B' => get_magic_quotes_gpc() ? addslashes( $return ) : $return );
			$return			=	cbGetParam( $arrToClean, 'B', '' );
		}

		if ( ! preg_match( '!^(?:(?:' . preg_quote( $_CB_framework->getCfg( 'live_site' ), '!' ) . ')|(?:index.php))!', $return ) ) {
			$return		=	'';
		}

		$return			=	preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $return ) ) ) );

		$return			=	cbUnHtmlspecialchars( $return );

		if ( preg_match( '!index\.php\?option=com_comprofiler&task=confirm&confirmCode=|index\.php\?option=com_comprofiler&task=login!', $return ) ) {
			$return		=	'index.php';
		}

		return $return;
	}

	/**
	 * get CB session object
	 *
	 * @return object
	 */
	public static function getSession() {
		static $session	=	null;

		if ( ! isset( $session ) ) {
			cbimport( 'cb.session' );

			$session	=&	CBSession::getInstance();

			if ( ! $session->session_id() ) {
				$session->session_start();
			}
		}

		return $session;
	}

	/**
	 * prepare reload
	 */
	public static function setReload() {
		global $_CB_framework;

		static $RELOAD_js	=	0;

		if ( ! $RELOAD_js++ ) {
			$_CB_framework->outputCbJQuery( "location.reload();" );
		}
	}
}
?>