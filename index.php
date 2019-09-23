<?php 
  require_once "pdo.php";
  require_once "util.php";
  session_start();

  if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    return;
  }
  
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<title>Home</title>
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
      <li class="breadcrumb-item active" aria-current="page">الرئيسية</li>
    </ol>
  </nav>
 </header>
<div class="container" style="margin-top: 1em;">
  <?php 
    flashMessage();
  ?>
	<a style="text-decoration: none;" href="cow.php"><button style="margin-bottom: 0.5em;" type="button" class="btn btn-primary btn-lg btn-block">البقر</button></a>
	<a style="text-decoration: none;" href="goat.php"><button type="button" class="btn btn-secondary btn-lg btn-block">الماعز</button></a>

 <table class="table table-bordered" style="margin-top: 1em;">
  <thead>
    <tr>
      <th colspan="2" style="width: 15em;">
        <h5  class="text-right">تحصينات ال 30 يوم القادمين للبقر</h5>    
      </th>
    </tr>
    <tr>
      <th scope="col" class="text-center">رقم الحيوان</th>
      <th scope="col" class="text-center">نوع التحصين</th>
      <th scope="col" class="text-center">اسم التحصين</th>
      <th scope="col" class="text-center">مدة التحصين</th>
      <th scope="col" class="text-center">تاريخ التحصين</th>
    </tr>
  </thead>
  <tbody>
<?php 
  $cows = loadNearImms($pdo, "cow");
  if(!$cows || count($cows>0)){
    foreach ($cows as $cow) {
      echo('
        <tr>
          <th scope="row" class="text-center"><a style="display: block;text-decoration:none;" href="immune.php?id='.$cow['animal_ID'].'&type=cow">'.$cow['animal_ID'].'</a></th>
          <td class="text-center">'.$cow['Imm_type'].'</td>
          <td class="text-center">'.$cow['Imm_name'].'</td>
          <td class="text-center">'.$cow['Imm_duration'].' شهر</td>
          <td class="text-center">'.date('Y-m-d', strtotime($cow['Imm_date'])).'</td>
        </tr>'
        );
    }
  }
?>
</tbody>
</table>

 <table class="table table-bordered">
  <thead>
    <tr>
      <th colspan="2" style="width: 15em;">
        <h5  class="text-right">تحصينات ال 30 يوم القادمين للماعز</h5>    
      </th>
    </tr>
    <tr>
      <th scope="col" class="text-center">رقم الحيوان</th>
      <th scope="col" class="text-center">نوع التحصين</th>
      <th scope="col" class="text-center">اسم التحصين</th>
      <th scope="col" class="text-center">مدة التحصين</th>
      <th scope="col" class="text-center">تاريخ التحصين</th>
    </tr>
  </thead>
  <tbody>
<?php 
  $goats = loadNearImms($pdo, "goat");
  if(!$goats || count($goats>0)){
    foreach ($goats as $goat) {
      echo('
        <tr>
          <th scope="row" class="text-center"><a style="display: block;text-decoration:none;" href="immune.php?id='.$goat['animal_ID'].'&type=goat">'.$goat['animal_ID'].'</a></th>
          <td class="text-center">'.$goat['Imm_type'].'</td>
          <td class="text-center">'.$goat['Imm_name'].'</td>
          <td class="text-center">'.$goat['Imm_duration'].' شهر</td>
          <td class="text-center">'.date('Y-m-d', strtotime($goat['Imm_date'])).'</td>
        </tr>'
        );
    }
  }
?>
</tbody>
</table>

</div>
</body>
</html>