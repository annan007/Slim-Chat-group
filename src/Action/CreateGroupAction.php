<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class CreateGroupAction
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();

        // check if the group name field is empty
        if (empty($data['name'])) {
            $errorResponse = ['error' => 'Group name is required'];
            $response->getBody()->write((string) json_encode($errorResponse));
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        // check if the group name already exist
        // Check if group name already exists
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM groups WHERE name = :name');
        $stmt->execute(['name' => $data['name']]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errorResponse = ['error' => 'Group name already exists. Pick a new one.'];
            $response->getBody()->write((string) json_encode($errorResponse));
            return $response->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
        }

        $stmt = $this->pdo->prepare('INSERT INTO groups (name) VALUES (:name)');
        $stmt->execute(['name' => $data['name']]);

        $groupId = $this->pdo->lastInsertId();

        $successResponse = ['id' => $groupId, 'name' => $data['name']];
        
        $response->getBody()->write((string) json_encode($successResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
