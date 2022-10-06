<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['name'])) {
		$valid = 0;
		$error_message .= 'Name can not be empty<br>';
	}
    if(empty($_POST['email'])) {
		$valid = 0;
		$error_message .= 'email can not be empty<br>';
	}
    if(empty($_POST['password'])) {
		$valid = 0;
		$error_message .= 'password can not be empty<br>';
	}
    if(empty($_POST['role'])) {
		$valid = 0;
		$error_message .= 'role can not be empty<br>';
	}

    $_POST['password'] = md5($_POST['password']);
    $_POST['status'] = 'Active';
    

	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must have to select a photo<br>';
    }

	if($valid == 1) {

		// getting auto increment id
		$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_user'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$ai_id=$row[10];
		}


		$final_name = $_POST['name'].$ai_id.'.'.$ext;
        move_uploaded_file( $path_tmp, '../assets/uploads/karyawan/'.$final_name );

	
		$statement = $pdo->prepare("INSERT INTO tbl_user (email,password,photo,role,status,name) VALUES (?,?,?,?,?,?)");
		$statement->execute(array($_POST['email'],$_POST['password'],$final_name,$_POST['role'],$_POST['status'],$_POST['name']));
			
		$success_message = 'User is added successfully!';

		unset($_POST['email']);
        unset($_POST['password']);
        unset($_POST['photo']);
        unset($_POST['status']);
        unset($_POST['name']);
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Users</h1>
	</div>
	<div class="content-header-right">
		<a href="user.php" class="btn btn-primary btn-sm">View All</a>
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
							<label for="" class="col-sm-2 control-label">Name <span>*</span></label>
							<div class="col-sm-6">
								<input type="text" autocomplete="off" class="form-control" name="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];} ?>">
							</div>
						</div>
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Email <span>*</span></label>
							<div class="col-sm-6">
								<input type="email" autocomplete="off" class="form-control" name="email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>">
							</div>
						</div>
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Password <span>*</span></label>
							<div class="col-sm-6">
								<input type="password" autocomplete="off" class="form-control" name="password" value="<?php if(isset($_POST['password'])){echo $_POST['password'];} ?>">
							</div>
						</div>
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Role <span>*</span></label>
							<div class="col-sm-6">
								<select name="role" class="form-control">
                                    <option value="User">User</option>
                                    <option value="Admin">Admin</option>
                                </select>
							</div>
						</div>					
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
							<div class="col-sm-9" style="padding-top:5px">
								<input type="file" name="photo">(Only jpg, jpeg, gif and png are allowed)
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
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