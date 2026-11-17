<?php

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at https://github.com/JamesHeinrich/getID3       //
//            or https://www.getid3.org                        //
//            or http://getid3.sourceforge.net                 //
//                                                             //
// extension.cache.mysql.php - part of getID3()                //
// Please see readme.txt for more information                  //
//                                                            ///
/////////////////////////////////////////////////////////////////
//                                                             //
// This extension written by Allan Hansen <ahØartemis*dk>      //
// Table name mod by Carlo Capocasa <calroØcarlocapocasa*com>  //
//                                                            ///
/////////////////////////////////////////////////////////////////


/**
* This is a caching extension for getID3(). It works the exact same
* way as the getID3 class, but return cached information very fast
*
* Example:  (see also demo.cache.mysql.php in /demo/)
*
*    Normal getID3 usage (example):
*
*       require_once 'getid3/getid3.php';
*       $getID3 = new getID3;
*       $getID3->encoding = 'UTF-8';
*       $info1 = $getID3->analyze('file1.flac');
*       $info2 = $getID3->analyze('file2.wv');
*
*    getID3_cached usage:
*
*       require_once 'getid3/getid3.php';
*       require_once 'getid3/getid3/extension.cache.mysql.php';
*       // 5th parameter (tablename) is optional, default is 'getid3_cache'
*       $getID3 = new getID3_cached_mysql('localhost', 'database', 'username', 'password', 'tablename');
*       $getID3->encoding = 'UTF-8';
*       $info1 = $getID3->analyze('file1.flac');
*       $info2 = $getID3->analyze('file2.wv');
*
*
* Supported Cache Types    (this extension)
*
*   SQL Databases:
*
*   cache_type          cache_options
*   -------------------------------------------------------------------
*   mysql               host, database, username, password
*
*
*   DBM-Style Databases:    (use extension.cache.dbm)
*
*   cache_type          cache_options
*   -------------------------------------------------------------------
*   gdbm                dbm_filename, lock_filename
*   ndbm                dbm_filename, lock_filename
*   db2                 dbm_filename, lock_filename
*   db3                 dbm_filename, lock_filename
*   db4                 dbm_filename, lock_filename  (PHP5 required)
*
*   PHP must have write access to both dbm_filename and lock_filename.
*
*
* Recommended Cache Types
*
*   Infrequent updates, many reads      any DBM
*   Frequent updates                    mysql
*/


class getID3_cached_mysql extends getID3
{
	/**
	 * @var \mysqli_result|bool
	 */
	private $cursor;

	/**
	 * @var \mysqli
	 */
	private $connection;

	/**
	 * @var string
	 */
	private $table;


	/**
	 * constructor - see top of this file for cache type and cache_options
	 *
	 * @param string $host
	 * @param string $database
	 * @param string $username
	 * @param string $password
	 * @param string $table
	 *
	 * @throws Exception
	 * @throws getid3_exception
	 */
	public function __construct($host, $database, $username, $password, $table='getid3_cache') {

		// Check for mysqli support
		if (!function_exists('mysqli_connect')) {
			throw new Exception('PHP not compiled with mysqli support.');
		}

		// Connect to database (persistent connection)
		$this->connection = mysqli_connect('p:'.$host, $username, $password, $database);
		if (!$this->connection) {
			throw new Exception('mysqli_connect() failed - check permissions and spelling.');
		}

		// Set table
		$this->table = $table;

		// Create cache table if not exists
		$this->create_table();

		// Check version number and clear cache if changed
		$version = '';
		$SQLquery  = 'SELECT `value`';
		$SQLquery .= ' FROM `'.mysqli_real_escape_string($this->connection, $this->table).'`';
		$SQLquery .= ' WHERE (`filename` = \''.mysqli_real_escape_string($this->connection, getID3::VERSION).'\')';
		$SQLquery .= ' AND (`filesize` = -1)';
		$SQLquery .= ' AND (`filetime` = -1)';
		$SQLquery .= ' AND (`analyzetime` = -1)';
		if ($this->cursor = mysqli_query($this->connection, $SQLquery)) {
			list($version) = mysqli_fetch_array($this->cursor);
		}
		if ($version != getID3::VERSION) {
			$this->clear_cache();
		}

		parent::__construct();
	}



	/**
	 * clear cache
	 */
	public function clear_cache() {

		$this->cursor = mysqli_query($this->connection, 'DELETE FROM `'.mysqli_real_escape_string($this->connection, $this->table).'`');
		$this->cursor = mysqli_query($this->connection, 'INSERT INTO `'.mysqli_real_escape_string($this->connection, $this->table).'` VALUES (\''.getID3::VERSION.'\', -1, -1, -1, \''.getID3::VERSION.'\')');
	}



	/**
	 * analyze file
	 *
	 * @param string   $filename
	 * @param int      $filesize
	 * @param string   $original_filename
	 * @param resource $fp
	 *
	 * @return mixed
	 */
	public function analyze($filename, $filesize=null, $original_filename='', $fp=null) {

		$filetime = 0;
		if (file_exists($filename)) {

			// Short-hands
			$filetime = filemtime($filename);
			$filesize =  filesize($filename);

			// Lookup file
			$SQLquery  = 'SELECT `value`';
			$SQLquery .= ' FROM `'.mysqli_real_escape_string($this->connection, $this->table).'`';
			$SQLquery .= ' WHERE (`filename` = \''.mysqli_real_escape_string($this->connection, $filename).'\')';
			$SQLquery .= '   AND (`filesize` = \''.mysqli_real_escape_string($this->connection, $filesize).'\')';
			$SQLquery .= '   AND (`filetime` = \''.mysqli_real_escape_string($this->connection, $filetime).'\')';
			$this->cursor = mysqli_query($this->connection, $SQLquery);
			if ($this->cursor && mysqli_num_rows($this->cursor) > 0) {
				// Hit
				list($result) = mysqli_fetch_array($this->cursor);
				return unserialize(base64_decode($result));
			}
		}

		// Miss
		$analysis = parent::analyze($filename, $filesize, $original_filename, $fp);

		// Save result
		if (file_exists($filename)) {
			$SQLquery  = 'INSERT INTO `'.mysqli_real_escape_string($this->connection, $this->table).'` (`filename`, `filesize`, `filetime`, `analyzetime`, `value`) VALUES (';
			$SQLquery .=   '\''.mysqli_real_escape_string($this->connection, $filename).'\'';
			$SQLquery .= ', \''.mysqli_real_escape_string($this->connection, $filesize).'\'';
			$SQLquery .= ', \''.mysqli_real_escape_string($this->connection, $filetime).'\'';
			$SQLquery .= ', \''.mysqli_real_escape_string($this->connection, time()   ).'\'';
			$SQLquery .= ', \''.mysqli_real_escape_string($this->connection, base64_encode(serialize($analysis))).'\')';
			$this->cursor = mysqli_query($this->connection, $SQLquery);
		}
		return $analysis;
	}



	/**
	 * (re)create sql table
	 *
	 * @param bool $drop
	 */
	private function create_table($drop=false) {

		$SQLquery  = 'CREATE TABLE IF NOT EXISTS `'.mysqli_real_escape_string($this->connection, $this->table).'` (';
		$SQLquery .=   '`filename` VARCHAR(990) NOT NULL DEFAULT \'\'';
		$SQLquery .= ', `filesize` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `filetime` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `analyzetime` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `value` LONGTEXT NOT NULL';
		$SQLquery .= ', PRIMARY KEY (`filename`, `filesize`, `filetime`))';
		$this->cursor = mysqli_query($this->connection, $SQLquery);
		echo mysqli_error($this->connection);
	}
}
