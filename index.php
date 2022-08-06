<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");


include 'dbConnect.php';

$objDb = new DbConnect;
$con = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case "GET":
        $sql = 'SELECT * FROM simpledata';
        $data =  explode('/', $_SERVER['REQUEST_URI']);
        if(isset($data[3]) && is_numeric($data[3])){
            $sql .= " WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $data[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        }else {
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($users);

    break;

    case "POST":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "INSERT INTO simpledata(id, name, email, phone, password) VALUES(null, :name, :email, :phone, :pass)";
        $stmt = $con->prepare($sql);
        // $stmt->bindParam(':id', $user[0]->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':phone', $user->phone);
        $stmt->bindParam(':pass', $user->password);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }
        echo json_encode($response);
    break;

    case "PUT":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "UPDATE simpledata SET name=:name, email=:email, phone=:phone, password=:pass where id=:id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':phone', $user->phone);
        $stmt->bindParam(':pass', $user->password);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($response);
    break;

    case "DELETE":
        $sql = "DELETE FROM simpledata WHERE id=:id";
        $data =  explode('/', $_SERVER['REQUEST_URI']);
        
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $data[3]);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);


    break;


}


?>