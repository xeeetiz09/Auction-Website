<?php
class AllQueries {
    private $pdo;

    // Constructor to establish a database connection
    public function __construct() {
        try {
            $pdo = new PDO('mysql:dbname=cars;host=db', 'student', 'student');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    // Getter for the PDO object
    public function getPdo(){
        return $this->pdo;
    }

    // Function to insert a record into a table
    public function insert($table, $values) {
        $keys = array_keys($values);
        $val1 = implode(', ', $keys);
        $val2 = implode(', :', $keys);
        try {
            $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (' . $val1 . ')' . ' VALUES (:' . $val2 . ')');
            $stmt->execute($values);
        } catch (PDOException $e) {
            die('Insertion failed: ' . $e->getMessage());
        }
    }

    // Function to find a record by a specific field and value
    public function find($table, $field, $value) {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = :value');
        $criteria = [
            'value' => $value
        ];
        $stmt->execute($criteria);
        return $stmt->fetch();
    }

    // Function to select records by a specific field and value
    public function select($table, $field, $value) {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = ' . $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Function to select all records from a table
    public function selectAll($table, $condition = '') {
        $sql = 'SELECT * FROM ' . $table;
        if (!empty($condition)) {
            $sql .= ' WHERE ' . $condition;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    

    // Function to update a record in a table
    public function update($table, $record, $primaryKey) {
        $query = 'UPDATE `' . $table . '` SET ';
        $parameters = [];
        foreach ($record as $key => $value) {
            $parameters[] = '`' . $key . '` = :' . $key;
        }
        $query .= implode(', ', $parameters);
        $query .= ' WHERE `' . $primaryKey . '` = :primaryKey';
        $record['primaryKey'] = $record[$primaryKey];
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($record);
    }
    

    // Function to delete a record by a specific field and value
    public function delete($table, $field, $value) {
        $stmt = $this->pdo->prepare('DELETE FROM ' . $table . ' WHERE ' . $field . ' = :value');
        $criteria = [
            'value' => $value
        ];
        $stmt->execute($criteria);
    }

    // Function to count the number of rows in a table
    public function countRows($table){
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $table);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Function to count the number of rows in a table based on two conditions
    public function countSpecRows($table, $field1, $value1, $field2, $value2){
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE ' . $field1 . ' = :value1 AND ' . $field2 . ' = :value2');
        $stmt->bindParam(':value1', $value1);
        $stmt->bindParam(':value2', $value2);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Function to select a limited number of records from a table
    public function limitSelection($table, $field, $value){
        $stmt = $this->pdo->prepare('SELECT * FROM '.$table.' ORDER BY '. $field.' desc LIMIT '. $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Function to count the number of rows in a table based on a single condition
    public function countSpecRows_($table, $field, $value){
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $table . ' WHERE ' . $field . ' = :value1');
        $stmt->bindParam(':value1', $value);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>
