<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Capitalis - Deathray Games - Ludum Dare 28</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Macondo+Swash+Caps' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Iceland' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Boogaloo' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/capital_style.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	
</head>
<body>

	<header>
		<h1>Capitalis
			<div class="subtitle">The World Only Gets One Capital City</div>
		</h1>
	</header>
	
	<section id="loading">
		<h1>Loading...</h1>
	</section>

	<nav id="mainMenu">
		<div class="instructions">
		How To Play: 
		<ul>
			<li>If you've never played before, you will be given a new character. 
			Otherwise you will continue with your previous character.
			</li>
			<li>Scroll around the map with the scroll bars.</li>
			<li>Click the ground to purchase a plot of land, then build a
				residential structure for rent income, or a commercial building to provide
				the people with jobs.
			</li>
			<li>Once there are enough jobs and housing, new people will appear in
				the world.
			</li>
			<li>The population of each city is shown at the top left. 
				The city with the highest population is the capital.
				Try to get your	favorite city to Capital status!
			</li>
		</ul>
		</div>
		<button type="button" class="play">Play Game</button>
		<p>
			Made in 24 hours for Ludum Dare 28 by Luke Nickerson.
			<br />(Theme: You Only Get One)
		</p>
		<p>
			For more information check out 
			<a href="deathraygames.tumblr.com">Tumblr</a> or the 
			<a href="http://www.ludumdare.com/compo/author/deathray/">Ludum Dare page</a>.
		</p>
		<p>
			&copy; 2013
		</p>
	</nav>
	
	<nav id="floater">
		<button type="button" class="build buildR" data-type="R">Build Residential for 100</button>
		<button type="button" class="build buildC" data-type="C">Build Commercial for 100</button>
		<!---
		<button type="button" class="build buildI" data-type="I">Build I for 100</button>
		<button type="button" class="build buildF" data-type="F">Build F for 100</button>
		--->
		<button type="button" class="claim">Claim Plot for 500</button>
		<button type="button" class="rent">Change Rent (R)</button>
		<button type="button" class="wages">Change Wages (C,I,F)</button>
		<button type="button" class="work" >Work Here</button>
		<button type="button" class="home" >Live Here</button>
	</nav>
	
	<section id="map">
		<div class="layer plots">
			<div class="ground"></div>
			<ol></ol>
		</div>
		<div class="layer floors">
			<ul></ul>
		</div>
		<div class="layer persons">
			<ul></ul>
		</div>
		
	</section>
	
	<section id="stats">
	</section>
	
	<section id="notifications">
		<ol>
		</ol>
	</section>
	
	<section id="chat">
		<ul>
		</ul>
		<div>
			<form>
				<input type="text">
				<input type="submit">
			</form>
		</div>
	</section>
	
	<script src="js/capitalis.js"></script>
	<script>
		window.cap = new CapitalisGameClass();
	</script>
</body>
</html>
	