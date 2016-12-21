<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface
{
    /**
     * @param Throwable|Exception $e        Will accept Throwable for PHP 7 or Exception for PHP < 7.
     * @param int                 $maxdepth The maximum depth of causes for $e to go back.
     *
     * @return string
     */
    public function generate($e, $maxdepth = 10);
}