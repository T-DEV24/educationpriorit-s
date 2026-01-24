<?php

class Router
{
    private $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }
                [$controller, $action] = $route['handler'];
                if (!class_exists($controller)) {
                    throw new RuntimeException('Controller not found: ' . $controller);
                }
                $instance = new $controller();
                if (!method_exists($instance, $action)) {
                    throw new RuntimeException('Action not found: ' . $action);
                }
                $instance->{$action}($params);
                return;
            }
        }

        http_response_code(404);
        View::render('404', ['title' => 'Page introuvable']);
    }
}
