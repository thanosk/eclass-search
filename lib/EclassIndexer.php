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

require_once 'LogUtil.php';
require_once 'CourseIndexer.php';
require_once 'Zend/Search/Lucene.php';
require_once 'Zend/Search/Lucene/Storage/Directory/Filesystem.php';
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';
require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8Num/CaseInsensitive.php';
require_once 'Zend/Search/Lucene/Search/QueryParser.php';

class EclassIndexer
{
    private $index = null;
    private $cfg = null;
    
    public function __construct($index_path = null, $thecfg = null)
    {
        if ($index_path === null) {
            throw new Exception('No index directory specified');
        }

        if ($thecfg === null) {
            throw new Exception('No config object specified');
        }

        // Give read-writing permissions only for current user and group
        Zend_Search_Lucene_Storage_Directory_Filesystem::setDefaultFilePermissions(0660);
        // Utilize UTF-8 compatible text analyzer
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());

        if (file_exists($index_path)) {
            // Open index
            $this->index = Zend_Search_Lucene::open($index_path);
            LogUtil::debug("index opened");
        } else {
            // Create index
            $this->index = Zend_Search_Lucene::create($index_path);
            LogUtil::debug("index created");
        }

        // Set Index Format Version
        $this->index->setFormatVersion(Zend_Search_Lucene::FORMAT_2_3);

        $this->cfg = $thecfg;
    }
    
    public function process()
    {
        if ($this->index === null) {
            throw new Exception('No index created or opened');
        }

        $courseindexer = new CourseIndexer($this->index, $this->cfg);
        $courseindexer->process();

        $this->finalize();
    }
    
    private function finalize()
    {
        // Finish
        $this->index->optimize();
        $this->index->commit();

        LogUtil::debug("index size: ". $this->index->count());
        LogUtil::debug("index no. of docs: ". $this->index->numDocs());
    }
    
    public function find($queryStr = null)
    {
        if ($this->index === null) {
            throw new Exception('No index created or opened');
        }

        if ($queryStr === null) {
            throw new Exception('No query string specified');
        }

        $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr, 'utf-8');
        return $this->index->find($query);
    }
    
    public function displayStats()
    {
        if ($this->index === null) {
            throw new Exception('No index created or opened');
        }

        LogUtil::info('format version: '. $this->index->getFormatVersion());
        LogUtil::info('MaxBufferedDocs: '. $this->index->getMaxBufferedDocs());
        LogUtil::info('MaxMergeDocs: '. $this->index->getMaxMergeDocs());
        LogUtil::info('MergeFactor: '. $this->index->getMergeFactor());
    }
}
