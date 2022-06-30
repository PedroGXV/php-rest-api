<?php

    define('DB_NAME', "BD221041058");
    
    $atividade = new AtividadeController(
        new PDO("mysql:host=localhost;dbname=" . DB_NAME, "root", "")
    );

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            print_r($atividade->index());
            break;
        case 'POST':
            print_r($atividade->create());
            break;

        case 'PUT':
            print_r($atividade->update());
            break;

        case 'DELETE':
            print_r($atividade->delete());
            break;

        default: break;
    }

    // Controllers :Start
    class AtividadeController
    {
        private readonly PDO $con;
        private String $tableName = "tb221041058";

        function __construct($con) {
            $this->con = $con;
        }

        function index(): String
        {
            $query = "SELECT * FROM {$this->tableName}";
            // Filtro ->
            if ( array_key_exists('nome', $_REQUEST) ) {
                $query .= " WHERE nome LIKE \"%{$_REQUEST['nome']}%\"";
            }
            // <- Filtro

            $rs = $this->con->query($query);
            $result = $rs->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode($result);
        }
    
        function create(): String
        {
            try {
                $rs = $this->con->prepare("INSERT INTO {$this->tableName}(codigo, nome, turma, nota) VALUES(?, ?, ?, ?)");
                $rs->bindParam(1, $_GET['codigo']);
                $rs->bindParam(2, $_GET['nome']);
                $rs->bindParam(3, $_GET['turma']);
                $rs->bindParam(4, $_GET['nota']);
                $rs->execute();
    
                return "Registro " . $_GET["codigo"]. " adicionado com sucesso!";
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    
        function update(): String
        {
            try {
                $query = "UPDATE {$this->tableName} SET ";

                QueryHelper::mountParamsQuery($query);
                $query .= 'WHERE codigo = ' . $_GET['codigo'];
                QueryHelper::removeComma($query);

                $rs = $this->con->query($query);
    
                return "Registro " . $_GET['codigo'] . " atualizado com sucesso!";
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    
        function delete(): String
        {
            try {
                $query = "DELETE FROM {$this->tableName} WHERE ";
                QueryHelper::mountParamsQuery($query);
                QueryHelper::removeComma($query);                
            
                $rs = $this->con->query($query);
    
                return "Registro " . $_GET['codigo'] . " deletado com sucesso!";
            } catch (Exception $e) {
                return ($e->getMessage());
            }
        }
    }
    // Controllers :END

    // Helpers :Start
    // Classe de ajuda
    class QueryHelper
    {
        // metodos com passagem de parametros por referencia
        // metodo utilizado para fazer bind de parametros de forma automatica para metodo UPDATE
        static public function mountParamsQuery(&$query): void
        {
            if (array_key_exists('nome', $_GET)) {
                $query .= "nome = \"{$_GET['nome']}\", ";
            }
            if (array_key_exists('turma', $_GET)) {
                $query .= "turma = \"{$_GET['turma']}\", ";
            }
            if (array_key_exists('nota', $_GET)) {
                $query .= "nota = \"{$_GET['nota']}\", ";
            }
            if (array_key_exists('codigo', $_GET)) {
                $query .= "codigo = \"{$_GET['codigo']}\", ";
            }
        }

        // metodo utilizado para retirar vírgulas no final da query, evitando erros SQL
        static public function removeComma(&$query): void
        {
            $query = str_replace(', WHERE', ' WHERE', $query);
            if (substr($query, -2, 2) == ', ') {
                $query = substr_replace($query, '', -2, 2);
            }
        }
    }
    // Helpers :END
?>