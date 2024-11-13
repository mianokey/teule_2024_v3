function closeAnimation() {
    document.getElementById('birthday-animation').style.display = 'none';
}

  // Function to mark animation as shown for today and close it
  function dontShowAgainToday() {
    // Get the CSRF token value from the meta tag
    var token = $('meta[name="csrf-token"]').attr('content');

    // Send AJAX request to mark animation as shown for today
    $.ajax({
        url: '/mark-animation-shown', // Use direct route URL
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': token // Include CSRF token in headers
        },
        success: function(response) {
            // Close the animation
            closeAnimation();
        }
    });
}



// Event listener for close button
document.getElementById('close-animation').addEventListener('click', closeAnimation);

// Event listener for "Don't show again today" button
document.getElementById('dont-show-again').addEventListener('click', dontShowAgainToday);


    // helper functions
    const PI2 = Math.PI * 2;
    const random = (min, max) => Math.random() * (max - min + 1) + min | 0;
    const timestamp = () => new Date().getTime();

    // container
    class Birthday {
        constructor() {
            this.resize();

            // create a lovely place to store the firework and balloons
            this.fireworks = [];
            this.balloons = [];
            this.counter = 0;
            this.balloonCounter = 0;
        }

        resize() {
            this.width = canvas.width = window.innerWidth;
            let center = this.width / 2 | 0;
            this.spawnA = center - center / 4 | 0;
            this.spawnB = center + center / 4 | 0;

            this.height = canvas.height = window.innerHeight;
            this.spawnC = this.height * .1;
            this.spawnD = this.height * .5;
        }

        onClick(evt) {
            let x = evt.clientX || evt.touches && evt.touches[0].pageX;
            let y = evt.clientY || evt.touches && evt.touches[0].pageY;

            let count = random(5, 10);  // Increased number of fireworks
            for (let i = 0; i < count; i++) this.fireworks.push(new Firework(
                random(this.spawnA, this.spawnB),
                this.height,
                x,
                y,
                random(0, 260),
                random(30, 110)));

            this.counter = -1;
        }

        update(delta) {
            ctx.clearRect(0, 0, this.width, this.height);  // Clear the canvas with a transparent background

            ctx.globalCompositeOperation = 'lighter';
            for (let firework of this.fireworks) firework.update(delta);

            ctx.globalCompositeOperation = 'source-over';
            for (let balloon of this.balloons) balloon.update(delta);

            // if enough time passed... create new firework
            this.counter += delta * 3; // each second
            if (this.counter >= 0.5) {  // Increase frequency of fireworks
                this.fireworks.push(new Firework(
                    random(this.spawnA, this.spawnB),
                    this.height,
                    random(0, this.width),
                    random(this.spawnC, this.spawnD),
                    random(0, 360),
                    random(30, 110)));
                this.counter = 0;
            }

            // if enough time passed... create new balloon
            this.balloonCounter += delta;
            if (this.balloonCounter >= 1) {
                this.balloons.push(new Balloon(
                    random(0, this.width),
                    this.height,
                    random(20, 50),
                    random(0, 360),
                    random(30, 110)));
                this.balloonCounter = 0;
            }

            // remove the dead fireworks and balloons
            if (this.fireworks.length > 1000) this.fireworks = this.fireworks.filter(firework => !firework.dead);
            if (this.balloons.length > 100) this.balloons = this.balloons.filter(balloon => !balloon.dead);
        }
    }

    class Firework {
        constructor(x, y, targetX, targetY, shade, offsprings) {
            this.dead = false;
            this.offsprings = offsprings;

            this.x = x;
            this.y = y;
            this.targetX = targetX;
            this.targetY = targetY;

            this.shade = shade;
            this.history = [];
        }

        update(delta) {
            if (this.dead) return;

            let xDiff = this.targetX - this.x;
            let yDiff = this.targetY - this.y;
            if (Math.abs(xDiff) > 3 || Math.abs(yDiff) > 3) { // is still moving
                this.x += xDiff * 2 * delta;
                this.y += yDiff * 2 * delta;

                this.history.push({
                    x: this.x,
                    y: this.y
                });

                if (this.history.length > 20) this.history.shift();

            } else {
                if (this.offsprings && !this.madeChilds) {
                    let babies = this.offsprings / 2;
                    for (let i = 0; i < babies; i++) {
                        let targetX = this.x + this.offsprings * Math.cos(PI2 * i / babies) | 0;
                        let targetY = this.y + this.offsprings * Math.sin(PI2 * i / babies) | 0;

                        birthday.fireworks.push(new Firework(this.x, this.y, targetX, targetY, this.shade, 0));
                    }
                }
                this.madeChilds = true;
                this.history.shift();
            }

            if (this.history.length === 0) this.dead = true;
            else if (this.offsprings) {
                for (let i = 0; this.history.length > i; i++) {
                    let point = this.history[i];
                    ctx.beginPath();
                    ctx.fillStyle = 'hsl(' + this.shade + ',100%,' + i + '%)';
                    ctx.arc(point.x, point.y, 1, 0, PI2, false);
                    ctx.fill();
                }
            } else {
                ctx.beginPath();
                ctx.fillStyle = 'hsl(' + this.shade + ',100%,50%)';
                ctx.arc(this.x, this.y, 1, 0, PI2, false);
                ctx.fill();
            }
        }
    }

    class Balloon {
        constructor(x, y, radius, hue, lightness) {
            this.dead = false;

            this.x = x;
            this.y = y;
            this.radius = radius;
            this.hue = hue;
            this.lightness = lightness;
        }

        update(delta) {
            if (this.dead) return;

            this.y -= 50 * delta; // Balloon rising speed
            if (this.y < -this.radius) {
                this.dead = true;
            }

            ctx.beginPath();
            ctx.fillStyle = `hsl(${this.hue}, 100%, ${this.lightness}%)`;
            ctx.arc(this.x, this.y, this.radius, 0, PI2, false);
            ctx.fill();
        }
    }

    let canvas = document.getElementById('birthday');
    let ctx = canvas.getContext('2d');

    let then = timestamp();

    let birthday = new Birthday();
    window.onresize = () => birthday.resize();
    document.onclick = evt => birthday.onClick(evt);
    document.ontouchstart = evt => birthday.onClick(evt);

    (function loop() {
        requestAnimationFrame(loop);

        let now = timestamp();
        let delta = now - then;

        then = now;
        birthday.update(delta / 1000);
    })();

    function closeAnimation() {
        // Hide the birthday animation
        document.getElementById('birthday-animation').style.display = 'none';
    }

 

function closeAnimation() {
    document.getElementById('birthday-animation').style.display = 'none';
}



    // Event listener for close button
    document.getElementById('close-animation').addEventListener('click', closeAnimation);

    // Event listener for "Don't show again today" button
    document.getElementById('dont-show-again').addEventListener('click', dontShowAgainToday);

