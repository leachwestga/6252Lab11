<?php
class Database {
    private $db;
    private $error_message;
    
    /**
     * Instantiates a new database object that connects
     * to the database
     */
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=task_manager';
        $username = 'mgs_user';
        $password = 'pa55word';
        $this->error_message = '';
        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            $this->error_message = $e->getMessage();
        }
    }
    
    /**
     * Checks the connection to the database
     *
     * @return boolean - true if a connection to the database has been established
     */
    public function isConnected() {
        return ($this->db != Null);
    }
    
    /**
     * Returns the error message
     * 
     * @return string - the error message
     */
    public function getErrorMessage() {
        return $this->error_message;
    }
    
    /**
     * Checks if the specified username is in this database
     * 
     * @param string $username
     * @return boolean - true if username is in this database
     */
    public function isValidUser($username) {
        $query = 'SELECT * FROM users
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return !($row === false);
    }
    
    /**
     * Adds the specified user to the table users
     * 
     * @param type $username
     * @param type $password
     */
    public function addUser($username, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = 'INSERT INTO users (username, password)
              VALUES (:username, :password)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $hash);
        $statement->execute();
        $statement->closeCursor();
    }
    
    /**
     * Checks the login credentials
     * 
     * @param type $username
     * @param type $password
     * @return boolen - true if the specified password is valid for the 
     *              specified username
     */
    public function isValidUserLogin($username, $password) {
        $query = 'SELECT password FROM users
              WHERE username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        if ($row === false) {
            return false;
        }
        $hash = $row['password'];
        return password_verify($password, $hash);
    }
    
    /**
     * Retrieves the tasks for the specified user
     * 
     * @param string $username
     * @return array - array of tasks for the specified username
     */
    public function getTasksForUser($username) {
        $query = 'SELECT * FROM tasks
                  WHERE tasks.username = :username';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $tasks = $statement->fetchAll();
        $statement->closeCursor();
        return $tasks;
    }
    
    /**
     * Adds the specified task to the tasks table
     * 
     * @param string $username
     * @param string $task
     */
    public function addTask($username, $task) {
        $query = 'INSERT INTO tasks (username, task)
                  VALUES (:username, :task)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':task', $task);
        $statement->execute();
        $statement->closeCursor();
    }
    
    /**
     * Deletes the task with the specified id
     * 
     * @param integer $task_id
     */
    public function deleteTask($task_id) {
        $query = 'DELETE FROM tasks
                  WHERE taskID = :task_id';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':task_id', $task_id);
        $statement->execute();
        $statement->closeCursor();
    }
}
?>