<?php

namespace Edev\Database\Connector;

class Connector
{
    protected $options = [
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function createConnection($dsn, array $config, $options)
    {
        [$username, $password] = [
            $config['username'] ?? null, $config['password'] ?? null,
        ];

        try {
            return $this->createPdoConnection(
                $dsn,
                $username,
                $password,
                $options
            );
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    protected function createPdoConnection($dsn, $username, $password, $options)
    {

        return new \PDO($dsn, $username, $password, $options);
    }

    protected function createPdo(array $config)
    {
    }

    protected function getHostDsn(array $config)
    {
        extract($config);
        return isset($port) ?
        "mysql:host=$host;port=$port;dbname=$database" :
        "mysql:host=$host;dbname=$database";
    }

    protected function getOptions(array $config)
    {
        $options = $config['options'] ?? [];

        return array_diff_key($this->options, $options) + $options;
    }

    public function connect($config)
    {

        $dsn = $this->getHostDsn($config);

        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        return $connection;
    }

    /**
     * Stashed PDO execution method in the Connector class. I don't think it belongs here
     * long-term, but will work for now.
     */
    public function execute($query, $data)
    {
        if ($data != null && !is_array($data)) {
            throw new \PDOException('Data type mismatch: data must be null OR array.');
        }
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        $stmt = $this->pdo->prepare($query);
        // echo 'outside statement';
        try {
            if ($stmt->execute($data)) {

                $action = trim(explode(' ', $stmt->queryString)[0]);
                // IF THE ACTION IS SELECT, RETURN A FETCH STATEMENT
                // OTHERWISE RETURN TRUE BOOL VALUE
                if (strtoupper($action) == 'SELECT') {
                    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } else if (strtoupper($action) == 'DELETE' || strtoupper($action) == 'UPDATE') {
                    return $stmt->rowCount();
                } else {
                    return true;
                }
            } else {
                throw new \PDOException('Error executing query. <br />' . __CLASS__ . '\\' . __FUNCTION__);
            }
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == MYSQL_CODE_DUPLICATE_KEY) {
                echo $e->getMessage();
            }
        }
    }
}
