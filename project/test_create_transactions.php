<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
    <h3>Create Transaction</h3>
    <form method="POST">
        <label>Source Account ID</label>
        <input type="number" name="src" placeholder="1"/>
        <label>Destination Account ID</label>
        <input type="number" name="dest" placeholder="2"/>
        <label>Amount</label>
        <input type="number" min="0.01" step="0.01" name="amount"/>
        <label>Type</label>
        <select name="type">
		<option value="Deposit">Deposit</option>
		<option value="Withdraw">Withdraw</option>
		<option value="Transfer">Transfer</option>
	</select>
	<label>Memo</label>
	<input type="text" name="memo" placeholder="Message to other person"/>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $src = $_POST["src"];
    $dest = $_POST["dest"];
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $created = date('Y-m-d H:i:s');

    $db = getDB();

    //calculating each total
    //woops, calculated this the total with using SQL's sum function. Consider changing
    $stmt = $db->prepare("SELECT balance FROM Accounts WHERE Accounts.id = :acct");
    $r = $stmt->execute([":acct" => $src]);
    $resultSrc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultSrc){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a1total = $resultSrc["balance"];

    $r = $stmt->execute([":acct" => $dest]);
    $resultDest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$resultDest){
	$e = $stmt->errorInfo();
	flash($e[2]);
    }
    $a2total = $resultDest["balance"];

    switch($type){
	case "Deposit":
		$a1total += $amount;
		$a2total -= $amount;
		break;
	case "Withdraw":
		$a1total -= $amount;
		$a2total += $amount;
		$amount = $amount * -1;
		break;
	case "Transfer": //in the future. don't let them select destination account unless it's a transfer 
		$a1total -= $amount; //case 1 and 2 technically the same right now.
		$a2total += $amount;
		$amount = $amount * -1;
		break;
    }

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
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
