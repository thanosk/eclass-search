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

require_once 'EclassConfigInterface.php';

class EclassConfig implements EclassConfigInterface
{
    private $pdo = null;
    private $urlBasePath = null;
    
    public function __construct($dsn, $username, $password, $urlBasePath)
    {
        if ($dsn === null) {
            throw new Exception('No PDO Data Source Name specified.');
        }
        
        if ($urlBasePath === null) {
            throw new Exception('No urlBasePath specified.');
        }
        
        try {
            $this->pdo = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch(PDOException $e) {
            die('Database Connection failed: '. $e->getMessage());
        }
        
        $this->urlBasePath = $urlBasePath;
    }
    
    public function getPdo()
    {
        return $this->pdo;
    }
    
    public function getUrlBasePath()
    {
        return $this->urlBasePath;
    }
}