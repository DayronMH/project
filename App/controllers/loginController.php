<?php
require_once '../models/usersModel.php';
require_once '../views/targetPage.php';
require_once '../../scripts/script.php'; 


class loginController {

     /**
     * Constructor for the controller
     * 
     * Starts a session if none exists and checks for incoming POST requests with actions:
     *   - 'login': calls the static `authLogin` method for user login
     *   - 'register': calls the static `routeRegister` method to redirect to the registration page
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] == 'login') {
                self::authLogin();
            } else if ($_POST['action'] == 'register') {
                self::routeRegister();
            }
        }
    }

     /**
     * Handles user login form submission
     *
     * Extracts email and password from the POST request, validates if they are empty,
     * calls the `UsersModel` to retrieve user information by email
     * Verifies the password using `password_verify` and sets session data for successful login
     * Redirects the user to the appropriate dashboard based on their role ('admin' or 'friend')
     */
    public static function authLogin() {
        $userModel = new UsersModel();
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = $userModel->handleLogin($email);

        
        if (empty($email) || empty($password)) {
            setTargetMessage('error', 'Debe llenar los campos de correo y contraseña');
            header('Location: http://mytrees.com/app/views/login.php');
            exit();
        }
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];

                if ($user['role'] === 'admin') {
                    sendTreesNotificationsTest();
                    setTargetMessage('success', 'Bienvenido, Administrador');
                    header('Location: http://mytrees.com/app/views/adminDashboard.php');
                    exit();
                } else if ($user['role'] === 'friend') {
                    setTargetMessage('success', 'Login exitoso');
                    header('Location: http://mytrees.com/app/views/friendDashboard.php');
                    exit();
                }
                else {
                    setTargetMessage('error', 'Rol no válido');
                    header('Location: http://mytrees.com/app/views/login.php');
                    exit();
                }
            } else {
                setTargetMessage('error', 'Contraseña incorrecta');
                header('Location: http://mytrees.com/app/views/login.php');
                exit(); 
            }
        } else {
            setTargetMessage('error', 'Usuario no encontrado');
            header('Location: http://mytrees.com/app/views/login.php');
            exit(); 
        }
    }

     /**
     * Redirects the user to the registration page
     */
    public static function routeRegister() {
        header('Location: http://mytrees.com/app/views/register.php');
        exit();
    }
}

$authController = new loginController();