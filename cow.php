<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    return;
}

if (isset($_POST['edit']) && isset($_POST['cowID'])){
	if ($_POST['Age'] == "" || $_POST['Weight'] == "" || $_POST['Weight_date'] == "") {
	  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زر التعديل!";
	  	header("Location: cow.php");
	  	return;		
	}
	else {
		updateCow($pdo, $_POST['cowID']);
		$_SESSION['success'] = "تم تعديل البقرة ".$_POST['cowID']." بنجاح.";
		header("Location: cow.php");
		return;
	}
}

if (isset($_POST['delete']) && isset($_POST['cowID'])){
	deleteRow($pdo, $_POST['cowID'], "cow");
	$_SESSION['success'] = "تم حذف البقرة ".$_POST['cowID']." بنجاح.";
	header("Location: cow.php");
	return;
}	


// Check to see if we have some POST data, if we do process it
if (isset($_POST['add']) ) {
	if ($_POST['ID'] == "" || $_POST['Age'] == "" || $_POST['Weight'] == "") {
	  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زرار الإضافة!";
	  	header("Location: cow.php");
	  	return;		
	}
	else{
	  $stmt = $pdo->query("SELECT ID FROM `cow` WHERE cow.ID = ".$_POST['ID']);
	  $row = $stmt->fetch(PDO::FETCH_ASSOC);
	  	if ($row !== false ) {
	  		$_SESSION['error'] = "خطأ: رقم هذه البقةر مستخدم بالفعل!";
	  		header("Location: cow.php");
	  		return;
	  	}
  		else {
  			insertCow($pdo);
	  		$_SESSION['success'] = "تم إضافة البقرة  رقم ".$_POST['ID']." بنجاح.";
	  		header("Location: cow.php");
	  		return;
  		}
	}
}

?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<title>Cows</title>
<?php require_once 'head.php'; ?>
</head>
<body>
<header>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">الرئيسية</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a href="cow.php" class="nav-link">البقر</a>
      </li>
      <li class="nav-item">
        <a href="goat.php" class="nav-link">الماعز</a>
      </li>
    </ul>
  </div>
  <a href="logout.php"><button class="btn btn-outline-light" type="submit">تسجيل الخروج</button></a>
  </nav>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
      <li class="breadcrumb-item active" aria-current="page"> البقر</li>
    </ol>
  </nav>
 </header>

 <div class="container col-11" style="margin-top: 1em;" >
<?php 
	flashMessage();
?>
 <table class="table table-bordered">
  <thead>
  <tr>
     <th id="toHide0" style="width: 11em" class="text-center">
  		<button type="submit" class="btn btn-primary btn-md" onclick="toggleRow(0)">إضافة بقرة جديدة</button>
    </th>
    </tr>
    <tr>
      <th scope="col" class="text-center">رقم البقرة</th>
      <th scope="col" class="text-center">العمر</th>
      <th scope="col" class="text-center">الوزن</th>
      <th scope="col" class="text-center">تاريخ تسجيل الوزن</th>
      <th scope="col" class="text-center">الأكل</th>
      <th scope="col" class="text-center">النوع</th>
      <th scope="col" class="text-center">تاريخ الدخول</th>
      <th scope="col" class="text-center" style="width: 11.2em;">تعديل/إضافة/حذف</th>
    </tr>
  </thead>
  <tbody>
    <tr style="display: none;" id="toShow0">
    	<form method="POST">
      		<th scope="row">
	      		<input type="number" min="1" max="999" class="form-control" placeholder="رقم البقرة" name="ID">
      		</th>
	      	<td align="center">
			    <input type="number" min="0" max="100" class="form-control " placeholder="العمر" name="Age">
		    </td>
	      	<td align="center"> 
		    	<input type="number" min="0" max="1000" class="form-control" placeholder="الوزن" name="Weight">
		    </td>
		    <td align="center" style="width: 5em;">-</td>
		    <td class="text-center">-</td>
		    <td align="center">
		    	<select id="Gender" class="form-control" name="Gender">
	       			<option value="1" selected>أنثى</option>
	        		<option value="2">ذكر</option>
	      		</select>	    
		     </td>
	      	<td align="center">
		    	<input type="date" class="form-control" name="inDate" value="<?= date('Y-m-d'); ?>" required style="max-width: 11em;">
		    </td>
		    <td align="center">
	      		<button type="submit" class=" btn btn-primary btn-md" name="add">إضافة</button>
	      		<button type="button" class=" btn btn-primary btn-md" onclick="toggleRow(0);">إلغاء</button>
		    </td>
		</form>
	</tr>

<?php 
	$cows = loadCows($pdo);
	if(!$cows || count($cows>0)){
		foreach ($cows as $cow) {
			if ($cow['Gender'] === "أنثى") {
				$other_gender = "ذكر";
				$value = 1;
				$other_value = 2;
			}
			else {
				$other_gender = "أنثى";
				$value = 2;
				$other_value = 1;
			}
			echo('
		    <tr id="toHide'.$cow['ID'].'">
		      <th scope="row" class="text-center"><a style="display: block;text-decoration:none;" href="immune.php?id='.$cow['ID'].'&type=cow">'.$cow['ID'].'</a></th>
		      <td class="text-center">'.$cow['Age'].'</td>
		      <td class="text-center">'.$cow['Weight'].'</td>
		      <th class="text-center"><a style="display: block;text-decoration:none;" href="weights.php?id='.$cow['ID'].'&type=cow">'.date('Y-m-d', strtotime($cow['Weight_date'])).'</a></th>
		      <td class="text-center">'.$cow['Food'].'</td>
		      <td class="text-center">'.$cow['Gender'].'</td>
		      <td class="text-center">'.date('Y-m-d', strtotime($cow['inDate'])).'</td>
			  <td align="center">
			      <form method="POST">
					<input type="hidden" name="cowID" value="'.$cow["ID"].'">
		      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow('.$cow['ID'].');">تعديل</button>
		      		<button type="submit" class="btn btn-primary btn-md" onclick="return confirmDel('.$cow["ID"].')" name="delete">حذف</button>
		      	  </form>
			  </td>
		    </tr>'
		    );
		    echo('    
		    <tr style="display: none;" id="toShow'.$cow['ID'].'">
		    	<form method="POST">
				    <th scope="row" class="text-center">'.$cow['ID'].'</th>
			      	<td align="center">
					    <input type="number" min="0" max="100" class="form-control " name="Age" value="'.$cow['Age'].'" style="max-width: 5.5em;">
				    </td>
			      	<td align="center"> 
				    	<input type="number" min="0" max="1000" class="form-control" name="Weight" value="'.$cow['Weight'].'" style="max-width: 5.5em;">
				    </td>
			      	<td align="center">
				    	<input type="date" class="form-control" name="Weight_date" value="'.date('Y-m-d').'" style="max-width: 11em;" required>
				    </td>
			      	<td align="center"> 
				    	<input type="number" step="any" min="0" max="10" class="form-control" name="Food" value="'.$cow['Food'].'" style="max-width: 5.5em;">
				    </td>
				    <td align="center">
				    	<select id="Gender" class="form-control" name="Gender">
			       			<option value="'.$value.'" selected>'.$cow['Gender'].'</option>
			        		<option value="'.$other_value.'">'.$other_gender.'</option>
			      		</select>	    
				    </td>
			      	<td align="center">
				    	<input type="date" class="form-control" name="inDate" value="'.date('Y-m-d', strtotime($cow['inDate'])).'" style="max-width: 11em;" required>
				    </td>
				    <td align="center">
						<input type="hidden" name="cowID" value="'.$cow["ID"].'">
			      		<button type="submit" class=" btn btn-primary btn-md" name="edit">تأكيد</button>
			      		<button type="button" class=" btn btn-primary btn-md" onclick="toggleRow('.$cow["ID"].');">إلغاء</button>
				    </td>
				</form>
			</tr>');
			}
}
?>

  </tbody>
</table>
</div>
<script type="text/javascript">
	function confirmDel(id) {
		if (!confirm("حذف البقرة رقم " + id + "؟")) {
	    	return false;
	    }
	}
	function toggleRow(id) {
	  var x = document.getElementById("toShow"+id);
	  var y = document.getElementById("toHide"+id);
	  if (x.style.display === "none") {
	    x.style.display = "";
	  } else {
	    x.style.display = "none";
	  }
	  if (y.style.display === "none") {
	    y.style.display = "";
	  } else {
	    y.style.display = "none";
	  }
	}
</script>
</body>
</html>