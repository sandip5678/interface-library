<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider
{
    /**
     * Wrapper that checks if error_get_last exists and if so returns its result.
     *
     * @return array|null
     */
    public function get()
    {
        return function_exists('error_get_last')
            ? error_get_last()
            : null;
    }
}