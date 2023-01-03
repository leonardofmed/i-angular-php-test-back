<?php
require_once __DIR__."/../config.php";

class Connection {
    protected $mysqli;

    // Check if the connection is active. Otherwise connect to the database.
    public function __construct() {
        if (!$this->mysqli || !$this->mysqli->ping()) {
            $this->mysqli = mysqli_connect(HOST, USER, PWD, DB);
        }
    }
    
}

// TODO default connection
class DefaultRequest extends Connection {

    public function insert() {        
        // $sqlAdd = "INSERT INTO projects (projectId, name, area, date_string, user, date_modified, zoom, team_id, client_id) VALUES (?, ?, ST_PolyFromText(?), ?, ?, ?, ?, ?, ?)";
        // $typesAdd = "isssssiii";
        // $valuesAdd = [$projectId, $projectName, $coordinates, $date, $user, $date, $zoom, $teamId, $clientId];
        // $this->query($sqlAdd, $types, $values);
    }

    private function query($sql, $types, $arrayOfValues) {
        $stmt = $this->mysqli->prepare($sql);
        if ( false === $stmt ) {
            echo json_encode(array(
                'message' => htmlspecialchars($this->mysqli->error),
                "code" => "0",
                "line" => __LINE__
            ));
            exit();
        }
        if (!$stmt->bind_param($types, ...$arrayOfValues)) {
            echo json_encode(array(
                'message' => 'Binding error: ' . htmlspecialchars($stmt->error),
                "code" => "0",
                "line" => __LINE__
            ));
            exit();
        }
        if (!$stmt->execute()) {
            echo json_encode(array(
                'message' => htmlspecialchars($stmt->error),
                "code" => "0",
                "line" => __LINE__
            ));
            exit();
        }
        $stmt->close(); 
    }
}

class Client extends Connection {
    public string $uid, $nome, $cpf, $endereco, $email, $nascimento, $image;

    public function upsert(Client $client) {
        $exist = $this->select($client->uid);
        if ($exist && $exist->num_rows > 0) {
            // Update
            return $this->update($client);
        } else {
            // Insert
            return $this->insert($client);
        }
    }

    public function insert(Client $client) {
        $sql = $this->mysqli->query("INSERT INTO clients (uid, nome, cpf, endereco, email, nascimento, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssssss", ...[$client->uid, $client->nome, $client->cpf, $client->endereco, $client->email, $client->nascimento, $client->image]);
        $stmt->execute();
        $stmt->close();
    }

    public function update(Client $client) {
        $sql = "UPDATE clients SET nome = ?, cpf = ?, endereco = ?, email = ?, nascimento = ?, image = ? WHERE uid = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssssss", ...[$client->nome, $client->cpf, $client->endereco, $client->email, $client->nascimento, $client->image, $client->uid]);
        $stmt->execute();
        $stmt->close();
    }

    public function select(?string $uid) {
        $sql = $uid ? "SELECT * FROM clients WHERE uid = ?" : "SELECT * FROM clients";        
        $stmt = $this->mysqli->prepare($sql);
        if ($uid) $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function remove(string $uid) {        
        $sql = "DELETE FROM clients WHERE uid = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();
    }
}

class Product extends Connection {
    public string $uid, $nome, $valor, $image;

    public function upsert(Product $product) {
        $exist = $this->select($product->uid);
        if ($exist && mysqli_num_rows($exist) > 0) {
            // Update
            $this->update($product);
        } else {
            // Insert
            $this->insert($product);
        }
    }

    public function insert(Product $product) {
        $sql = $this->mysqli->query("INSERT INTO products (uid, nome, valor, image) VALUES (?, ?, ?, ?)");
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssss", ...[$product->uid, $product->nome, $product->valor, $product->image]);
        $stmt->execute();
        $stmt->close();
    }

    public function update(Product $product) {
        $sql = "UPDATE products SET nome = ?, valor = ?, image = ? WHERE uid = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssssss", ...[$product->nome, $product->valor, $product->image, $product->uid]);
        $stmt->execute();
        $stmt->close();
    }

    public function select(?string $uid) {
        $sql = $uid ? "SELECT * FROM products WHERE uid = ?" : "SELECT * FROM products";        
        $stmt = $this->mysqli->prepare($sql);
        if ($uid) $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function remove(string $uid) {        
        $sql = "DELETE FROM products WHERE uid = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->close();
    }
}

class Sale extends Connection {
    public string $uid, $data, $user_uid, $products_uids, $total;

    public function insert(Sale $sale) {
        $sql = $this->mysqli->query("INSERT INTO sales (uid, data, user_uid, products_uids, total) VALUES (?, ?, ?, ?, ?)");
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssss", ...[$sale->uid, $sale->data, $sale->user_uid, $sale->products_uids, $sale->total]);
        $stmt->execute();
        $stmt->close();
    }

    public function select() {
        $sql = "SELECT * FROM sales";        
        $stmt = $this->mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}