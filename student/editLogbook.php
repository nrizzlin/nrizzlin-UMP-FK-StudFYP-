<?php

$db_host="localhost"; //localhost server 
$db_user="root";	//database username
$db_password="";	//database password   
$db_name="studfyp";	//database name

try
{
	$db=new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOEXCEPTION $e)
{
	$e->getMessage();
}

if(isset($_REQUEST['update_id']))
{
	try
	{
		$id = $_REQUEST['update_id']; //get "update_id" from index.php page through anchor tag operation and store in "$id" variable
		$select_stmt = $db->prepare('SELECT * FROM logbook WHERE logbook_id =:id'); //sql select query
		$select_stmt->bindParam(':id',$id);
		$select_stmt->execute(); 
		$row = $select_stmt->fetch(PDO::FETCH_ASSOC);
		extract($row);
	}
	catch(PDOException $e)
	{
		$e->getMessage();
	}
	
}

if(isset($_REQUEST['btn_update']))
{
	try
	{
        $date	= $_REQUEST['date'];	//textbox name "txt_name"
		$day	= $_REQUEST['day'];	//textbox name "txt_name"
        $activities	= $_REQUEST['txt_activities'];	//textbox name "txt_name"
		$note	= $_REQUEST['txt_note'];	//textbox name "txt_name"
		
		$pdf_file	= $_FILES["txt_file"]["name"];
		$type		= $_FILES["txt_file"]["type"];	//file name "txt_file"
		$size		= $_FILES["txt_file"]["size"];
		$temp		= $_FILES["txt_file"]["tmp_name"];
			
		$path="files/".$pdf_file; //set upload folder path
		
		$directory="files/"; //set upload folder path for update time previous file remove and new file upload for next use
		
		if($pdf_file)
		{
			if($type=="application/pdf" || $type=='image/jpeg' || $type=='image/png' ) //check file extension
			{	
				if(!file_exists($path)) //check file not exist in your upload folder path
				{
					if($size < 5000000) //check file size 5MB
					{
						unlink($directory.$row['log_file']); //unlink function remove previous file
						move_uploaded_file($temp, "files/" .$pdf_file);	//move upload file temperory directory to your upload folder	
					}
					else
					{
						$errorMsg="Your File To large Please Upload 5MB Size"; //error message file size not large than 5MB
					}
				}
				else
				{	
					$errorMsg="File Already Exists...Check Upload Folder"; //error message file not exists your upload folder path
				}
			}
			else
			{
				$errorMsg="Upload JPG, JPEG, PNG & GIF File Formate.....CHECK FILE EXTENSION"; //error message file extension
			}
		}
		else
		{
			$pdf_file=$row['log_file']; //if you not select new image than previous image sam it is it.
		}
	
		if(!isset($errorMsg))
		{
			$update_stmt=$db->prepare('UPDATE logbook SET log_date=:date_up, log_day=:day_up , log_activity=:act_up , log_file=:file_up ,  log_note=:note_up WHERE logbook_id=:id'); //sql update query
			$update_stmt->bindParam(':date_up',$date);
            $update_stmt->bindParam(':day_up',$day);
            $update_stmt->bindParam(':act_up',$activities);
            $update_stmt->bindParam(':file_up',$pdf_file);
			$update_stmt->bindParam(':note_up',$note);	//bind all parameter
			$update_stmt->bindParam(':id',$id);
			 
			if($update_stmt->execute())
			{
				$updateMsg="File Update Successfully.......";	//file update success message
				header("refresh:3;logbook.php");	//refresh 3 second and redirect to index.php page
			}
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
}

?>
	
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Logbook | Student</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
    <?php include('includes/header.php');?>
    <!-- LOGO HEADER END-->
    <?php 
// if($_SESSION['login']!="")
// {
    include('includes/menubar.php');
// }
    ?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">Proposal FYP </h1>

                    <?php
                        if(isset($errorMsg))
                        {
                    ?>
                        <div class="alert alert-danger">
                            <strong>WRONG ! <?php echo $errorMsg; ?></strong>
                        </div>
                    <?php
                        }
                        if(isset($insertMsg)){
                    ?>
                        <div class="alert alert-success">
                            <strong>SUCCESS ! <?php echo $insertMsg; ?></strong>
                        </div>
                    <?php
		            }
		            ?>  
                </div>
            </div>
            <div class="row" >
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <center>UPDATE LOGBOOK</center>
                        </div>
                        <font color="black" align="left">
                            <div class="panel-body">
                            <form class="form-inline" method="POST"  enctype="multipart/form-data">
                                    <table align="center" width="100%">

                                            <tr>
                                                <td><label class="control-label">Student ID : </label></td>
                                                <td><input type="text" name="txt_id" class="form-control" value="<?php echo $studID; ?>" readonly /></td>
                                            </tr>

                                            <tr>
                                                <td><label class="control-label">Date: </label></td>
                                                <td><input type="date" name="date" class="form-control" /></td>

                                            </tr>

                                            <tr>
                                                <td><label class="control-label">Day: </label></td>
                                                <td><input type="text" name="day" class="form-control" value="<?php echo $day;?>"/></td>

                                            </tr>

                                            <tr>
                                                <td><label class="control-label">Activities: </label></td>
                                                <td><textarea name="txt_activities" cols="50" rows="10" class="form-control" value="<?php echo $activities;?>"></textarea></td>
                                            </tr>

                                            <tr>
                                            <td><label class="control-label">Attachment: </label></td>
                                            <td>
                                                <input type="file" name="txt_file" class="form-control" />
                                                <p><a href="file_logbook/<?php echo $row['log_file']; ?>" download><?php echo $row['log_file']?></a></p>
                                            </td>
                                            </tr>
                                                
                                            <tr>
                                            <td><label class="control-label">Note: </label></td>
                                            <td><input type="text" name="txt_note" class="form-control" value="<?php echo $note;?>"/></td>
                                            
                                            </tr>
                                                
                                            <tr>
                                                    <td colspan="2">
                                                        <br>
                                                        <input type="submit"  name="btn_update" class="btn btn-primary" value="UPDATE">
                                                        <input type="reset"   name="btn_reset" class="btn btn-info "value="RESET">
                                                    </td>
                            
                                            </tr>
                                    </table>
		                    </form>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
  <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.11.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>

<?php 
// } 
?>
