<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$email = "";
$results = [];
if (isset($_SESSION["user"])) {
    $email = $_SESSION["user"]["email"];
}
if (!empty($email)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT acc.account_number, acc.account_type, acc.balance FROM Accounts as acc JOIN Users on acc.user_id = Users.id WHERE Users.email = :email LIMIT 5");
    $r = $stmt->execute([":email" => "$email"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem trying to view your account. Please contact us at 222-222-2222");
    }
}

?>
<!--
<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
-->
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
                        <div>Account Type:</div>
                        <div><?php getAccountType($r["account_type"]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($r["balance"]); ?></div>
                    </div>
<!--
                    <div>
                        <a type="button" href="test_edit_accounts.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="view_accounts.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
-->
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You don't have any account with us yet.</p>
    <?php endif; ?>
</div>
