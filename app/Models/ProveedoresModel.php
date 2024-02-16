<?php

namespace Com\Daw2\Models;

class ProveedoresModel extends \Com\Daw2\Core\BaseModel {

    private const SELECT_FROM = "select proveedor.* , aux_tipo_proveedor.nombre_tipo_proveedor , aux_continente.nombre_continente, aux_continente.continente_avisar , proveedor.anho_fundacion  FROM proveedor LEFT JOIN aux_tipo_proveedor ON aux_tipo_proveedor.id_tipo_proveedor = proveedor.id_tipo_proveedor LEFT JOIN aux_continente ON proveedor.id_continente = aux_continente.id_continente";
    private const SELECT_PAG = "select count(*) as total from proveedor p  left join aux_tipo_proveedor atp  on  atp.id_tipo_proveedor = p.id_tipo_proveedor left join aux_continente ac on p.id_continente = ac.id_continente";
    private const NUM_REG = 25;
    function getAll() {
        return $this->pdo->query(self::SELECT_FROM)->fetchAll();
    }

    private function executeQuery(string $query, array $vars): array {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($vars);
        return $stmt->fetchAll();
    }

    function getFiltros(array $filtros): array {
        $query = self::SELECT_FROM;
        $condiciones = [];
        $vars = [];

        if (!empty($filtros['alias'])) {
           
            $condiciones[] = "alias LIKE :alias";

            $vars['alias'] = "%$filtros[alias]%";
        }

        if (!empty($filtros['nombre_completo'])) {
  
            $condiciones[]= "nombre_completo LIKE :nombre_completo";
            $vars['nombre_completo'] = "%$filtros[nombre_completo]%";
        }

        if (!empty($filtros['id_continente']) && filter_var($filtros['id_continente'], FILTER_VALIDATE_INT)) {
            $condiciones[] = "proveedor.id_continente = :id_continente";
            $vars['id_continente'] = $filtros['id_continente'];
        }


        if (!empty($filtros['id_tipo_proveedor']) && is_array($filtros['id_tipo_proveedor'])) {
            $tipos = [];
            $bind = [];
            $i = 1;
            foreach ($filtros['id_tipo_proveedor'] as $id) {
                $key = "id_tipo_proveedor" . $i;
                $tipos[] = ":$key";
                $bind[$key] = $id;
                $i++;
            }
            $condiciones[] = " proveedor.id_tipo_proveedor IN (" . implode(", ", $tipos) . ")";
            $vars = array_merge($vars, $bind);
        }

        if(!empty($filtros['min_anho']) && filter_var($filtros['min_anho'], FILTER_VALIDATE_INT)){
            $condiciones[] = "anho_fundacion >= :min_anho";
            $vars['min_anho'] =  $filtros['min_anho'];
        }
        
        if(!empty($filtros['max_anho']) && filter_var($filtros['max_anho'], FILTER_VALIDATE_INT)){
            $condiciones[] = "anho_fundacion <= :max_anho";
            $vars['max_anho'] =  $filtros['max_anho'];
        }
        
        
        if (!empty($condiciones)) {
            $query .= " where " . implode(' AND ', $condiciones);
          //  var_dump($query);
         //   die;
            return $this->executeQuery($query, $vars);
        } else {
            return $this->pdo->query($query)->fetchAll();
        }
    }

    function getNumRegPages(array $filtros): int {
      $filtrados = $this->getFiltros($filtros);
      
      if(empty($filtrados['condiciones'])){
          $query= self::SELECT_PAG;
          return  $this->pdo->query($query)->fetchColumn();
      }else{
          $query = self::SELECT_PAG . " where " . implode(" AND ", $filtrados);
          $stmt = $this->pdo->prepare($query);
          $stmt->execute($filtrados['vars']);
          return  $stmt->fetchColumn();
      }
      
    }
    
    function getPages(array $filtros) : int{
        if(isset($filtros['page']) && filter_var($fitros['page'], FILTER_VALIDATE_INT) && $filtres['page'] > 0){
            return (int)$fitros['page'];
        }else{
            return 1;
        }
        
    }
    
}

?>
