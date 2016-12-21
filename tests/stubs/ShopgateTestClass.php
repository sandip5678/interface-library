<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class ShopgateTestClass
{
    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param string $one
     */
    private function methodPrivate($one)
    {
    }
    
    protected function methodProtected($one)
    {
    }
    
    public function methodWithNoParameters()
    {
    }
    
    public function methodWithOneParameter($one)
    {
    }
    
    public function methodWithTwoParameters($one, $two = 'optional')
    {
    }
}