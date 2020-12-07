<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>
    <p>Welcome, <?php echo $email; ?></p>

/* Trynna make a slideshow. Need to find out how to get images into this VM
<div class="slideshow-container">
	<div class="fadeSlides">
*/
<?php require(__DIR__ . "/partials/flash.php");
