<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Com\Daw2\Models;

/**
 * Description of AuxContinenteModel
 *
 * @author Sandra Queimadelos 
 */
class AuxContinenteModel extends \Com\Daw2\Core\BaseModel {
    
    function getAllContinentes(){
    $query = "select * from aux_continente ac";
    
    return $this->pdo->query($query)->fetchAll();
    }
}
