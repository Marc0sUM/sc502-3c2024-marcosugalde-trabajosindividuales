<?php
session_start();
require 'db.php';

function login($username, $password){
    try{
        global $pdo;        
        $sql = "SELECT * FROM users where username = :username";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(['username' => $username]);
        //$user va a ser un arreglo asociativo con los datos de usuario;
        $user = $stmt -> fetch(PDO::FETCH_ASSOC);
        if($user){
            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user["id"];
                return true;
            }
        }
        return false;
    }catch(Exception $e){
        logError($e -> getMessage());   
        return false;
    }
}

//Guardar el método HTTP
$method = $_SERVER['REQUEST_METHOD'];
if($method == 'POST'){
    //Obtener los datos del request
    if(isset($_POST['email']) && isset($_POST['password'])){
        $username = $_POST['email'];
        $password = $_POST['password'];
        if(login($username, $password)){
            http_response_code(200);
            echo json_encode(["message" => "Login exitoso"]);
        }else{
            http_response_code(401);
            echo json_encode(["error" => "Usuario o password incorrecto"]);
        }
    }else{
        http_response_code(400);
        echo json_encode(["error" => "Email y password son requeridos"]);
    }   
}else{
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
}