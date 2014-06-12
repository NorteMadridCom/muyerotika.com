<?php

class Pasafoto
{
	/***********************************************************************
	Las posibles salidas son:
		<div id="orbit"> 
			<div id="featured"> 	
				<a href="http://www.nortemadrid.com/"><img src="./pasafoto/img1.jpg" /></a>
				<img src="pasafoto/img2.jpg" data-caption="#texto_anuncio" />
				<img src="pasafoto/img3.jpg"  />
			</div>
		</div>
		
	Requiere ./orbit/ -> donde se ponen las imagenes del orbit
	
	Requiere ./js/jquery*
	
	Requiere orbit*.css -> estilos
	
	En el editor del pasafotos es necesario un orden y un determinado nombre para evitar BBDD
	- imagenes -> img_xx.ext (son las imágenes y pueden ser de cualquier extension)
	- link -> url_xx.txt (son las cadenas de text que contienen la URL completa)
	- captions -> caption_xxx.txt (son cadenas de texto con los titulos)
	
	Se puede ver la posibilidad de cambiar los patrones (img_,url_,caption_ y las extensiones)
	
	Para insertar copiar y pegar el código:
	
			$pasafoto = new Pasafoto();
			$pasafoto->mostrar();
			
			
	*************************************************************************/
	
	
	public $dir = "./pasafoto/"; //posibilidad de meter en el config
	
	protected $_imagenes = array();
	

	public function __construct()
	{

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		if (is_dir($this->dir)) {
			if ($gd = opendir($this->dir)) {
				while ($archivo = readdir($gd)) {
    				if(strpos(finfo_file($finfo, $this->dir.$archivo),'image')!==false) {
    					$nombre_archivo = explode('.', $archivo, -1); //quito lo último que es la extension, debe quedar solo img_xx
    					$numero_archivo = explode('_', $nombre_archivo[0], 2); //ponemos el numero
    					$i=$numero_archivo[1];
    					$this->_imagenes[$i]['nombre']=$archivo;
						//buscamos url y caption
    					if(file_exists($this->dir.'url_'.$i.'.txt')) $this->_imagenes[$i]['url']=file_get_contents($this->dir.'url_'.$i.'.txt');
    					if(is_file($this->dir.'caption_'.$i.'.txt')) $this->_imagenes[$i]['caption']=file_get_contents($this->dir.'caption_'.$i.'.txt');
    				}
				}
				closedir($gd);
			}
		}
		finfo_close($finfo);
		
		ksort($this->_imagenes);
		
	}
	
	
	public function mostrar()
	{
		/*
		if(is_array($this->_imagenes) && count($this->_imagenes)>0) {
			echo '
			<div id="orbit"> 
				<div id="featured"> 	
			';
			//asort($this->_imagenes);
			foreach($this->_imagenes as $imagen) {
				if($imagen['caption']) $caption = ' caption="'.$imagen['caption'].'" ';
				$foto = '<img border="0" src="'.$this->dir.$imagen['nombre'].'"'.$caption.' />';
				if($imagen['url']) $foto = '<a href="'.$imagen['url'].'">'.$foto.'</a>'; 
				echo $foto;			
			}
			echo '
				</div>
			</div>
			';
		}
		*/
		echo '
		<!-- ORBIT  -->
					
					<div id="recuadro_orbit">
					
						<div id="orbit"> 
							<img src="pasafoto/img1.jpg" data-caption="#texto_anuncio1" />
							<img src="pasafoto/img2.jpg" data-caption="#texto_anuncio2" />
							<img src="pasafoto/img3.jpg" data-caption="#texto_anuncio3" />
							
							<!-- TXT ORBIT -->
							<span class="orbit-caption" id="texto_anuncio1">
								<a href="#" class="orbit">
									Descubre los nuevos estampados. </br> 
								</a>
							</span>
							
							<span class="orbit-caption" id="texto_anuncio2">
								<a href="#" class="orbit">
									Vibradores a todo color.  </br> 
								</a>
							</span>
							
							<span class="orbit-caption" id="texto_anuncio3">
								<a href="#" class="orbit">
									Afrodisiacos y aceites corporales.  </br> 
								</a>
							</span>
							
							
						</div>	
					</div> 
					<!-- FIN ORBIT -->
		';
	}
	
}

class Editar_pasafoto extends Pasafoto
{
	
	public function listado_fotos()
	{

		if(is_array($this->_imagenes) && count($this->_imagenes)>0) {
			echo '
				<form action="" method="post" enctype="multipart/form-data"  class="form_listado">
					<p>Las imágenes han de ser de 950x250 px o de esa proporción pero superior tamaño.</p>
					<p>Las imagenes se ordenan por el orden de subida.</p>
			';	
			//ksort($this->_imagenes);
			//var_dump($this->_imagenes);
			foreach($this->_imagenes as $i=>$imagen) {
				echo '
					<p><h3>Imagen Nº'.$i.'</h3>
		 				<img src="'.$this->dir.$imagen['nombre'].'" width="600" /> <br>
		 				Para sustituir la imagen:<br>
		 				<input type="hidden" name="archivo_'.$i.'" value="'.$imagen['nombre'].'" />
		 				<input type="file" name="img_'.$i.'" /> o marque para eliminar: <input type="checkbox" name="borrar_'.$i.'" value="'.$i.'" /><br>
		 				Leyenda: <input type="text" name="caption_'.$i.'" value="'.$imagen['caption'].'" /><br>
		 				Vínculo: <input type="url" name="url_'.$i.'" value="'.$imagen['url'].'"/><br>
		 			</p>
		 		';
			}
			for($j=++$i;$j<$i+3;$j++) {
				echo '
					<p><h3>Imagen Nº'.$j.'</h3>
		 				<input type="file" name="img_'.$j.'" /><br>
		 				Leyenda: <input type="text" name="caption_'.$j.'" value="'.$_POST['caption_'.$j].'" /><br>
		 				Vínculo: <input type="url" name="url_'.$j.'" value="'.$_POST['url_'.$j].'"/><br>
		 			</p>
		 		';
			}
			echo '
					<input type="hidden" name="ultimo" value="'. ++$j .'" />
					<input type="submit" name="accion" value="Subir" /> <input type="submit" name="eliminar" value="Eliminar todas" />
				</form>
			';
		}
	}
	
	public function eliminar_todas()
	{
		if(is_array($this->_imagenes) && count($this->_imagenes)>0) {
			foreach($this->_imagenes as $imagen) {
				if(file_exists($this->dir.$imagen['nombre'])) unlink($this->dir.$imagen['nombre']);
				if(file_exists($this->dir.$imagen['url'])) unlink($this->dir.$imagen['url']);
				if(file_exists($this->dir.$imagen['caption'])) unlink($this->dir.$imagen['caption']);
			}
		}
	}
	
	public function subir_imagenes() 
	{
		
		foreach($_POST as $clave => $valor){
			if(strpos($clave,'borrar')!==false) {
				echo $_POST['archivo_'.$valor];
				if(file_exists($this->dir.$_POST['archivo_'.$valor])) unlink($this->dir.$_POST['archivo_'.$valor]);
				if(file_exists($this->dir.'url_'.$valor.'.txt')) unlink($this->dir.'url_'.$valor.'.txt');
				if(file_exists($this->dir.'caption_'.$valor.'.txt')) unlink($this->dir.'caption_'.$valor.'.txt');
				unset($this->_imagenes[$valor]);
			}
		}
		//var_dump($_FILES);
		for($i=1;$i<=$_POST['ultimo'];$i++) {
			if($_FILES['img_'.$i]['name']!='') $imagen = new Subir_imagen($_FILES['img_'.$i], $this->dir, "img_".$i, 250, 950);
			$partes = explode('.', $_FILES['img_'.$i]['name']);
			$ext = $partes[count($partes)-1];
			$this->_imagenes[$i]['nombre'] = "img_$i.$ext";
			if($_FILES['img_'.$i]['name']!='' || $_POST['archivo_'.$i]) {
				if($_POST['url_'.$i]) $this->_escribir_txt('url',$i);
				if($_POST['caption_'.$i]) $this->_escribir_txt('caption',$i);
			}	
		}
		
		$this->_renombrar_imagenes();
		
		echo '<p>Acción realizada exitosamente.</p>';
		
	}

	private function _escribir_txt($tipo,$i) 
	{
		$archivo = fopen($this->dir.$tipo.'_'.$i.'.txt', 'w');
		fwrite($archivo, $_POST[$tipo.'_'.$i]);
		fclose($archivo);
	}
	
	private function _renombrar_imagenes() 
	{
		unset($this->_imagenes);
		parent::__construct();
		$i=1;
		foreach($this->_imagenes as $no=>$imagen) {
			$partes = explode('.', $imagen['nombre']);
			$ext = $partes[count($partes)-1];
			if(file_exists($this->dir.$imagen['nombre'])) rename($this->dir.$imagen['nombre'], $this->dir.'img_'.$i.'.'.$ext);
			if(file_exists($this->dir.'caption_'.$no.'.txt')) rename($this->dir.'caption_'.$no.'.txt', $this->dir.'caption_'.$i.'.txt');
			if(file_exists($this->dir.'url_'.$no.'.txt')) rename($this->dir.'url_'.$no.'.txt', $this->dir.'url_'.$i.'.txt');
			$i++;
		}
	
	}
	
	
}
