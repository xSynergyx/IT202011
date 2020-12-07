<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>
    <p>Welcome, <?php echo $email; ?></p>


<div class="slideshow-container">
	<div class="fadeSlides">
		<img src="images/openaccount.jpg" style="width:100%" style="height:100%">
		<div class="caption">Open an Account!</div>
	</div>

	<div class="fadeSlides">
		<img src="images/transfer.jpg" style="width:100%" style="height:100%">
		<div class="caption">Easily trasnfer money to friends and family!</div>
	</div>

	<div class="fadeSlides">
		<img src="images/invest.jpg" style="width:100%" style="height:100%">
		<div class="caption">Invest safely with Synergy Investments (Coming Soon)</div>
	</div>

	<a class="prev" onClick="slides(-1)">&#10094;</a>
	<a class="next" onClick="slides(1)">&#10095;</a>
</div>
<br>

<script>
var slideIndex = 1;
showSlides(slideIndex);

function slides(num){
	showSlides(slideIndex += num);
}

function currentSlide(num){
	showSlides(slideIndex = num);
}
/*
function autoShowSlides(){
	var j;
	var slides = document.getElenmentsByClassName("fadeSlides");
	for (j = 0; j < slides.length; j++){
		slides[j].style.display = "none";
	}
	slideIndex++;
	if (slideIndex > slides.length){
		slideIndex = 1;
	}
	slides[slideIndex-1].style.display = "block";
	setTimeout(autoShowSlides, 2000); //2 seconds
}
*/
function showSlides(num){
	var i;
	var slides = document.getElementsByClassName("fadeSlides");
	if (num > slides.length){
		slideIndex = 1;
	}
	if (num < 1){
		slideIndex = slides.length;
	}
	for (i=0; i<slides.length; i++){
		slides[i].style.display="none";
	}
	slides[slideIndex-1].style.display = "block";
}
</script>

<?php require(__DIR__ . "/partials/flash.php");
