<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['news_title'])) {
		$valid = 0;
		$error_message .= 'News title can not be empty<br>';
	} else {
		// Duplicate Checking
    	$statement = $pdo->prepare("SELECT * FROM tbl_newsemployee WHERE news_title=?");
    	$statement->execute(array($_POST['news_title']));
    	$total = $statement->rowCount();
    	if($total) {
    		$valid = 0;
        	$error_message .= "News title already exists<br>";
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
		$publisher = $_SESSION['user']['name'];
	} else {
		$publisher = $_POST['publisher'];	
	}


	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];


    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' && $ext!='JPG' && $ext!='PNG' && $ext!='JPEG' && $ext!='GIF' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }
	

	if($valid == 1) {

		// getting auto increment id for photo renaming
		$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_newsemployee'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$ai_id=$row[10];
		}


		if($path=='') {
			// When no photo will be selected
			$statement = $pdo->prepare("INSERT INTO tbl_newsemployee (news_title,news_content,news_content_short,news_date,photo,publisher) VALUES (?,?,?,?,?,?)");
			$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],'',$publisher));
		} else {
    		// uploading the photo into the main location and giving it a final name
    		$final_name = 'news-'.$ai_id.'.'.$ext;
            move_uploaded_file( $path_tmp, '../assets/uploads/newsKaryawan/'.$final_name );

            $statement = $pdo->prepare("INSERT INTO tbl_newsemployee (news_title,news_content,news_content_short,news_date,photo,publisher) VALUES (?,?,?,?,?,?)");
			$statement->execute(array($_POST['news_title'],$_POST['news_content'],$_POST['news_content_short'],$_POST['news_date'],$final_name,$publisher));
		}
	
		$success_message = 'News is added successfully!';
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add News For Employee</h1>
	</div>
	<div class="content-header-right">
		<a href="newsemployee.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


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
								<input type="text" class="form-control" name="news_title" placeholder="Example: News Headline">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Content <span>*</span></label>
							<div class="col-sm-8">
								<textarea class="form-control editor" name="news_content"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Content (Short) <span>*</span></label>
							<div class="col-sm-8">
								<textarea class="form-control" name="news_content_short" style="height:100px;"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">News Publish Date <span>*</span></label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="news_date" id="datepicker" value="<?php echo date('d-m-Y'); ?>">(Format: dd-mm-yy)
							</div>
						</div>
						<div class="form-group">
				            <label for="" class="col-sm-3 control-label">Featured Photo</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				                <input type="file" name="photo">
				            </div>
				        </div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Publisher </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="publisher"> (If you keep this blank, logged user will be treated as the publisher)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>