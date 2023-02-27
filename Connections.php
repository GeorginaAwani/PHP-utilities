<?php

namespace Utility;

use Exception;
use PDO;

trait DBConnection
{
	abstract protected function db_host();
	abstract protected function db_name();
	abstract protected function db_user();
	abstract protected function db_pass();

	private $conn;

	private function conn()
	{
		try {
			if (!$this->conn) {
				$this->conn = new PDO("mysql:host={$this->db_host()};dbname={$this->db_name()}", $this->db_user(), $this->db_pass());
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function query(string $sql)
	{
		$this->conn();

		return $this->conn->query($sql);
	}

	public function transaction()
	{
		$this->conn();
		$this->conn->beginTransaction();
	}

	public function transactionStarted()
	{
		return $this->conn->inTransaction();
	}

	public function commit()
	{
		$this->conn->commit();
	}

	public function rollback()
	{
		$this->conn->rollBack();
	}

	public function prepare(string $sql, array $parameters)
	{
		try {
			$this->conn();
			$query = $this->conn->prepare($sql);
			foreach ($parameters as $name => $value) {
				$query->bindValue($name, $value);
			}

			return $query->execute();
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	public function execute(string $sql)
	{
		$this->conn();
		return $this->conn->exec($sql);
	}

	private function fields($fields)
	{
		if (is_string($fields)) $fields = [$fields];
		elseif (!is_array($fields)) throw new Exception("Invalid SELECT fields. 'fields' must be an array or a string");

		$fields = array_map(function ($field) {
			return "`$field`";
		}, $fields);
		$fields = join(", ", $fields);
	}

	public function selectFromTable(string $table, $fields, $where = null, $groupBy = null, $orderBy = null, $limit = null)
	{
		$sql = "SELECT {$this->fields($fields)} FROM `$table`";
		if ($where) $sql .= " WHERE $where";
		if ($groupBy) $sql .= " GROUP BY {$this->fields($fields)}";
		if ($orderBy) {
			$order = [];
			foreach ($orderBy as $field => $o) {
				$order[] = "$field $o";
			}
			$orderBy = join(", ", $order);
			$sql .= " ORDER BY $order";
		}
		if ($limit) $sql .= " LIMIT $limit";

		return $this->query($sql);
	}

	public function updateTable(string $table, array $set, $where = null)
	{
		$sql = "UPDATE `$table` SET ";

		$fields = [];
		foreach ($set as $name => $value) {
			$fields[] = "`$name` = '$value'";
		}

		$sql .= join(", ", $fields);
		$sql .= " WHERE $where";

		return $this->execute($sql);
	}

	public function deleteFromTable(string $table, string $where)
	{
		$sql = "DELETE FROM `$table` WHERE $where";
		return $this->execute($sql);
	}

	public function insertIntoTable(string $table, array $set)
	{
		$fields = $values = [];
		foreach ($set as $name => $value) {
			$fields[] = "`$name`";
			$values[] = "'$value'";
		}

		$fields = join(", ", $fields);
		$values = join(", ", $values);
		$sql = "INSERT INTO `$table`($fields) VALUES($values)";

		return $this->execute($sql);
	}
}
