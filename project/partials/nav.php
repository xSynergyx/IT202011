<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<ul class="nav">
    <li><a href="home.php">Home</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if (has_role("Admin")): ?>
	<li><a href="test_create_accounts.php">Create Account</a></li>
	<li><a href="test_list_accounts.php">View Accounts</a></li>
	<li><a href="test_create_transactions.php">Create Transaction</a></li>
	<li><a href="test_edit_transactions.php">Edit Transaction</a></li>
	<li><a href="test_list_transactions.php">List Transactions</a></li>
    <?php endif; ?>
    <?php if (is_logged_in()): ?>
	<li><a href="list_accounts.php">Accounts</a></li>
	<li><a href="transactions.php">Transactions</a></li>
	<li><a href="depositwithdraw.php">Deposit/Withdraw</a></li>
	<li><a href="transfer.php">Transfer</a></li>
	<li><a href="exttransfer.php">External Transfer</a></li>
	<li><a href="create_account.php">Open an Account</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>
</nav>
