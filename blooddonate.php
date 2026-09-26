<?php 
require 'file/connection.php';
session_start();
require 'file/csrf.php';
  if(!isset($_SESSION['rid']))
  {
    header('location:login.php');
    exit();
  }
  else {
    $rid = (int)$_SESSION['rid'];
    $stmt = $conn->prepare("SELECT blooddonate.*, hospitals.* FROM blooddonate JOIN hospitals ON blooddonate.hid = hospitals.id WHERE blooddonate.rid = ?");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<style>
    body{
    background: url(image/p4.jpg) no-repeat center;
    background-size: cover;
    min-height: 0;
    height: 650px;
  }
.login-form{
    width: calc(100% - 20px);
    max-height: 650px;
    max-width: 450px;
    background-color: white;
}
.footer {​​
position: fixed;
left: 0;
bottom: 0;
width: 100%;
background-color: white;
color: black;
text-align: center;
}​​
</style>
<?php $title="Bloodbank | Blood Donate"; ?>
<?php require 'head.php'; ?>
<body>
	<?php require 'header.php'; ?>
	<div class="container cont">

		<?php require 'message.php'; ?>

	<table class="table table-responsive table-striped rounded mb-5">
		<tr><th colspan="9" class="title">Blood Donate</th></tr>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Email</th>
			<th>City</th>
			<th>Phone</th>
			<th>Blood Group</th>
			<th>Status</th>
			<th colspan="2">Action</th>
		</tr>

		    <div>
                <?php
                if ($result) {
                    $row =mysqli_num_rows( $result);
                    if ($row) { //echo "<b> Total ".$row." </b>";
                }else echo '<b style="color:white;background-color:red;padding:7px;border-radius: 15px 50px;">No one has requested yet. </b>';
            }
            ?>
            </div>

		<?php while($row = mysqli_fetch_array($result)) { ?>

		<tr>
			<td><?php echo ++$counter;?></td>
			<td><?php echo $row['hname'];?></td>
			<td><?php echo $row['hemail'];?></td>
			<td><?php echo $row['hcity'];?></td>
			<td><?php echo $row['hphone'];?></td>
			<td><?php echo $row['bg'];?></td>
<td><?php echo 'You have '.$row['status'];?></td>
			<td><?php if($row['status'] == 'Accepted'){ ?> <a href="" class="btn btn-success disabled">Accepted</a> <?php }
			else{ ?>
				<form action="file/acceptd.php" method="post" style="display:inline">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="donoid" value="<?php echo $row['donoid'];?>">
					<button type="submit" class="btn btn-success">Accept</button>
				</form>
			<?php } ?>
			</td>
			<td><?php if($row['status'] == 'Rejected'){ ?> <a href="" class="btn btn-danger disabled">Rejected</a> <?php }
			else{ ?>
				<form action="file/rejectd.php" method="post" style="display:inline">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="donoid" value="<?php echo $row['donoid'];?>">
					<button type="submit" class="btn btn-danger">Reject</button>
				</form>
			<?php } ?>
			</td>
			
		</tr>
		<?php } ?>
	</table>

</div>
<?php require 'footer.php'; ?>
</body>
</html>
<?php } ?>