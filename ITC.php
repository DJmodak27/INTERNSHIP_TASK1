<?php
require "GUMP/gump.class.php";
require "db_connect.php";


if(isset($_POST['gp_vc'],$_POST['gp_vcbuilding'],$_POST['powersupply'],$_POST['functionalcomputer'],$_POST['functionalprinter'],$_POST['functionalups'],$_POST['activeinternet'],$_POST['submit']))
{
  $validator=new GUMP();
  $gp_vc = $_POST["gp_vc"];
  $gp_vcbuilding = $_POST["gp_vcbuilding"];
  $powersupply = $_POST["powersupply"];
  $functionalcomputer = $_POST["functionalcomputer"];
  $functionalprinter = $_POST["functionalprinter"];
  $functionalups = $_POST["functionalups"];
  $activeinternet = $_POST["activeinternet"];

$_POST = array(
  'gp_vc' => $gp_vc,
  'gp_vcbuilding' =>$gp_vcbuilding,
  'powersupply' =>$powersupply,
  'functionalcomputer' =>$functionalcomputer,
  'functionalprinter' =>$functionalprinter,
  'functionalups' =>$functionalups,
  'activeinternet' =>$activeinternet);
  
$_POST=$validator->sanitize($_POST);

$rules = array( 
  'gp_vc' => 'required',
  'gp_vcbuilding' =>'required|boolean',
  'powersupply' =>'required|boolean',
  'functionalcomputer' =>'required',
  'functionalprinter' =>'required',
  'functionalups' =>'required',
  'activeinternet' =>'required|boolean');


// $validator->set_fields_error_messages([
//   'gp_vc'      => ['required' => 'Fill the Name of GP/VC field please, its required.'],
//   'gp_vcbuilding'   => ['required' => 'Fill the Total No. of LI (irrigation) schemes available field please, its required.'],
//   'powersupply'     => ['required' => 'Fill the Name of LI schemes field please, its required.'],
//   'functionalcomputer'  => ['required' => 'Fill the Total No. of DTW (irrigation) schemes available field please, its required.'],
//   'functionalprinter' => ['required' => 'Fill the Name of DTW schemes field please, its required.'],
//   'functionalups'    => ['required' => 'Fill the Name of Part time pump operator engaged by the Panchayat Department field please, its required.'],
//   'activeinternet'             => ['required' => 'Fill the Name of Part time pump operator engaged by the PWD (WR) Department field please, its required.']]);

$validated=$validator->validate($_POST,$rules);

if($validated===TRUE)
{
  $sql =$conn->query("SELECT gp_vc_id from jurisdiction_gp_vc WHERE gp_vc_name  = '$gp_vc'");
//   $sql->execute(array(":gp_vc"=>$gp_vc));
  
while($rows = $sql->fetch(PDO::FETCH_ASSOC)){
  $id = $rows['gp_vc_id'];
}

    $sql=$conn->query("SELECT count(*) as 'cnt' from ict_status_gp_vc where gp_vc_id  = $id");
    // $sql->execute(array(
    //   $id
    // ));
    
    // $result1 =  mysqli_query($conn, $sql);
while($row = $sql->fetch(PDO::FETCH_ASSOC))
{
// print_r($row);

  if ($row['cnt'] > 0 ){
    try {
      $conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();
      $update = $conn->prepare("UPDATE `ict_status_gp_vc` SET `available_office_building` = :gp_vcbuilding, `available_power_service_connection_office` = :powersupply, `no_available_computer` = :functionalcomputer, `no_available_printers` = :functionalprinter, `no_available_UPS` = :functionalups, `available_internet_connection` = :activeinternet   WHERE `gp_vc_name` = :gp_vc");
      $update->execute(array(
        ":gp_vc"=>$gp_vc,
        ":gp_vcbuilding" => $gp_vcbuilding,
        ":powersupply" => $powersupply,
        ":functionalcomputer" => $functionalcomputer,
        ":functionalprinter" => $functionalprinter,
        ":functionalups" => $functionalups,
        ":activeinternet" => $activeinternet
      ));
      $conn->commit();
    } catch (Exception $e){
      $conn->rollBack();
      echo $e->getMessage();
    }
    if ($update){
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Updated!</strong> Your record has been udpated.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
    }
  }
  else{
    try {
      $conn ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->beginTransaction();
      $insert = $conn->prepare("INSERT INTO ict_status_gp_vc (`gp_vc_id`,`gp_vc_name`, `available_office_building`, `available_power_service_connection_office`, `no_available_computer`, `no_available_printers`, `no_available_UPS`, `available_internet_connection`) VALUES (:gp_vc_id,:gp_vc,:gp_vcbuilding,:powersupply,:functionalcomputer,:functionalprinter,:functionalups,:activeinternet)");
      $insert->execute([
      ":gp_vc_id"=>$id,
      ":gp_vc"=>$gp_vc,
      ":gp_vcbuilding" => $gp_vcbuilding,
      ":powersupply" => $powersupply,
      ":functionalcomputer" => $functionalcomputer,
      ":functionalprinter" => $functionalprinter,
      ":functionalups" => $functionalups,
      ":activeinternet" => $activeinternet 
    ]);
      $conn->commit();
    } catch (Exception $e){
      $conn->rollBack();
      echo $e->getMessage();
    }
    if($insert){
      echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
      <strong>Inserted!</strong> Your record has been inserted.
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
    }
}
}
}if($validator->errors()){
  echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
      <strong>REQUIRED!</strong> Please fill all the credentials.
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
  // var_dump($validator);
}
}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>

    <title>Status of ICT and Panchayat Building</title>
    <style>
      input[type=submit] {
        background-color: blue;
        border: none;
        color: #fff;
        padding: 15px 30px;
        text-decoration: none;
        margin: 4px 2px;
        cursor: pointer;
      }
    </style>
</head>

<body>
    <?php
 $gp_vcErr = $gp_vcbuildingErr = $powersupplyErr = $functionalcomputerErr = $functionalprinterErr = $functionalupsErr = $activeinternetErr = "";
$gp_vc = $gp_vcbuilding = $powersupply = $functionalcomputer = $functionalprinter = $functionalups = $activeinternet = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  } 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["gp_vc"])) {
    $gp_vcErr = "Required";
  } else {
    $gp_vc = test_input($_POST["gp_vc"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$gp_vc)) {
      $gp_vcErr = "Only letters and white space allowed";
    }
  }
  
  if (empty($_POST["gp_vcbuilding"])) {
    $gp_vcbuildingErr = "Required";
  } else {
    $gp_vcbuilding = test_input($_POST["gp_vcbuilding"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$gp_vcbuilding)) {
      // $gp_vcbuildingErr = "Only letters and white space allowed";
    }
  }

  if (empty($_POST["powersupply"])) {
    $powersupplyErr = "Required";
  } else {
    $powersupply = test_input($_POST["powersupply"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$powersupply)) {
      $powersupplyErr = "Only letters and white space allowed";
    }
  }
  if (empty($_POST["functionalcomputer"])) {
    $functionalcomputerErr = "Required";
  } else {
    $functionalcomputer = test_input($_POST["functionalcomputer"]);
    // check if name only contains letters and whitespace
    $functionalcomputerErr = "Only numbers allowed";
  }
  if (empty($_POST["functionalprinter"])) {
    $functionalprinterErr = "Required";
  } else {
    $functionalprinter = test_input($_POST["functionalprinter"]);
    // check if name only contains letters and whitespace
    // if (!preg_match("/^[a-zA-Z-' ]*$/",$functionalprinter)) {
      $functionalprinterErr = "Only numbers allowed";
    // }
  }
  if (empty($_POST["functionalups"])) {
    $functionalupsErr = "Required";
  } else {
    $functionalups = test_input($_POST["functionalups"]);
    // check if name only contains letters and whitespace
    $functionalupsErr = "Only numbers allowed";
  }
  if (empty($_POST["activeinternet"])) {
    $activeinternetErr = "Required";
  } else {
    $activeinternet = test_input($_POST["activeinternet"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$activeinternet)) {
      // $activeinternetErr = "Only numbers allowed";
    }
  }
}


?>

    <div class="container my-4">
        <h2 class="text-center">Status of ICT and Panchayat Building</h2>
        <p style="color:red;"><span class="error" >* fields are required</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="mb-3">
                <label for="gp_vc" class="form-label">Name of GP/VC <a style="color:red;">*</a></label>
                <input type="text" class="form-control" id="gp_vc" name="gp_vc" value="<?php echo $gp_vc; ?>">
                <span class="error"><?php echo $gp_vcErr ?></span>
            </div>
            <div class="mb-3">
                <label for="gp_vcbuilding" class="form-label">Availability of GP/VC Office Building <a style="color:red;">*</a></label>
                <select class="form-select" aria-label="Default select example" id="gp_vcbuilding" name="gp_vcbuilding" value="<?php echo $gp_vcbuilding; ?>">
  <option selected>Select </option>
  <option value="1">Yes</option>
  <option value="0">No</option>
</select> 

                <span class="error"><?php echo $gp_vcbuildingErr ?></span>
            </div>
            <div class="mb-3">
                <label for="nli_schemes" class="form-label">Availability of Power Service Connection in GP/VC office <a style="color:red;">*</a></label>
                <select class="form-select" aria-label="Default select example" id="powersupply" name="powersupply" value="<?php echo $powersupply; ?>">
  <option selected>Select </option>
  <option value="1">Yes</option>
  <option value="0">No</option>
</select> 
               
                <span class="error"><?php echo $powersupplyErr ?></span>
            </div>
            <div class="mb-3">
                <label for="functionalcomputer" class="form-label">No of Functional Computer available <a style="color:red;">*</a></label>
                <input type="number" class="form-control" id="functionalcomputer" name="functionalcomputer" value="<?php echo $functionalcomputer; ?>">
            </div>
            <div class="mb-3">
                <label for="functionalprinter" class="form-label">No of Functional Printers available <a style="color:red;">*</a></label>
                <input type="number" class="form-control" id="functionalprinter" name="functionalprinter" value="<?php echo $functionalprinter; ?>">
                <span class="error"> <?php echo $functionalprinterErr ?></span>
            </div>
            <div class="mb-3">
                <label for="functionalups" class="form-label">No of Functional UPS available <a style="color:red;">*</a></label>
                <input type="number" class="form-control" id="functionalups" name="functionalups" value="<?php echo $functionalups; ?>">
                <span class="error"><?php echo $functionalupsErr ?></span>
            </div>
            <div class="mb-3">
                <label for="activeinternet" class="form-label">Availability of active internet connection in GP/VC office <a style="color:red;">*</a></label>
                <select class="form-select" aria-label="Default select example" id="activeinternet" name="activeinternet" value="<?php echo $activeinternet; ?>">
  <option selected>Select </option>
  <option value="1">Yes</option>
  <option value="0">No</option>
</select> 
                
                <span class="error"><?php echo $activeinternetErr ?></span>
            </div>
            <input type="submit" name="submit" value="Submit" class=""> 
        </form>
    </div>
    
    
</body>

</html>