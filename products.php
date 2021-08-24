<?php

// DB Connect

$servename = "127.0.0.1";

$username = "root";

$password = "";

$port = "3306";

$db = "db_store_test";


$con = new mysqli($servename, $username, $password, $db, $port);

if ($con->connect_errno) {
    die("Error DB!");
}

// CRUD

// get product by id

if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {

    $id = $_GET['id'];
    
    $data = get_product($con, $id);

    if (empty($data)) {
        error('product not found !');
    }

    $msg = ['result' => '0', 'message' => '', 'data' => $data];

    echo json_encode($msg); 
}

if (isset($_GET['all'])) {
    
    $data = get_all_products($con);

    if (empty($data) || count($data) == 0) {
        error('products not found !');
    }

    // $data['img'] = get_host().'/img/'.$data['img'];

    $msg = ['result' => '0', 'message' => '', 'data' => $data];

    echo json_encode($msg); 
}

if (!isset($_POST['action'])) exit; // Exit when no action param in request post

switch ($_POST['action']) {

    case 'insert' :
        $name =         isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : error('error name'); 
        $description =  isset($_POST['description']) && !empty($_POST['description']) ? $_POST['description'] : error('error description'); 
        $price =        isset($_POST['price']) && !empty($_POST['price']) && is_numeric($_POST['price']) ? $_POST['price'] : error('error price'); 
        $img =          isset($_POST['img']) && !empty($_POST['img']) ? $_POST['img'] : error('error img'); 
        $quantity =     isset($_POST['quantity']) && !empty($_POST['quantity']) && is_numeric($_POST['quantity']) ? $_POST['quantity'] : error('error quantity'); 

        $result = insert_product($con, $name, $description, $price, $img, $quantity);

        if ($result == 0) {
            $msg = ['result' => '0', 'message' => 'Product Added successfully'];
        } else {
            $msg = ['result' => '1', 'message' => 'Error: Product Not Added !'];
        }

        echo json_encode($msg);

        break; // Insert

    case 'update' :
        $id =         isset($_POST['id']) && !empty($_POST['id']) && is_numeric($_POST['id']) ? $_POST['id'] : error('error id'); 
        $name =         isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : error('error name'); 
        $description =  isset($_POST['description']) && !empty($_POST['description']) ? $_POST['description'] : error('error description'); 
        $price =        isset($_POST['price']) && !empty($_POST['price']) && is_numeric($_POST['price']) ? $_POST['price'] : error('error price'); 
        $img =          isset($_POST['img']) && !empty($_POST['img']) ? $_POST['img'] : error('error img'); 
        $quantity =     isset($_POST['quantity']) && !empty($_POST['quantity']) && is_numeric($_POST['quantity']) ? $_POST['quantity'] : error('error quantity'); 

        $result = update_product($con, $id, $name, $description, $price, $img, $quantity);

        if ($result == 0) {
            $msg = ['result' => '0', 'message' => 'Product Updated successfully'];
        } else {
            $msg = ['result' => '1', 'message' => 'Error: Product Not Updated !'];
        }

        echo json_encode($msg);

        break; // update

    case 'delete' :
        $id =         isset($_POST['id']) && !empty($_POST['id']) && is_numeric($_POST['id']) ? $_POST['id'] : error('error id'); 

        $result = delete_product($con, $id);
        
        if ($result == 0) {
            $msg = ['result' => '0', 'message' => 'Product Deleted successfully'];
        } else {
            $msg = ['result' => '1', 'message' => 'Error: Product Not Deleted !'];
        }

        echo json_encode($msg);

        break; // delete

}


///////////////// Function Helper ////////////////////////////////////////

function insert_product($con, $name, $description, $price, $img, $quantity)
{
    $error = 0;

    $stmt = $con->prepare("INSERT INTO products (name, description, price, img, quantity) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("sssss", $name, $description, $price, $img, $quantity);

    if (!$stmt->execute()) {
        $error = 1;
    }

    $stmt->close();

    return $error;
}

function update_product($con,$id, $name, $description, $price, $img, $quantity)
{
    $error = 0;

    $stmt = $con->prepare("UPDATE products SET name = ?, description = ?, price = ?, img = ?, quantity = ? WHERE id = ?");

    $stmt->bind_param("ssssss", $name, $description, $price, $img, $quantity, $id);

    if (!$stmt->execute()) {
        $error = 1;
    }

    $stmt->close();

    return $error;
}

function delete_product($con,$id)
{
    $error = 0;

    $stmt = $con->prepare("DELETE FROM products WHERE id = ?");

    $stmt->bind_param("s", $id);

    if (!$stmt->execute()) {
        $error = 1;
    }

    $stmt->close();

    return $error;
}

function get_product($con,$id)
{
    $error = 0;

    $data = array();

    $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");

    $stmt->bind_param("s", $id);

    if (!$stmt->execute()) {
        $error = 1;
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()){

        $data = product_ressource($row);
        
    }

    $stmt->close();

    return $data;
}

function get_all_products($con)
{
    $error = 0;

    $products = array();

    $stmt = $con->prepare("SELECT * FROM products");

    if (!$stmt->execute()) {
        $error = 1;
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()){

        array_push($products, product_ressource($row));

    }

    $stmt->close();

    return $products;
}


function error($msg) // function show message json
{
    $result = ['result' => '1', 'message' => $msg];

    die(json_encode($result));
}

function get_host() // function show message json
{
    return $_SERVER['HTTP_HOST'];
}

function product_ressource($row)
{
    $data = array();

    $data['id'] = $row['id'];
    $data['name'] = $row['name'];
    $data['description'] = $row['description'];
    $data['price'] = $row['price'];
    $data['img'] = 'http://' . get_host().'/storetest/img/'.$row['img'];
    $data['quantity'] = $row['quantity'];

    return $data;
}










