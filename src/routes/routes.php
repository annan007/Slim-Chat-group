<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$pdo = new PDO('sqlite:../src/database/chat_app.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create a new chat group
$app->post('/groups', function (Request $request, Response $response, $args) use ($pdo) {
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
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM groups WHERE name = :name');
    $stmt->execute(['name' => $data['name']]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errorResponse = ['error' => 'Group name already exists. Pick a new one.'];
        $response->getBody()->write((string) json_encode($errorResponse));
        return $response->withStatus(409)
            ->withHeader('Content-Type', 'application/json');
    }

    $stmt = $pdo->prepare('INSERT INTO groups (name) VALUES (:name)');
    $stmt->execute(['name' => $data['name']]);

    $groupId = $pdo->lastInsertId();

    $successResponse = ['id' => $groupId, 'name' => $data['name']];
    $response->getBody()->write((string) json_encode($successResponse));
    return $response->withHeader('Content-Type', 'application/json');
});

// Join a chat group
// We are assuming that the user id is already available via some token and hence is unique.
$app->post('/groups/{groupId}/join', function (Request $request, Response $response, $args) use ($pdo) {
    $groupId = $args['groupId'];

    // Check if the group exists
    $stmt = $pdo->prepare('SELECT id FROM groups WHERE id = :groupId');
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
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM group_users WHERE group_id = :groupId AND user_id = :userId');
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
    $stmt = $pdo->prepare('INSERT INTO group_users (group_id, user_id) VALUES (:groupId, :userId)');
    $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId']]);

    $successResponse = ['groupId' => $groupId, 'userId' => $data['userId']];
    $response->getBody()->write(json_encode($successResponse));
    return $response->withHeader('Content-Type', 'application/json');

});

// Send a message to a chat group
$app->post('/groups/{groupId}/messages', function (Request $request, Response $response, $args) use ($pdo) {
    $groupId = $args['groupId'];

    // Check if the group exists
    $stmt = $pdo->prepare('SELECT id FROM groups WHERE id = :groupId');
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
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM group_users WHERE group_id = :groupId AND user_id = :userId');
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
    $stmt = $pdo->prepare('INSERT INTO messages (group_id, user_id, message) VALUES (:groupId, :userId, :message)');
    $stmt->execute(['groupId' => $groupId, 'userId' => $data['userId'], 'message' => $data['message']]);

    $messageId = $pdo->lastInsertId();

    $successResponse = ['id' => $messageId, 'groupId' => $groupId, 'userId' => $data['userId'], 'message' => $data['message']];
    $response->getBody()->write(json_encode($successResponse));
    return $response->withHeader('Content-Type', 'application/json');
});


// Get all messages within a chat group
$app->get('/groups/{groupId}/messages', function (Request $request, Response $response, $args) use ($pdo) {
    $groupId = $args['groupId'];

    // Check if the group exists
    $stmt = $pdo->prepare('SELECT id FROM groups WHERE id = :groupId');
    $stmt->execute(['groupId' => $groupId]);

    if (!$stmt->fetch()) {
        $errorResponse = ['error' => 'Group not found'];
        $response = $response->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($errorResponse));
        return $response;
    }

    // Fetch all messages for the group
    $stmt = $pdo->prepare('SELECT user_id, message, timestamp FROM messages WHERE group_id = :groupId');
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
});
