<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Com\Daw2\Models;
class AuxTipoModel extends \Com\Daw2\Core\BaseModel {
   
    function getAllTipos(){
        $query = "select * from aux_tipo_proveedor atp";
        return $this->pdo->query($query)->fetchAll();
    }
    
}
