<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Logging_Obfuscator
{
    const OBFUSCATION_STRING = 'XXXXXXXX';
    const REMOVED_STRING     = '<removed>';
    
    /** @var string[] Names of the fields that should be obfuscated on logging. */
    private $obfuscationFields;
    
    /** @var string Names of the fields that should be removed from logging. */
    private $removeFields;
    
    public function __construct()
    {
        $this->obfuscationFields = array('pass');
        $this->removeFields      = array('cart');
    }
    
    /**
     * Adds field names to the list of fields that should be obfuscated in the logs.
     *
     * @param string[] $fieldNames
     */
    public function addObfuscationFields(array $fieldNames)
    {
        $this->obfuscationFields = array_merge($fieldNames, $this->obfuscationFields);
    }
    
    /**
     * Adds field names to the list of fields that should be removed from the logs.
     *
     * @param string[] $fieldNames
     */
    public function addRemoveFields(array $fieldNames)
    {
        $this->removeFields = array_merge($fieldNames, $this->removeFields);
    }
    
    /**
     * Function to prepare the parameters of an API request for logging.
     *
     * Strips out critical request data like the password of a get_customer request.
     *
     * @param mixed[] $data The incoming request's parameters.
     *
     * @return mixed[] The cleaned parameters.
     */
    public function cleanParamsForLog($data)
    {
        foreach ($data as $key => &$value) {
            if (in_array($key, $this->obfuscationFields)) {
                $value = self::OBFUSCATION_STRING;
            }
            
            if (in_array($key, $this->removeFields)) {
                $value = self::REMOVED_STRING;
            }
        }
        
        return $data;
    }
}