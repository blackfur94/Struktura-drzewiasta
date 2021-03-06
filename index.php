<?php

require_once("functions.php");

// Przenoszenie węzła (zamiana rodzica)

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'zmien_rodzica')
{
	
if(isset($_POST['id_wezla']) && isset($_POST['nowy_rodzic']) && $_POST['id_wezla'] != null) {
	
	
$id_nowego_rodzica = $_POST['nowy_rodzic'];
$id_wezla = $_POST['id_wezla'];

if($id_wezla == 1) {
	$message = "Nie można przenieść korzenia do nowego węzła";	
} else {
	
	$conn = polaczDB();

$query = "SELECT id, parent_id FROM {$nazwa_tabeli} WHERE id = '{$id_wezla}';";

$result = queryDB($conn,$query);



if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {

$id_rodzica_wezla = $row['parent_id'];
if($id_wezla == $id_nowego_rodzica) {
	$message = "Wybrano ten sam węzeł";
} elseif($id_rodzica_wezla == $id_nowego_rodzica) {
	
$message = "Węzeł jest już dzieckiem tego rodzica";	
	
} else {
	
	
// Sprawdza czy węzeł nie jest przenoszony do niższego poziomu w tej samej gałęzi
	
$query = "SELECT GROUP_CONCAT(lv SEPARATOR ',') AS potomkowie FROM (SELECT @pv:=(SELECT GROUP_CONCAT(id SEPARATOR ',')".
" FROM countries WHERE parent_id IN (@pv)) AS lv FROM countries".
" JOIN (SELECT @pv:={$id_wezla})tmp WHERE parent_id IN (@pv)) a;";

$result = queryDB($conn,$query);
$ok = 1;
if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) { 
	break;
	}
	
	$potomkowie = $row['potomkowie'];

$potomkowie_tablica = explode(",", $potomkowie);

if (in_array($id_nowego_rodzica, $potomkowie_tablica)) {
$message = "Nie można przenieść węzła do niższego poziomu w tej samej gałęzi";
} else {
	
	// Zamienia w bazie danych wartość pola 'parent_id' wybranego węzła na ID nowego rodzica.
	
$query = "UPDATE {$nazwa_tabeli} SET parent_id = '{$id_nowego_rodzica}' WHERE id = '{$id_wezla}';";

$result = queryDB($conn,$query);
if(mysqli_affected_rows($conn) > 0) {
$message = "Węzeł został przeniesiony";	
} else {
$message = "Wystąpił błąd podczas przenoszenia węzła";	
	
}	
	
}

	
	
	
} else {
	
	
// Zamienia w bazie danych wartość pola 'parent_id' wybranego węzła na ID nowego rodzica.
	
$query = "UPDATE {$nazwa_tabeli} SET parent_id = '{$id_nowego_rodzica}' WHERE id = '{$id_wezla}';";

$result = queryDB($conn,$query);
if(mysqli_affected_rows($conn) > 0) {
$message = "Węzeł został przeniesiony";	
} else {
$message = "Wystąpił błąd podczas przenoszenia węzła";	
	
}	
	
}
	
	
$result = queryDB($conn,$query);
	
	
	

	
}

break;
	}
} else {
	
$message = "Wystąpił błąd podczas przenoszenia węzła";	
}

}


	
} else {
$message = "Nie wybrano węzła lub nowego rodzica";		
	
}
}






// Usuwanie węzła oraz jego potomków


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'usun_wezel')
{
	

	
if(isset($_POST['id_wezla']) && $_POST['id_wezla'] != null) {
	
	
$id_wezla = $_POST['id_wezla']; 

	
$usunieto = 0;
$conn = polaczDB();

	usunWezel($conn,$id_wezla);
	if($usunieto != 0) {
		
		$message = "Węzeł został usunięty";		
	} else {
			$message = "Wystąpił błąd podczas usuwania węzła";			
		
	}
		
	
	
	
} else {
	$message = "Nie wybrano żadnego węzła";	
}


}



// Zmiana nazwy węzła

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'nowa_nazwa')
{




if(isset($_POST['id_wezla']) && isset($_POST['nowa_nazwa']) && $_POST['id_wezla'] != null) {
	
$id_wezla = $_POST['id_wezla'];
$nowa_nazwa = $_POST['nowa_nazwa'];

$conn = polaczDB();

// Zamienia w bazie danych wartość pola 'text' wybranego węzła na nową nazwę.

$query = "UPDATE {$nazwa_tabeli} SET text = '{$nowa_nazwa}' WHERE ID = {$id_wezla};";
$result = queryDB($conn,$query);
if(mysqli_affected_rows($conn) > 0) {
	
		$message = "Nazwa węzła została zmieniona";	
} else {
		$message = "Wystąpił błąd podczas zmiany nazwy węzła";	
}
	
	
	
	
} else {
	$message = "Nie wybrano węzła lub nowej nazwy";	
}


}





// Dodawanie nowego węzła


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'nowy_wezel')
{


if(isset($_POST['nazwa']) && isset($_POST['rodzic'])) {
	
$rodzic = $_POST['rodzic'];
$nazwa = $_POST['nazwa'];

$conn = polaczDB();

// Wstawia do bazy danych rekord identyfikujący nowy węzeł.

$query = "INSERT INTO {$nazwa_tabeli}(text,parent_id) VALUES ('{$nazwa}', '{$rodzic}');";
$result = queryDB($conn,$query);

if(mysqli_affected_rows($conn) > 0) {
	
		$message = "Węzeł został dodany";	
} else {
		$message = "Wystąpił błąd podczas dodawania węzła";	
}
	
	
	
	
} else {
	$message = "Nie wybrano nazwy lub rodzica węzła";	
}


}


// Usuwa zawartość tablicy i wypełnia ją przykładowymi danymi.


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_name'] == 'wczytaj_baze')
{



$conn = polaczDB();

$sqlSource = file_get_contents('reset_table.sql');

$sqlSource = str_replace("countries",$nazwa_tabeli,$sqlSource);

mysqli_multi_query($conn,$sqlSource);

}



?><!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Zarządzanie strukturą drzewiastą</title>
<link href="tree.ico" rel="shortcut icon" type="image/x-icon">
<link href="index.css" rel="stylesheet">
<script>
function Validatezmien_rodzica()
{
   var regexp;
   var nowy_rodzic = document.getElementById('nowy_rodzic');
   if (!(nowy_rodzic.disabled ||
         nowy_rodzic.style.display === 'none' ||
         nowy_rodzic.style.visibility === 'hidden'))
   {
      if (nowy_rodzic.selectedIndex < 0)
      {
         alert("Musisz wybrać nowego rodzica");
         nowy_rodzic.focus();
         return false;
      }
      if (nowy_rodzic.selectedIndex == 0)
      {
         alert("Musisz wybrać nowego rodzica");
         nowy_rodzic.focus();
         return false;
      }
   }
   return true;
}
</script>
<script>
function Validatenowa_nazwa()
{
   var regexp;
   var nowa_nazwa = document.getElementById('nowa_nazwa');
   if (!(nowa_nazwa.disabled || nowa_nazwa.style.display === 'none' || nowa_nazwa.style.visibility === 'hidden'))
   {
      regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f0-9-]*$/;
      if (!regexp.test(nowa_nazwa.value))
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nowa_nazwa.focus();
         return false;
      }
      if (nowa_nazwa.value == "")
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nowa_nazwa.focus();
         return false;
      }
      if (nowa_nazwa.value.length < 1)
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nowa_nazwa.focus();
         return false;
      }
      if (nowa_nazwa.value.length > 250)
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nowa_nazwa.focus();
         return false;
      }
   }
   return true;
}
</script>
<script>
function Validatenowy_wezel()
{
   var regexp;
   var rodzic = document.getElementById('rodzic');
   if (!(rodzic.disabled ||
         rodzic.style.display === 'none' ||
         rodzic.style.visibility === 'hidden'))
   {
      if (rodzic.selectedIndex < 0)
      {
         alert("Musisz wybrać rodzica węzła");
         rodzic.focus();
         return false;
      }
      if (rodzic.selectedIndex == 0)
      {
         alert("Musisz wybrać rodzica węzła");
         rodzic.focus();
         return false;
      }
   }
   var nazwa = document.getElementById('nazwa');
   if (!(nazwa.disabled || nazwa.style.display === 'none' || nazwa.style.visibility === 'hidden'))
   {
      regexp = /^[A-Za-zÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ \t\r\n\f0-9-]*$/;
      if (!regexp.test(nazwa.value))
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nazwa.focus();
         return false;
      }
      if (nazwa.value == "")
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nazwa.focus();
         return false;
      }
      if (nazwa.value.length < 1)
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nazwa.focus();
         return false;
      }
      if (nazwa.value.length > 250)
      {
         alert("Nazwa może zawierać litery, cyfry i spacje");
         nazwa.focus();
         return false;
      }
   }
   return true;
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script charset="utf8" src="jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="dist/style.min.css" />
<script src="dist/jstree.min.js"></script>
</head>
<body>
<div id="container">
<div id="wb_wczytaj_baze" style="position:absolute;left:261px;top:857px;width:210px;height:51px;z-index:49;">
<form name="wczytaj_baze" method="post" action="./index.php" id="wczytaj_baze">
<input type="hidden" name="form_name" value="wczytaj_baze">
<input type="submit" id="Button6" name="" value="Zresetuj drzewo" style="position:absolute;left:0px;top:12px;width:210px;height:39px;z-index:0;" tabindex="11">
</form>
</div>
<input type="text" id="search" style="position:absolute;left:522px;top:869px;width:253px;height:30px;z-index:50;" name="search" value="" maxlength="250" tabindex="12" autocomplete="off" spellcheck="false">
<div id="Html3" style="position:absolute;left:0px;top:270px;width:994px;height:564px;overflow:auto;z-index:51">
<div id="tree-container"></div></div>
<div id="Layer5" style="position:absolute;text-align:left;left:0px;top:19px;width:994px;height:241px;z-index:52;">
<div id="Layer4" style="position:absolute;text-align:left;left:0px;top:0px;width:231px;height:241px;z-index:17;">
<div id="wb_nowy_wezel" style="position:absolute;left:0px;top:15px;width:221px;height:208px;z-index:6;">
<form name="nowy_wezel" method="post" action="./index.php" id="nowy_wezel" onsubmit="return Validatenowy_wezel()">
<input type="hidden" name="form_name" value="nowy_wezel">
<input type="submit" id="Button1" name="" value="Dodaj węzeł" style="position:absolute;left:0px;top:161px;width:210px;height:40px;z-index:1;" tabindex="3">
<select name="rodzic" size="1" id="rodzic" style="position:absolute;left:0px;top:109px;width:210px;height:33px;z-index:2;" tabindex="2">
<option selected>Wybierz...</option>
</select>
<div id="wb_Text7" style="position:absolute;left:0px;top:80px;width:135px;height:18px;z-index:3;">
<span style="color:#000000;font-family:Arial;font-size:15px;"><strong>Rodzic węzła:</strong></span></div>
<input type="text" id="nazwa" style="position:absolute;left:0px;top:32px;width:192px;height:23px;z-index:4;" name="nazwa" value="" maxlength="250" tabindex="1" autocomplete="off" spellcheck="false">
<div id="wb_Text8" style="position:absolute;left:0px;top:3px;width:183px;height:18px;z-index:5;">
<span style="color:#000000;font-family:Arial;font-size:15px;"><strong>Nazwa nowego węzła:</strong></span></div>
</form>
</div>
</div>
<div id="Layer1" style="position:absolute;text-align:left;left:242px;top:78px;width:247px;height:162px;z-index:18;">
<div id="wb_zmien_rodzica" style="position:absolute;left:19px;top:9px;width:210px;height:136px;z-index:10;">
<form name="zmien_rodzica" method="post" action="./index.php" id="zmien_rodzica" onsubmit="return Validatezmien_rodzica()">
<input type="hidden" name="form_name" value="zmien_rodzica">
<input type="hidden" name="id_wezla" value="" id="id_wezla">
<select name="nowy_rodzic" size="1" id="nowy_rodzic" style="position:absolute;left:0px;top:37px;width:210px;height:33px;z-index:7;" tabindex="4">
<option selected>Wybierz...</option>
</select>
<input type="submit" id="Button4" name="" value="Przenieś węzeł" style="position:absolute;left:0px;top:90px;width:210px;height:39px;z-index:8;" tabindex="5">
<div id="wb_Text5" style="position:absolute;left:0px;top:8px;width:183px;height:18px;z-index:9;">
<span style="color:#000000;font-family:Arial;font-size:15px;"><strong>Nowy rodzic węzła:</strong></span></div>
</form>
</div>
</div>
<div id="Layer3" style="position:absolute;text-align:left;left:503px;top:78px;width:247px;height:162px;z-index:19;">
<div id="wb_nowa_nazwa_form" style="position:absolute;left:19px;top:9px;width:210px;height:136px;z-index:14;">
<form name="nowa_nazwa" method="post" action="./index.php" id="nowa_nazwa_form" onsubmit="return Validatenowa_nazwa()">
<input type="hidden" name="form_name" value="nowa_nazwa">
<input type="hidden" name="id_wezla" value="" id="id_wezla3">
<input type="button" id="Button5" onclick="submitNowaNazwa();return false;" name="" value="Zmień nazwę" style="position:absolute;left:0px;top:90px;width:210px;height:39px;z-index:11;" tabindex="7">
<div id="wb_Text6" style="position:absolute;left:0px;top:8px;width:183px;height:18px;z-index:12;">
<span style="color:#000000;font-family:Arial;font-size:15px;"><strong>Nowa nazwa węzła:</strong></span></div>
<input type="text" id="nowa_nazwa" style="position:absolute;left:0px;top:37px;width:192px;height:23px;z-index:13;" name="nowa_nazwa" value="" maxlength="250" autocomplete="off" spellcheck="false" title="6">
</form>
</div>
</div>
<div id="Layer2" style="position:absolute;text-align:left;left:761px;top:159px;width:232px;height:81px;z-index:20;">
<div id="wb_usun_wezel" style="position:absolute;left:13px;top:11px;width:219px;height:53px;z-index:16;">
<form name="usun_wezel" method="post" action="./index.php" id="usun_wezel">
<input type="hidden" name="form_name" value="usun_wezel">
<input type="hidden" name="id_wezla" value="" id="id_wezla2">
<input type="button" id="Button7" onclick="submitUsunWezel();return false;" name="" value="Usuń węzeł" style="position:absolute;left:9px;top:7px;width:210px;height:39px;z-index:15;" tabindex="8">
</form>
</div>
</div>
</div>


<div id="Html4" style="position:absolute;left:1039px;top:418px;width:100px;height:100px;z-index:55">
<script>


// Sprawdza poprawność danych i wysyła formularz z żądaniem zmiany nazwy węzła

function submitNowaNazwa() {
	

    var id = document.getElementById("id_wezla3").value;

if(id == "") {
	
	alert("Nie wybrano żadnego węzła");
	
} else {
	  var ok = Validatenowa_nazwa();
	  if(ok == true) {
		  document.getElementById("nowa_nazwa_form").submit();
	  }
}

	
}

// Sprawdza czy został wybrany węzeł i wysyła formularz z żądaniem jego usunięcia

function submitUsunWezel() {
	

    var id = document.getElementById("id_wezla2").value;

if(id == "") {
	
	alert("Nie wybrano żadnego węzła");
	
} else {

		  document.getElementById("usun_wezel").submit();
	  
}

	
}

// Przegląda drzewo w poszukiwaniu danego wyrażenia

function przeszukajDrzewo() {

var wartosc = document.getElementById("search").value; 
 $('#tree-container').jstree(true).search(wartosc);

}

// Dodaje nazwy węzłów do ComboBoxów
 
 function dodajDoListy(nazwa, id) {

var option = document.createElement("option");
option.text = nazwa;
option.value = id;
var option2 = document.createElement("option");
option2.text = nazwa;
option2.value = id;
rodzic.add(option);
nowy_rodzic.add(option2);
}

// Dodaje ID węzła do formularza

   function ustawIdWezla(id) {
   document.getElementById('id_wezla').value=id;
      document.getElementById('id_wezla2').value=id;
         document.getElementById('id_wezla3').value=id;
   }
   
// Rozwija wszystkie gałęzie drzewa
   
   var rozwiniete = 0;
   
   function rozwinDrzewo() {
	   
	   if(rozwiniete == 0) {
		   rozwiniete = 1;
		       $("#tree-container").jstree('open_all');
	   } else {
		 		   rozwiniete = 0;  
		         $("#tree-container").jstree('close_all');
	   }
   

   }
   
// Nasłuchiwanie klawisza Enter dla wyszukiwarki
   
   document.getElementById("search")
    .addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("Button8").click();
    }
});
   
  
</script></div>



<input type="button" id="Button2" onclick="rozwinDrzewo();return false;" name="" value="Rozwiń / zwiń drzewo" style="position:absolute;left:0px;top:869px;width:210px;height:40px;z-index:59;" tabindex="9">
<input type="button" id="Button8" onclick="przeszukajDrzewo();return false;" name="" value="Wyszukaj w drzewie" style="position:absolute;left:783px;top:869px;width:210px;height:40px;z-index:60;" tabindex="13">
</div>
<script>

// Ładowanie kontenera JStree

$(document).ready(function(){ 
    //fill data to tree  with AJAX call
  
    $('#tree-container')
  // listen for event
  .on('changed.jstree', function (e, data) {
    var i, j, r = [];
    for(i = 0, j = data.selected.length; i < j; i++) {
      r.push(data.instance.get_node(data.selected[i]).id);
           r.push(data.instance.get_node(data.selected[i]).text);
    }
    var idWezla = r[0];
        var textWezla = r[1];
ustawIdWezla(idWezla);

  })

    $('#tree-container').jstree({

	'plugins': ["wholerow","sort","search"],
        'core' : {
			'multiple' : false,
            'data' : {
                "url" : "response.php",
                "dataType" : "json" // needed only if you do not supply JSON headers
            }
        }
    }) 

				
}


);


</script><?php
  
// Pobiera nazwy węzłów z bazy danych

$query = "SELECT text, id FROM {$nazwa_tabeli};";

$conn = polaczDB();
$result = queryDB($conn,$query);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $nazwa = $row["text"];
		$id = $row["id"];
echo "<script> dodajDoListy('{$nazwa}', {$id}); </script>";
			
    }
} else {
	
echo "<script> dodajDoListy('Brak (korzeń)', '0'); </script>";
	
}
 
  
?>



</body>
</html>  <?php 
  
  // Wyświetla okno z komunikatem
  
   if(isset($message)) {
   komunikat($message);
   };
   
   ?>
