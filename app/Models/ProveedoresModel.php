<?php

namespace Com\Daw2\Models;

class ProveedoresModel extends \Com\Daw2\Core\BaseModel {

    private const SELECT_FROM = "select p.*  , atp.nombre_tipo_proveedor , ac.nombre_continente, ac.continente_avisar , p.anho_fundacion  FROM proveedor p LEFT JOIN aux_tipo_proveedor atp  ON atp.id_tipo_proveedor = p.id_tipo_proveedor LEFT JOIN aux_continente ac ON p.id_continente = ac.id_continente";
    private const SELECT_PAG = "select count(*) as total from proveedor p  left join aux_tipo_proveedor atp  on  atp.id_tipo_proveedor = p.id_tipo_proveedor left join aux_continente ac on p.id_continente = ac.id_continente";
    private const NUM_REG = 25;
    private const ORDER_ARRAY = ['alias', 'nombre_completo', 'nombre_tipo_proveedor', 'nombre_continente', 'anho_fundacion'];

    function getAll() {
        return $this->pdo->query(self::SELECT_FROM)->fetchAll();
    }

    private function executeQuery(string $query, array $vars): array {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($vars);
        return $stmt->fetchAll();
    }

    private function getParameters(array $filtros): array {
        $query = self::SELECT_FROM;
        $condiciones = [];
        $vars = [];

        if (!empty($filtros['alias'])) {

            $condiciones[] = "alias LIKE :alias";

            $vars['alias'] = "%$filtros[alias]%";
        }

        if (!empty($filtros['nombre_completo'])) {

            $condiciones[] = "nombre_completo LIKE :nombre_completo";
            $vars['nombre_completo'] = "%$filtros[nombre_completo]%";
        }

        if (!empty($filtros['id_continente']) && filter_var($filtros['id_continente'], FILTER_VALIDATE_INT)) {
            $condiciones[] = "p.id_continente = :id_continente";
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

        if (!empty($filtros['min_anho']) && filter_var($filtros['min_anho'], FILTER_VALIDATE_INT)) {
            $condiciones[] = "anho_fundacion >= :min_anho";
            $vars['min_anho'] = $filtros['min_anho'];
        }

        if (!empty($filtros['max_anho']) && filter_var($filtros['max_anho'], FILTER_VALIDATE_INT)) {
            $condiciones[] = "anho_fundacion <= :max_anho";
            $vars['max_anho'] = $filtros['max_anho'];
        }

        return [
            'condiciones' => $condiciones,
            'vars' => $vars
        ];
    }

    /* este devuelve los registros como tal */

    function getByFiltros(array $filtros): array {
        $parameters = $this->getParameters($filtros);
        $actualPage = $this->getPages($filtros);
        $registrosInit = ($actualPage - 1) * $_ENV['page.size'];
        $orderBy = $this->getOrder($filtros);
        $orderSentido = $this->getSentido($orderBy);
        $campo= self::ORDER_ARRAY[abs($orderBy)-1];
       
        $query = self::SELECT_FROM;
        if (!empty($parameters['condiciones'])) {

            $query .= " where " . implode(' AND ', $parameters['condiciones']) . " ORDER BY $campo " . $orderSentido . " LIMIT  $registrosInit,   " . $_ENV['page.size'];
            
            return $this->executeQuery($query, $parameters['vars']);
        } else {
            $query .= " ORDER BY $campo " . $orderSentido . " LIMIT  $registrosInit,   " . $_ENV['page.size'];
            //var_dump($query);
            //die;
            return $this->pdo->query($query)->fetchAll();
        }
    }

    /* Recoge el numero los registros que coincidan con nuestro filtros o no */

    function getNumRegPages(array $filtros): int {
        $filtrados = $this->getParameters($filtros);

        if (empty($filtrados['condiciones'])) {
            $query = self::SELECT_PAG;

            return $this->pdo->query($query)->fetchColumn();
        } else {
            $query = self::SELECT_PAG . " where " . implode(" AND ", $filtrados['condiciones']);

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($filtrados['vars']);
            return $stmt->fetchColumn();
        }
    }

    function getPages(array $filtros): int {
        if (isset($filtros['page']) && filter_var($filtros['page'], FILTER_VALIDATE_INT) && $filtros['page'] > 0) {
            return (int) $filtros['page'];
        } else {
            return 1;
        }
    }

    /*abs función que obtine el número absoluto*/
    function getOrder(array $filtros): int {
        if (!isset($filtros['order']) || abs($filtros['order']) < 1 || abs($filtros['order']) > count(self::ORDER_ARRAY)) {
            $order = 1;
        } else {
            $order = (int) $filtros['order'];
        }
        return $order;
    }
    function getSentido(int $order){
        if($order<1){
            return 'desc';
        }else{
            return 'asc';
        }
    }
}
?>
