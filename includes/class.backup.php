<?php

require_once 'class.mysqldump.php';
//require 'configuraciones.php'; //archivo que contiene datos de acceso a la base de datos
//$copia = new Backup(__DIR__,array()); //ES MUY IMPORTANTE LLAMARLO CON __DIR__, EL ARRAY PUEDE SER CONFIG O VACIO EN ESTE CASO
//desde el $this->formulario_backup se hace un control. pero no tiene formato, es una tabla simple

/*************************************************************************************
* Requiere del archivo de configuraciones.php, colocado en el raiz, con los siguientes datos:
* define('BD', 'base_datos');
* define('USUARIO', 'usuario_db');
* define('PASS', 'contraseña_bd');
* define('SERVIDOR', 'localhost');
*
* Es importante que se llame al objeto desde el lugar que se quiere hacer la copia con el 
* parámetro __DIR__ para que lo ejecute en su lugar
* 
* Se pueden llamar a las funciones de forma independiente, leer y crear para diversos usos,
* pero el control ppal se hace desde la funcion pública formulario_backup
* Se puede hacer un include de un fomulario/tabla externa que tenga un mejor formato
* creando dicha dependencia aqui
********************************************************************************************/




class Backup extends ZipArchive
{
	private $_dir;
	private $_backup_dir;
	private $_backup_file;
	private $_backup_file_pre;
	private $_archivo_bbdd;
	private $_biblioteca = 3;
	
	public function __construct($carpeta,$config=array(),$bbdd=true)
	{
		$this->_dir=$carpeta;
		$this->_bbdd=$bbdd;//exite el error de solo crear la copia de una sola BBDD
		$this->_backup_dir=$carpeta.'/backups';
		if($config->conf['backup_dir']) $this->_backup_dir = $carpeta.'/'.$config->conf['backup_dir'];
		if(!is_dir($this->_backup_dir)) mkdir($this->_backup_dir, 0644);
		
		$this->_backup_file_pre = "backup_";
		if($config->conf['backup_file']) $this->_backup_file_pre = $config->conf['backup_file'];
		
		$this->_backup_file = $this->_backup_file_pre . date("y_m_d_H_i") . ".zip";
	}
	
	public function crear_backup() 
	{		
		if($this->_bbdd) $this->_backup_mysql();		
		
		$this->open($this->_backup_dir."/".$this->_backup_file, parent::CREATE);
		$this->_add_carpeta_completa_zip($this->_dir,$this->_dir);
		$this->close();
		
		if($this->_bbdd) unlink($this->_archivo_bbdd);			
	}
	
	private function _add_carpeta_completa_zip($dir, $base)
	{
	    $newFolder = str_replace($base, '', $dir);
	    $this->addEmptyDir($newFolder);
	    foreach(glob($dir . '/*') as $file)
	    {
	        if(is_dir($file) && !strstr($file,$this->_backup_dir)) {
	        	self::_add_carpeta_completa_zip($file, $base);
	        	//echo "$file<br>";
	        } elseif(!is_dir($file)) {
	            $newFile = str_replace($base, '', $file);
	            $this->addFile($file, $newFile);
	            //echo "$file<br>";
	        }
	    }
	}
	
	private function _backup_mysql() 
	{
		require 'configuraciones.php';

		$this->_archivo_bbdd = BD.'.sql';
		
   		$dump = new MySQLDump(BD, USUARIO, PASS, SERVIDOR);
   		$dump->start($this->_archivo_bbdd);			
	}
	
	public function leer_backup() 
	{
		echo '<table><tr><th>Fecha</th><th>Tama&#241;o</th><th>Acciones</th></tr>';
		foreach (glob($this->_backup_dir."/".$this->_backup_file_pre."*.zip") as $nombre_archivo) {
			$archivo=str_replace($this->_dir, '.', $nombre_archivo);
    		echo '<tr><td>' . date("d/m/Y H:i",fileatime($nombre_archivo)) .'</td><td>'. round(filesize($nombre_archivo)/1024/1024,2) . "MB</td><td>";
    		echo '
    				<form action="" method="post" enctype="multipart/form-data">
    					<button type="submit" name="descargar" value="'.$archivo.'">Descargar</button>
    					<button type="submit" name="eliminar" value="'.$archivo.'">Eliminar</button>
    				</form>
    		';
    		echo '</td></tr>';
		}
		echo '<tr><td></td><td></td><td><form action="" method="post" enctype="multipart/form-data">
    					<button type="submit" name="accion" value="nuevo">Crear Nuevo</button>
    				</form></td></table>
    	';
	}
	
	public function formulario_backup() 
	{
		if($_POST['descargar']) echo '<p>Pinche <a href="'.$_POST['descargar'].'">AQU&#205;</a> para iniciar la descarga.</p>';
		elseif($_POST['eliminar']) {
			if(unlink($_POST['eliminar'])) echo "<p>El archivo de ha eliminado del alojamiento.</p>";
			else echo "<p>Exite un error y no se ha podido realizar la solicitud de eliminación. P&#243;ngase en contacto con el administrador del sistema.</p>";
		} elseif($_POST['accion']) {
			$this->crear_backup();
			if(is_file($this->_backup_dir."/".$this->_backup_file)) echo "<p>El archivo se ha creado. Recuerde que ocupa espacio en el alojamiento.</p>";
			else echo "<p>Exite un error y no se ha podido realizar el backup. P&#243;ngase en contacto con el administrador del sistema.</p>";
		}
		$this->leer_backup();
	}
	
}
 
