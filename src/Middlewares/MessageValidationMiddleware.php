<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Validators\MessageValidator;

class MessageValidationMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $data = $request->getParsedBody();
        $route = $request->getAttribute('route'); // Get the current route

        try {
            // Validate based on the route
            if ($route) {
                $routeName = $route->getName(); // Get route name (defined in index.php)

                if ($routeName === 'post_message') {
                    // For posting a message, validate user_id and message
                    MessageValidator::validateUserId($data);
                    MessageValidator::validateMessageContent($data);
                } elseif ($routeName === 'get_messages') {
                    // Add any other validation for getting messages (if needed)
                    MessageValidator::validateUserName($data['username']);
                }
            }
        } catch (\InvalidArgumentException $e) {
            // If validation fails, return a 400 error response
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // If validation passes, proceed to the next middleware or controller
        return $handler->handle($request);
    }
}
