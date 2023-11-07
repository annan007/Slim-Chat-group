<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class SendMessageAction
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

        // Add the message to the group
        $data = $request->getParsedBody();
        
        if (empty($data['userId']) || empty($data['message'])) {
            $errorResponse = ['error' => 'User ID and message are required'];
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    
        // Check if the user ID exists in the group
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM group_users WHERE group_id = :groupId AND user_id = :userId');
        $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId']]);
        $count = $stmt->fetchColumn();
    
        if ($count === 0) {
            $errorResponse = ['error' => 'User ID does not exist in the group'];
            $response = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }
    
        // Insert the message into the group
        $stmt = $this->pdo->prepare('INSERT INTO messages (group_id, user_id, message) VALUES (:groupId, :userId, :message)');
        $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId'], 'message' => $data['message']]);
    
        $messageId = $this->pdo->lastInsertId();
    
        $successResponse = ['id' => $messageId, 'groupId' => $groupId, 'userId' => $data['userId'], 'message' => $data['message']];
        $response->getBody()->write(json_encode($successResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
