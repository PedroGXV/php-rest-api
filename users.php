<?php

    $user = new UsersController(
        new PDO("mysql:host=localhost;dbname=api", "root", "")
    );

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            print_r($user->index());
            break;
        case 'POST':
            print_r($user->create());
            break;

        case 'PUT':
            print_r($user->update());
            break;

        case 'DELETE':
            print_r($user->delete());
            break;

        default: break;
    }

    class UsersController
    {
        private readonly PDO $con;

        function __construct($con) {
            $this->con = $con;
        }

        function index(): string
        {
            $rs = $this->con->query("SELECT * FROM users");
            $result = $rs->fetchAll();

            return json_encode($result);
        }
    
        function create(): string
        {
            try {
                $rs = $this->con->prepare("INSERT INTO users(nome, email, senha) VALUES(?, ?, ?)");
                $rs->bindParam(1, $_GET['nome']);
                $rs->bindParam(2, $_GET['email']);
                $rs->bindParam(3, $_GET['senha']);
                $rs->execute();
    
                return "Usuário " . $_GET["nome"]. " adicionado com sucesso!";
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    
        function update(): string
        {
            try {
                $query = "UPDATE users SET ";

                QueryHelper::mountParamsQuery($query);
                $query .= 'WHERE id = ' . $_GET['id'];
                QueryHelper::removeComma($query);

                $rs = $this->con->query($query);
    
                return "Usuário " . $_GET['nome'] . " atualizado com sucesso em " . $_GET['id'] . "!";
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    
        function delete(): string
        {
            try {
                $query = "DELETE FROM users WHERE ";
                QueryHelper::mountParamsQuery($query);
                QueryHelper::removeComma($query);                
            
                $rs = $this->con->query($query);
    
                return "Usuário " . $_GET['id'] . " deletado com sucesso!";
            } catch (Exception $e) {
                return ($e->getMessage());
            }
        }
    }

    class QueryHelper
    {
        // metodos com passagem de parametros por referencia
        static public function mountParamsQuery(&$query): void
        {
            if (array_key_exists('nome', $_GET)) {
                $query .= "nome = \"{$_GET['nome']}\", ";
            }
            if (array_key_exists('email', $_GET)) {
                $query .= "email = \"{$_GET['email']}\", ";
            }
            if (array_key_exists('senha', $_GET)) {
                $query .= "senha = \"{$_GET['senha']}\", ";
            }
            if (array_key_exists('id', $_GET)) {
                $query .= "id = \"{$_GET['id']}\", ";
            }
        }

        static public function removeComma(&$query): void
        {
            $query = str_replace(', WHERE', ' WHERE', $query);
            if (substr($query, -2, 2) == ', ') {
                $query = substr_replace($query, '', -2, 2);
            }
        }
    }

?>