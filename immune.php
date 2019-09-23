<?php
	require_once 'pdo.php';
	require_once 'util.php';
	session_start();

	if (!isset($_SESSION['username'])) {
	    header("Location: login.php");
	    return;
	}

	if (isset($_GET['id']) && $_GET['id'] !== "" && isset($_GET['type']) && $_GET['type'] !== ""){
		$id = $_GET['id'];
		$type = $_GET['type'];
		if ($type === "cow") {
			$animal = "بقرة";
		}
		elseif ($type === "goat") {
			$animal = "ماعز";
		}
		else{
			$_SESSION['error'] = "خطأ: نوع حيوان غير معروف!";
		    header("Location: index.php");
		    return;
		}
	}
	else {
		$_SESSION['error'] = "خطأ: رقم أو نوع الحيوان غير محددان!";
	    header("Location: index.php");
	    return;
	}

	if (isset($_GET['view'])) {
		if ($_GET['view'] === "Imm") {
			$view = "Imm";
		}
		elseif ($_GET['view'] === "Preg") {
			$view = "Preg";
		}
		else
		{
			$_SESSION['error'] = "خطأ: عرض صفحة معلومات غير موجودة!";
		    header("Location: index.php");
		    return;	
		}
	}


	if (isset($_POST['addImm']) ) {
		if ($_POST['Imm_type'] == "" || $_POST['Imm_name'] == "" || $_POST['Imm_date'] == "" || 
			$_POST['Imm_duration'] == "") {
		  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زرار الإضافة!";
		  	header("Location: immune.php?id=".$id."&type=".$type."&view=Imm");
		  	return;		
		}
		else {
			insertImm($pdo, $id, $type);
			$_SESSION['success'] = "تم إضافة تحصين  \"".$_POST['Imm_name']."\" بنجاح.";
			header("Location: immune.php?id=".$id."&type=".$type."&view=Imm");
			return;
		}
	}

	if (isset($_POST['addPreg']) ) {
		if ($_POST['Preg_count'] == "" || $_POST['Preg_month'] == "" || $_POST['Birth_month'] == "") {
		  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زرار الإضافة!";
		  	header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		  	return;		
		}
		else {
			insertPreg($pdo, $id, $type);
			$_SESSION['success'] = "تم إضافة معلومات حمل وولادة  ال".$animal." رقم  ".$id."بنجاح";
			header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
			return;
		}
	}
	if (isset($_POST['addChild']) ) {
		if ($_POST['ID'] == "") {
		  	$_SESSION['error'] = "من فضلك إختر رقم الابن!";
		  	header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		  	return;		
		}
		else {
			insertChild($pdo, $id, $type);
			$_SESSION['success'] = "تم إضافة الابن رقم ".$_POST['ID']." بنجاح.";
			header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
			return;
		}
	}


	if (isset($_POST['editImm']) && isset($_POST['ID'])){
		if ($_POST['Imm_type'] == "" || $_POST['Imm_name'] == "" || $_POST['Imm_date'] == "" || 
			$_POST['Imm_duration'] == "") {
		  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زر التعديل!";
		  	header("Location: immune.php?id=".$id."&type=".$type."&view=Imm");
		  	return;		
		}
		else {
			updateImm($pdo, $_POST['ID']);
			$_SESSION['success'] = "تم تعديل تحصين  \"".$_POST['Imm_name']."\" بنجاح.";
			header("Location: immune.php?id=".$id."&type=".$type."&view=Imm");
			return;
		}
	}

	if (isset($_POST['editPreg']) && isset($_POST['ID'])){
		if ($_POST['Preg_count'] == "" || $_POST['Preg_month'] == "" || $_POST['Birth_month'] == "") {
		  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زر التعديل!";
		  	header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		  	return;		
		}
		else {
			updatePreg($pdo, $_POST['ID']);
			$_SESSION['success'] = "تم تعديل معلومات حمل وولادة ال".$animal." رقم  ".$_POST['ID']." بنجاح.";
			header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
			return;
		}
	}

	if (isset($_POST['editChild']) && isset($_POST['ID'])){
	if ($_POST['Age'] == "" || $_POST['Weight'] == "") {
	  	$_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زر التعديل!";
	  	header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
	  	return;		
	}
	else {
		if ($type === "cow") {
			updateCow($pdo, $_POST['ID']);
		}
		else {
			updateGoat($pdo, $_POST['ID']);
		}
		$_SESSION['success'] = "تم تعديل ال".$animal." رقم ".$_POST['ID']." بنجاح.";
		header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		return;
		}
	}

	if (isset($_POST['deleteImm']) && isset($_POST['ID'])){
		deleteRow($pdo, $_POST['ID'], "immunization");
		$_SESSION['success'] = "تم حذف تحصين ال".$animal." رقم  ".$id." بنجاح.";
		header("Location: immune.php?id=".$id."&type=".$type."&view=Imm");
		return;
	}

	if (isset($_POST['deletePreg']) && isset($_POST['ID'])){
		deleteRow($pdo, $_POST['ID'], "preg_info");
		$_SESSION['success'] = "تم حذف معلومات حمل وولادة ال".$animal." رقم ".$id." بنجاح.";
		header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		return;
	}	

	if (isset($_POST['deleteChild']) && isset($_POST['ID'])){
		deleteChild($pdo, $_POST['ID'], $type);
		$_SESSION['success'] = "تم حذف معلومات حمل وولادة ال".$animal." رقم ".$id." بنجاح.";
		header("Location: immune.php?id=".$id."&type=".$type."&view=Preg");
		return;
	}	
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<title>التحصينات</title>
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
	      <li class="breadcrumb-item active" aria-current="page"> التحصينات ومعلومات الحمل والولادة</li>
	    </ol>
	  </nav>
	 </header>
	<div class="container col-11" style="margin-top: 1em;" >
	 	<?php 
			flashMessage();
		?>
	 <table class="table table-bordered text-center">
	 	<thead>
	 	  <tr>
	 	  	<th colspan="5">
				<button type="button" class="btn btn-info btn-lg" style="width: 49%;" onclick="showImmunes()">التحصينات</button>
				<button type="button" class="btn btn-info btn-lg" style="width: 50%;" onclick="showPregnancy()">تفاصيل الحمل والولادة</button>
			</th>
	      </tr>
	  </thead>
	</table>
	<div id="Immunes" <?php
	 if (isset($view)) {
	 	if ($view === "Preg") {
	 		echo('style="display: none;"');
	 	}
	 }
	 else{
	 		echo('style="display: none;"');	
	 } ?> >
		<table class="table table-bordered">
		 	<thead>
		      <tr>
		      	<th colspan="4" id="toSpread0">
		      		<h5  class="text-right">تحصينات ال<?= $animal ?> رقم <?= $id ?></h5> 		
		      	</th>
		      	<th id="toHide0" style="width: 12.5em;" class="text-center">	  		
		      		<button type="submit" class="btn btn-primary btn-md text-left" onclick="toggleRow(0);colspan(0, '5');">إضافة تحصين جديد</button>
		      	</th>
		      </tr>
		      <tr>
			      <th scope="col" class="text-center">نوع التحصين</th>
			      <th scope="col" class="text-center">اسم التحصين</th>
			      <th scope="col" class="text-center" style="width: 10em;">تاريخ بداية التحصين</th>
			      <th scope="col" class="text-center">مدة التحصين </th>      
			      <th scope="col" class="text-center" style="width: 11em;" >تعديلات</th>      
		      </tr>
		 	</thead>
		 	<tbody>
		      	<form method="POST">
			      <tr id="toShow0" style="display: none;">
			      	<td>
			      		<input type="text" class="form-control text-center" name="Imm_type">
					</td>
					<td>
			     		<input type="text" class="form-control text-center" name="Imm_name">
			     	</td>
			     	<td>
			     		<input type="date" class="form-control" name="Imm_date" value="<?= date('Y-m-d'); ?>" required>
			     	</td>
			     	<td>
			     		<input type="number" min="1" class="form-control" name="Imm_duration">
		      		</td>
				    <td align="center">
			      		<button type="submit" class="btn btn-primary btn-md" name="addImm">إضافة</button>
			      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow(0);colspan(0, '4')">إلغاء</button>
				    </td>
				  </tr>
		      	</form>		
			<?php 
				$immunes = loadImms($pdo, $id, $type);
					if(!$immunes || count($immunes)<1){
						echo('  <tr id="toHide0">
						<th colspan="5">
				      		<h6  class="text-center">لا يوجد  معلومات حمل وولادة لهذه ال'.$animal.'.</h6> 		
			      		</th>
			      </tr>');
					}
					else {
						foreach ($immunes as $immune) {
						echo('
						    <tr id="toHide'.$immune["ID"].'">
						      <td class="text-center" style="width: 15em;">'.$immune["Imm_type"].'</td>
						      <td class="text-center" style="width: 15em;">'.$immune["Imm_name"].'</td>
						      <th class="text-center"><a style="display: block;text-decoration:none;" href="immunes.php?id='.$immune['ID'].'&ai='.$id.'&Imm_name='.$immune["Imm_name"].'&type=cow">'.date('Y-m-d', strtotime($immune['Imm_date'])).'</td>
						      <th class="text-center">'.$immune["Imm_duration"].' شهر</td>
							  <td align="center"  style="width: 12.5em;">
							      <form method="POST">
									<input type="hidden" name="ID" value="'.$immune["ID"].'">
						      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow('.$immune['ID'].');">تعديل</button>
						      		<button type="submit" class="btn btn-primary btn-md" onclick="return confirmDelImm(\''.$immune["Imm_name"].'\');" name="deleteImm">حذف</button>
						      	  </form>
							  </td>
						    </tr>'
						    );
						    echo('    
						      <tr style="display: none;" id="toShow'.$immune['ID'].'">
						      	<form method="POST">
							      	<td>
							      		<input type="text" class="form-control text-center" name="Imm_type" value="'.$immune["Imm_type"].'">
									</td>
									<td>
							     		<input type="text" class="form-control text-center" name="Imm_name" value="'.$immune["Imm_name"].'">
							     	</td>
							     	<td>
							     		<input type="date" class="form-control" name="Imm_date" required value="'.date('Y-m-d', strtotime($immune['Imm_date'])).'">
							     	</td>
							     	<td>
							     		<input type="number" min="1" class="form-control" name="Imm_duration" value="'.$immune["Imm_duration"].'">
						      		</td>
								    <td align="center">
										<input type="hidden" name="ID" value="'.$immune["ID"].'">
							      		<button type="submit" class="btn btn-primary btn-md" name="editImm">حفظ</button>
							      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow('.$immune["ID"].');">إلغاء</button>
								    </td>
							      </tr>
						      	</form>');		
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<div id="Pregnancy"
	 <?php
		 if (isset($view)) {
		 	if ($view === "Imm") {
		 		echo('style="display: none;"');
		 	}
		 }
		 else{
		 		echo('style="display: none;"');	
		 } ?> >
		<table class="table table-bordered">
		 	<thead>
		      <tr>
		      	<th colspan="3" id="toSpread+">
		      		<h5  class="text-right">معلومات حمل وولادة  ال<?= $animal ?> رقم <?= $id ?></h5> 		
		      	</th>
		      	<th id="toHide+" style="width: 17em;" class="text-center">	  		
		      		<button type="submit" class="btn btn-primary btn-md text-left" onclick="toggleRow('+'); colspan('+', '4');">إضافة معلومات الحمل والولادة</button>
		      	</th>
		      </tr>
		      <tr>
			      <th scope="col" class="text-center">عدد مرات الحمل</th>
			      <th scope="col" class="text-center">شهر الحمل</th>
			      <th scope="col" class="text-center">شهر الولادة</th>   
			      <th scope="col" class="text-center">إضافة/تعديل</th>      
		      </tr>
		 	</thead>
		 	<tbody>
		      	<form method="POST">
			      <tr id="toShow+" style="display: none;">
			      	<td>
			      		<input type="number" min="0" class="form-control text-center" name="Preg_count">
					</td>
					<td>
			     		<input type="month" class="form-control text-center" name="Preg_month" value="<?=date('Y-m')?>">
			     	</td>
			     	<td>
			     		<input type="month" class="form-control" name="Birth_month" value="<?=date('Y-m');?>">
		      		</td>
				    <td align="center">
			      		<button type="submit" class="btn btn-primary btn-md" name="addPreg">إضافة</button>
			      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow('+'); colspan('+' ,'3');">إلغاء</button>
				    </td>
				  </tr>
		      	</form>	
			<?php 
				$pregs = loadPregs($pdo, $id, $type);
				if(!$pregs || count($pregs)<1){
					echo('  <tr id="toHide+">
					<th colspan="4">
		      			<h6  class="text-center">لا يوجد  معلومات حمل وولادة لهذه ال'.$animal.'.</h6> 		
		      		</th>
		      </tr>'."\n");
				}
				else {
					foreach ($pregs as $preg) {
					echo('
					    <tr id="toHide'.$preg["ID"].'+">
					      <td class="text-center">'.$preg["Preg_count"].'</td>
					      <td class="text-center">'.$preg["Preg_month"].'</td>
					      <td class="text-center">'.$preg["Birth_month"].'</td>
						  <td align="center">
						      <form method="POST">
								<input type="hidden" name="ID" value="'.$preg["ID"].'">
					      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow(\''.$preg["ID"].'+\')">تعديل</button>
					      		<button type="submit" class="btn btn-primary btn-md" onclick="return confirmDelPreg('.$id.', \''.$animal.'\');" name="deletePreg">حذف</button>
					      	  </form>
						  </td>
					    </tr>'
					    );
					    echo('    
					      <tr style="display: none;" id="toShow'.$preg["ID"].'+">
					      	<form method="POST">
						      	<td>
						      		<input type="number" class="form-control text-center" name="Preg_count" value="'.$preg['Preg_count'].'">
								</td>
								<td>
						     		<input type="month" class="form-control text-center" name="Preg_month" value="'.$preg['Preg_month'].'">
						     	</td>
						     	<td>
						     		<input type="month" class="form-control" name="Birth_month" value="'.$preg['Birth_month'].'">
					      		</td>
							    <td align="center">
									<input type="hidden" name="ID" value="'.$preg["ID"].'">
						      		<button type="submit" class="btn btn-primary btn-md" name="editPreg">حفظ</button>
						      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow(\''.$preg["ID"].'+\');">إلغاء</button>
							    </td>
					      	</form>		
					      </tr>');
					}
				}
			?>
			</tbody>		
		</table>
		<br>
		<table class="table table-bordered">
		 	<thead>
		      <tr>
		      	<th colspan="6" id="toSpread-">
		      		<h5  class="text-right">أولاد ال<?= $animal ?> رقم <?= $id ?></h5> 		
		      	</th>
		      	<th id="toHide-" style="width: 17em;" class="text-center">	  		
		      		<button type="submit" class="btn btn-primary btn-md text-left" onclick="toggleRow('-'); colspan('-', '7')">إضافة  ابن جديد</button>
		      	</th>
		      </tr>
		      <tr>
			      <th scope="col" class="text-center">رقم ال<?= $animal ?></th>
			      <th scope="col" class="text-center">العمر</th>
			      <th scope="col" class="text-center">الوزن</th>
			      <th scope="col" class="text-center">الأكل</th>
			      <th scope="col" class="text-center">النوع</th>
			      <th scope="col" class="text-center">تاريخ الدخول</th>
			      <th scope="col" class="text-center">إضافة / حذف</th>      
		      </tr>
		 	</thead>
		 	<tbody>
		      	<form method="POST">
			      <tr id="toShow-" style="display: none;" class="text-center">
			    	<td>
						<?php 
							if ($type === "cow") {
								$options = loadCows($pdo);
							}
							else {
								$options = loadGoats($pdo);
							}
							echo('<select class="form-control" name="ID">');
							foreach ($options as $option) {
								echo('
					       			<option value="'.$option['ID'].'">'.$option['ID'].'</option>');
							}
							echo('</select>');
						?>
					</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td>-</td>
				    <td align="center">
			      		<button type="submit" class="btn btn-primary btn-md" name="addChild">إضافة</button>
			      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow('-'); colspan('-' ,'6')">إلغاء</button>
				    </td>
				  </tr>
		      	</form>	
			<?php 
				$children = loadChildren($pdo, $id, $type);
				if(!$children || count($children)<1){
					echo('  <tr id="toHide-">
					<th colspan="7">
		      			<h6  class="text-center">لا يوجد أبناء لهذه ال'.$animal.'</h6> 		
		      		</th>
		      </tr>'."\n");
				}
				else {
					foreach ($children as $child) {
						if ($child['Gender'] === "أنثى") {
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
					    <tr id="toHide'.$child["ID"].'-">
						      <th scope="row" class="text-center"><a style="display: block;text-decoration:none;" href="immune.php?id='.$child['ID'].'&type='.$type.'">'.$child['ID'].'</a></th>
						      <td class="text-center">'.$child['Age'].'</td>
						      <td class="text-center">'.$child['Weight'].'</td>
						      <td class="text-center">'.$child['Food'].'</td>
						      <td class="text-center">'.$child['Gender'].'</td>
						      <td class="text-center">'.date('Y-m-d', strtotime($child['inDate'])).'</td>
						      <td align="center">
							      <form method="POST">
									<input type="hidden" name="ID" value="'.$child["ID"].'">
						      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow(\''.$child["ID"].'-\')">تعديل</button>
						      		<button type="submit" class="btn btn-primary btn-md" onclick="return confirmDelChild('.$child['ID'].');" name="deleteChild">حذف من الأبناء</button>
						      	  </form>
						  	  </td>
					    </tr>'
					    );
					    echo('    
					      <tr style="display: none;" id="toShow'.$child["ID"].'-">
					      <form method="POST">
							    <th scope="row" class="text-center">'.$child['ID'].'</th>
						      	<td align="center">
								    <input type="number" min="0" max="100" class="form-control " name="Age" value="'.$child['Age'].'" style="max-width: 7em;">
							    </td>
						      	<td align="center"> 
							    	<input type="number" min="0" max="1000" class="form-control" name="Weight" value="'.$child['Weight'].'" style="max-width: 7em;">
							    </td>
						      	<td align="center"> 
							    	<input type="number" step="any" min="0" max="10" class="form-control" name="Food" value="'.$child['Food'].'" style="max-width: 7em;">
							    </td>
							    <td align="center">
							    	<select id="Gender" class="form-control" name="Gender">
						       			<option value="'.$value.'" selected>'.$child['Gender'].'</option>
						        		<option value="'.$other_value.'">'.$other_gender.'</option>
						      		</select>	    
							    </td>
						      	<td align="center">
							    	<input type="date" class="form-control" name="inDate" value="'.date('Y-m-d', strtotime($child['inDate'])).'" style="max-width: 10em;" required>
							    </td>
							    <td align="center">
									<input type="hidden" name="ID" value="'.$child["ID"].'">
						      		<button type="submit" class="btn btn-primary btn-md" name="editChild">حفظ</button>
						      		<button type="button" class="btn btn-primary btn-md" onclick="toggleRow(\''.$child["ID"].'-\');">إلغاء</button>
							    </td>
							</tr>
					      </form>		
					      ');
					}
				}
			?>
			</tbody>		
		</table>
	</div>
	</div>
	<script type="text/javascript">
		function confirmDelImm(id) {
		    if (!confirm("حذف تحصين " + id + "؟")) {
		    	return false;
		    }
		}

		function confirmDelPreg(id, animal) {
		    if (!confirm("حذف معلومات حمل وولادة ال" + animal + " رقم " + id + "؟")) {
		    	return false;
		    }
		}

		function confirmDelChild(id) {
		    if (!confirm("حذف الابن رقم  " + id + "؟")) {
		    	return false;
		    }
		}

		function showImmunes(){
			var toShow = document.getElementById("Immunes");
			var toHide = document.getElementById("Pregnancy");
			toHide.style.display = "none";
			toShow.style.display = "";
		}

		function showPregnancy(){
			var toShow = document.getElementById("Pregnancy");
			var toHide = document.getElementById("Immunes");
			toHide.style.display = "none";
			toShow.style.display = "";
		}

		function toggleRow(id) {
		  var toShow = document.getElementById("toShow"+id);
		  var toHide = document.getElementById("toHide"+id);
		  if (toShow.style.display == "none") {
		    toShow.style.display = "";
		  } else {
		    toShow.style.display = "none";
		  }
		  if (toHide.style.display == "none") {
		    toHide.style.display = "";
		  } else {
		    toHide.style.display = "none";
		  }
		}

		function colspan(id, cols) {
			var th = document.getElementById("toSpread"+id);
			th.colSpan = cols;
		}

	</script>
</body>
</html>