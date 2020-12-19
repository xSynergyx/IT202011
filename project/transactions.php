<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
?>
<?php

$page = 1;
$per_page = 10;
$total_pages = 1;
$offset = 0;
if(isset($_GET["page"])){
    try {
	$page = (int)$_GET["page"];
    }
    catch(Exception $e){
}

$id = "";
$results = [];
/*
if (isset($_SESSION["user"])) {
    $id = $_SESSION["user"]["id"];
}

*/
if (!empty($id)) {
    $db = getDB();

    //getting total page count
    $stmt = $db->prepare("SELECT count(*) as total from Transactions as tr JOIN Accounts as acc on tr.act_src_id = acc.id JOIN Users on acc.user_id = Users.id WHERE Users.id = :id");
    $stmt->execute([":id" => get_user_id()]);
    $totalResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if ($totalResult){
	$total = (int)$totalResult["total"];
    }
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;

    //query the info
    $stmt = $db->prepare("SELECT acc.account_number, tr.amount, tr.action_type, tr.memo, tr.expected_total FROM Transactions as tr JOIN Accounts as acc on tr.act_src_id = acc.id JOIN Users on acc.user_id = Users.id WHERE Users.id =:id LIMIT :offset, :count");
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $stmt->bindValue(":id", get_user_id());
    $r = $stmt->execute();
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
<div>
    <nav>
            <ul>
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
            </ul>
    </nav>
</div>

<?php require(__DIR__ . "/partials/flash.php");
