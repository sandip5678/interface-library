<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/


class Shopgate_Model_XmlResultObject extends SimpleXMLElement {

	/**
	 * define default main node
	 */
	const DEFAULT_MAIN_NODE = '<items></items>';

	/**
	 * Adds a child with $value inside CDATA
	 *
	 * @param      $name
	 * @param null $value
	 *
	 * @return SimpleXMLElement
	 */
	public function addChildWithCDATA($name, $value = null) {
		$new_child = $this->addChild($name);

		if ($new_child !== null) {
			$node = dom_import_simplexml($new_child);
			$no = $node->ownerDocument;
			if ($value != '') {
				$node->appendChild($no->createCDATASection($value));
			}
		}

		return $new_child;
	}

	/**
	 * @param SimpleXMLElement $new
	 * @param SimpleXMLElement $old
	 *
	 * @return SimpleXMLElement
	 */
	public function replaceChild(SimpleXMLElement $new, SimpleXMLElement $old) {
		$tmp = dom_import_simplexml($this);
		$new = $tmp->ownerDocument->importNode(dom_import_simplexml($new), true);

		$node = $tmp->replaceChild($new, dom_import_simplexml($old));

		return simplexml_import_dom($node, get_class($this));
	}

	/**
	 * Adds an attribute to the SimpleXML element is value not empty
	 *
	 * @param string $name
	 * @param string $value
	 * @param string $namespace
	 */
	public function addAttribute($name, $value = null, $namespace = null) {
		if (isset($value)) {
			parent::addAttribute($name, $value, $namespace);
		}
	}
} 