#!/usr/bin/php
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

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) )); // CWD

// Ensure lib/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/lib'),
    get_include_path(),
)));

// Define index path
defined('INDEX_PATH')
    || define('INDEX_PATH', APPLICATION_PATH . '/data/eclass-index');


// Define log level
require_once 'LogUtil.php';
define('LOG_LEVEL', LogUtil::DEBUG);


require_once 'config.php';
require_once 'EclassConfig.php';
require_once 'EclassIndexer.php';


$indexer = new EclassIndexer(INDEX_PATH, new EclassConfig($dsn, $username, $password, $urlBasePath));
$hits = $indexer->displayStats();

