<?php 

// ************************************************************************* 
//                              Printing Section
// *************************************************************************

function flashMessage(){
	if (isset($_SESSION['error'])){
		echo('<p style="color :red;" class="text-right">'.$_SESSION['error']."</p>\n");
		unset($_SESSION['error']);
	}
	if (isset($_SESSION['success'])){
		echo('<p style="color :green;" class="text-right">'.$_SESSION['success']."</p>\n");
		unset($_SESSION['success']);
	}
}

// ************************************************************************* 
//                              Inserting Section
// *************************************************************************

function insertCow($pdo){
	$stmt = $pdo->prepare('INSERT INTO cow
	  (ID, Age, Weight, Weight_date, Food, Gender, inDate, isSold)
	  VALUES ( :id, :age, :weight, :weight_date, :food, :gender, :indate, :issold)');
	if ($_POST['Gender'] == 1) {
		$gender = 'أنثى';
	}
	else {
		$gender = 'ذكر';
	}
	$stmt->execute(array(
	  ':id' => $_POST['ID'],
	  ':age' => $_POST['Age'],
	  ':weight' => $_POST['Weight'],
	  ':weight_date' => $_POST['inDate'],
	  ':food' => $_POST['Weight']/100,
	  ':gender' => $gender,
	  ':indate' => $_POST['inDate'],
	  ':issold' => 0,
	));	
}

function insertGoat($pdo){
	$stmt = $pdo->prepare('INSERT INTO goat
	  (ID, Age, Weight, Weight_date, Food, Gender, inDate, isSold)
	  VALUES ( :id, :age, :weight, :weight_date, :food, :gender, :indate, :issold)');
	if ($_POST['Gender'] == 1) {
		$gender = 'أنثى';
	}
	else {
		$gender = 'ذكر';
	}
	$stmt->execute(array(
	  ':id' => $_POST['ID'],
	  ':age' => $_POST['Age'],
	  ':weight' => $_POST['Weight'],
	  ':weight_date' => $_POST['inDate'],
	  ':food' => $_POST['Weight']/100,
	  ':gender' => $gender,
	  ':indate' => $_POST['inDate'],
	  ':issold' => 0,
	));	
}

function insertImm($pdo, $ai, $type){
	if ($type === "cow") {
		$stmt = $pdo->prepare('INSERT INTO immunization
		  (cow_ID, Imm_type, Imm_name, Imm_date, Imm_duration)
		  VALUES ( :id, :imm_type, :imm_name, :imm_date, :imm_duration)');	
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare('INSERT INTO immunization
		  (goat_ID, Imm_type, Imm_name, Imm_date, Imm_duration)
		  VALUES ( :id, :imm_type, :imm_name, :imm_date, :imm_duration)');	
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}

	$stmt->execute(array(
	  ':id' => $ai,
	  ':imm_type' => $_POST['Imm_type'],
	  ':imm_name' => $_POST['Imm_name'],
	  ':imm_date' => $_POST['Imm_date'],
	  ':imm_duration' => $_POST['Imm_duration'],
	));	

  	$Imm_ID = $pdo->lastInsertId();

	$stmt2 = $pdo->prepare('INSERT INTO dates (Imm_ID ,Imm_date, checked)
		VALUES (:imm_id, :imm_date, :checked)');
	$date = date('Y-m-d', strtotime($_POST['Imm_date']));

	for ($i=0; $i < 20; $i++) { 
		$stmt2->execute(array(
			':imm_id' => $Imm_ID,
			':imm_date' => $date,
			':checked' => 0 ));
		$date = date('Y-m-d', strtotime($date." +".$_POST['Imm_duration']." month"));
	}
}

function insertPreg($pdo, $ai, $type){
	if ($type === "cow") {
		$stmt = $pdo->prepare('INSERT INTO preg_info
		  (cow_ID, Preg_count, Preg_month, Birth_month)
		  VALUES ( :id, :preg_count, :preg_month, :birth_month)');	
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare('INSERT INTO preg_info
		  (goat_ID, Preg_count, Preg_month, Birth_month)
		  VALUES ( :id, :preg_count, :preg_month, :birth_month)');		
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}

	$stmt->execute(array(
	  ':id' => $ai,
	  ':preg_count' => $_POST['Preg_count'],
	  ':preg_month' => $_POST['Preg_month'],
	  ':birth_month' => $_POST['Birth_month'],
	));	
}

function insertChild($pdo, $ai, $type){
	if ($type === "cow") {
		$stmt = $pdo->prepare('UPDATE cow SET mother_ID=:ai
		  WHERE ID = :id');	
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare('UPDATE goat SET mother_ID=:ai
		  WHERE ID = :id');		
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}

	$stmt->execute(array(
	  ':id' => $_POST['ID'],
	  ':ai' => $ai
	));	
}

// ************************************************************************* 
//                              Loading Section
// *************************************************************************

function loadNearImms($pdo, $type) {
	if ($type === "cow") {
		$stmt = $pdo->prepare("SELECT cow.ID as animal_ID, dates.Imm_date, immunization.Imm_name, immunization.Imm_type, immunization.Imm_duration FROM dates JOIN immunization ON immunization.ID = dates.Imm_ID JOIN cow ON immunization.cow_ID = cow.ID WHERE goat_ID IS NULL AND dates.Imm_date >= CURDATE() AND dates.Imm_date < CURDATE() + INTERVAL 31 DAY AND dates.checked = 0");
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare("SELECT goat.ID as animal_ID, dates.Imm_date, immunization.Imm_name, immunization.Imm_type, immunization.Imm_duration FROM dates JOIN immunization ON immunization.ID = dates.Imm_ID JOIN goat ON immunization.goat_ID = goat.ID WHERE cow_ID IS NULL AND dates.Imm_date >= CURDATE() AND dates.Imm_date < CURDATE() + INTERVAL 31 DAY AND dates.checked = 0");
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}
	$stmt->execute();
	$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $animals;
}

function loadCows($pdo) {
	$stmt = $pdo->prepare("SELECT * FROM cow");
	$stmt->execute();
	$cows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $cows;
}

function loadGoats($pdo) {
	$stmt = $pdo->prepare("SELECT * FROM goat");
	$stmt->execute();
	$goats = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $goats;
}

function loadImms($pdo, $ai, $type) {
	$stmt = $pdo->prepare("SELECT * FROM immunization WHERE ".$type."_ID = :ai ORDER BY Imm_date");
	$stmt->execute(array(':ai' => $ai));
	$immunes = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $immunes;
}

function loadPregs($pdo, $ai, $type) {
	$stmt = $pdo->prepare("SELECT * FROM preg_info WHERE ".$type."_ID = :ai");
	$stmt->execute(array(':ai' => $ai));
	$pregs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $pregs;
}

function loadChildren($pdo, $ai, $type) {
	if ($type === "cow") {
		$stmt = $pdo->prepare("SELECT * FROM cow WHERE mother_ID = :ai");
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare('SELECT * FROM goat WHERE mother_ID = :ai');		
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}
	$stmt->execute(array(':ai' => $ai));
	$children = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $children;
}

function loadWeights($pdo, $ai, $type) {
	if ($type === "cow") {
		$stmt = $pdo->query("SELECT * FROM `weights` WHERE cow_ID=".$ai);
	}
	elseif ($type === "goat") {
		$stmt = $pdo->query("SELECT * FROM `weights` WHERE goat_ID=".$ai);
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}
	$stmt->execute();
	$weights = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $weights;
}

function loadDates($pdo, $id) {
	$stmt = $pdo->query("SELECT * FROM `dates` WHERE Imm_ID =".$id);
	$stmt->execute();
	$dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
	return $dates;
}



// ************************************************************************* 
//                              Updating Section
// *************************************************************************

function updateCow($pdo, $ci){
	$stmt = $pdo->prepare("SELECT Weight FROM cow WHERE ID = :ci");
	$row = $stmt->execute(array(
		':ci' => $ci
		 ));

	if ($_POST['Weight'] !== $row['Weight']) {
		$date = date('Y-m-d', strtotime($_POST['Weight_date']));
		$stmt = $pdo->prepare("INSERT INTO weights(cow_ID, edit_date, weight)
			VALUES (:cow_ID, :edit_date, :weight)");

		$stmt->execute(array(
			':cow_ID' => $ci,
			':edit_date' => $date,
			':weight' => $_POST['Weight']
		 ));
		$stmt2 = $pdo->prepare("UPDATE cow SET Weight=:weight, Weight_date=:weight_date
			WHERE ID = :ci");
		$stmt2->execute(array(
			':ci' => $ci,
			':weight' => $_POST['Weight'],
			':weight_date' => $date
			 ));
	}
	$stmt2 = $pdo->prepare("UPDATE cow SET Age=:age, Food = :food, Gender=:gender, inDate = :indate WHERE ID=:ci");
	if ($_POST['Gender'] == 1) {
		$gender = 'أنثى';
	}
	else {
		$gender = 'ذكر';
	}
	$stmt2->execute(array(
		':ci' => $ci,
		':age' => $_POST['Age'],
		':food' => $_POST['Food'],
		':gender' => $gender,
		':indate' => $_POST['inDate'],
	));
}

function updateGoat($pdo, $gi){
	$stmt = $pdo->prepare("SELECT Weight FROM goat WHERE ID = :gi");
	$row = $stmt->execute(array(
		':gi' => $gi
		 ));

	if ($_POST['Weight'] !== $row['Weight']) {
		$date = date('Y-m-d', strtotime($_POST['Weight_date']));
		$stmt = $pdo->prepare("INSERT INTO weights(goat_ID, edit_date, weight)
			VALUES (:goat_ID, :edit_date, :weight)");

		$stmt->execute(array(
			':goat_ID' => $gi,
			':edit_date' => $date,
			':weight' => $_POST['Weight']
		 ));
		$stmt2 = $pdo->prepare("UPDATE goat SET Weight=:weight, Weight_date=:weight_date
			WHERE ID = :gi");
		$stmt2->execute(array(
			':gi' => $gi,
			':weight' => $_POST['Weight'],
			':weight_date' => $date
			 ));
	}
	$stmt2 = $pdo->prepare("UPDATE goat SET Age=:age, Food = :food, Gender=:gender, inDate = :indate WHERE ID=:gi");
	if ($_POST['Gender'] == 1) {
		$gender = 'أنثى';
	}
	else {
		$gender = 'ذكر';
	}
	$stmt2->execute(array(
		':gi' => $gi,
		':age' => $_POST['Age'],
		':food' => $_POST['Food'],
		':gender' => $gender,
		':indate' => $_POST['inDate'],
	));
}

function updateImm($pdo, $id){
	$stmt = $pdo->prepare("UPDATE immunization SET Imm_type=:imm_type, Imm_name=:imm_name, Imm_date = :imm_date, Imm_duration=:imm_duration, WHERE ID=:id");
	$stmt->execute(array(
	  ':id' => $id,
	  ':imm_type' => $_POST['Imm_type'],
	  ':imm_name' => $_POST['Imm_name'],
	  ':imm_date' => $_POST['Imm_date'],
	  ':imm_duration' => $_POST['Imm_duration'],
	));
}

function updatePreg($pdo, $id){
	$stmt = $pdo->prepare("UPDATE preg_info SET Preg_count=:preg_count, Preg_month=:preg_month, Birth_month = :birth_month WHERE ID=:id");
	$stmt->execute(array(
	  ':id' => $id,
	  ':preg_count' => $_POST['Preg_count'],
	  ':preg_month' => $_POST['Preg_month'],
	  ':birth_month' => $_POST['Birth_month'],
	));
}

function updateImms($pdo, $id, $checked){
	$stmt = $pdo->prepare("UPDATE dates SET checked=:checked WHERE ID=:id");
	$stmt->execute(array(
		':id' => $id,
		':checked' => $checked,
	));
}

function updateWeight($pdo, $id){
	$date = date('Y-m-d', strtotime($_POST['Weight_date']));
	$stmt = $pdo->prepare("UPDATE weights SET weight=:weight, edit_date=:edit_date WHERE ID=:id");
	$stmt->execute(array(
	  ':id' => $id,
	  ':weight' => $_POST['Weight'],
	  ':edit_date' => $date,
	));
}

// ************************************************************************* 
//                              Deleting Section
// ************************************************************************* 

function deleteRow($pdo, $id, $type){
	$stmt = $pdo->prepare('DELETE FROM '.$type.' WHERE ID=:id');
	$stmt->execute(array( ':id' => $id));	
}

function deleteChild($pdo, $id, $type){
	if ($type === "cow") {
		$stmt = $pdo->prepare('UPDATE cow SET mother_ID = NULL
		  WHERE ID = :id');	
	}
	elseif ($type === "goat") {
		$stmt = $pdo->prepare('UPDATE goat SET mother_ID = NULL
		  WHERE ID = :id');		
	}
	else{ 
		$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		return;
	}
	$stmt->execute(array( ':id' => $id));	
}