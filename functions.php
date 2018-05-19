<?php

error_reporting(0);

// Łączenie z bazą danych MySQL

function polaczDB() {

$servername = "localhost";
$dbname = "countries";
$username = "root";
$password = "";


$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

if ($conn->connect_error) {
komunikat("Brak połączenia z bazą danych");
exit();
} else {
return $conn;
}
}

// Zamyka połączenie z bazą danych

function rozlaczDB($conn) {
$conn->close(); 
}


// Wykonuje zapytania do bazy danych

function queryDB($conn,$sql) {


if (strpos($sql, 'INSERT') === 0) {

if ($conn->query($sql) === TRUE) {
    return TRUE;
} else {
    return $conn->error;
}

} else if (strpos($sql, 'SELECT') === 0) {
	
$result = $conn->query($sql);
return $result;
	
} else if (strpos($sql, 'DELETE') === 0) {
	
if ($conn->query($sql) === TRUE) {
    return TRUE;
} else {
    return $conn->error;
}

	

} elseif (strpos($sql, 'UPDATE') === 0) {

if ($conn->query($sql) === TRUE) {
    return TRUE;
} else {
    return $conn->error;
}

} else {
	
	return "Bład";
}


}

// Wyświetla okno z komunikatem

function komunikat($tresc) {
	
echo '<script>

    alert("'.$tresc.'");

</script>';	


}


// Przekierowuje pod inny adres


function przekierowanie($adres) {
	
	header('Location: '.$adres);
	exit;  

}

// Rekursywnie usuwa węzły z drzewa w bazie danych


function usunWezel($conn,$id) {
	
	global $usunieto;

    $query = "SELECT * FROM countries WHERE parent_id='{$id}';";
	$result = queryDB($conn,$query);
    while($row = $result->fetch_assoc()) {
              usunWezel($conn,$row['id']);
	}
	
	    $query = "DELETE FROM countries WHERE id='$id';";
	$result = queryDB($conn,$query);
	if($conn->affected_rows > 0) $usunieto = 1;
}	




?>