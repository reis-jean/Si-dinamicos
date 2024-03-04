<?php

use App\Models\Contract;
use App\Models\UserAdmin;
use Slim\Http\Request;
use Slim\Http\Response;



$app->group('/loginclients', function(){

    


    $this->post('/auth', function($request, $response){
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];
    
        $user = Contract::where('email', $email)->first();
    
        if (!$user || !password_verify($password, $user->password)) {
            return $response->withStatus(401)->withJson(['error' => 'Credenciais inválidas']);
        }
        
        // Define os dados do usuário na sessão
        $_SESSION['user'] = [
            'id' => $user->id,
            'database' => $user->database 
        ];
    
        return $response->withJson(['message' => 'Login bem-sucedido']);
    });


    $this->get('/products', function($request, $response) {
        $database = $_SESSION['user']['database'];
        
        // Configurações do banco de dados do cliente
        $clientDbConfig = [
            'driver' => 'mysql',
            'host' => 'Localhost',
            'dbname' => $database, // Nome do banco de dados específico do cliente
            'username' => 'reisje33_jv',
            'password' => 'lanadelrey24',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];
    
        // Conexão com o banco de dados do cliente
        try {
            $clientDb = new PDO("mysql:host={$clientDbConfig['host']};dbname={$clientDbConfig['dbname']}",
                                $clientDbConfig['username'], $clientDbConfig['password']);
            $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Consulta para recuperar os produtos
            $query = $clientDb->query("SELECT * FROM produtos");
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
    
            // Fechar a conexão com o banco de dados do cliente
            $clientDb = null;
    
            // Retornar os produtos como JSON
            return $response->withJson($products);
        } catch (PDOException $e) {
            // Se ocorrer algum erro na conexão ou na consulta, retorna uma resposta de erro
            return $response->withStatus(500)->withJson(['error' => 'Erro ao recuperar produtos: ' . $e->getMessage()]);
        }
    });

    $this->post('/produtoscadastrar', function($request, $response) {
        $data = $request->getParsedBody();
        
        // Verificar se todos os campos necessários foram fornecidos
        if (!isset($data['titulo'], $data['descricao'], $data['preco'], $data['fabricante'])) {
            return $response->withStatus(400)->withJson(['error' => 'Todos os campos são obrigatórios']);
        }
    
        // Recuperar o nome do banco de dados do cliente da sessão
        $database = $_SESSION['user']['database'];
        
        // Configurações do banco de dados do cliente
        $clientDbConfig = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => $database, // Nome do banco de dados específico do cliente
            'username' => 'reisje33_jv',
            'password' => 'lanadelrey24',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];
    
        // Conexão com o banco de dados do cliente
        try {
            $clientDb = new PDO("mysql:host={$clientDbConfig['host']};dbname={$clientDbConfig['dbname']}",
                                $clientDbConfig['username'], $clientDbConfig['password']);
            $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            // Preparar a consulta para inserir o novo produto
            $stmt = $clientDb->prepare("INSERT INTO produtos (titulo, descricao, preco, fabricante) VALUES (:titulo, :descricao, :preco, :fabricante)");
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descricao', $data['descricao']);
            $stmt->bindParam(':preco', $data['preco']);
            $stmt->bindParam(':fabricante', $data['fabricante']);
            $stmt->execute();
    
            // Fechar a conexão com o banco de dados do cliente
            $clientDb = null;
    
            // Retornar uma mensagem de sucesso
            return $response->withJson(['message' => 'Produto cadastrado com sucesso']);
        } catch (PDOException $e) {
            // Se ocorrer algum erro na conexão ou na consulta, retorna uma resposta de erro
            return $response->withStatus(500)->withJson(['error' => 'Erro ao cadastrar produto: ' . $e->getMessage()]);
        }
        
    });
    
    
    $this->get('/nomecliente', function ($request, $response) {
        
        $clientId = $_SESSION['user']['id'];
    
        // Consulta o banco de dados para recuperar o nome do cliente com base no ID
        $contract = Contract::find($clientId);
    
        // Verifica se o contrato foi encontrado
        if ($contract) {
            // Retorna o nome do cliente como resposta JSON
            return $response->withJson(['nome_cliente' => $contract->name]);
        } else {
            // Se o contrato não for encontrado, retorna uma mensagem de erro
            return $response->withStatus(404)->withJson(['error' => 'Cliente não encontrado']);
        }
    });
    

    






    
});