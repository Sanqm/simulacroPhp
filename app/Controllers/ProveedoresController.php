<?php
declare(strict_types = 1);


namespace Com\Daw2\Controllers;


class ProveedoresController  extends \Com\Daw2\Core\BaseController {

    public function proveedoresView() {
        
        $model = new  \Com\Daw2\Models\ProveedoresModel();
        $modelauxtipo = new \Com\Daw2\Models\AuxTipoModel();
        $modelauxContinente = new \Com\Daw2\Models\AuxContinenteModel();
        $data = array(
            'titulo' => 'Proveedores',
            'breadcrumb' => ['Inicio', 'Proveedores'],
            'seccion' => '/proveedores',
            'proveedores' => $model->getFiltros($_GET),
            'tipos' => $modelauxtipo->getAllTipos(),
            'continentes' =>$modelauxContinente->getAllContinentes(),
            'input' => filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS)
        );
        $this->view->showViews(array('templates/header.view.php', 'proveedores.view.php',  'templates/footer.view.php'), $data);
    }
    
    
    
}
