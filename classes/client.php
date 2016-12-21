<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class ShopgateClient extends ShopgateContainer {

	const TYPE_MOBILESITE       = 'mobilesite';
	const TYPE_IPHONEAPP        = 'iphoneapp';
	const TYPE_IPADAPP          = 'ipadapp';
	const TYPE_ANDROIDPHONEAPP  = 'androidphoneapp';
	const TYPE_ANDROIDTABLETAPP = 'androidtabletapp';

	/** @var string */
	protected $type;

	/**
	 * @param string
	 */
	public function setType($data)
	{
		return $this->type = $data;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function isMobileWebsite()
	{
		return $this->type == $this::TYPE_MOBILESITE;
	}

	/**
	 * @return bool
	 */
	public function isApp()
	{
		$appTypes = array(
			$this::TYPE_ANDROIDPHONEAPP,
			$this::TYPE_ANDROIDTABLETAPP,
			$this::TYPE_IPADAPP,
			$this::TYPE_IPHONEAPP
		);
		return in_array($this->type, $appTypes);
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitClient($this);
	}
}