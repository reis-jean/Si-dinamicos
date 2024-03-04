<?php 
if (PHP_SAPI != 'cli') {
    exit('Rodar via CLI');
}

require __DIR__.'/vendor/autoload.php'; 

// Carregar as configurações
$settings = require __DIR__ . '/src/settings.php';

// Inicializar o contêiner de aplicativo Slim
$app = new \Slim\App($settings);

// Carregar as dependências
$dependencies = require __DIR__ . '/src/dependencies.php';
$dependencies($app);
// Obter o contêiner de aplicativo Slim
$container = $app->getContainer();

$db = $container->get('db');

$schema = $db->schema();

// $tabela = 'user_admins';

// $schema->dropIfExists($tabela); 

// $schema->create($tabela, function($table){
//     $table->increments('id');
//     $table->string('name', 100);
//     $table->string('email')->unique();
//     $table->string('password');
//     $table->timestamps(); 
// });

// $passwordHash = password_hash('1234', PASSWORD_DEFAULT);

// $db->table($tabela)->insert([
//     'name' => 'jean', 
//     'email' => 'jean@n2.com', 
//     'password' => $passwordHash, // Inserir o hash da senha
//     'created_at'=> '2024-03-01',
//     'updated_at'=> '2024-03-01'

// ]); 


// $tabela = 'contracts';

// $schema->dropIfExists($tabela); 

// $schema->create($tabela, function($table){
//     $table->increments('id');
//     $table->string('name', 100);
//     $table->string('email')->unique();
//     $table->string('database')->unique();
//     $table->string('password');
//     $table->timestamps(); 
// });

// $passwordHash = password_hash('1234', PASSWORD_DEFAULT);

// $db->table($tabela)->insert([
//     'name' => 'jean', 
//     'email' => 'jean@n2.com', 
//     'database' => 'system_jean', 
//     'password' => $passwordHash, // Inserir o hash da senha
//     'created_at'=> '2024-03-01',
//     'updated_at'=> '2024-03-01'

// ]); 
