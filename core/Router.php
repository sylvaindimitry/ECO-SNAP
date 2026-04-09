<?php
/**
 * Router Class
 * Gestionnaire de routage simple pour URLs propres
 */
class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    /**
     * Ajouter une route GET
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }
    
    /**
     * Ajouter une route POST
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * Ajouter une route PUT
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
        return $this;
    }
    
    /**
     * Ajouter une route DELETE
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
        return $this;
    }
    
    /**
     * Ajouter une route interne
     */
    private function addRoute($method, $path, $handler) {
        $path = $this->basePath . $path;
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertToRegex($path)
        ];
    }
    
    /**
     * Convertir un chemin en expression régulière
     */
    private function convertToRegex($path) {
        // Convertir les paramètres dynamiques {id} en regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Dispatcher la requête vers le bon handler
     */
    public function dispatch() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Support pour method override via POST
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // Extraire les paramètres de l'URL
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return $this->callHandler($route['handler'], $params);
            }
        }
        
        // Route non trouvée
        http_response_code(404);
        $this->renderError(404, 'Page non trouvée');
    }
    
    /**
     * Appeler le handler de la route
     */
    private function callHandler($handler, $params) {
        if (is_string($handler)) {
            // Format: "Controller@method"
            list($controllerName, $method) = explode('@', $handler);
            
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            
            if (!file_exists($controllerFile)) {
                http_response_code(500);
                $this->renderError(500, "Contrôleur '{$controllerName}' non trouvé");
                return;
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerName)) {
                http_response_code(500);
                $this->renderError(500, "Classe '{$controllerName}' non trouvée");
                return;
            }
            
            $controller = new $controllerName();
            
            if (!method_exists($controller, $method)) {
                http_response_code(500);
                $this->renderError(500, "Méthode '{$method}' non trouvée dans '{$controllerName}'");
                return;
            }
            
            return call_user_func_array([$controller, $method], $params);
        }
        
        // Closure ou callable direct
        return call_user_func_array($handler, $params);
    }
    
    /**
     * Afficher une page d'erreur
     */
    private function renderError($code, $message) {
        http_response_code($code);
        require_once __DIR__ . '/../views/errors/404.php';
    }
    
    /**
     * Générer une URL à partir d'une route
     */
    public function generate($path, $params = []) {
        $url = $this->basePath . $path;
        
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', urlencode($value), $url);
        }
        
        return $url;
    }
}
