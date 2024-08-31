<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use DI\Container;

class BaseTestCase extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        // Call the parent setUp method to ensure any necessary PHPUnit setup is done
        parent::setUp();

        // Criar um novo contêiner e configurar a aplicação Slim
        $container = new Container();
        AppFactory::setContainer($container);

        $app = AppFactory::create();

        // Carregar as dependências
        require __DIR__ . '/../src/Settings/dependencies.php';

        // Carregar as rotas
        require __DIR__ . '/../src/Routes/web.php';

        $this->app = $app;
    }

    // Método para criar requisições facilmente nos testes filhos
    public function request(string $method, string $path, array $queryParams = [])
    {
        $request = (new \Slim\Psr7\Factory\ServerRequestFactory())->createServerRequest($method, $path);

        if (!empty($queryParams)) {
            $request = $request->withQueryParams($queryParams);
        }

        return $this->app->handle($request);
    }
}
