 <!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<style type="text/css">
  body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #8E24AA, #b06ab3);
    color: #D7D7EF;
    font-family: 'Lato', sans-serif
}

h2 {
    margin: 50px 0
}

.file-drop-area {
    position: relative;
    display: flex;
    align-items: center;
    width: 450px;
    max-width: 100%;
    padding: 25px;
    border: 1px dashed rgba(255, 255, 255, 0.4);
    border-radius: 3px;
    transition: 0.2s
}

.choose-file-button {
    flex-shrink: 0;
    background-color: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    padding: 8px 15px;
    margin-right: 10px;
    font-size: 12px;
    text-transform: uppercase
}

.file-message {
    font-size: small;
    font-weight: 300;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
}

.file-input {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
    cursor: pointer;
    opacity: 0
}

.mt-100 {
    margin-top: 100px
} 

</style>
<body>  
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
 <?php
$fileloc='files/';
$file_folder='/var/www/html/rpi/files/'; //must be writeable and contain temp folder DO NOT STORE xlsx files in this folder, tey will be deleted!
$script="/home/kpavicic/zipaj.sh";
echo "<h2><p>Otključavanje xlsx datoteke</p></h2>
   <a href='xlsunp2.php' class='btn btn-primary'>Početak</a><br/>";
if (isset($_POST['unos']))
{
   $target_file = "files/original.zip"; //original file
   $FileType = strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
    if ($FileType=='xlsx')
    {
      if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
         echo "Datoteka ". basename( $_FILES["file"]["name"]). " je uspješno učitana.";
      } else {
         echo "Greška prilikom unosa datoteke.";
      }
      exec ("rm -r ".$file_folder."temp/*;");
      exec ("rm -r ".$file_folder."*.xlsx;");
      exec("unzip ".$file_folder."original.zip -d ".$file_folder."temp;");
      echo "<br/>Datoteka otpakirana";
      $files = scandir("files/temp/xl/worksheets");
      $i=3;
      while(isset($files[$i]))
      {
         $datot=fopen($fileloc."temp/xl/worksheets/".$files[$i], "r");
         $contents = fread($datot, filesize($fileloc."temp/xl/worksheets/".$files[$i]));
         $poc=strpos($contents, "<sheetProtection");
         fclose($datot);
         $kraj=strpos($contents, ">", $poc);
         $datot2=fopen($fileloc."temp/xl/worksheets/".$files[$i], "w");
         fwrite($datot2, $contents, $poc);
         fwrite($datot2, substr($contents, $kraj+1));
         fclose($datot2);
         $i++;
      }

      //echo $contents;
        exec("bash ".$script.";");
        exec("mv ".$file_folder."temp.zip ".$file_folder."'".$_FILES["file"]["name"]."';");
      echo "<br/>Datoteka zapakirana
      <br/> 
      <br/><a href='files/".$_FILES["file"]["name"]."'class='btn btn-primary'>Nova datoteka </a>";
    }
    else
    {
       echo "File is not an xlsx.";
       $uploadOk = 0;
    }
}
else
{

   echo "<form action='xlsunp2.php' method='POST' enctype='multipart/form-data'>Unesite datoteku: <input type='file' name='file'>
   <input type='submit' name='unos' value='Unesi'></form>";
}

?>


</body>

</html>





