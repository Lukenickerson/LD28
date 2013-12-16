


var CapitalisGameClass = function () 
{
	if (!window.jQuery) { alert("ERROR - jQuery is not loaded!"); }

	this.data = {};
	this.dataNameArray = ["myPerson", "plots", "floors", "persons", "cities"];
	this.$map			= $('#map');
	this.$loading 		= $('#loading');
	this.$floaterMenu	= $('nav#floater');
	this.$mainMenu		= $('nav#mainMenu');
	this.$notificationList = $('#notifications > ol');
	this.$stats		 	= $('#stats');
	this.$plotsLayer 	= this.$map.find('.plots');
	this.$floorsLayer 	= this.$map.find('.floors');
	this.$personsLayer 	= this.$map.find('.persons');
	
	this.$plotsList		= this.$plotsLayer.children('ol').first();
	this.$ground		= this.$plotsLayer.find('.ground');
	this.$personsList 	= this.$personsLayer.children('ul').first();
	this.$floorsList 	= this.$floorsLayer.children('ul').first();
	this.plotSize 		= { "x" : 100, "y" : 100 };
	this.floorSize		= { "x" : 80, "y" : 100 };
	this.personSize		= { "x" : 50, "y" : 70 };
	this.isGodMode		= false;
	this.isPaused		= false;
	this.loopTime		= (1000/60); // 60 frames per second
	this.loopTimerId	= 0;
	this.breath			= 0;
	
	
	//=========================== MAIN LOOP ===================================
	
	
	this.loop = function(iteration){
		var o = this;
		iteration++;		
		
		if (iteration % 120 == 0) {	// every 2 seconds
			o.callTick();
			if (o.breath == 0) {
				o.breath = 1;
				o.personSize.x += 20;
				o.personSize.y -= 20;
			} else {
				o.breath = 0;
				o.personSize.x -= 20;
				o.personSize.y += 20;			
			}			
		}
		if (iteration % 480 == 0) {	// every 8 seconds
			o.loadData("persons", function(){ o.updateLayer("persons"); }, true);
			o.loadData("plots", function(){ o.fillLayer("plots"); }, true);
			o.loadData("floors", function(){ o.fillLayer("floors"); }, true);
			o.loadData("cities", function(){ o.fillLayer("stats"); }, true);
			//o.updateLayer("stats");
		}
		if (iteration % 4800 == 0) {	// every 80 seconds
			o.loadData("persons", function(){ o.fillLayer("persons"); }, true);
		}
		

		o.updateLayer("persons");
		
		if (!o.isPaused) {
			o.loopTimerId = setTimeout(function(){	o.loop(iteration) }, o.loopTime);
		}
	}

	this.callTick = function (callback) {
		var o = this;
		var ajaxGetData = {
			//"get" : 	getName
		};
		console.log("Calling tick.php");
		$.ajax({
			type: 		"get"
			,url:		"tick.php"
			,data: 		ajaxGetData
			,complete: function(x,t) {
			}
			,success: function(response) {
				//alert("Success\n" + response);
				try {
					var responseObj = $.parseJSON(response);
					console.log("Ajax Success loading tick ");
				} catch (err) {
					o.notify("ERROR PARSING JSON (callTick)");
					console.log(response);
					var responseObj = {"_message":"ERROR PARSING JSON"};
				}
				console.log(responseObj._message);
				//if (typeof callback === "function") callback(responseObj);
			}
			,failure: function(msg) {
				console.log("Fail\n"+ msg);
			}
			,error: function(x, textStatus, errorThrown) {
				console.log("Error\n" + x.responseText + "\nText Status: " + textStatus + "\nError Thrown: " + errorThrown);
			}
		});
	}

	this.notify = function(text) {
		var o = this;
		//alert(text);
		var $notification = $( '<li></li>' ).html(text);
		$notification.on("click",function(e){
			var $n = $(this);
			$n.slideUp(500, function(){
				$n.remove();
			});
		}).appendTo( o.$notificationList );
	}
	
	
	//============ UPDATE MAP LAYERS ====================================================\\
	
	this.fillLayer = function(layerName)
	{
		var o = this;
		var listHtml = "";
		switch (layerName) {
			case "floors":
				o.$floorsList.empty();
				for (var f in o.data.floors) {
					listHtml += '<li id="floor_' + o.data.floors[f].floorId + '" '
						+ ' class="floor '
						+ ' type_' + o.data.floors[f].floorTypeKey + ' '
						+ '" '
						+ ' data-floorid="' + o.data.floors[f].floorId + '">'					
						+ ((o.data.floors[f].floorTypeKey == "R") ? 'Home' : 'Work')
						+ '</li>';
				}
				o.$floorsList.html( listHtml );
				break;
				
			case "persons":
				o.$personsList.empty();
				for (var p in o.data.persons) {
					listHtml += '<li id="person_' + o.data.persons[p].personId + '">'
						+ '<div class="face">..</div><div class="name">'
						+ o.data.persons[p].firstName + ' ' + o.data.persons[p].lastName
						+'</div></li>';
				}
				o.$personsList.html( listHtml );
				break;
				
			case "plots":

				o.$ground.empty();
				o.$plotsList.empty();
				var plotCount = 0;
				for (var p in o.data.plots) {
					var isYours = (o.data.plots[p].ownerPersonId == o.data.myPerson.personId);
					plotCount++;
					//console.log(o.data.plots[p]);
					listHtml += '<li style="width: ' + o.plotSize.x + 'px" '
						+ ' id="plot_' + o.data.plots[p].plotId + '" '
						+ ' class="plot city' + o.data.plots[p].cityId 
						+ (isYours ? ' yours ' : '')
						+ '"'
						+ ' data-plotid="' + o.data.plots[p].plotId + '">';
					if (o.data.plots[p].ownerPersonId > 0) {
						if (isYours) {
							listHtml += '<span class="owner">owned by YOU</span>';
						} else {
							listHtml += '<span class="owner">owned</span>';
						}
						
					} else {
						listHtml += '<span class="owner">available</span>';						
					}
					listHtml += '</li>';
				}
				o.$plotsList.html( listHtml );
				o.$plotsLayer.add(o.$ground).add(o.$plotsList)
					.css({ 
						"width" : (plotCount * o.plotSize.x)
						,"height" : (o.plotSize.y * 2)
						,"bottom" : (-1 * o.plotSize.y)
					});
				break;
				
			case "stats":
				var me = o.data.persons[o.data.myPerson.personId];
				var h = me.firstName + ' ' + me.lastName
					+ '<br />Coins: ' + me.coins
					+ '<ul>';
				for (var c in o.data.cities) {
					var city = o.data.cities[c];
					//console.log(city);
					h += '<li>' + city.cityName + ': ' + city.population + '</li>';
				}
				h += '</ul>';
				o.$stats.fadeIn(1000).html(h);
				break;
		}
		o.updateLayer(layerName);
	}
	
	
	this.updateLayer = function(layerName)
	{
		var o = this;
		
		switch (layerName) {
			case "floors":
				var baseOffsetX = (o.plotSize.x - o.floorSize.x) / 2;
				for (var f in o.data.floors) {
					var floor = o.data.floors[f];
					floor.$elt = $('#floor_' + floor.floorId);
					floor.x = (o.plotSize.x * o.data.plots[floor.plotId].surfaceX) + baseOffsetX;
					floor.y = o.plotSize.y + (o.floorSize.y * floor.surfaceY);
					floor.$elt.css({ "bottom" : floor.y, "left" : floor.x , "width" : o.floorSize.x, "height" : o.floorSize.y });
				}
				break;
				
			case "persons":
				var baseOffsetX = (o.plotSize.x/2) - (o.personSize.x / 2);
				for (var p in o.data.persons) {
					var person = o.data.persons[p];
					var heightY = 0;
					person.$elt = $('#person_' + person.personId);
					//console.log(person);
					if (person.currentLocationType == "plot") {
						person.currentPlotId = person.currentLocationId;
					} else if (person.currentLocationType == "floor") {
						person.currentPlotId = o.data.floors[person.currentLocationId].plotId;
						heightY = (o.floorSize.y * o.data.floors[person.currentLocationId].surfaceY);
					}
					//console.log(person.currentLocationType);
					//console.log(person.currentLocationId);
					//console.log(person.currentPlotId);
					person.x = (o.plotSize.x * o.data.plots[person.currentPlotId].surfaceX) + baseOffsetX;
					person.y = o.plotSize.y + heightY;
					person.$elt.css({ "bottom" : person.y, "left" : person.x , "width" : o.personSize.x, "height" : o.personSize.y });
				}
				break;
				
			case "plots":

				break;
				
			case "stats":

				break;
		}
	}
	
	//=========================================== FLOATER MENU
	
	this.clearFloaterMenu = function(){
		this.$floaterMenu.children().hide();
	}
	this.hideFloaterMenu = function(){
		var o = this;
		o.$floaterMenu.fadeOut(100, function(){
			o.clearFloaterMenu();
		});
	}
	
	this.setFloaterMenuTarget = function(evt){
		var o = this;
		o.$floaterMenu.fadeIn(400).css({
			"left" : 	evt.clientX - (o.$floaterMenu.width() / 2)
			,"top" :	evt.clientY - o.$floaterMenu.height() - 20 // 20 ~= 1em estimate
		});		
	}
	
	//========================== INTERACTIONS ==============================
	
	this.setupInteractions = function(){
		var o = this;
		o.$map.on("hover", function(e){
			var $target = $(e.target);
			
		}).on("click", function(e){
			var $target = $(e.target);
			if ($target.hasClass("owner")) {
				$target = $target.closest('.plot');
			}
			if ($target.hasClass("plot")) {
				
				o.clearFloaterMenu();
				if ($target.hasClass("yours")) {
					o.$floaterMenu.find('.build').show();
				} else {
					o.$floaterMenu.find('.claim').show();
				}
				o.$floaterMenu.data("plotid", $target.data("plotid"));
				o.setFloaterMenuTarget(e);
				
			} else if ($target.hasClass("floor")) {
				o.clearFloaterMenu();
				if ($target.hasClass("type_R")) {
					//o.$floaterMenu.find('.rent').show();
					//o.$floaterMenu.find('.live').show();
				} else {
					//o.$floaterMenu.find('.wages').show();
					//o.$floaterMenu.find('.work').show();
				}
				o.$floaterMenu.data("floorid", $target.data("floorid"));
				o.setFloaterMenuTarget(e);
			} else {
				o.hideFloaterMenu();
			}
		});
		
		o.$floaterMenu.click(function(e){
			var $target = $(e.target);
			if ($target.hasClass("build")) {
				var plotId = o.$floaterMenu.data("plotid");
				var type = $target.data("type");
				//alert(plotId + "\n" + type );
				o.apiAction({ "build" : plotId, "type" : type }, function(respObj){
					console.log(respObj);
					if (respObj._success) {
						o.loadData("floors", function(){ o.updateLayer("floors"); });
						o.notify("New construction!");
					} else {
						o.notify(respObj._message);
					}
				});				
			} else if ($target.hasClass("claim")) {
				var plotId = o.$floaterMenu.data("plotid");
				o.apiAction({ "claim" : plotId }, function(respObj){
					console.log(respObj);
					if (respObj._success) {
						o.loadData("plots", function(){	o.updateLayer("plots"); });
						o.notify("Claiming plot of land...");
					} else {
						o.notify(respObj._message);
					}
				});				
			} else if ($target.hasClass("work")) {
				var floorId = o.$floaterMenu.data("floorid");
				o.notify(floorId);
			} else if ($target.hasClass("live")) {
				var floorId = o.$floaterMenu.data("floorid");
				o.notify(floorId);
			}
			o.hideFloaterMenu();
		});
	
		o.$mainMenu.click(function(e){
			var $target = $(e.target);
			if ($target.hasClass("play")) {
				o.$mainMenu.slideUp(200);
				o.fillLayer("stats");
				o.$map.fadeIn(300, function(){
					console.log("---Starting Loop---");
					o.loop(0);
				});
			}
		});
	}

	this.apiAction = function (apiData, callback) {
		var o = this;
		console.log("Calling API Action: " + apiData);
		$.ajax({
			type: 		"get"
			,url:		"api.php"
			,data: 		apiData
			,complete: function(x,t) {
			}
			,success: function(response) {
				//alert("Success\n" + response);
				try {
					var responseObj = $.parseJSON(response);
					console.log("Ajax Success loading " + apiData);
				} catch (err) {
					o.notify("ERROR PARSING JSON (apiAction)");
					console.log(response);
					$('body').prepend(response);
					var responseObj = {};
				}
				if (typeof callback === "function") callback(responseObj);
			}
			,failure: function(msg) {
				o.notify("Fail\n"+ msg);
			}
			,error: function(x, textStatus, errorThrown) {
				o.notify("Error\n" + x.responseText + "\nText Status: " + textStatus + "\nError Thrown: " + errorThrown);
			}			
		});
	}
	
	
	//========================== DATA ======================================
	
	
	this.loadData = function (getName, callback, useCache) {
		var o = this;
		if (typeof useCache === 'undefined') useCache = false;
		var ajaxGetData = {
			"get" : 	getName
		};
		if (useCache) ajaxGetData.cache = true;
		//console.log(ajaxGetData);
		console.log("Calling API to load " + getName);
		$.ajax({
			type: 		"get"
			,url:		"api.php"
			,data: 		ajaxGetData
			,complete: function(x,t) {
			}
			,success: function(response) {
				//o.notify("Success\n" + response);
				try {
					var responseObj = $.parseJSON(response);
					console.log("Ajax Success loading " + getName);
				} catch (err) {
					o.notify("ERROR PARSING JSON (loadData " + getName + ")");
					console.log(response);
					var responseObj = {};
				}
				//console.log(responseObj);
				if (typeof responseObj._isChanged !== 'boolean' || responseObj._isChanged == true) {
					o.data[getName] = responseObj;
				}
				if (typeof callback === "function") callback(responseObj);
			}
			,failure: function(msg) {
				o.notify("Fail\n"+ msg);
			}
			,error: function(x, textStatus, errorThrown) {
				o.notify("Error\n" + x.responseText + "\nText Status: " + textStatus + "\nError Thrown: " + errorThrown);
			}			
		});
	}
	
	this.loadAllData = function (useCache) {
		var o = this;
		if (typeof useCache === 'undefined') useCache = false;
		for (var i=0; i < o.dataNameArray.length; i++) {
			o.loadData(o.dataNameArray[i], null, useCache);
		}
	}
	
	this.isDataLoaded = function (getName) {
		//console.log("isDataLoaded: " + getName + " " + typeof this.data[getName]);
		if (typeof this.data[getName] === 'undefined') {
			return false;
		} else {
			// *** check length of data obj? or feedback?
			return true;
		}
	}
	
	this.isAllDataLoaded = function () {
		var o = this;
		var dataLoadCount = 0;
		for (var i=0; i < o.dataNameArray.length; i++) {
			if (o.isDataLoaded(o.dataNameArray[i])) dataLoadCount++;
		}
		//console.log(dataLoadCount);
		return (dataLoadCount >= o.dataNameArray.length) ? true : false;
	}
	
	this.setup = function () {
		var o = this;
		console.log("Setting up Capitalis...");
		$('body > header').fadeIn(4000, function(){
			$('h1 > .subtitle ').fadeIn(4000);
		});
		o.$loading.fadeIn(1000);
		
		o.loadAllData(false);
		
		var postLoadSetup = function (i) {
			console.log("Checking load status...");
			i++;
			if (o.isAllDataLoaded()) {
				console.log("Everything is loaded.");
				o.fillLayer("plots");
				o.fillLayer("persons");
				o.fillLayer("floors");
				
				// ** ADD MORE?
				
				o.setupInteractions();
			
				// Start up animation
				o.$loading.slideUp(300, function() {
					o.$mainMenu.fadeIn(500);
				});
			} else {
				if (i > 120) {
					o.notify("Load failed.");
				} else {
					setTimeout(function(){ postLoadSetup(i) }, 500);
				}
			}
		};
		setTimeout(function(){ postLoadSetup(0) }, 500);
	}

	this.setup();
}

$(document).ready(function(){




});