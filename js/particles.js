(function () {
  var canvas = document.getElementById('particleCanvas');
  if (!canvas) return;

  var ctx = canvas.getContext('2d');
  var W, H;
  var particles = [];
  var stars = [];
  var shootingStars = [];
  var lastShootTime = 0;
  var animId;

  function resize() {
    W = canvas.width = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }

  window.addEventListener('resize', resize);
  resize();

  var PARTICLE_COUNT = Math.min(70, Math.floor(W * H / 15000));
  var STAR_COUNT = Math.min(12, Math.floor(W * H / 100000));
  var MAX_CONNECT_DIST = 150;
  var MAX_CONNECT_DIST2 = MAX_CONNECT_DIST * MAX_CONNECT_DIST;

  function initParticles() {
    particles = [];
    for (var i = 0; i < PARTICLE_COUNT; i++) {
      particles.push({
        x: Math.random() * W,
        y: Math.random() * H,
        r: Math.random() * 1.5 + 0.5,
        dx: (Math.random() - 0.5) * 0.3,
        dy: -(Math.random() * 0.4 + 0.1),
        opacity: Math.random() * 0.5 + 0.2,
        pulse: Math.random() * Math.PI * 2
      });
    }
  }

  function initStars() {
    stars = [];
    for (var i = 0; i < STAR_COUNT; i++) {
      stars.push({
        x: Math.random() * W,
        y: Math.random() * H * 0.5,
        r: Math.random() * 1 + 0.5,
        speed: Math.random() * 0.02 + 0.01,
        phase: Math.random() * Math.PI * 2
      });
    }
  }

  function makeShootingStar() {
    shootingStars.push({
      x: Math.random() * W * 0.8 + W * 0.1,
      y: Math.random() * H * 0.3,
      vx: -(Math.random() * 4 + 3),
      vy: Math.random() * 2 + 1,
      life: 1,
      len: Math.random() * 60 + 40
    });
  }

  function drawParticles(time) {
    for (var i = 0; i < particles.length; i++) {
      var p = particles[i];
      p.x += p.dx;
      p.y += p.dy;
      if (p.y < -10) { p.y = H + 10; p.x = Math.random() * W; }
      if (p.x < -10 || p.x > W + 10) { p.dx *= -1; }

      var opacity = p.opacity * (0.7 + 0.3 * Math.sin(p.pulse + time * 0.001));
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(255, 255, 255, ' + opacity + ')';
      ctx.fill();
    }
  }

  function drawStars(time) {
    for (var i = 0; i < stars.length; i++) {
      var s = stars[i];
      var opacity = 0.3 + 0.5 * (0.5 + 0.5 * Math.sin(s.phase + time * s.speed));
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(255, 255, 255, ' + opacity + ')';
      ctx.fill();
    }
  }

  function drawShootingStars() {
    for (var i = shootingStars.length - 1; i >= 0; i--) {
      var ss = shootingStars[i];
      ss.x += ss.vx;
      ss.y += ss.vy;
      ss.life -= 0.02;
      if (ss.life <= 0) { shootingStars.splice(i, 1); continue; }

      ctx.beginPath();
      ctx.moveTo(ss.x, ss.y);
      ctx.lineTo(ss.x - ss.len * 0.5, ss.y - ss.len * 0.3);
      ctx.strokeStyle = 'rgba(255, 255, 255, ' + (ss.life * 0.4) + ')';
      ctx.lineWidth = 1.5;
      ctx.stroke();
    }
  }

  function drawConnections() {
    var len = particles.length;
    for (var i = 0; i < len; i++) {
      var a = particles[i];
      for (var j = i + 1; j < len; j++) {
        var b = particles[j];
        var dx = a.x - b.x;
        var dy = a.y - b.y;
        var dist2 = dx * dx + dy * dy;
        if (dist2 < MAX_CONNECT_DIST2) {
          var dist = Math.sqrt(dist2);
          var opacity = (1 - dist / MAX_CONNECT_DIST) * 0.3;
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.strokeStyle = 'rgba(255, 255, 255, ' + opacity + ')';
          ctx.lineWidth = 0.6;
          ctx.stroke();
        }
      }
    }
  }

  function animate(time) {
    ctx.clearRect(0, 0, W, H);
    drawConnections();
    drawParticles(time);
    drawStars(time);
    drawShootingStars();

    if (time - lastShootTime > 5000 + Math.random() * 8000) {
      makeShootingStar();
      lastShootTime = time;
    }

    animId = requestAnimationFrame(animate);
  }

  initParticles();
  initStars();

  var visibilityHidden = false;
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      visibilityHidden = true;
      if (animId) cancelAnimationFrame(animId);
    } else {
      visibilityHidden = false;
      animId = requestAnimationFrame(animate);
    }
  });

  animId = requestAnimationFrame(animate);

  window.addEventListener('resize', function () {
    resize();
    initParticles();
    initStars();
  });
})();
