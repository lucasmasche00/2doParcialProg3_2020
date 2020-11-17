<?php
namespace App\Models;
use Config\AccesoDatos;
use PDO;

class Inscripcion
{
    public $alumnoId;
    public $materiaId;
    public $fecha;

    public static function Constructor($alumnoId, $materiaId, $fecha)
    {
        $obj = new Inscripcion();
        $obj->alumnoId = $alumnoId;
        $obj->materiaId = $materiaId;
        $obj->fecha = $fecha;
        return $obj;
    }

    //==================== CLASS ============================
    public static function GetInstance($obj)
    {
        return self::Constructor($obj->alumnoId, $obj->materiaId, $obj->fecha);
    }
    public static function ListToInscripcion($lista)
    {
        $listaObj = array();
        foreach ($lista as $value)
        {
            array_push($listaObj, self::GetInstance($value));
        }
        return $listaObj;
    }
    
    public static function FindById($lista, $obj)
    {
        foreach ($lista as $value)
        {
            if($value->alumnoId != null && $value->materiaId != null && isset($obj) && $value->alumnoId === $obj->alumnoId && $value->materiaId == $obj->materiaId)
                return $value;
        }
        return false;
    }

    public static function IsInList($lista, $obj)
    {
        return (!is_null($lista) && count($lista) > 0) ? ((self::FindById($lista, $obj) === false) ? false : true) : false;
    }

    public static function Add($lista, $obj)
    {
        if(!self::IsInList($lista, $obj->alumnoId))
        {
            array_push($lista, $obj);
            return $lista;
        }
        return false;
    }

    public static function Remove($lista, $obj)
    {
        $obj = self::GetInstance($obj);
        if(self::IsInList($lista, $obj->alumnoId))
        {
            foreach ($lista as $key => $value)
            {
                if($value->alumnoId != null && $obj->alumnoId != null && $value->alumnoId === $obj->alumnoId)
                {
                    unset($lista[$key]);
                    return $lista;
                }
            }
        }
        return false;
    }
    
    //==================== DAO ============================
    public static function GetAll()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta('SELECT * FROM inscripciones');
        return $query->execute() ? $query->fetchAll(PDO::FETCH_CLASS, 'App\Models\Inscripcion') : false;
    }

    public function Insert()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $query = $objetoAccesoDato->RetornarConsulta("INSERT INTO inscripciones (alumnoId, materiaId, fecha) VALUES (:alumnoId,:materiaId,:fecha)");
        $query->bindValue(':alumnoId',$this->alumnoId, PDO::PARAM_STR);
        $query->bindValue(':materiaId', $this->materiaId, PDO::PARAM_INT);
        $query->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $query->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }
}
?>