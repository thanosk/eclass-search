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
require_once 'ResourceIndexer.php';
require_once 'EclassConfig.php';
require_once 'Zend/Search/Lucene/Document.php';
require_once 'Zend/Search/Lucene/Field.php';
require_once 'Zend/Search/Lucene/Index/Term.php';

class CourseIndexer implements ResourceIndexer
{
    private $index = null;
    private $cfg = null;
    
    public function __construct($theindex = null, $thecfg = null)
    {
        if ($theindex === null) {
        	throw new Exception('No index object specified');
        }
        
        if ($thecfg === null) {
        	throw new Exception('No config object specified');
        }
        
        $this->index = $theindex;
        $this->cfg = $thecfg;
    }
    
    public function process()
    {
        $delcount = 0;
        $dbh = $this->cfg->getPdo();
        
        $q = $dbh->prepare("SELECT id, title, keywords, code, public_code, prof_names, created FROM course");
        $q->execute();
        $courses = $q->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($courses as $c) {
            
            // delete existing course from index
            $term = new Zend_Search_Lucene_Index_Term($c->id, 'course_id');
            $docIds  = $this->index->termDocs($term);
            foreach ($docIds as $id) {
                $this->index->delete($id);
                $delcount++;
            }
            
            $doc = new Zend_Search_Lucene_Document();
            
            $doc->addField(Zend_Search_Lucene_Field::Keyword('course_id', $c->id));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $c->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('keywords', $c->keywords));
            $doc->addField(Zend_Search_Lucene_Field::Text('code', $c->code));
            $doc->addField(Zend_Search_Lucene_Field::Text('public_code', $c->public_code));
            $doc->addField(Zend_Search_Lucene_Field::Text('prof_names', $c->prof_names));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('created', $c->created));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $this->cfg->getUrlBasePath() . 'courses/'. $c->code));
            
            $this->index->addDocument($doc);
        }
        
        if ($delcount > 0)
            LogUtil::debug('updated documents: '. $delcount);
    }
}