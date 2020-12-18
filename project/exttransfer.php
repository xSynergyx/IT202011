<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be signed in to access this page");
    die(header("Location: login.php"));
}
?>

<?php
if (isset($_SESSION["user"])) {
	$email = $_SESSION["user"]["email"];
}
//getting all the user's accounts
if (!empty($email)) {
	$db = getDB();
	$stmt = $db->prepare("SELECT acc.account_number FROM Accounts as acc JOIN Users on acc.user_id = Users.id WHERE Users.email =:email");
   	$r = $stmt->execute([":email" => "$email"]);
   	if ($r) {
       		 $accResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
   	}
   	else {
       		 flash("There was a problem trying to view your account. Please contact us at 222-222-2222");
   	}
}
?>
    <h3>Transfer</h3>
    <form method="POST">
        <label>From</label>
        <select name="accountsrc">
		<?php foreach ($accResults as $ar): ?>
		<option value="<?php safer_echo($ar["account_number"]); ?>"><?php safer_echo($ar["account_number"]); ?></option>
		<?php endforeach; ?>
	</select>
	<label>Receiver's Last Name</label>
	<input type="text" name="lastname"/>
	<label>Last Four Digits of Receiver's Account</label>
	<input type="number" name="accountdigits"/>
	<label>Amount</label>
        <input type="number" min="0.01" step="0.01" name="amount"/>
	<label>Memo</label>
	<input type="text" name="memo" placeholder="Message to other person"/>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    $src = $_POST["accountsrc"];
    $digits = $_POST["accountdigits"];
    $last = $_POST["lastname"];
    $dest = "";
    $amount = $_POST["amount"];
    $type = "Ext-transfer";
    $memo = $_POST["memo"];
    $user = get_user_id();
    $created = date('Y-m-d H:i:s');

    $db = getDB();

    //Looking for the destination account
    $stmt = $db->prepare("SELECT acc.account_number FROM Accounts as acc JOIN Users on acc.user_id = Users.id JOIN Names as n on n.user_id = Users.id WHERE acc.account_number LIKE :digits AND n.last_name = :last LIMIT 1");
    $r = $stmt->execute([":digits" => "%$digits", ":last" => "$last"]);
    if ($r){
	$resultExt = $stmt->fetch(PDO::FETCH_ASSOC);
	$dest = $resultExt["account_number"];
    }

    //Cancel transaction if we cannot find the destination account
    if (empty($dest)){
	flash("Could not find the account you are looking for. Please try again.");
	die(header("Location: exttransfer.php"));
    }

    //calculating each total
    $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acct");
    $r = $stmt->execute([":acct" => $src]);
    $resultSrc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultSrc){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a1total = $resultSrc["balance"];
    //checking for enough funds
    if ($amount > $a1total){
	flash("Cannot transfer more funds than are available in the source account. Please try again.");
	die(header("Location: transfer.php"));
    }
    $src = $resultSrc["id"]; //changing $src to id for inserting transaction details

    $r = $stmt->execute([":acct" => $dest]);
    $resultDest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultDest){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a2total = $resultDest["balance"];
    $dest = $resultDest["id"];

    $a1total -= $amount;
    $a2total += $amount;
    $amount = $amount * -1;

    $stmt = $db->prepare("INSERT INTO Transactions (act_src_id, act_dest_id, amount, action_type, memo, expected_total, created) VALUES(:p1a1, :p1a2, :p1amount, :type, :memo, :a1total, :created), (:p2a1, :p2a2, :p2amount, :type, :memo, :a2total, :created)"); // TODO insert both transactions into table (p1 to p2 and p2 to p1)
    $r = $stmt->execute([
        ":p1a1" => $src,
        ":p1a2" => $dest,
        ":p1amount" => $amount,
        ":type" => $type,
        ":memo" => $memo,
	":a1total" => $a1total,
	":created" => $created,

	":p2a1" => $dest, //switched accounts
        ":p2a2" => $src,
        ":p2amount" => ($amount*-1),
        ":type" => $type,
        ":memo" => $memo,
	":a2total" => $a2total,
	":created" => $created
    ]);
    if ($r) {
        //nothing
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }

    //Updating each account
    $stmt = $db->prepare("UPDATE Accounts set balance=:balance WHERE id=:id");
    $r = $stmt->execute([
	":balance" => $a1total,
	":id" => $src
    ]);

    $r2 = $stmt->execute([
	":balance" => $a2total, //world account
	"id" => $dest
    ]);

    if($r && $r2) {
	flash("Succesfully completed your $type!");
    }
    else{
	 flash("Error updating your account balance");
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
