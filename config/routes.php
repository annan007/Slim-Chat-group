<?php

use Slim\App;

return function (App $app) {

    // Create a new chat group
    $app->post('/groups', \App\Action\CreateGroupAction::class);

    // Join a chat group
    $app->post('/groups/{groupId}/join', \App\Action\JoinGroupAction::class);

    // Send a message to a chat group
    $app->post('/groups/{groupId}/messages', \App\Action\SendMessageAction::class);
    
    // Get all messages within a chat group
    $app->get('/groups/{groupId}/messages', \App\Action\GetMessagesAction::class);
};
