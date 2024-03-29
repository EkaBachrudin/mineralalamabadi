<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['news_title'])) {
		$valid = 0;
		$error_message .= 'News title can not be empty<br>';
	} else {
		// Duplicate Category checking
    	// current news title that is in the database
    	$statement = $pdo->prepare("SELECT * FROM tbl_newsemployee WHERE id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row) {
			$current_news_title = $row['news_title'];
		}

		$statement = $pdo->prepare("SELECT * FROM tbl_newsemployee WHERE news_title=? and news_title!=?");
    	$statement->execute(array($_POST['news_title'],$current_news_title));
    	$total = $statement->rowCount();							
    	if($total) {
    		$valid = 0;
        	$error_message .= 'News title already exists<br>';
    	}
	}

	if(empty($_POST['news_content'])) {
		$valid = 0;
		$error_message .= 'News content can not be empty<br>';
	}

	if(empty($_POST['news_content_short'])) {
		$valid = 0;
		$error_message .= 'News content (short) can not be empty<br>';
	}

	if(empty($_POST['news_date'])) {
		$valid = 0;
		$error_message .= 'News publish date can not be empty<br>';
	}

	if($_POST['publisher'] == '') {
		$publisher = $_SESSION['user']['full_name'];
	} else {
		$publisher = $_POST['publisher'];	
	}


	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    $previous_photo = $_POST['previous_photo'];

	if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' && $ext!='JPG' && $ext!='PNG' && $ext!='JPEG' && $ext!='GIF' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }

	if($valid == 1) {
		// If previous image not found and user do not want to change the photo
	    if($previous_photo == '' && $path == '') {
	    	$statement = $pdo->prepare("UPDATE tbl_newsemployee SET news_title=?, news_content=?, news_content_short=?, news_date=?, publisher=? WHERE id=?");
	    	$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],$publisher,$_REQUEST['id']));
	    }

		// If previous image found and user do not want to change the photo
	    if($previous_photo != '' && $path == '') {
	    	$statement = $pdo->prepare("UPDATE tbl_newsemployee SET news_title=?, news_content=?, news_content_short=?, news_date=?, publisher=? WHERE id=?");
	    	$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],$publisher,$_REQUEST['id']));
	    }


	    // If previous image not found and user want to change the photo
	    if($previous_photo == '' && $path != '') {

	    	$final_name = 'news-'.$_REQUEST['id'].'.'.$ext;
            move_uploaded_file( $path_tmp, '../assets/uploads/newsKaryawan/'.$final_name );

	    	$statement = $pdo->prepare("UPDATE tbl_newsemployee SET news_title=?, news_content=?, news_content_short=?, news_date=?, photo=?, publisher=? WHERE id=?");
	    	$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],$final_name,$publisher,$_REQUEST['id']));
	    }

	    
	    // If previous image found and user want to change the photo
		if($previous_photo != '' && $path != '') {

	    	unlink('../assets/uploads/newsKaryawan/'.$previous_photo);

	    	$final_name = 'news-'.$_REQUEST['id'].'.'.$ext;
            move_uploaded_file( $path_tmp, '../assets/uploads/newsKaryawan/'.$final_name );

	    	$statement = $pdo->prepare("UPDATE tbl_newsemployee SET news_title=?, news_content=?, news_content_short=?, news_date=?, photo=?, publisher=? WHERE id=?");
	    	$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],$final_name,$publisher,$_REQUEST['id']));
	    }

	    $success_message = 'News is updated successfully!';
	}
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_newsemployee WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit News</h1>
	</div>
	<div class="content-header-right">
		<a href="newsemployee.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_newsemployee WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$news_title         = $row['news_title'];
	$news_content       = $row['news_content'];
	$news_content_short = $row['news_content_short'];
	$news_date          = $row['news_date'];
	$photo              = $row['photo'];
	$publisher          = $row['publisher'];
}
?>

<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
			
			<p>
			<?php echo $error_message; ?>
			</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
			
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Title <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="news_title" value="<?php echo $news_title; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Content <span>*</span></label>
							<div class="col-sm-8">
								<textarea class="form-control editor" name="news_content"><?php echo $news_content; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Content (Short) <span>*</span></label>
							<div class="col-sm-8">
								<textarea class="form-control" name="news_content_short" style="height:100px;"><?php echo $news_content_short; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Publish Date <span>*</span></label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="news_date" id="datepicker" value="<?php echo $news_date; ?>">(Format: dd-mm-yy)
							</div>
						</div>
						<div class="form-group">
				            <label for="" class="col-sm-3 control-label">Existing Featured Photo</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				            	<?php
				            	if($photo == '') {
				            		echo 'No photo found';
				            	} else {
				            		echo '<img src="../assets/uploads/newsKaryawan/'.$photo.'" class="existing-photo" style="width:200px;">';	
				            	}
				            	?>
				                <input type="hidden" name="previous_photo" value="<?php echo $photo; ?>">
				            </div>
				        </div>
						<div class="form-group">
				            <label for="" class="col-sm-3 control-label">Change Featured Photo</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				                <input type="file" name="photo">
				            </div>
				        </div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Publisher </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="publisher" value="<?php echo $publisher; ?>"> (If you keep this blank, logged user will be treated as the publisher)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>