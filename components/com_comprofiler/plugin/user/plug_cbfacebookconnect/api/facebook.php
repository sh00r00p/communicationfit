<?php
/**
* Facebook PHP SDK - MODIFIED
* @copyright (C) http://www.facebook.com
* @license http://www.apache.org/licenses/LICENSE-2.0.html Apache version 2.0
* @source http://github.com/facebook/php-sdk
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) {
	die( 'Direct Access to this location is not allowed.' );
}

class cbFacebook {
	private $id;
	private $key;
	private $secret;
	private $session;
	private $cookies	=	false;
	private $domain		=	null;
	private $agent		=	'facebook-php-2.0';
	private $loaded		=	false;
	private $timeout	=	30;

	/**
	 * Initialize a Facebook Application
	 *
	 * @param string $key
	 * @param string $secret
	 * @param string $id
	 * @param boolean $cookies
	 * @param string $domain
	 */
	public function cbFacebook( $key, $secret, $id, $cookies = true, $domain = null ) {
		$this->setKey( $key );
		$this->setSecret( $secret );
		$this->setId( $id );
		$this->setCookies( $cookies );
		$this->setDomain( $domain );
	}

	/**
	 * Set the Application ID
	 *
	 * @param string $value the application ID
	 * @return string
	 */
	public function setId( $value ) {
		$this->id	=	$value;

		return $value;
	}

	/**
	 * Get the Application ID
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set the API Key
	 *
	 * @param string $value the application key
	 * @return string
	 */
	public function setKey( $value ) {
		$this->key	=	$value;

		return $value;
	}

	/**
	 * Get the API Key
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Set the API Secret
	 *
	 * @param string $value the application secret
	 * @return string
	 */
	public function setSecret( $value ) {
		$this->secret	=	$value;

		return $value;
	}

	/**
	 * Get the API Secret
	 *
	 * @return string
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * Set application Cookies support
	 *
	 * @param boolean $value
	 * @return boolean
	 */
	public function setCookies( $value ) {
		$this->cookies	=	$value;

		return $value;
	}

	/**
	 * Get application Cookies support
	 *
	 * @return boolean
	 */
	public function getCookies() {
		return $this->cookies;
	}

	/**
	 * Get application Cookies name
	 *
	 * @return string
	 */
	private function getCookiesName() {
		return 'fbs_' . $this->getId();
	}

	/**
	 * Set application Cookie
	 *
	 * @param array $session
	 * @return boolean
	 */
	private function setCookie( $session = null ) {
		if ( ! $this->getCookies() ) {
			return false;
		}

		$cookieName		=	$this->getCookiesName();
		$value			=	'deleted';
		$expires		=	time() - 3600;
		$domain			=	$this->getDomain();

		if ( $session ) {
			$value		=	'"' . http_build_query( $session, null, '&' ) . '"';

			if ( isset( $session['base_domain'] ) ) {
				$domain	=	$session['base_domain'];
			}

			$expires	=	$session['expires'];
		}

		if ( ( $value == 'deleted' ) && empty( $_COOKIE[$cookieName] ) ) {
			return false;
		}

		if ( headers_sent() ) {
			if ( ! array_key_exists( 'argc', $_SERVER ) ) {
				throw new Exception( CBTxt::T( 'Could not set cookie. Headers already sent.' ) );
			}
		} else {
			setcookie( $cookieName, $value, $expires, '/', '.' . $domain );
		}

		return true;
	}

	/**
	 * Set application Domain
	 *
	 * @param string $value
	 * @return string
	 */
	public function setDomain( $value ) {
		$this->domain	=	$value;

		return $value;
	}

	/**
	 * Get application Domain
	 *
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Set application User Agent
	 *
	 * @param string $value
	 * @return string
	 */
	public function setAgent( $value ) {
		$this->agent	=	$value;

		return $value;
	}

	/**
	 * Get application User AGent
	 *
	 * @return string
	 */
	public function getAgent() {
		return $this->agent;
	}

	/**
	 * Set API timeout in seconds
	 *
	 * @param int $value
	 * @return int
	 */
	public function setTimeout( $value ) {
		$this->timeout	=	$value;

		return $value;
	}

	/**
	 * Get API timeout in seconds
	 *
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * Get application User from session
	 *
	 * @return array
	 */
	public function getUser() {
		$session	=	$this->getSession();

		return ( $session ? $session['uid'] : null );
	}

	/**
	 * Set application Session
	 *
	 * @param array $session
	 * @param boolean $write_cookie
	 * @return cbFacebook
	 */
	public function setSession( $session = null, $write_cookie = true ) {
		$session		=	$this->validateSession( $session );
		$this->loaded	=	true;
		$this->session	=	$session;

		if ( $write_cookie ) {
			$this->setCookie( $session );
		}

		return $this;
	}

	/**
	 * Get application Session
	 *
	 * @return array
	 */
	public function getSession() {
		if ( ! $this->loaded ) {
			$session				=	null;
			$write_cookie			=	true;

			if ( isset( $_GET['session'] ) ) {
				$session			=	json_decode( get_magic_quotes_gpc() ? stripslashes( $_GET['session'] ): $_GET['session'], true );
				$session			=	$this->validateSession( $session );
			}

			if ( ( ! $session ) && $this->getCookies() ) {
				$cookieName			=	$this->getCookiesName();

				if ( isset( $_COOKIE[$cookieName] ) ) {
					$session		=	array();

					parse_str( trim( get_magic_quotes_gpc() ? stripslashes( $_COOKIE[$cookieName] ): $_COOKIE[$cookieName], '"' ), $session );

            		$session		=	$this->validateSession( $session );
           			$write_cookie	=	empty( $session );
				}
			}

			$this->setSession( $session, $write_cookie );
		}

		return $this->session;
	}

	/**
	 * Validate application Session
	 *
	 * @param array $session
	 * @return mixed
	 */
	private function validateSession( $session ) {
		if ( is_array( $session ) && isset( $session['uid'] ) && isset( $session['session_key'] ) && isset( $session['secret'] ) && isset( $session['access_token'] ) && isset( $session['sig'] ) ) {
			$invalid_sig	=	$session;

			unset( $invalid_sig['sig'] );

			$valid_sig		=	$this->generateSignature( $invalid_sig, $this->getSecret() );

			if ( $session['sig'] != $valid_sig ) {
				if ( ! array_key_exists( 'argc', $_SERVER ) ) {
					throw new Exception( CBTxt::T( 'Got invalid session signature in cookie.' ) );
				}

				$session	=	null;
			}
		} else {
			$session		=	null;
		}

		return $session;
	}

	/**
	 * Generate application Signature
	 *
	 * @param array $params
	 * @param string $secret
	 * @return string
	 */
	private static function generateSignature( $params, $secret ) {
		ksort( $params );

		$base_string		=	null;

		foreach( $params as $key => $value ) {
			$base_string	.=	$key . '=' . $value;
		}

		$base_string		.=	$secret;

		return md5( $base_string );
	}

	/**
	 * Make an API Call
	 *
	 * @param string $call
	 * @param array $args
	 * @param string $api
	 * @param string $protocol
	 * @return mixed
	 */
	public function api( $call = null, $args = array(), $api = 'graph', $protocol = null ) {
		switch ( $api ) {
			case 'api':
				$url						=	'https://api.facebook.com/';
				break;
			case 'api_read':
				$url						=	'https://api-read.facebook.com/';
				break;
			case 'www':
				$url						=	'https://www.facebook.com/';
				break;
			case 'graph':
			default:
				$url						=	'https://graph.facebook.com/';
				break;

		}

		if ( $protocol ) {
			$url							=	preg_replace( '/^https/', $protocol, $url );
		}

		if ( $api != 'graph' ) {
			if ( ! isset( $args['api_key'] ) ) {
				$args['api_key']			=	$this->getId();
			}

			if ( ! isset( $args['format'] ) ) {
				$args['format']				=	'json';
			}
		} else {
			if ( ! isset( $args['method'] ) ) {
				$args['method']				=	'GET';
			}
		}

		if ( ! isset( $args['access_token'] ) ) {
			$session						=	$this->getSession();

			if ( $session ) {
				$args['access_token']		=	$session['access_token'];
			}
		}

		if ( $args ) foreach ( $args as $key => $value ) {
			if ( ! is_string( $value ) ) {
				$args[$key]					=	json_encode( $value );
			}
		}

		$result								=	null;

		if ( function_exists( 'curl_init' ) ) {
			$ch								=	curl_init();
			$opts							=	array();
			$opts[CURLOPT_RETURNTRANSFER]	=	true;
			$opts[CURLOPT_TIMEOUT]			=	$this->getTimeout();
			$opts[CURLOPT_SSL_VERIFYPEER]	=	false;
			$opts[CURLOPT_USERAGENT]		=	$this->getAgent();
			$opts[CURLOPT_POSTFIELDS]		=	$this->getUrl( null, null, $args );
			$opts[CURLOPT_URL]				=	$this->getUrl( $url, $call, null );

			curl_setopt_array( $ch, $opts );

			$result							=	curl_exec( $ch );

			curl_close( $ch );
		} else {
			cbimport( 'cb.snoopy' );

			$snoopy							=	new CBSnoopy();

			$snoopy->agent					=	$this->getAgent();
			$snoopy->read_timeout			=	$this->getTimeout();

			$url							=	$this->getUrl( $url, $call, $args );

			$snoopy->fetch( $url );

			$result							=	$snoopy->results;
		}

		$results							=	json_decode( $result, true );

		if ( isset( $results['error'] ) ) {
			$type							=	( isset( $results['error']['type'] ) ? $results['error']['type'] . ': ' : null );
			$msg							=	( isset( $results['error']['message'] ) ? $results['error']['message'] : $result['error_msg'] );
			$code							=	( isset( $results['error_code'] ) ? $results['error_code'] : 0 );

			throw new Exception( $type . $msg, $code );
		}

		return $results;
	}

	/**
	 * Get API Call url
	 *
	 * @param string $url
	 * @param string $call
	 * @param array $args
	 * @return string
	 */
	private function getUrl( $url, $call, $args ) {
		if ( $call ) {
			if ( $call[0] === '/' ) {
				$call	=	substr( $call, 1 );
			}

			$url		.=	$call;
		}

		if ( $args ) {
			if ( $url ) {
				$url	.=	( strpos( $url, '?' ) ? '&' : '?' );
			}

			$url		.=	http_build_query( $args, null, '&' );
		}

		return $url;
	}
}
?>