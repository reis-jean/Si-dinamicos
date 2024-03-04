<?php

use App\Models\Contract;
use App\Models\UserAdmin;
use Slim\Http\Request;
use Slim\Http\Response;



$app->group('/loginadmin', function(){

    $this->get('/admins', function($request, $response){
        $admins =  UserAdmin::get();
        return $response->withJson($admins); 
    });


    $this->get('/clients', function($request, $response){
        $clients =  Contract::get();
        return $response->withJson($clients); 
    });

    $this->post('/auth', function($request, $response){
        
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];

        $user = UserAdmin::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            return $response->withStatus(401)->withJson(['error' => 'Credenciais inválidas']);
        }
        
        $_SESSION['user'] = $user->id;
        return $response->withJson(['message' => 'Login bem-sucedido']);
    });

   


    $this->post('/register', function($request, $response){
        
        $data = $request->getParsedBody();
    
    // Verifica se o email já está cadastrado no banco de dados
    $existingUser = UserAdmin::where('email', $data['email'])->first();
    
    // Se o email já existe, retorna uma mensagem de erro
    if ($existingUser) {
        return $response->withStatus(400)->withJson(['error' => 'O email já está cadastrado']);
    }

    // Hash da senha usando bcrypt
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Adiciona o hash da senha aos dados do contrato
        $data['password'] = $hashedPassword;
    

    // Cria o usuário no banco de dados
    $user = UserAdmin::create($data);

    return $response->withJson(['message' => 'Cadastrado com sucesso']);
    });



    $this->post('/newcontract', function($request, $response){
        
        $data = $request->getParsedBody();
        
       // Hash da senha usando bcrypt
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Adiciona o hash da senha aos dados do contrato
        $data['password'] = $hashedPassword;
    
        // Verifica se o email já está em uso
        $existingContract = Contract::where('email', $data['email'])->first();
        if ($existingContract) {
            return $response->withStatus(400)->withJson(['error' => 'Email já cadastrado']);
        }
    
        // Verifica se o nome do banco de dados já está em uso
        $existingDatabase = Contract::where('database', $data['database'])->first();
        if ($existingDatabase) {
            return $response->withStatus(400)->withJson(['error' => 'Nome do banco de dados já cadastrado']);
        }
    
        // Cria o contrato no banco de dados
        $contract = Contract::create($data);
    
        // Criação do banco de dados para o cliente
        $databaseName = 'reisje33_' . $data['database']; 
        $mainDBConnection = new PDO('mysql:host=Localhost;dbname=reisje33_slim', 'reisje33_jv', 'lanadelrey24');
    
        if ($mainDBConnection) {
        
            try {
                $mainDBConnection->query("CREATE DATABASE $databaseName");
    
                // Conectar-se ao banco de dados do cliente
                $clientDBConnection = new PDO("mysql:host=Localhost;dbname=$databaseName", 'reisje33_jv', 'lanadelrey24');
                
                // Cria a tabela de produtos no banco de dados do cliente
                $clientDBConnection->query("
                    CREATE TABLE produtos (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        titulo VARCHAR(100),
                        descricao TEXT,
                        preco DECIMAL(11, 2),
                        fabricante VARCHAR(60),
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                
                // Fechar a conexão com o banco de dados do cliente
                $clientDBConnection = null;
                
                // Fechar a conexão com o banco de dados principal
                $mainDBConnection = null;
                
                // Retornar uma resposta de sucesso
                return $response->withJson(['message' => 'Contrato e banco de dados criados com sucesso']);
            } catch (\Exception $e) {
                // Imprimir mensagem de erro
                return $response->withJson(['error' => 'Erro ao criar tabela de produtos: ' . $e->getMessage()]); 
            }
        } else {
            // Se houver um erro na conexão, retornar uma resposta de erro
            return $response->withStatus(500)->withJson(['error' => 'Erro ao conectar ao banco de dados principal']);
        }
       
    });


    
});