<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->prepare('SELECT id, parcel_number, size, user_id FROM parcels WHERE id = ?');
    $stmt->execute([$_GET['parcels']]);
    $data = (array) $stmt->fetch(PDO::FETCH_ASSOC);
    
    $user = $pdo->prepare('SELECT id, first_name, last_name, email_address, phone_number FROM users WHERE id = ?');
    $user->execute([intval($_GET['parcels'])]);
    
    $data['user'] = $user->fetch(PDO::FETCH_ASSOC);

    $data = (object) $data;
    unset($data->user_id);
    
    header("HTTP/1.1 200 OK");

    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $input = json_decode(file_get_contents('php://input'));

  if(in_array($input->size,['S','M','L','XL']) == false){
    header("HTTP/1.1 400 Bad Request");
    exit;
  }

  do{
    //GENERATING PARCEL NUMBER
    $parcel_number = implode( array_map( function() { return dechex( mt_rand( 0, 15 ) ); }, array_fill( 0, 10, null ) ) );
    
    //CHECK IF PARCEL NUMBER EXISTS
    $stmt = $pdo->prepare("SELECT * FROM parcels WHERE parcel_number = :parcel_id");
    $stmt->bindParam(':parcel_id', $parcel_number, PDO::PARAM_STR);
    $stmt->execute();
  }while($stmt->rowCount() > 0);
  
  //NEW RECORD
  $stmtINSERT= $pdo->prepare("INSERT INTO parcels (parcel_number, size, user_id) VALUES (?,?,?)");
  $stmtINSERT->execute([$parcel_number, $input->size, $input->user_id]);

  //FETCHING NEW RECORD
  $stmt = $pdo->prepare("SELECT * FROM parcels WHERE parcel_number = :id");
  $stmt->bindParam(':id', $parcel_number, PDO::PARAM_STR);
  $stmt->execute();
  $data = (array) $stmt->fetch(PDO::FETCH_ASSOC);

  //FETCH USER
  $user = $pdo->prepare('SELECT id, first_name, last_name, email_address, phone_number FROM users WHERE id = ?');
  $user->execute([intval($data['user_id'])]);
  $data['user'] = $user->fetch(PDO::FETCH_ASSOC);

  $data = (object) $data;
  unset($data->user_id);

  header("HTTP/1.1 201 Created");

  return;
}