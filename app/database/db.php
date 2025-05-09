<?php

require ('connection.php');

function logError($error)
{
    $logPath = './error_log.txt';
    $errorMessage = "[" . date("Y-m-d H:i:s") . "] " . $error . "\n";
    file_put_contents($logPath, $errorMessage, FILE_APPEND);
}

function handleError($con_or_stmt)
{
    $error = htmlspecialchars($con_or_stmt->error);
    logError($error);
    header('Location: error_page.php');
    exit;
}

function executeQuery($sql, $data)
{
    global $con;
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . $con->error);
    }

    if (!empty($data)) {
        $values = array_values($data);
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
    }

    $stmt->execute();
    return $stmt;
}

function selectAll($table, $conditions = [])
{
    global $con;
    $sql = "SELECT * FROM $table";

    if (empty($conditions)) {
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $records;
    } else {
        $i = 0;
        foreach ($conditions as $key => $value) {
            if ($i === 0) {
                $sql .= $value === NULL ? " WHERE $key IS NULL" : " WHERE $key=?";
            } else {
                $sql .= $value === NULL ? " AND $key IS NULL" : " AND $key=?";
            }
            $i++;
        }
        $stmt = executeQuery($sql, $conditions);
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $records;
    }
}

function selectOne($table, $conditions = [])
{
    global $con;
    $sql = "SELECT * FROM $table ";

    $i = 0;
    $values = [];
    foreach ($conditions as $key => $value) {
        if ($i === 0) {
            $sql .= $value === NULL ? " WHERE $key IS NULL" : " WHERE $key=?";
        } else {
            $sql .= $value === NULL ? " AND $key IS NULL" : " AND $key=?";
        }
        if ($value !== NULL) {
            $values[] = $value;
        }
        $i++;

    }

    $stmt = executeQuery($sql, $values);
    $record = $stmt->get_result()->fetch_assoc();
    return $record;
}


function create($table, $data)
{
    global $con;
    $sql = "INSERT INTO $table SET ";
    $i = 0;
    $values = [];
    foreach ($data as $key => $value) {
        if ($i === 0) {
            $sql .= "$key=?";
        } else {
            $sql .= ", $key=?";
        }
        $values[] = $value;
        $i++;
    }

    $stmt = executeQuery($sql, $values);
    if ($stmt) {
        return $stmt->insert_id;
    } else {
        return false;
    }
}

function update($table, $id, $data)
{
    global $con;
    $sql = "UPDATE $table SET ";

    $i = 0;
    foreach ($data as $key => $value) {
        if ($i === 0) {
            $sql .= "$key=?";
        } else {
            $sql .= ", $key=?";
        }
        $i++;
    }

    $sql .= " WHERE id=?";
    $data['id'] = $id;
    $stmt = executeQuery($sql, $data);
    return $stmt->affected_rows;
}

function delete($table, $id)
{
    global $con;
    $sql = "DELETE FROM " . $con->real_escape_string($table) . " WHERE id=?";
    $stmt = executeQuery($sql, ['id' => $id]);
    if ($stmt === false) {
        error_log("Delete query failed for table $table: " . $con->error);
        return false;
    }
    return $stmt->affected_rows > 0;
}


?>