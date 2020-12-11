<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $id2 = $id + 1;

    $db = getDB();
    $stmt = $db->prepare("SELECT amount, expected_total FROM Transactions where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $result["expected_total"] - $result["amount"]; //calculating what the previous balance used to be

    $stmt = $db->prepare("SELECT amount, expected_total FROM Transactions where id = :id2");
    $r = $stmt->execute([":id2" => $id2]);
    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance2 = $result2["expected_total"] - $result2["amount"];
}
/*
else {
    flash("ID isn't set, we need an ID in order to update");
}
*/
?>
<?php
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $amount = $_POST["amount"];
    $amount2 = $amount *-1;
    $a1total = $balance + $amount;
    $a2total = $balance2 + $amount2;
    $type = $_POST["type"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE Transactions set amount=:amount, action_type=:type, memo=:memo, expected_total=:a1total where id=:id");
        $r = $stmt->execute([
            ":amount" => $amount,
            ":type" => $type,
            ":memo" => $memo,
	    ":a1total" => $a1total,
            ":id" => $id
        ]);
	$stmt = $db->prepare("UPDATE Transactions set amount=:amount2, action_type=:type, memo=:memo, expected_total=:a2total where id=:id2");
        $r2 = $stmt->execute([
            ":amount2" => $amount2,
            ":type" => $type,
            ":memo" => $memo,
	    ":a2total" => $a2total,
            ":id2" => $id2
        ]);
        if ($r && $r2) {
            flash("Updated successfully with id: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>

<?php
//fetching the transaction
$result = [];
$result2 = []; //second transaction
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Transactions where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $id2 = $id + 1;
    $stmt = $db->prepare("SELECT * FROM Transactions where id = :id");
    $r = $stmt->execute([":id" => $id2]);
    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
    <h3>Edit Transaction</h3>
    <form method="POST">
        <label>Amount</label>
        <input name="amount" placeholder="amount" value="<?php echo $result["amount"]; ?>"/>
        <label>Type (Currently <?php echo $result["action_type"];?>)</label>
        <select name="type" value="<?php echo $result["action_type"];?>" >
            <option value="Deposit">Deposit</option>
            <option value="Withdraw">Withdraw</option>
	    <option value="Transfer">Transfer</option>
        </select>
        <label>Memo</label>
        <input type="text" name="memo" value="<?php echo $result["memo"]; ?>"/>
        <input type="submit" name="save" value="Update"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php");
