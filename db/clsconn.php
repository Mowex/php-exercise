<?php
/**
 *  DB - Clase para Base de Datos usando PDO 
 *
 * @author      A. Cedano
 * @git         https://github.com/padrecedano/PHP-PDO
 * @version      1.1
 *               En la versión 1.1 se suprime la escritura de errores en un log particular
 *               Escribiéndolos en el error_log por defecto de php junto con los demás mensajes de error
 */
class Conn
{
    # @object, Objeto PDO 
    private $pdo;
    
    # @object, Consulta preparada PDO  
    private $sSQL;
    
    # @strings, credenciales de conexión
    /*
        * Colocar los valores propios de conexión
    */

    // dev
    private $host="localhost";
    private $usr="homestead";
    private $pwd="secret";
    private $dbname="habitat";
        
    # @bool ,  Si conectado a la BD
    private $isConnected = false;
    
    # @array, Parámetros de la consulta SQL
    private $parametros;
    
    # @array, Array de Querys a ejecutar
    private $array_sql;
    
    /**
     *   Constructor por defecto 
     *
     *  1. Instancia la clase Log.
     *  2. Conecta a la base de datos.
     *  3. Crea la matriz (array) con los parámetros.
     */
    public function __construct()
    {
        $this->Connect();
        $this->parametros = array();
        $this->array_sql = array();
        // $this->rollBack();
    }
    
    /**
     *  Este método realiza la conexión a la BD.
     *  
     *  1. Lee las credenciales de la BD desde un archivo .ini. 
     *  2. Coloca el contenido del archivo ini en un arreglo (credenciales).
     *  3. Intenta conectarse a la BD.
     *  4. Si la conexión falla, despliega una excepción y escribe el mensaje de error en el archivo log creado.
     */
    private function Connect()
    {
        // $dsn = 'pgsql:dbname=' . $this->dbname . ';host=' . $this->host . '';
        $pwd = $this->pwd;
        $usr = $this->usr;
        $dsn = "mysql:host=$this->host;dbname=$this->dbname;user=$this->usr;password=$this->pwd";
    /**
     *  El array $options es muy importante para tener un PDO bien configurado
     *  
     *  1. PDO::ATTR_PERSISTENT => false: sirve para usar conexiones persistentes
     *      se puede establecer a true si se quiere usar este tipo de conexión. Ver: https://es.stackoverflow.com/a/50097/29967 
     *      En la práctica, el uso de conexiones persistentes fue problemático en algunos casos
     *  2. PDO::ATTR_EMULATE_PREPARES => false: Se usa para desactivar emulación de consultas preparadas 
     *      forzando el uso real de consultas preparadas. 
     *      Es muy importante establecerlo a false para prevenir Inyección SQL. Ver: https://es.stackoverflow.com/a/53280/29967
     *  3. PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION También muy importante para un correcto manejo de las excepciones. 
     *      Si no se usa bien, cuando hay algún error este se podría escribir en el log revelando datos como la contraseña !!!
     *  4. PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'": establece el juego de caracteres a utf8, 
     *      evitando caracteres extraños en pantalla. Ver: https://es.stackoverflow.com/a/59510/29967
     */
        $options = array(
            PDO::ATTR_PERSISTENT => false, 
            PDO::ATTR_EMULATE_PREPARES => false, 
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
        );
        try {
            # Intentar la conexión 
            $this->pdo = new PDO($dsn, $usr, $pwd, $options);

            # Conexión exitosa, asignar true a la variable booleana isConnected.
            $this->isConnected = true;
        }
        catch (PDOException $e) {
            # Escribir posibles excepciones en el error_log
            die($this->error = $e->getMessage());
        }
    }

    /*
     *   Este método cierra la conexión
     *   No es obligatorio, ya que PHP la cierra cuando termina el script
     *   Ver: http://es.stackoverflow.com/questions/50083/50097#50097
     */
    public function closeConnection()
    {
        # Setea el objeto PDO a null para cerrar la conexion
        # http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null;
    }
    
    /**
     *  Método que será usado para enviar cualquier consulta a la BD.
     *  
     *  1. Si no hay conexión, conectar a la BD.
     *  2. Preparar la consulta.
     *  3. Parametrizar la consulta.
     *  4. Ejecutar la consulta.    
     *  5. Si ocurre una excepción: Escribirla en el archivo log junto con la consulta.
     *  6. Resetear los parámetros.
     */
    private function Init($sql, $parametros = "")
    {
        # Conecta a la BD
        if (!$this->isConnected) {
            $this->Connect();
        }
        try {

            # Preparar la consulta
            $this->sSQL = $this->pdo->prepare($sql);
            
            # Agregar parámetros a la matriz de parámetros  
            $this->bindMas($parametros);
            
            # Asignar parámetros
            if (!empty($this->parametros)) {
                foreach ($this->parametros as $param => $value) {
                    if(is_int($value[1])) {
                        $type = PDO::PARAM_INT;
                    } else if(is_bool($value[1])) {
                        $type = PDO::PARAM_BOOL;
                    } else if(is_null($value[1])) {
                        $type = PDO::PARAM_NULL;
                    } else {
                        $type = PDO::PARAM_STR;
                    }
                    // Añade el tipo cuando asigna los valores a la columna 
                    $this->sSQL->bindValue($value[0], $value[1], $type);
                }
            }
            
            # Ejecuta la consulta SQL 
            $this->sSQL->execute();
        }
        catch (PDOException $e) {
            $this->errorData($e->getMessage(), $sql, $this->array_sql);
            // echo $e->getMessage().' -> '.$sql;
            // die();
        }
        
        # Resetea los parámetros
        $this->parametros = array();
    }
    
    /**
     *  @void 
     *
     *  Agrega un parámetro al arreglo de parámetros
     *  @param string $parametro  
     *  @param string $valor 
     */
    public function bind($parametro, $valor)
    {
        $this->parametros[sizeof($this->parametros)] = [":" . $parametro , $valor];
    }
    /**
     *  @void
     *  
     *  Agrega más parámetros al arreglo de parámetros
     *  @param array $parray
     */
    public function bindMas($parray)
    {
        if (empty($this->parametros) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }
    /**
     *  Si la consulta SQL contiene un SELECT o SHOW, devolverá un arreglo conteniendo todas las filas del resultado
     *     Nota: Si se requieren otros tipos de resultados la clase puede modificarse, 
     *           agregandolos o se pueden crear otros métodos que devuelvan los resultados como los necesitemos
     *           en nuesta aplicación. Para tipos de resultados ver: http://php.net/manual/es/pdostatement.fetch.php 
     *  Si la consulta SQL es un DELETE, INSERT o UPDATE, retornará el número de filas afectadas
     *
     *  @param  string $sql
     *  @param  array  $params
     *  @param  int    $fetchmode
     *  @return mixed
     */
    public function query($sql, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $sql = trim(str_replace("\r", " ", $sql));
        
        $this->Init($sql, $params);
        
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $sql));
        
        # Determina el tipo de SQL 
        $statement = strtolower($rawStatement[0]);
        
        if ($statement === 'select' || $statement === 'show') {
            return $this->sSQL->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sSQL->rowCount();
        } else {
            return NULL;
        }
    }

    /**
     *  Devuelve un arreglo que representa una columna específica del resultado 
     *
     *  @param  string $sql
     *  @param  array  $params
     *  @return array
     */

    public function column($sql, $params = null)
    {
        $this->Init($sql, $params);
        $Columns = $this->sSQL->fetchAll(PDO::FETCH_NUM);
        
        $column = null;
        
        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }
        
        return $column;
        
    }
    /**
     *  Devuelve un arreglo que representa una fila del resultado
     *
     *  @param  string $sql
     *  @param  array  $params
     *  @param  int    $fetchmode
     *  @return array
     */
    public function single_row($sql, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($sql, $params);
        $result = $this->sSQL->fetch($fetchmode);
        $this->sSQL->closeCursor(); // Libera la conexión para evitar algún conflicto con otra solicitud al servidor
        return $result;
    }
    /**
     *  Devuelve un valor simple campo o columna
     *
     *  @param  string $sql
     *  @param  array  $params
     *  @return string
     */
    public function scalar($sql, $params = null)
    {
        $this->Init($sql, $params);
        $result = $this->sSQL->fetchColumn();
        $this->sSQL->closeCursor(); // Libera la conexión para evitar algún conflicto con otra solicitud al servidor
        return $result;
    }
    
    /**
     *  Devuelve el último id insertado.
     *  @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Inicia una transacción
     * @return boolean, true si la transacción fue exitosa, false si hubo algún fallo
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     *  Ejecuta una transacciónn
     *  @return boolean, true si la transacción fue exitosa, false si hubo algún fallo
     */
    public function setCommit()
    {
        return $this->pdo->commit();
    }
    
    /**
     *  Rollback de una transacción
     *  @return boolean, true si la transacción fue exitosa, false si hubo algún fallo
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     *  Detiene la ejecución de los scripts y muestra los errores
     */
    public function errorData($error, $sql, $querys)
    {
        $info =  '<font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="-1"><strong><br>POSTGRES</strong><br></font>
        <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="-1">'.$error.'<br></font>
        <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="-2">'.$sql.'<br></font>
        <br>';
        foreach($querys as $query){
            if(is_array($query)) $info .= '<font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="-2">'.$this->sql_debug($query[0], $query[1]).'<br></font>';
            else $info .= '<font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="-2">'.$this->sql_debug($query).'<br></font>';
        }
        echo $info;
        $this->rollBack();
        $this->closeConnection();
        die();
    }

    /**
     *  Ejecuta las consultas que se encuentran en el arreglo principal ($this->array_sql)
     *  @return boolean
     */
    public function set_data()
    {
        $this->beginTransaction();
        foreach($this->array_sql as $sql){
            if(is_array($sql)) $this->Init($sql[0], $sql[1]);
            else $this->Init($sql);
        }
        $this->setCommit();
        return true;
    }

    /**
     *  Agrega los querys que se van a ejecutar al arreglo general 
     */
    public function addSQL($sql)
    {
        array_push($this->array_sql, $sql);
    }

    /**
     *  Muestra los querys que se van a ejecutar
     */
    public function debugSQL()
    {
        for ($i=0; $i < count($this->array_sql) ; $i++) { 
            if(is_array($this->array_sql[$i])) echo '<font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="-1">'.$this->sql_debug($this->array_sql[$i][0], $this->array_sql[$i][1]).';</font><br>';
            else echo '<font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="-1">'.$this->sql_debug($this->array_sql[$i]).';</font><br>';
        }
        die();
    }

    /**
     *  Reemplaza los parametros de la consulta por las variables que se prentenden agregar mediante el bind
     *  NOTA: Para que esto funcione lo mas recomendable es que el nombre de la columna, el parametro a remplazar y la 
     *  variable dentro del array asociativo coincidan en nombre... 
     *  EJEMPLO (fijarse en la variable USERNAME):
     *  $sql = INSERT INTO local.user(id, USERNAME*, password) values(3, :USERNAME*, :password)"; 
     *  $conn->addSQL([$sql, ['USERNAME*' => $username, 'password' => $password] ]);
     */
    public function sql_debug($sql_string, array $params = null) 
    {
        if (!empty($params)) {
            $indexed = $params == array_values($params);
            foreach($params as $k=>$v) {
                if (is_object($v)) {
                    if ($v instanceof \DateTime) $v = $v->format('Y-m-d H:i:s');
                    else continue;
                }
                elseif (is_string($v)) $v="'$v'";
                elseif ($v === null) $v='NULL';
                elseif (is_array($v)) $v = implode(',', $v);

                if ($indexed) {
                    $sql_string = preg_replace('/\?/', $v, $sql_string, 1);
                }
                else {
                    if ($k[0] != ':') $k = ':'.$k; //add leading colon if it was left out
                    $sql_string = str_replace($k,$v,$sql_string);
                }
            }
        }
        return $sql_string;
    }
    
}

?>
