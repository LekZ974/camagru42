<?php

namespace Camagru;

use PDO;

class Database
{
    private $_DB_DSN;
    private $_DB_USER;
    private $_DB_PASSWORD;
    private $_db;
//A faire plus propre!!
    /**
     * Database constructor.
     * @param $_DB_DSN
     * @param $_DB_USER
     * @param $_DB_PASSWORD
     */
    public function __construct()
    {
        $this->setDSN('sqlite:'.__DIR__.'/../../app/config/db_camagru.db');
        $this->setUSER('root');
        $this->setPASSWORD('root');
    }
    /**
     * @return string
     */
    public function getDSN()
    {
        return $this->_DB_DSN;
    }
    /**
     * @param string $_DB_DSN
     */
    public function setDSN($DB_DSN)
    {
        $this->_DB_DSN = $DB_DSN;
        return $this;
    }
    /**
     * @return string
     */
    public function getUSER()
    {
        return $this->_DB_USER;
    }
    /**
     * @param string $_DB_USER
     */
    public function setUSER($DB_USER)
    {
        $this->_DB_USER = $DB_USER;
        return $this;
    }
    /**
     * @return string
     */
    public function getPASSWORD()
    {
        return $this->_DB_PASSWORD;
    }
    /**
     * @param string $_DB_PASSWORD
     */
    public function setPASSWORD($DB_PASSWORD)
    {
        $this->_DB_PASSWORD = $DB_PASSWORD;
        return $this;
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        if ($this->_db == null)
        {
            try
            {
                $db = new PDO($this->_DB_DSN, $this->_DB_USER, $this->_DB_PASSWORD);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_db = $db;
            }
            catch (Exception $error)
            {
                die('Erreur : ' . $error->getMessage());
            }
        }
        return $this->_db;
    }
}