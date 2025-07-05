@extends('component.client.layout.masterlayoutsclient')

@section('title')
    Đặt hàng thành công
@endsection

@section('css')
   <style>
        body {
            background: #000;
            color: white;
            overflow: hidden;
        }

        .success-container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
            padding: 20px;
        }

        .success-box {
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid #00ffcc;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 0 30px #00ffcc;
        }

        .success-box h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #00ffcc;
        }

        .success-box p {
            font-size: 1.2rem;
        }

        canvas {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
        }

    </style>
@endsection

@section('js')
    <script>
        const canvas = document.getElementById("fireworks");
        const ctx = canvas.getContext("2d");

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        window.addEventListener("resize", () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        let particles = [];

        function random(min, max) {
            return Math.random() * (max - min) + min;
        }

        function createFirework(x, y) {
            const count = 100;
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: x,
                    y: y,
                    radius: 2,
                    color: `hsl(${Math.floor(Math.random() * 360)}, 100%, 50%)`,
                    angle: Math.random() * 2 * Math.PI,
                    speed: random(2, 6),
                    life: 100
                });
            }
        }

        function updateParticles() {
            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i];
                p.x += Math.cos(p.angle) * p.speed;
                p.y += Math.sin(p.angle) * p.speed + 0.5;
                p.radius *= 0.96;
                p.life--;
                if (p.life <= 0 || p.radius < 0.5) {
                    particles.splice(i, 1);
                }
            }
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, 2 * Math.PI);
                ctx.fillStyle = p.color;
                ctx.fill();
            });
        }

        function loop() {
            updateParticles();
            drawParticles();
            requestAnimationFrame(loop);
        }

        setInterval(() => {
            const x = random(200, canvas.width - 200);
            const y = random(100, canvas.height / 2);
            createFirework(x, y);
        }, 800);

        loop();
    </script>
@endsection

@section('content')
<section>
      <!-- Canvas Fireworks -->
    <canvas id="fireworks"></canvas>

    <!-- Success Message -->
    <div class="success-container">
        <div class="success-box">
            <h1>🎉 Đặt hàng thành công!</h1>
            <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
            <a href="{{route('myOrder')}}" class="btn btn-outline-light mt-4">Xem đơn hàng của bạn</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Firework JS -->

</section>
@endsection
