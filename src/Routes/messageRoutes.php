<?php

use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $group) {
    $group->post('/{groupId}', App\Controllers\MessageController::class . ':postMessage'); // Post a message to a group
    $group->get('/{groupId}/{userId}', App\Controllers\MessageController::class . ':getMessages'); // Get all messages from a group
};
