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
        
        
        $clientDbConfig = [
            'driver' => 'mysql',
            'host' => 'Localhost',
            'dbname' => $database, 
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];
    
        
        try {
            $clientDb = new PDO("mysql:host={$clientDbConfig['host']};dbname={$clientDbConfig['dbname']}",
                                $clientDbConfig['username'], $clientDbConfig['password']);
            $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
           
            $query = $clientDb->query("SELECT * FROM produtos");
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
    
          
            $clientDb = null;
    
  
            return $response->withJson($products);
        } catch (PDOException $e) {
            
            return $response->withStatus(500)->withJson(['error' => 'Erro ao recuperar produtos: ' . $e->getMessage()]);
        }
    });

    $this->post('/produtoscadastrar', function($request, $response) {
        $data = $request->getParsedBody();
        
       
        if (!isset($data['titulo'], $data['descricao'], $data['preco'], $data['fabricante'])) {
            return $response->withStatus(400)->withJson(['error' => 'Todos os campos são obrigatórios']);
        }
    
        
        $database = $_SESSION['user']['database'];
        
        // pega o banco de dados do clienteee

        $clientDbConfig = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'dbname' => $database, 
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];
    
       
        try {
            $clientDb = new PDO("mysql:host={$clientDbConfig['host']};dbname={$clientDbConfig['dbname']}",
                                $clientDbConfig['username'], $clientDbConfig['password']);
            $clientDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $stmt = $clientDb->prepare("INSERT INTO produtos (titulo, descricao, preco, fabricante) VALUES (:titulo, :descricao, :preco, :fabricante)");
            $stmt->bindParam(':titulo', $data['titulo']);
            $stmt->bindParam(':descricao', $data['descricao']);
            $stmt->bindParam(':preco', $data['preco']);
            $stmt->bindParam(':fabricante', $data['fabricante']);
            $stmt->execute();
    
            
            $clientDb = null;
    
            return $response->withJson(['message' => 'Produto cadastrado com sucesso']);
        } catch (PDOException $e) {
            
            return $response->withStatus(500)->withJson(['error' => 'Erro ao cadastrar produto: ' . $e->getMessage()]);
        }
        
    });
    
    
    $this->get('/nomecliente', function ($request, $response) {
        
        $clientId = $_SESSION['user']['id'];
    
        $contract = Contract::find($clientId);
    
        if ($contract) {
            
            return $response->withJson(['nome_cliente' => $contract->name]);
        } else {
            
            return $response->withStatus(404)->withJson(['error' => 'Cliente não encontrado']);
        }
    });
    

    






    
});