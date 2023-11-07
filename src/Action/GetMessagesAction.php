<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class GetMessagesAction
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

        // Fetch all messages for the group
        $stmt = $this->pdo->prepare('SELECT user_id, message, timestamp FROM messages WHERE group_id = :groupId');
        $stmt->execute(['groupId' => $groupId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (empty($messages)) {
            $errorResponse = ['error' => 'No messages found in the group'];
            $response = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errorResponse));
            return $response;
        }

        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
