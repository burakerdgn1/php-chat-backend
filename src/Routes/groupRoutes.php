<?php

use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $group) {
    $group->post('', App\Controllers\GroupController::class . ':createGroup')->setName('create_group'); // Create a group
    $group->get('', App\Controllers\GroupController::class . ':getAllGroups'); // List all groups
    $group->post('/{groupId}/join', App\Controllers\GroupController::class . ':joinGroup')->setName('join_group');
    $group->get('/user-groups/{username}', App\Controllers\GroupController::class . ':getUserGroupsByUsername');
};
