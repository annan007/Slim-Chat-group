<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class JoinGroupAction
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $groupId = $args['groupId'];

        // Check if the group exists
        $stmt = $this->pdo->prepare('SELECT id FROM groups WHERE id = :groupId');
        $stmt->execute(['groupId' => $groupId]);

        if (!$stmt->fetch()) {
            $errorResponse = ['error' => 'Group not found'];
            $response = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }

        // assuming the user ID is provided in the request body
        $data = $request->getParsedBody();


        if (empty($data['userId'])) {
            $errorResponse = ['error' => 'User ID is required'];
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    
        // Check if the user ID already exists in the group
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM group_users WHERE group_id = :groupId AND user_id = :userId');
        $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId']]);
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            $errorResponse = ['error' => 'User ID already exists in the group'];
            $response = $response->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    
        // Insert the user into the group
        $stmt = $this->pdo->prepare('INSERT INTO group_users (group_id, user_id) VALUES (:groupId, :userId)');
        $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId']]);
    
        $successResponse = ['groupId' => $groupId, 'userId' => $data['userId']];
        $response->getBody()->write(json_encode($successResponse));
        return $response->withHeader('Content-Type', 'application/json');
    
    }
}
