<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<h3>Please choose what kind of account you'd like to open and how much money you will be depositing today</h3>
<h6>Note: In order to open a new account you must deposit at least $5</h6>
<form method="POST">
	<label>Account Type:</label>
	<select name="account_type">
		<option value="0">Checking</option>
		<option value="1">Savings</option>
		<option value="2">CD</option>
		<option value="3">IRA</option>
	</select>
	<label>Initial Deposit:</label>
	<input type="number" min="0.00" step="0.01" name="balance"/>
	<input type="submit" name="save" value="Create"/>
</form>


<?php
if(isset($_POST["save"]) && ($_POST["balance"]>=5)){
	$account = rand(10000000, 99999999);
	$account = sprintf("%012d", $account); // Adding 4 leading 0's infront of all accounts. It's my bank's thing
	$account_type = $_POST["account_type"];
	$balance = $_POST["balance"];
	$opened_date = date('Y-m-d H:i:s');//calc
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, balance, opened_date, user_id) VALUES(:account_number, :account_type, :balance, :opened_date, :user)");
	$r = $stmt->execute([
		":account_number"=>$account,
		":account_type"=>$account_type,
		":balance"=>$balance,
		":opened_date"=>$opened_date,
		":user"=>$user
	]);
	if($r){
		//FIRST create the accounts page
		//TODO create a transaction into this account from world account. update each account accordingly.
		//TODO redirect user to accounts page (i should probably create the accounts page)
		flash("Created your $account_type successfuly!");
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating your account. Please contact us at 222-222-2222 ");
	}
}
elseif ($_POST["balance"]<5)){
	flash("Please deposit at least $5 to open your account");
}
?>
<?php require(__DIR__ . "/partials/flash.php");
