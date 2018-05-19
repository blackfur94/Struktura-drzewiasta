
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "countries";

$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Błąd połączenia: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Nie udalo sie nawiązać połączenia: %s\n", mysqli_connect_error());
    exit();
}
$sql = "SELECT * FROM `countries` ";
$res = mysqli_query($conn, $sql) or die("Błąd bazy danych:". mysqli_error($conn));
	//iterate on results row and create new index array of data
	while( $row = mysqli_fetch_assoc($res) ) { 
		$data[] = $row;
	}
	$itemsByReference = array();

// Build array of item references:
foreach($data as $key => &$item) {
   $itemsByReference[$item['id']] = &$item;
   // Children array:
   $itemsByReference[$item['id']]['children'] = array();
   // Empty data class (so that json_encode adds "data: {}" ) 
   $itemsByReference[$item['id']]['data'] = new StdClass();
}

// Set items as children of the relevant parent item.
foreach($data as $key => &$item)
   if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
      $itemsByReference [$item['parent_id']]['children'][] = &$item;

// Remove items that were added to parents elsewhere:
foreach($data as $key => &$item) {
   if($item['parent_id'] && isset($itemsByReference[$item['parent_id']]))
      unset($data[$key]);
}
// Encode:
echo json_encode($data);
?>
