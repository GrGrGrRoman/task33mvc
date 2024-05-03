<?php 

namespace App\lib;

class Db
{
    protected $db;
    private $host = 'localhost';
	private $dbName = 'websocket';
	private $user = 'root';
	private $pass = '';

	public function connect()
	{
		try {
			$this->db = new \PDO('mysql:host=' . $this->host . '; dbname=' . $this->dbName, $this->user, $this->pass);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return $this->db;
		} catch( \PDOException $e) {
			echo 'Database Error: ' . $e->getMessage();
		}
	} 

    public function query($sql, $params = [])
    {
		$stmt = $this->db->prepare($sql);
		if (!empty($params))
        {
			foreach ($params as $key => $val)
            {
				if (is_int($val))
                {
					$type = \PDO::PARAM_INT;
				}
                else
                {
					$type = \PDO::PARAM_STR;
				}
				$stmt->bindValue(':'.$key, $val, $type);
			}
		}
		$stmt->execute();
		return $stmt;
	}
  
    public function row($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function column($sql, $params = [])
    {
        $result = $this->query($sql, $params);
        return $result->fetchColumn();
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }
}