<?php

#EXTENSIÓN DE CLASES: Los objetos pueden ser extendidos, y pueden heredar propiedades y métodos. Para definir una clase como extensión, debo definir una clase padre, y se utiliza dentro de una clase hija.

/*
	Autor: Luis Angel Torres Grimaldo, estudiante de Ingeniería en Tecnologías de la Información de la Universidad Politécnica de Victoria.
	
	- Las funciones tienen una gran similitud entre sí y regularmente retornan un valor boleano para saber si se realizó la consulta con éxito, un arreglo con los valores de una fila en específico o todos los registros de una tabla, lo que vendría siendo un arreglo de arreglos.

	- Se utiliza un objeto PDO para realizar la conexión a la bd, este estándar permite conectar el proyecto con cualquier tipo de gestor de bases de datos.
		
*/

######################## ACERCA DE LA CONEXIÓN A LA BASE DE DATOS CON PDO #####################################
#Aquí se usan las funciones php que consultan a la bd por medio de la clase PDO, esta particularmente al hacer las consultas tiene 3 funciones principales, las cuales son:
/*
	* prepare(): Retorna un objeto tipo consulta al cual llamaremos "query", a su vez este objeto cuenta con 2 funciones más
	* query->bindParam(): bindParam es una especie de apuntado hacia la sentencia anteriormente realizada, este paso se puede omitir si la sentencia fue escrita con comillas dobles ya que en php esto permite agregar variables directamente dentro de esta cadena.
	* query->execute(): Con esta función se ejecuta la función hacia el servidor de BD y retorna true o false dependiendo si se realizó la consulta satisfactoriamente o no respectivamente.

	- Otras 2 funciones extra son fetch y fetchAll, estas retornan una sola fila de una consulta o todas las filas de una consulta respectivamente en forma de un arreglo asociativo e indexado, para ver sus valores es recomendable utilizar la función var_dump($valor) que proporciona PHP

*/

require_once "conexion.php";

class Datos extends Conexion{

	public function insert_alumno($datos_consulta){
		$query = Conexion::conectar()->prepare("INSERT INTO alumnos(matricula,id_persona)
			VALUES('$datos_consulta[matricula]','$datos_consulta[id_persona]')");
		$ret = $query->execute();
		return $ret;
	}

	public function insert_maestro($datos_consulta){
		$query = Conexion::conectar()->prepare("INSERT INTO maestros(numero_empleado,id_persona)
			VALUES('$datos_consulta[numero_empleado]','$datos_consulta[id_persona]')");
		$ret = $query->execute();
		return $ret;
	}

	public function insert_persona($datos_consulta){
		$query = Conexion::conectar()->prepare("INSERT INTO personas(nombres,paterno,materno,telefono,correo)VALUES('$datos_consulta[nombres]','$datos_consulta[paterno]','$datos_consulta[materno]','$datos_consulta[telefono]','$datos_consulta[correo]')");
		$r = $query->execute();
		if($r){
			$query = Conexion::conectar()->prepare("SELECT * FROM personas");
			$query->execute();
			$ret = $query->fetchAll();
			
			return $ret[count($ret)-1]['id'];
		}else
			return false;
	}

	public function insert_carrera($datos_consulta){
		$query = Conexion::conectar()->prepare("INSERT INTO carreras(nombre,siglas)VALUES('$datos_consulta[nombre]','$datos_consulta[siglas]')");
		$ret = $query->execute();
		return $ret;
	}

	public function insert_materia($datos_consulta){
		$query = Conexion::conectar()->prepare("INSERT INTO materias(clave,nombre,id_maestro,id_carrera)
			VALUES('$datos_consulta[clave]','$datos_consulta[nombre]','$datos_consulta[id_maestro]','$datos_consulta[id_carrera]')");
		$ret = $query->execute();
		return $ret;
	}

	public function update_carrera($datos_consulta){
		$query = Conexion::conectar()->prepare("UPDATE carreras SET
					nombre = '$datos_consulta[nombre]',
					siglas = '$datos_consulta[siglas]'
			WHERE id='$datos_consulta[id]'");
		$ret = $query->execute();
		return $ret;
	}

	public function update_maestro($datos_consulta){
		$query = Conexion::conectar()->prepare("UPDATE maestros SET 
				numero_empleado = '$datos_consulta[numero_empleado]'
			WHERE id_persona = '$datos_consulta[id_persona]'");
		$ret = $query->execute();
		if($ret){
			$query = Conexion::conectar()->prepare("UPDATE personas SET
					nombres = '$datos_consulta[nombres]',
					paterno = '$datos_consulta[paterno]',
					materno = '$datos_consulta[materno]',
					telefono = '$datos_consulta[telefono]',
					correo = '$datos_consulta[correo]'
				WHERE id='$datos_consulta[id_persona]'");
			$res = $query->execute();
			return $res;
		}else
			return false;
	}

	public function update_alumno($datos_consulta){
		$query = Conexion::conectar()->prepare("UPDATE alumnos SET 
				matricula = '$datos_consulta[matricula]'
			WHERE id_persona = '$datos_consulta[id_persona]'");
		$ret = $query->execute();
		if($ret){
			$query = Conexion::conectar()->prepare("UPDATE personas SET
					nombres = '$datos_consulta[nombres]',
					paterno = '$datos_consulta[paterno]',
					materno = '$datos_consulta[materno]',
					telefono = '$datos_consulta[telefono]',
					correo = '$datos_consulta[correo]'
				WHERE id='$datos_consulta[id_persona]'");
			$res = $query->execute();
			return $res;
		}else
			return false;
	}

	public function get_all($tabla){
		$query = Conexion::conectar()->prepare("SELECT * FROM $tabla");
		$query->execute();

		return $query->fetchAll();
	}

	public function get_maestros(){
		$query = Conexion::conectar()->prepare("SELECT m.id as id_maestro,
			m.numero_empleado,p.paterno,p.materno,p.nombres
			FROM maestros m INNER JOIN personas p
			ON m.id_persona=p.id");
		$query->execute();
		return $query->fetchAll();
	}	

	public function get_personas($tabla){
		$query = Conexion::conectar()->prepare("SELECT * FROM $tabla t INNER JOIN personas p
			ON t.id_persona=p.id");
		$query->execute();
		return $query->fetchAll();
	}

	public function get_materias(){
		$query = Conexion::conectar()->prepare("SELECT mt.id as id_materia,
				mt.clave,mt.nombre as nombre_materia,
				c.siglas,c.nombre as nombre_carrera,
				p.nombres,p.paterno,p.materno,
				ma.numero_empleado
					FROM materias mt 
			    INNER JOIN maestros ma 
			    ON mt.id_maestro=ma.id 
			    INNER JOIN personas p 
			    ON ma.id_persona=p.id 
			    INNER JOIN carreras c 
				ON c.id=mt.id_carrera");
		$query->execute();
		return $query->fetchAll();
	}

	public function get_persona_by_id($id,$tabla){
		$query = Conexion::conectar()->prepare("SELECT * FROM $tabla t INNER JOIN personas p
			ON t.id_persona=p.id WHERE id_persona=$id");
		$query->execute();
		return $query->fetch();
	}

	public function get_registro_by_id($id,$tabla){
		$query = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE id=$id");
		$query->execute();

		return $query->fetch();
	}

	public function delete_registro($id,$tabla){
		$query = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id=$id");
		$query->execute();
	}


}

?>