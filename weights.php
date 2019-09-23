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

  if (isset($_POST['edit'])) {
    if ($_POST['Weight'] == "" || $_POST['weightID'] == "" || $_POST['Weight_date'] == "") {
        $_SESSION['error'] = "من فضلك املأ كل الخانات قبل الضغط على زر التعديل!";
        header("Location: weights.php?id=".$id."&type=".$type);
        return;   
    }
    else {
      updateWeight($pdo, $_POST['weightID']);
      $_SESSION['success'] = "تم التعديل بنجاح.";
      header("Location: weights.php?id=".$id."&type=".$type);
      return;
    }
  }

  if (isset($_POST['delete']) && isset($_POST['weightID'])){
    deleteRow($pdo, $_POST['weightID'], "weights");
    $_SESSION['success'] = "تم حذف الوزن بنجاح.";
    header("Location: weights.php?id=".$id."&type=".$type);
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
  <a href="logout.php"><button class="btn btn-outline-light" type="submit">Logout</button></a>
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
      <li class="breadcrumb-item active" aria-current="page"> تعديلات الوزن</li>
    </ol>
  </nav>
 </header>
  <div class="container col-11" style="margin-top: 1em;">
<?php 
  flashMessage();
?>
    <table class="table table-bordered">
      <thead>
          <tr>
            <th colspan="3">
              <h5  class="text-center">أوزان ال<?= $animal ?> رقم <?= $id ?></h5>  
            </th>
          </tr>
            <tr>
            <th scope="col" class="text-center">التاريخ</th>
            <th scope="col" class="text-center">الوزن</th>
            <th scope="col" class="text-center">تعديل/حذف </th>
          </tr>
      </thead>
      <tbody>
        <?php 
          $weights = loadWeights($pdo, $id, $type);
          if(count($weights>0)){
            foreach ($weights as $weight) {
              echo('
                  <tr id="toHide'.$weight['ID'].'">
                    <td class="text-center">'.date('Y-m-d', strtotime($weight['edit_date'])).'</td>
                    <td class="text-center"> '.$weight['weight'].' </td>
                    <td align="center">
                      <form method="POST">
                      <input type="hidden" name="weightID" value="'.$weight["ID"].'">
                          <button type="button" class="btn btn-primary btn-md" onclick="toggleRow('.$weight['ID'].');">تعديل</button>
                          <button type="submit" class="btn btn-primary btn-md" onclick="return confirmDel('.$weight["weight"].')" name="delete">حذف</button>
                          </form>
                    </td>
                  </tr>
                ');
              echo('
                  <form method="POST">
                    <tr style="display: none;" id="toShow'.$weight['ID'].'">
                      <td align="center">
                        <input type="date" class="form-control" name="Weight_date" value="'.date('Y-m-d', strtotime($weight['edit_date'])).'" required>
                      </td>
                      <td align="center"> 
                        <input type="number" min="0" max="1000" class="form-control" name="Weight" value="'.$weight['weight'].'">
                      </td>
                      <td align="center">
                        <input type="hidden" name="weightID" value="'.$weight["ID"].'">
                            <button type="submit" class="btn btn-primary btn-md" name="edit">تأكيد</button>
                            <button type="button" class="btn btn-primary btn-md" onclick="toggleRow('.$weight['ID'].');" name="delete">إلغاء</button>
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
<script type="text/javascript">
  function confirmDel(id) {
    if (!confirm("حذف الوزن "+ id +" ؟")) {
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