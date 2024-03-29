<?php require_once('header.php'); ?>
<?php
$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$email              = $row['email'];
	$photo              = $row['photo'];
	$role               = $row['role'];
	$status             = $row['status'];
	$name               = $row['name'];
}
?>
<?php
if(isset($_POST['form1'])) {

		$valid = 1;

	    if(empty($_POST['email'])) {
	        $valid = 0;
	        $error_message .= 'Email address can not be empty<br>';
	    } else {
	    	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		        $valid = 0;
		        $error_message .= 'Email address must be valid<br>';
		    }
	    }

        if(empty($_POST['name'])) {
	        $valid = 0;
	        $error_message .= 'name can not be empty<br>';
	    }
        if(empty($_POST['role'])) {
	        $valid = 0;
	        $error_message .= 'role can not be empty<br>';
	    }

	    if($valid == 1) {
			// updating the database
			$statement = $pdo->prepare("UPDATE tbl_user SET email=?,name=?,role=? WHERE id=?");
			$statement->execute(array($_POST['email'],$_POST['name'],$_POST['role'],$_REQUEST['id']));

	    	$success_message = 'General information updated is updated successfully.';
	    }
}

if(isset($_POST['form2'])) {

	$valid = 1;

	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' && $ext!='JPG' && $ext!='PNG' && $ext!='JPEG' && $ext!='GIF' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    }

    if($valid == 1) {

    	// removing the existing photo
    	unlink('../assets/uploads/karyawan/'.$photo);

    	// updating the data
    	$final_name = $name.$_REQUEST['id'].'.'.$ext;
        move_uploaded_file( $path_tmp, '../assets/uploads/karyawan/'.$final_name );
        $photo = $final_name;

        // updating the database
		$statement = $pdo->prepare("UPDATE tbl_user SET photo=? WHERE id=?");
		$statement->execute(array($final_name,$_REQUEST['id']));

        $success_message = 'User Photo is updated successfully.';
    	
    }
}

if(isset($_POST['form3'])) {
	$valid = 1;

	if( empty($_POST['password']) || empty($_POST['re_password']) ) {
        $valid = 0;
        $error_message .= "Password can not be empty<br>";
    }

    if( !empty($_POST['password']) && !empty($_POST['re_password']) ) {
    	if($_POST['password'] != $_POST['re_password']) {
	    	$valid = 0;
	        $error_message .= "Passwords do not match<br>";	
    	}        
    }

    if($valid == 1) {

    	$_POST['password'] = md5($_POST['password']);

    	// updating the database
		$statement = $pdo->prepare("UPDATE tbl_user SET password=? WHERE id=?");
		$statement->execute(array(md5($_POST['password']),$_REQUEST['id']));

    	$success_message = 'User Password is updated successfully.';
    }
}
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$email              = $row['email'];
	$photo              = $row['photo'];
	$role               = $row['role'];
	$status             = $row['status'];
	$name               = $row['name'];
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit <?php echo $name ?> Profile</h1>
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
				
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_1" data-toggle="tab"><strong>Update General Information</strong></a></li>
						<li><a href="#tab_2" data-toggle="tab">Update Photo</a></li>
						<li><a href="#tab_3" data-toggle="tab">Update Password</a></li>
					</ul>
					<div class="tab-content">
          				<div class="tab-pane active" id="tab_1">
							
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Email Address <span>*</span></label>
										<div class="col-sm-4">
											<input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
										</div>										
									</div>
                                    <div class="form-group">
										<label for="" class="col-sm-2 control-label">Name <span>*</span></label>
										<div class="col-sm-4">
											<input type="text" class="form-control" name="name" value="<?php echo $name; ?>" autocomplete="off">
										</div>										
									</div>
                                    <div class="form-group">
										<label for="" class="col-sm-2 control-label">Role <span>*</span></label>
										<div class="col-sm-4">
											<select name="role" class="form-control">
                                                <option value="User"  <?php if($role== 'User'){echo 'selected';} ?> >User</option>
                                                <option value="Admin"  <?php if($role== 'Admin'){echo 'selected';} ?> >Admin</option>
                                            </select>
										</div>										
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form1">Update Information</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_2">
							<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">Existing Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
							                <img src="../assets/uploads/karyawan/<?php echo $photo; ?>" class="existing-photo" width="140">
							            </div>
							        </div>
									<div class="form-group">
							            <label for="" class="col-sm-2 control-label">New Photo</label>
							            <div class="col-sm-6" style="padding-top:6px;">
							                <input type="file" name="photo">
							            </div>
							        </div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form2">Update Photo</button>
										</div>
									</div>
								</div>
							</div>
							</form>
          				</div>
          				<div class="tab-pane" id="tab_3">
							<form class="form-horizontal" action="" method="post">
							<div class="box box-info">
								<div class="box-body">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Password </label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="password">
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Retype Password </label>
										<div class="col-sm-4">
											<input type="password" class="form-control" name="re_password">
										</div>
									</div>
							        <div class="form-group">
										<label for="" class="col-sm-2 control-label"></label>
										<div class="col-sm-6">
											<button type="submit" class="btn btn-success pull-left" name="form3">Update Password</button>
										</div>
									</div>
								</div>
							</div>
							</form>

          				</div>
          			</div>
				</div>			

		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>