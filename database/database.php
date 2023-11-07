<?php

class Database
{
    private $pdo;

    //the constructor creates the table if they don't exist
    public function __construct()
    {
        $databasePath = __DIR__ . '/../database/chat_app.db';
        $this->pdo = new PDO('sqlite:' . $databasePath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    private function createTables()
    {
        $this->createGroupsTable();
        $this->createMessagesTable();
        $this->createGroupUsersTable();
        $this->createUsersTable();
    }

    private function createGroupsTable()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )');
    }

    private function createMessagesTable()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_id INTEGER,
            user_id INTEGER,
            message TEXT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES groups(id)
        )');
    }

    private function createGroupUsersTable()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS group_users (
            group_id INTEGER,
            user_id INTEGER,
            FOREIGN KEY (group_id) REFERENCES groups(id),
            FOREIGN KEY (user_id) REFERENCES users(id),
            PRIMARY KEY (group_id, user_id)
        )');
    }

    private function createUsersTable()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )');
    }
}