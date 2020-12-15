<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<?php

$query = "";
$results = [];
if (isset($_GET["account_number"])) {
    $query = $_GET["account_number"];
}
if (isset($_GET["account_number"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT acc.account_number, tr.act_dest_id, tr.amount, tr.action_type, tr.memo, tr.expected_total FROM Transactions as tr JOIN Accounts as acc on tr.act_src_id = acc.id JOIN Users on acc.user_id = Users.id WHERE acc.account_number =:q LIMIT 10");
    $r = $stmt->execute([":q" => "$query"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem getting your transactions");
    }
}
?>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Account Number:</div>
                        <div><?php safer_echo($r["account_number"]); ?></div>
                    </div>
                    <div>
                        <div>Recipient account ID:</div>
                        <div><?php safer_echo($r["act_dest_id"]); ?></div>
                    </div>
                    <div>
                        <div>Amount:</div>
                        <div><?php safer_echo($r["amount"]); ?></div>
                    </div>
		    <div>
                        <div>Action Type:</div>
                        <div><?php safer_echo($r["action_type"]); ?></div>
                    </div>
                    <div>
                        <div>Memo:</div>
                        <div><?php safer_echo($r["memo"]); ?></div>
                    </div>
		    <div>
                        <div>Resulting Balance:</div>
                        <div><?php safer_echo($r["expected_total"]); ?></div>
                    </div>
		    <div>--------------------------------------------</div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/partials/flash.php");
