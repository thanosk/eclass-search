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
$starttime = microtime(true);

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


// Command line arguments
if ($argc < 2) {
	LogUtil::info('usage: '. $argv[0] .' query_string');
	exit(1);
}


require_once 'config.php';
require_once 'EclassConfig.php';
require_once 'EclassIndexer.php';


$indexer = new EclassIndexer(INDEX_PATH, new EclassConfig($dsn, $username, $password, $urlBasePath));
$hits = $indexer->find($argv[1]);

LogUtil::info(count($hits) .' hits found for query: \''. $argv[1] .'\'.');

foreach($hits as $hit) {
    LogUtil::info('------------');
    LogUtil::info('hit title: '. $hit->title);
    LogUtil::info('hit keywords: '. $hit->keywords);
    LogUtil::info('hit url: '. $hit->url);
}


$endtime = microtime(true);
$duration = round($endtime - $starttime, 2);
LogUtil::debug('exec time: '. $duration .'s');

