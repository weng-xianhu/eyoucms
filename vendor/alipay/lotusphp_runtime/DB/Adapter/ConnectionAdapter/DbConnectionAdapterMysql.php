<?php
class LtDbConnectionAdapterMysql implements LtDbConnectionAdapter
{
	public function connect($connConf)
	{
		try {
			return mysqli_connect($connConf["host"], $connConf["username"], $connConf["password"], '', (int)$connConf["port"]);
		} catch (\mysqli_sql_exception $e) {
			return false;
		}
	}

	public function exec($sql, $connResource)
	{
		try {
			return mysqli_query($connResource, $sql) ? mysqli_affected_rows($connResource) : false;
		} catch (\mysqli_sql_exception $e) {
			return false;
		}
	}

	public function query($sql, $connResource)
	{
		$result = mysqli_query($connResource, $sql);
		$rows = array();
		if ($result instanceof \mysqli_result) {
			while($row = mysqli_fetch_assoc($result))
			{
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function lastInsertId($connResource)
	{
		return mysqli_insert_id($connResource);
	}

	public function escape($sql, $connResource)
	{
		return mysqli_real_escape_string($connResource, $sql);
	}
}
