<?php 
    require __DIR__ . "/../db/conn.php";

    $stmt= $conn->prepare("SELECT * FROM products");
    if(!$stmt){
        error_log("Prepare failed:" . $conn->error);
        http_response_code(500);
        exit("Server error");
    }

    if(!$stmt->execute()){
        error_log("Execute failed: " . $stmt->error);
        http_response_code(500);
        exit("Server error");
    }

    $result = $stmt->get_result();
    $products = [];
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
    $stmt->close();



    $encoded_data = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if($encoded_data == false){
        error_log("JSON encoding failed: " . json_last_error_msg());
        http_response_code(500);
        exit("Server error");
    }

    $file = __DIR__ . "/../api/products.json";
    if(file_put_contents($file, $encoded_data, LOCK_EX) === false){
        error_log("Could not write to $file");
        http_response_code(500);
        exit("Server error");
    }
?>