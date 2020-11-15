<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST">
	<label>Account Number</label>
	<input type="number"  name="account_number" />
	<label>Account Type</label>
	<select name="account_type">
		<option value="0">Checking</option>
		<option value="1">Savings</option>
		<option value="2">Certificate of Deposit</option>
		<option value="3">IRA</option>
	</select>
	<label>Balance</label>
	<input type="number" min="0.01" step="0.01"  name="balance"/>
	<input type="submit" name="save" value="Create"/>
</form>


<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$account = $_POST["account_number"];
	$account_type = $_POST["account_type"];
	$balance = $_POST["balance"];
	$opened_date = date('Y-m-d H:i:s');//calc
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, balance, opened_date, user_id) VALUES(:account_number, :account_type, :balance, :opened_date, :user)");
	$r = $stmt->execute([
		":accoun_number"=>$account,
		":account_type"=>$account_type,
		":balance"=>$balance,
		":opened_date"=>$opened_date,
		":user"=>$user
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/partials/flash.php");
