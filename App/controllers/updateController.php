<?php
session_start();
require_once "../models/usersModel.php";
require_once "../models/treesModel.php";
require_once "../models/speciesModel.php";

class UpdateController {

     /**
     * Processes an update request from a form submission
     *
     * This function handles incoming POST requests and updates the specified model instance
     * It extracts the model name and ID from the POST data, creates an instance of the corresponding model,
     * and calls the `update` method on the model with the updated data
     */
    public function processUpdate() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $model = $_POST['model'] ?? '';
            $id = $_POST['id'] ?? '';
            
            if (empty($model) || empty($id)) {
                $_SESSION['error'] = "Datos inválidos";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
            
            $className = ucfirst($model) . 'Model';
            $modelInstance = new $className();
            unset($_POST['model'], $_POST['id']);
            
            $result = $modelInstance->update($id, $_POST);
            
            if ($result) {
                $_SESSION['success'] = "Actualización exitosa";
            } else {
                $_SESSION['error'] = "Error en la actualización";
            }
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}

$controller = new UpdateController();
$controller->processUpdate();
