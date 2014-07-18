/* Blackbaud Inc. (Modified from excellent tutorial at http://www.drewbuck.com/2013/03/tetris-in-html5-for-noobs-part-1-introduction/, Drew Buck */
function Game() {

	var canvas;
	var ctx;

	var t;      	// Tetrimino type
	var x, y;   	// Tetrimino position
	var o;      	// Tetrimino orientation

	var grid;
	var timer;
	
	var score;
	var scoreIncrement = 100;
	var goal;
	var level;
	var levelScore;
	var papersBaseHeight = 45;
	
	var timeStep;	// Time between calls to gameStep()
	
	var levelDiv;
	var scoreDiv;
	var outBoxDiv;
	var papersDiv;
	
	var gameWidth = 400;
	var gameHeight = 700;
	var gameCols = 8;
	var gameRows = 14;
	var blockSize = 50;
	var xStart = 4;
	var yStart = 13;
	
	// Pieces
	var sources = {
		red: 'images/bg-piece-red.png',
		purple: 'images/bg-piece-purple.png',
		green: 'images/bg-piece-green.png',
		yellow: 'images/bg-piece-yellow.png',
		orange: 'images/bg-piece-orange.png',
		blue: 'images/bg-piece-blue.png',
		cyan: 'images/bg-piece-cyan.png'
	};
	
	var images = {};
	
	function initialize () {
  
		scoreDiv = $('#GameScore');
		levelDiv = $('#GameLevel');
		outBoxDiv = $('#OutBox');
		papersDiv = $('#OutBoxPapers');
  
	    // Get the canvas context object from the body
	    canvas = document.getElementById("GameCanvas");
	    ctx = canvas.getContext("2d");
	    
	    canvas.tabIndex = 1;
		
		// Initialize tetrimino variables
		t = 1 + Math.floor( Math.random() * 7 );
		x = xStart;
		y = yStart;
		o = 0;
		
		score = 0;
		level = 1;
		levelScore = 0;
		goal = 1000;
		timeStep = 1000;
		
		updateUI();
		
		$('#btn-game-pause').show();
		
		// Create an empty game state grid
		grid = [];
		for (var i = 0; i < gameRows; i++) {
			grid[i] = [];
			for (var j = 0; j < gameCols; j++) {
				grid[i][j] = 0;
			}
		}
		
		loadImages(function () {
		
			// Draw the current tetrimino
		    drawTetrimino(x, y, t, o, 1);
			
			// Redraw the grid
			drawGrid();
			
			startTimer();
			
			$(window).unbind('keydown').on('keydown', function (e) {
				keyDown(e);
			});
			
		});
		
	}
	
	function loadImages(callback) {
	
        var loadedImages = 0;
        var numImages = 0;
        
        for (var src in sources) {
          numImages++;
        }
        
		for (var src in sources) {
			images[src] = new Image();
			images[src].onload = function () {
				if (++loadedImages >= numImages) {
					callback();
				}
			};
			images[src].src = sources[src];
		}
	} 
	
	function drawGrid() {
		
		// Clear the canvas
	    ctx.clearRect(0, 0, gameWidth, gameHeight);
		
		// Loop over each grid cell
		for (var i = 0; i < gameRows; i++) {
			for (var j = 0; j < gameCols; j++) {
				drawBlock(j, i, grid[i][j]);
			}
		}
	}
	 
	 
	/************************************************
	Draws a block at the specified game coordinate
	x = [0,9]   x-coordinate
	y = [0,19]  y-coordinate
	t = [0,7]   block type
	************************************************/
	function drawBlock(x, y, t) {
	    
	    var img;
	    
		// Check if a block needs to be drawn
		if (t > 0) {
		
			switch (t) {
				case 1: // I type
					img = images.cyan;
				break; 
				case 2: // J type
					img = images.blue;
				break;
				case 3: // L type
					img = images.orange;
				break;
				case 4: // O type
					img = images.yellow;
				break;
				case 5: // S type
					img = images.green;
				break;
				case 6: // T type
					img = images.purple;
				break;
				default: // Z type
					img = images.red;
				break;
			}
			
			// Convert game coordinates to pixel coordinates
			var pixelX = x * blockSize;
			var pixelY = ((gameRows - 1) - y) * blockSize;
			ctx.drawImage(img,pixelX,pixelY);
		}
	}
	  
	  
	/*************************************************
	Draws a tetrimino at the specified game coordinate
	with the specified orientation
	x = [0,9]   x-coordinate
	y = [0,19]  y-coordinate
	t = [1,7]   tetrimino type
	o = [0,3]   orientation
	d = [-1,1]	test, erase, or draw
	*************************************************/
	function drawTetrimino(x, y, t, o, d) {
	
		// Determine the value to send to setGrid
		var c = -1;
		if (d >= 0) c = t * d;
		
		// Initialize validity test
		var valid = true;
	
	    /**** Pick the appropriate tetrimino type ****/
	    if (t == 1) { // I Type
	          
	        // Get orientation
	        if (o == 0) {
				valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x+2,y,c);
	        }
	        else if (o == 1) {
				valid = valid && setGrid(x+1,y+1,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x+1,y-1,c);
	            valid = valid && setGrid(x+1,y-2,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y-1,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x+1,y-1,c);
	            valid = valid && setGrid(x+2,y-1,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x,y-2,c);
	        }
	    }
	    if (t == 2) { // J Type
	          
	        // Get orientation
	        if (o == 0) {
	            valid = valid && setGrid(x-1,y+1,c);
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	        }
	        else if (o == 1) {
	            valid = valid && setGrid(x+1,y+1,c);
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x+1,y-1,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x-1,y-1,c);
	        }
	    }
	    if (t == 3) { // L Type
	          
	        // Get orientation
	        if (o == 0) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x+1,y+1,c);
	        }
	        else if (o == 1) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x+1,y-1,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y-1,c);
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x-1,y+1,c);
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	        }
	    }
	    if (t == 4) { // O Type
	          
	        // Orientation doesn’t matter
	        valid = valid && setGrid(x,y,c);
	        valid = valid && setGrid(x+1,y,c);
	        valid = valid && setGrid(x,y+1,c);
	        valid = valid && setGrid(x+1,y+1,c);
	    }
	    if (t == 5) { // S Type
	         
	        // Get orientation
	        if (o == 0) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x+1,y+1,c);
	        }
	        else if (o == 1) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x+1,y-1,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y-1,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x-1,y+1,c);
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	        }
	    }
	    if (t == 6) { // T Type
	          
	        // Get orientation
	        if (o == 0) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x,y+1,c);
	        }
	        else if (o == 1) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x+1,y,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x,y-1,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x-1,y,c);
	        }
	    }
	    if (t == 7) { // Z Type
	          
	        // Get orientation
	        if (o == 0) {
	            valid = valid && setGrid(x-1,y+1,c);
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x+1,y,c);
	        }
	        else if (o == 1) {
	            valid = valid && setGrid(x+1,y+1,c);
	            valid = valid && setGrid(x+1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	        }
	        else if (o == 2) {
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x,y-1,c);
	            valid = valid && setGrid(x+1,y-1,c);
	        }
	        else if (o == 3) {
	            valid = valid && setGrid(x,y+1,c);
	            valid = valid && setGrid(x,y,c);
	            valid = valid && setGrid(x-1,y,c);
	            valid = valid && setGrid(x-1,y-1,c);
	        }
	    }
		
		return valid;
	}
	
	 
	/*************************************************
	Sets a grid cell in the game state grid
	x = [0,9]   x-coordinate
	y = [0,19]  y-coordinate
	t = [-1,7]  test or block type
	*************************************************/
	function setGrid(x, y, t) {
		
		// Check if point is in range
		if (x >= 0 && x < gameCols && y >= 0 && y < (gameRows+2)) { // the piece can be two blocks above the ceiling...
			
			// Return test result if testing
			if (t < 0) {
				return ( (y < gameRows && x < gameCols && grid[y][x] == 0) || (y >= gameRows)) ? true : false; // if there's an empty block below, or the block hasn't entered the playing field just yet
			}
			
			// Otherwise assign block type to the grid
			if (y < gameRows && x < gameCols) {
				grid[y][x] = t;
			}
			
			return true;
		}
		return false;
	}
	 
	 
	/*************************************************
	Responds to a key press event
	*************************************************/
	function keyDown(e) {
		
		var keycode;
	    if (window.event) keycode = window.event.keyCode;
	    else if (e) keycode = e.which;
	    
		
	    if (keycode == 37) { // Left arrow
			drawTetrimino(x,y,t,o,0);  // Erase the current tetrimino
	        x2 = x - 1;
			if (drawTetrimino(x2,y,t,o,-1)) // Check if valid
				x = x2;
	    }
	    else if (keycode == 38) { // Up arrow
			drawTetrimino(x,y,t,o,0);  // Erase the current tetrimino
	        o2 = (o + 1) % 4;
			if (drawTetrimino(x,y,t,o2,-1)) // Check if valid
				o = o2;
	    }
	    else if (keycode == 39) { // Right arrow
			drawTetrimino(x,y,t,o,0);  // Erase the current tetrimino
	        x2 = x + 1;
			if (drawTetrimino(x2,y,t,o,-1)) // Check if valid
				x = x2;
	    }
	    else if (keycode == 40) { // Down arrow
			drawTetrimino(x,y,t,o,0);  // Erase the current tetrimino
	        y2 = y - 1;
			if (drawTetrimino(x,y2,t,o,-1)) // Check if valid
				y = y2;
	    }
		else if (keycode == 32) { // Space-bar
			drawTetrimino(x,y,t,o,0);  // Erase the current tetrimino
			
			// Move down until invalid
			while (drawTetrimino(x,y-1,t,o,-1))
				y -= 1;
				
			gameStep();
		}
	     
	    // Draw the current tetrimino
	    drawTetrimino(x,y,t,o,1);
		
		// Redraw the grid
		drawGrid();
		
		e.preventDefault();
		return false;
	}
	
	
	/*************************************************
	Updates the game state at regular intervals
	*************************************************/
	function gameStep() {
	
		// Erase the current tetrimino
		drawTetrimino(x,y,t,o,0);  
		
		// Check if the tetrimino can be dropped 1 block
		y2 = y - 1;
		if (drawTetrimino(x,y2,t,o,-1)) {
			y = y2;
		}
		else {
		
			// Redraw last tetrimino
			drawTetrimino(x,y,t,o,1);
		
			// Check if any lines are complete
			checkLines();
		
			// Create a new tetrimino 
	        t2 = 1 + Math.floor( Math.random() * 7 );
	        x2 = xStart;
	        y2 = yStart;
	        o2 = 0;
			
			// Check if valid
			if (drawTetrimino(x2,y2,t2,o2,-1)) {
				t = t2;
				x = x2;
				y = y2;
				o = o2;
			}
			else {
				gameOver();
				return;
			}
			
		}
		
		// Draw the current tetrimino
	    drawTetrimino(x,y,t,o,1);
		
		// Redraw the grid
		drawGrid();
	}
	
	
	/*************************************************
	Removes completed lines from the grid
	*************************************************/
	function checkLines() {
	
		// Loop over each line in the grid
		for (var i = 0; i < gameRows; i++) {
		
			// Check if the line is full
			var full = true;
			for (var j = 0; j < gameCols; j++) {
				full = full && (grid[i][j] > 0);
			}
				
			if (full) {
			
				// Update score
				score += scoreIncrement;
				levelScore += scoreIncrement;
				
				// Check if ready for the next level
				if (score >= goal) {
					level++;
					levelScore = 0;
					$('#modal-level-up').hide().slideDown().fadeIn('fast', function () {
						setTimeout(function () {
							$('#modal-level-up').fadeOut();
						}, 1000);
					});
					
					// Update the timer with a shorter timeStep
					timeStep *= 0.8;
					clearInterval(timer);
					timer = setInterval(gameStep, timeStep);
				}
				
				updateUI();
			
				// Loop over the remaining lines
				for (var ii = i; ii < (gameRows - 1); ii++) {
					
					// Copy each line from the line above
					for (var j = 0; j < gameCols; j++) {
						grid[ii][j] = grid[ii+1][j];
					}
				}
				
				// Make sure the top line is clear
				for (var j = 0; j < gameCols; j++) {
					grid[(gameRows - 1)][j] = 0;
				}
					
				// Repeat the check for this line
				i--;
			}
		}
	}
	
	function updateUI() {
		// Update score and level display
		goal = (level * (10 * scoreIncrement));
		scoreDiv.text("Score: " + score);
		levelDiv.text("Level: " + level);
		var percent = levelScore / goal;
		var papersHeight = papersBaseHeight + (outBoxDiv.height()-papersBaseHeight) * percent;
		papersDiv.css({'height':papersHeight});
	}
	
	function startTimer() {
		clearInterval(timer);
		timer = setInterval(gameStep, timeStep);
	}
	
	function pauseTimer() {
		$('#modal-game-paused').show();
		clearInterval(timer);
		$(window).unbind('keydown');
	}
	
	function resumeTimer() {
	    startTimer();
	}
	
	function gameOver() {
		clearInterval(timer);
		$('#btn-game-pause').hide();
		$(window).unbind('keydown');
		$('#modal-game-over').show();
		var review = "";
		if (level < 2) {
			review = '<strong>Those grants have no mercy.</strong>';
		} else if (level < 3) {
			review = '<strong>Way to flex your grant processing muscles.</strong>';
		} else if (level < 4) {
			review = '<strong>Now you\'re showing the grants who\'s boss.</strong>';
		} else if (level < 5) {
			review = '<strong>So close to grant wizard status!</strong>';
		} else if (level >= 5) {
			review = '<strong>You\'re good, <em>too</em> good. Aren\'t there real grants you should be managing?</strong>';
		}
		$('#game-over-results').html('You reached <strong>Level '+level+'</strong> with a final score of <strong>'+score+'</strong>!<br><h4>Assessment:</h4><div class="game-assessment">'+review+'</div>');
	}
	
	
	var _construct = function () {
		
		$('#btn-game-start').on('click', function (e) {
			e.preventDefault();
			$('#modal-game-start').hide();
			initialize();
		});
		
		$('#btn-game-pause').on('click', function (e) {
			e.preventDefault();
			pauseTimer();
			$(this).hide();
		});
		
		$('#btn-game-resume').on('click', function (e) {
			e.preventDefault();
			$('#modal-game-paused').hide();
			startTimer();
			$('#btn-game-pause').show();
			$(window).unbind('keydown').on('keydown', function (e) {
				keyDown(e);
			});
		});
		
		$('#btn-game-again').on('click', function (e) {
			e.preventDefault();
			$('#modal-game-over').hide();
			initialize();
		});
		
	}();
	
	return {}
}

var game = new Game();
