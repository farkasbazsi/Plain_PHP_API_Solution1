<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['users'] == '') {
      $stmt = $pdo->prepare('SELECT id, first_name, last_name, email_address, phone_number  FROM users');
      $stmt->execute();
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      return;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $data = json_decode(file_get_contents('php://input'));

  $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, password, email_address, phone_number) VALUES (?, ?, ?, ?, ?)');
  $stmt->execute([$data->first_name, 
                  $data->last_name, 
                  password_hash($data->password,PASSWORD_BCRYPT), 
                  $data->email_address, 
                  ($data->phone_number != null)?$data->phone_number:null]);

  $id = $pdo->lastInsertId();
  $data->id = $id;

  unset($data->password);

  header("HTTP/1.1 201 Created");
  return;
}