<?php

// namespace Database\Connectors;

// class Connector
// {
//     protected $options = [
//         PDO::ATTR_CASE => PDO::CASE_NATURAL,
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
//         PDO::ATTR_STRINGIFY_FETCHES => false,
//         PDO::ATTR_EMULATE_PREPARES => false,
//     ];

//     public function createConnection($dsn, $config, $options)
//     {

//         [$username, $password] = [
//             $config['username'] ?? null, $config['password'] ?? null,
//         ];

//         try {
//             return $this->createPdoConnection($dsn, $username, $password, $options);
//         } catch (\Exception $e) {

//         }
//     }

//     protected function createPdoConnection($dsn, $username, $password, $options)
//     {
//         return new PDO($dsn, $username, $password, $options);
//     }
// }
