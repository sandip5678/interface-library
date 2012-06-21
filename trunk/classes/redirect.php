<?php
/**
 * Helper class for redirection from shop system to mobile webpage.
 *
 * Provides analyzation of the client's user agent, creation of redirection links for
 * different redirects (e.g. product, category, search), keyword updating and caching,
 * javascript for the "on/off" switch and sending the redirect headers to the client's
 * browser.
 *
 * @author Shopgate GmbH, 35510 Butzbach, DE
 *
 */
class ShopgateMobileRedirect extends ShopgateObject {
	const SHOPGATE_STATIC = 'http://static.shopgate.com';
	const SHOPGATE_STATIC_SSL = 'https://static-ssl.shopgate.com';

	/**
	 * @var string the URL that is appended to the end of a shop alias (aka subdomain) if the shop is live
	 */
	const SHOPGATE_LIVE_ALIAS = '.shopgate.com';

	/**
	 * @var string the URL that is appended to the end of a shop alias (aka subdomain) if the shop is on playground
	 */
	const SHOPGATE_PG_ALIAS = '.shopgatepg.com';

	/**
	 * @var string name of the cookie to set in case a customer turns of mobile redirect
	 */
	const COOKIE_NAME = 'SHOPGATE_MOBILE_WEBPAGE';

	/**
	 * @var int (hours) the minimum time that can be set for updating of the cache
	 */
	const MIN_CACHE_TIME = 1;

	/**
	 * @var int (hours) the default time to be set for updating the cache
	 */
	const DEFAULT_CACHE_TIME = 24;


	/**
	 * @var string alias name of shop at Shopgate, e.g. 'yourshop' to redirect to 'https://yourshop.shopgate.com'
	 */
	protected $alias = '';

	/**
	 * @var string your shops cname entry to redirect to
	 */
	protected $cname = '';

	/**
	 * @var string[] list of strings that cause redirection if they occur in the client's user agent
	 */
	protected $redirectKeywords = array('iPhone', 'iPod', 'iPad', 'Android', 'Windows Phone OS 7.0', 'Bada');

	/**
	 * @var string[] list of strings that deny redirection if they occur in the client's user agent; overrides $this->redirectKeywords
	 */
	protected $skipRedirectKeywords = array();

	/**
	 * @var string
	 */
	protected $cacheFile;

	/**
	 * @var bool
	 */
	protected $updateRedirectKeywords;

	/**
	 * @var int (hours)
	 */
	protected $redirectKeywordCacheTime;

	/**
	 * @var bool true in case the website is delivered via HTTPS (this will load the Shopgate javascript via HTTPS as well to avoid browser warnings)
	 */
	protected $useSecureConnection;

	/**
	 * @var string
	 */
	protected $mobileHeaderTemplatePath;

	/**
	 * @var string expiration date of the cookie as defined in http://www.ietf.org/rfc/rfc2109.txt
	 */
	protected $cookieLife;

	/**
	 * @var string url to the image for the "switched on" button
	 */
	protected $buttonOnImageSource;

	/**
	 * @var string url to the image for the "switched off" button
	 */
	protected $buttonOffImageSource;

	/**
	 * @var string description to be displayed to the left of the button
	 */
	protected $buttonDescription;
	
	public function initLibrary() {
		$this->updateRedirectKeywords = false;
		$this->redirectKeywordCacheTime = self::DEFAULT_CACHE_TIME;
		$this->cacheFile = dirname(__FILE__).'/../temp/cache/redirect_keywords.txt';
		$this->useSecureConnection = isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] === "on" || $_SERVER["HTTPS"] == "1");

		// mobile header options
		$this->mobileHeaderTemplatePath = dirname(__FILE__).'/../assets/mobile_header.html';
		$this->cookieLife = gmdate('D, d-M-Y H:i:s T', time());
		$this->buttonDescription = 'Mobile Webseite aktivieren';

		// update keywords if enabled
		$this->updateRedirectKeywords();
	}


	####################
	# general settings #
	####################


	/**
	 * Sets the description to be displayed to the left of the button.
	 *
	 * @param string $description
	 */
	public function setButtonDescription($description) {
		if (!empty($description)) $this->buttonDescription = $description;
	}

	/**
	 * Sets the alias of the Shopgate shop
	 *
	 * @param string $alias
	 */
	public function setAlias($alias){
		$this->alias = $alias;
	}

	/**
	 * Sets the cname of the shop
	 */
	public function setCustomMobileUrl($cname){
		$this->cname = $cname;
	}

	/**
	 * Enables updating of the keywords that identify mobile devices from Shopgate Merchant API.
	 *
	 * @param int $cacheTime Time the keywords are cached in hours. Will be set to at least self::MIN_CACHE_TIME.
	 */
	public function enableKeywordUpdate($cacheTime = self::DEFAULT_CACHE_TIME) {
		$this->updateRedirectKeywords = true;
		$this->redirectKeywordCacheTime = ($cacheTime >= self::MIN_CACHE_TIME) ? $cacheTime : self::MIN_CACHE_TIME;
	}

	/**
	 * Disables updating of the keywords that identify mobile devices from Shopgate Merchant API.
	 */
	public function disableKeywordUpdate() {
		$this->updateRedirectKeywords = false;
	}

	/**
	 * Appends a new keyword to the redirect keywords list.
	 *
	 * @param string $keyword The redirect keyword to append.
	 */
	public function addRedirectKeyword($keyword){
		if(is_array($keyword)){
			$this->redirectKeywords = array_merge($this->redirectKeywords, $keyword);
		} else {
			$this->redirectKeywords[] = $keyword;
		}
	}

	/**
	 * Removes a keyword or an array of redirect keywords from the keywords list.
	 *
	 * @param string|string[] $keyword The redirect keyword or keywords to remove.
	 */
	public function removeRedirectKeyword($keyword){
		if(is_array($keyword)){
			foreach($keyword as $word){
				foreach($this->redirectKeywords as $key => $mobileKeyword){
					if(mb_strtolower($word) == mb_strtolower($mobileKeyword)){
						unset($this->redirectKeywords[$key]);
					}
				}
			}
		} else {
			foreach($this->redirectKeywords as $key => $mobileKeyword){
				if(mb_strtolower($keyword) == mb_strtolower($mobileKeyword)){
					unset($this->redirectKeywords[$key]);
				}
			}
		}
	}

	/**
	 * Replaces the current list of redirect keywords with a given list.
	 *
	 * @param string[] $redirectKeywords The new list of redirect keywords.
	 */
	public function setRedirectKeywords(array $redirectKeywords){
		$this->redirectKeywords = $redirectKeywords;
	}

	/**
	 * Replaces the current list of skiüp redirect keywords with a given list.
	 *
	 * @param string[] $skipRedirectKeywords The new list of skip redirect keywords.
	 */
	public function setSkipRedirectKeywords(array $skipRedirectKeywords){
		$this->skipRedirectKeywords = $skipRedirectKeywords;
	}

	/**
	 * Switches to secure connection instead of checking server-side.
	 *
	 * This will cause slower download of nonsensitive material (the mobile header button images) from Shopgate.
	 * Activate only if the secure connection is determined incorrectly (e.g. because of third-party components).
	 */
	public function setAlwaysUseSSL() {
		$this->useSecureConnection = true;
	}
	
	/**
	 * Detects by redirect keywords (and skip redirect keywords) if a request was sent by a mobile device.
	 *
	 * @return bool true if a mobile device could be detected, false otherwise.
	 */
	public function isMobileRequest(){
		// find user agent
		$userAgent = '';
		if(!empty($_SERVER['HTTP_USER_AGENT'])){
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			return false;
		}

		// check user agent for redirection keywords and skip redirection keywords and return the result
		return
			(!empty($this->redirectKeywords)     ?  preg_match('/'.implode('|', $this->redirectKeywords).'/', $userAgent)     : false) &&
			(!empty($this->skipRedirectKeywords) ? !preg_match('/'.implode('|', $this->skipRedirectKeywords).'/', $userAgent) : true);
	}

	/**
	 * Detects whether the customer wants to be redirected.
	 *
	 * @return bool true if the customer wants to be redirected, false otherwise.
	 */
	public function isRedirectAllowed() {
		// if GET parameter is set create cookie and do not redirect
		if (!empty($_GET['shopgate_redirect'])) {
			setcookie(self::COOKIE_NAME, 1, time() + 604800, '/'); // expires after 7 days
			return false;
		}


		return empty($_COOKIE[self::COOKIE_NAME]) ? true : false;
	}

	/**
	 * Redirects to a given (valid) URL.
	 *
	 * If the $url parameter is no valid URL the method will simply return false and do nothing else.
	 * Otherwise it will output the necessary redirection headers and stop script execution.
	 *
	 * @param string $url the URL to redirect to
	 * @param bool $setCookie true to set the redirection cookie and activate redirection
	 * @return false if the passed $url parameter is no valid URL
	 */
	public function redirect($url) {
		// validate url
		if (!preg_match('#^(http|https)\://#', $url)) {
			return false;
		}

		// perform redirect
		header("Location: ". $url, true, 302);
		exit;
	}

	/**
	 * Returns the javascript and HTML for the mobile redirect button
	 *
	 * @return string
	 */
	public function getMobileHeader() {
		if (!file_exists($this->mobileHeaderTemplatePath)) {
			return '';
		}

		$html = @file_get_contents($this->mobileHeaderTemplatePath);
		if (empty($html)) {
			return '';
		}

		// set parameters
		$this->buttonOnImageSource = (($this->useSecureConnection) ? self::SHOPGATE_STATIC_SSL : self::SHOPGATE_STATIC).'/api/mobile_header/button_on.png';
		$this->buttonOffImageSource = (($this->useSecureConnection) ? self::SHOPGATE_STATIC_SSL : self::SHOPGATE_STATIC).'/api/mobile_header/button_off.png';
		$html = str_replace('{$cookieName}', self::COOKIE_NAME, $html);
		$html = str_replace('{$buttonOnImageSource}',  $this->buttonOnImageSource,  $html);
		$html = str_replace('{$buttonOffImageSource}', $this->buttonOffImageSource, $html);
		$html = str_replace('{$buttonDescription}', $this->buttonDescription, $html);
		
		return $html;
	}


	###############
	### helpers ###
	###############

	/**
	 * Generates the root mobile Url for the redirect
	 */
	private function _getMobileUrl(){
		if(!empty($this->cname)){
			return $this->cname;
		} elseif(!empty($this->alias)){
			return 'https://'.$this->alias.$this->getShopgateUrl();
		}
	}

	/**
	 * Returns the URL to be appended to the alias of a shop.
	 *
	 * The method determines this by the "server" setting in ShopgateConfig. If it's set to
	 * "custom", localdev.cc will be used for Shopgate local development and testing.
	 *
	 * @return string The URL that can be appended to the alias, e.g. ".shopgate.com"
	 */
	private function getShopgateUrl() {
		$serverType = ShopgateConfig::getConfigField('server');

		switch ($serverType) {
			default: // fall through to "live"
			case 'live':	return self::SHOPGATE_LIVE_ALIAS;
			case 'pg':		return self::SHOPGATE_PG_ALIAS;
			case 'custom':	return '.localdev.cc/php/shopgate/index.php'; // for Shopgate development & testing
		}
	}

	/**
	 * Updates the keywords array from cache file or Shopgate Merchant API if enabled.
	 */
	protected function updateRedirectKeywords() {
		if (!$this->updateRedirectKeywords) return;
		$saveKeywords = false;

		if(file_exists($this->cacheFile)){

			$fp = @fopen($this->cacheFile, 'r');

			if(!$fp){
				return;
			}

			$lastRedirectKeywordsUpdate = 0;
			$redirectKeywords = array();
			$firstLine = true;
			while($line = fgets($fp)){
				if($firstLine){
					$lastRedirectKeywordsUpdate = $line;
					$firstLine = false;
					if ((time() - ($lastRedirectKeywordsUpdate + ($this->redirectKeywordCacheTime * 3600)) > 0)) {
						try{
							$redirectKeywords = ShopgateMerchantApi::getInstance()->getMobileRedirectKeywords();

							// save keywords in file
							$saveKeywords = true;

							break;
						} catch(Exception $ex){
							continue;
						}
					}
					continue;
				}
				$redirectKeywords[] = $line;
			}
			@fclose($fp);

			$this->redirectKeywords = $redirectKeywords;
		} else {
			try{
				$redirectKeywords = ShopgateMerchantApi::getInstance()->getMobileRedirectKeywords();

				// save keywords in file
				$saveKeywords = true;

				$this->redirectKeywords = $redirectKeywords;
			} catch(Exception $ex){
			}
		}

		if($saveKeywords){
			// Save the keywords in cache
			$fp = @fopen($this->cacheFile, 'w');

			if(!$fp){
				return false;
			}

			fwrite($fp, time()."\n");
			foreach($this->redirectKeywords as $redirectKeyWord){
				fwrite($fp, $redirectKeyWord."\n");
			}
			fclose($fp);
		}
	}

	#############################
	### mobile url generation ###
	#############################

	/**
	 * Create a mobile-shop-url to the startmenu
	 */
	public function getShopUrl(){
		return $this->_getMobileUrl();
	}

	/**
	 * Create a mobile-product-url to a item
	 *
	 * @param string $itemNumber
	 */
	public function getItemUrl($itemNumber){
		return $this->_getMobileUrl().'/item/'.bin2hex($itemNumber);
	}

	/**
	 * Create a mobile-category-url to a category
	 *
	 * @param string $categoryNumber
	 */
	public function getCategoryUrl($categoryNumber){
		return $this->_getMobileUrl().'/category/'.bin2hex($categoryNumber);
	}

	/**
	 * Create a mobile-cms-url to a cms-page
	 *
	 * @param string $key
	 */
	public function getCmsUrl($key){
		return $this->_getMobileUrl().'/cms/'.$key;
	}

	/**
	 * Create a mobile-brand-url to a page with results for a specific manufacturer
	 *
	 * @param string $manufacturer
	 */
	public function getBrandUrl($manufacturerName){
		return $this->_getMobileUrl().'/brand/?q='.urlencode($manufacturerName);
	}

	/**
	 * Create a mobile-search-url to a page with search results
	 *
	 * @param string $searchString
	 */
	public function getSearchUrl($searchString){
		return $this->_getMobileUrl().'/search/?s='.urlencode($searchString);
	}

}