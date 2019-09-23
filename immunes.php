<?php
	require_once 'pdo.php';
	require_once 'util.php';
	session_start();

	if (!isset($_SESSION['username'])) {
	    header("Location: login.php");
	    return;
	}

	if (isset($_GET['id']) && $_GET['id'] !== "" && isset($_GET['ai']) && $_GET['ai'] !== "" && isset($_GET['type']) && $_GET['type'] !== "" && isset($_GET['Imm_name']) && $_GET['Imm_name'] !== ""){
		$id = $_GET['id'];
		$ai = $_GET['ai'];
		$Imm_name = $_GET['Imm_name'];
		$type = $_GET['type'];
		if ($type === "cow") {
			$animal = "بقرة";
		}
		elseif ($type === "goat") {
			$animal = "ماعز";
		}
		else{
			$_SESSION['error'] = "خطأ: نوع حيوان أو اسم تحصين غير معروف!";
		    header("Location: index.php");
		    return;
		}
	}
	else {
		$_SESSION['error'] = "خطأ: رقم أو نوع الحيوان غير محددان!";
	    header("Location: index.php");
	    return;
	}

	if (isset($_POST['check']) && isset($_POST['ID'])) {
		$checked = 1;
		updateImms($pdo, $_POST['ID'], $checked);
		header("Location: immunes.php?id=".$id."&ai=".$ai."&type=".$type."&Imm_name=".$Imm_name);
		return;
	}

		if (isset($_POST['uncheck']) && isset($_POST['ID'])) {
		$checked = 0;
		updateImms($pdo, $_POST['ID'], $checked);
		header("Location: immunes.php?id=".$id."&ai=".$ai."&type=".$type."&Imm_name=".$Imm_name);
		return;
	}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<title>مواعيد التحصين</title>
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
	      <li class="breadcrumb-item"> <a href="<?= $type ?>.php"><?php 
	      	if ($animal == "بقرة") {
	      		echo("البقر");
	      	}
	      	else {
	      		echo("الماعز");
	      	}
	       ?></a></li>
	      <li class="breadcrumb-item"> <a href="immune.php?id=<?= $ai ?>&type=<?= $type ?>">التحصينات ومعلومات الحمل والولادة</li></a>
	      <li class="breadcrumb-item active" aria-current="page"> مواعيد التحصين</li>
	    </ol>
	  </nav>
	 </header>
	<div class="container col-11" style="margin-top: 1em;">
		<table class="table table-bordered">
		 	<thead>
		      <tr>
		      	<th colspan="3">
		      		<h5  class="text-center">تحصين <?= $Imm_name ?> ال<?= $animal ?> رقم <?= $ai ?></h5>	
		      	</th>
		      </tr>
	      	  <tr>
			      <th scope="col" class="text-center">تاريخ التحصين</th>
			      <th scope="col" class="text-center">تم التحصين؟</th>
			      <th scope="col" class="text-center" style="width: 22em;">تعديل </th>
		      </tr>
		 	</thead>
		 	<tbody>
		 		<?php 
					$dates = loadDates($pdo, $id);
					if(!$dates || count($dates>0)){
			 			foreach ($dates as $date) {
			 				if ($date['checked'] == 0) {
			 					$checkText = "لا";
			 				}
			 				else {
			 					$checkText = "نعم";
			 				}
			 				echo('
						      	<tr>
								    <td class="text-center">'.date('Y-m-d', strtotime($date['Imm_date'])).'</td>
								    <td class="text-center"> '.$checkText.' </td>
								    <td align="center">
								    <form method="POST">
										<input type="hidden" name="ID" value="'.$date["ID"].'">
							      		<button type="submit" class="btn btn-primary btn-md" name="check">تم التحصين</button>
							      		<button type="submit" class="btn btn-primary btn-md" name="uncheck">لم يتم التحصين</button>
							      	</form>
								    </td>
							    </tr>
			 					');
			 			}
			 		}
		 		?>
		 	</tbody>
		</table>
	</div>
</body>
</html>