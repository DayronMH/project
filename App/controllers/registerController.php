<?php
require_once '../models/usersModel.php';
require_once '../views/targetPage.php';
class RegisterController
{
     /**
     * Constructor for the controller
     * 
     * Handles incoming POST requests and dispatches them to appropriate methods based on the 'action' parameter:
     *   - 'register': calls `registerUser` to register a new user
     *   - 'login': calls `redirectToLogin` to redirect to the login page
     */
    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'register') {
                $this->registerUser();
            } elseif ($_POST['action'] === 'login') {
                $this->redirectToLogin();
            }
        }
    }

     /**
     * Processes user registration form submission
     *
     * Extracts user information from the POST request, validates it, checks for existing email,
     * and calls the `UsersModel` to create a new user. Sets session messages based on success or failure
     * and redirects the user accordingly
     */
    public function registerUser()
    {
        $name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_DEFAULT));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));
        $phone = htmlspecialchars(filter_input(INPUT_POST, 'phone', FILTER_DEFAULT));
        $address = htmlspecialchars(filter_input(INPUT_POST, 'address', FILTER_DEFAULT));
        $country = htmlspecialchars(filter_input(INPUT_POST, 'country', FILTER_DEFAULT));

        if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($country)) {
            setTargetMessage('error', 'Todos los campos son obligatorios');
            exit();
        }
        $userModel = new UsersModel();
        $existingUser = $userModel->handleLogin($email);
        if ($existingUser) {
            setTargetMessage('error', 'El usuario con este correo ya existe.');
            
            exit();
        }

        $isCreated = $userModel->createFriendUser($name, $email, $password, $phone, $address, $country);

        if ($isCreated) {
            setTargetMessage('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
            header('Location: http://mytrees.com/app/views/login.php');
        } else {
            setTargetMessage('error', 'Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        exit();
    }

     /**
     * Redirects the user to the login page
     */
    public function redirectToLogin()
    {
        header('Location: http://mytrees.com/app/views/login.php');
        exit();
    }  
}
$registerController = new RegisterController();
