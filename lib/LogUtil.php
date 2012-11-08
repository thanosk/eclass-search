<?php
/**
 * Copyright (c) 2012 by Thanos Kyritsis
 *
 * This file is part of eclass-search.
 *
 * eclass-search is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2 of the License.
 *
 * eclass-search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with eclass-search; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 */

class LogUtil
{
    const DEBUG = 0;
    const INFO = 1;
    
    public static function debug($msg)
    {
        if (defined('LOG_LEVEL') && LOG_LEVEL <= self::DEBUG)
            echo $msg . "\n"; 
    }
    
    public static function info($msg)
    {
        if (defined('LOG_LEVEL') && LOG_LEVEL <= self::INFO)
            echo $msg . "\n";
    }
}