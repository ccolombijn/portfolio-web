import { createNoise3D } from 'simplex-noise';

document.addEventListener('DOMContentLoaded', () => {

    const noise3D = createNoise3D(Math.random);
    const canvas = document.getElementById('dot-wave-canvas');
    const gradientBg = document.querySelector('.animated-gradient-bg');
    if (!canvas || !gradientBg) {
        console.error("Een van de benodigde elementen (.animated-gradient-bg of #dot-wave-canvas) is niet gevonden.");
        return;
    }

    const ctx = canvas.getContext('2d');
    let time = 0;
    let particles = [];
    const mouse = { x: null, y: null };

    // --- CUSTOMIZATION ---
    const PARTICLE_COUNT = 1500;
    const PARTICLE_SPEED = 0.2;
    const NOISE_SCALE = 1000;
    const MAX_RADIUS = 1.2;
    const COLOR_TRANSITION_SPEED = 0.005;
    const INTERACTION_RADIUS = 200;
    const ATTRACTION_FORCE = 0.002;
    const ORBITAL_FORCE = 0.05;
    const LERP_SPEED = 0.05;
    // -------------------

    let colorState = {
        c1: { r: 31, g: 153, b: 163 },
        c2: { r: 193, g: 70, b: 111 },
        t1: generateRandomColor(),
        t2: generateRandomColor()
    };

    function generateRandomColor() {
        return { r: Math.floor(Math.random() * 256), g: Math.floor(Math.random() * 256), b: Math.floor(Math.random() * 256) };
    }

    function lerp(start, end, amount) {
        return start + (end - start) * amount;
    }

    function setupParticles() {
        particles = [];
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            particles.push({
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                radius: Math.random() * MAX_RADIUS + 0.3,
                opacity: Math.random() * 0.5 + 0.2,
                color_choice: Math.random() > 0.5 ? 1 : 2,
                noise_offset_x: Math.random() * NOISE_SCALE,
                noise_offset_y: Math.random() * NOISE_SCALE,
                vx: 0,
                vy: 0
            });
        }
    }

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        setupParticles();
    }

    function animate() {
        // Kleuranimatie
        colorState.c1.r = lerp(colorState.c1.r, colorState.t1.r, COLOR_TRANSITION_SPEED);
        colorState.c1.g = lerp(colorState.c1.g, colorState.t1.g, COLOR_TRANSITION_SPEED);
        colorState.c1.b = lerp(colorState.c1.b, colorState.t1.b, COLOR_TRANSITION_SPEED);
        colorState.c2.r = lerp(colorState.c2.r, colorState.t2.r, COLOR_TRANSITION_SPEED);
        colorState.c2.g = lerp(colorState.c2.g, colorState.t2.g, COLOR_TRANSITION_SPEED);
        colorState.c2.b = lerp(colorState.c2.b, colorState.t2.b, COLOR_TRANSITION_SPEED);
        gradientBg.style.setProperty('--color1', `rgb(${colorState.c1.r}, ${colorState.c1.g}, ${colorState.c1.b})`);
        gradientBg.style.setProperty('--color2', `rgb(${colorState.c2.r}, ${colorState.c2.g}, ${colorState.c2.b})`);
        if (Math.abs(colorState.c1.r - colorState.t1.r) < 1) { colorState.t1 = generateRandomColor(); }
        if (Math.abs(colorState.c2.r - colorState.t2.r) < 1) { colorState.t2 = generateRandomColor(); }

        // Deeltjesanimatie
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
            // AANGEPAST: De aanroep naar de noise-functie
            const noise_val = noise3D(p.noise_offset_x, p.noise_offset_y, time);
            const angle = noise_val * Math.PI * 2;
            let target_vx = Math.cos(angle) * PARTICLE_SPEED;
            let target_vy = Math.sin(angle) * PARTICLE_SPEED;

            if (mouse.x !== null) {
                const dx = p.x - mouse.x;
                const dy = p.y - mouse.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < INTERACTION_RADIUS) {
                    target_vx -= dx * ATTRACTION_FORCE;
                    target_vy -= dy * ATTRACTION_FORCE;
                    target_vx += dy * ORBITAL_FORCE * (1 - distance / INTERACTION_RADIUS);
                    target_vy -= dx * ORBITAL_FORCE * (1 - distance / INTERACTION_RADIUS);
                }
            }

            p.vx = lerp(p.vx, target_vx, LERP_SPEED);
            p.vy = lerp(p.vy, target_vy, LERP_SPEED);
            p.x += p.vx;
            p.y += p.vy;

            if (p.x > canvas.width + p.radius) p.x = -p.radius;
            if (p.x < -p.radius) p.x = canvas.width + p.radius;
            if (p.y > canvas.height + p.radius) p.y = -p.radius;
            if (p.y < -p.radius) p.y = canvas.height + p.radius;

            const color = p.color_choice === 1 ? colorState.c1 : colorState.c2;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${Math.floor(color.r)}, ${Math.floor(color.g)}, ${Math.floor(color.b)}, ${p.opacity})`;
            ctx.fill();
        });
        
        time += 0.001;
        requestAnimationFrame(animate);
    }

    // Event Listeners voor de muis
    window.addEventListener('mousemove', (event) => {
        mouse.x = event.clientX;
        mouse.y = event.clientY;
    });
    canvas.addEventListener('mouseleave', () => {
        mouse.x = null;
        mouse.y = null;
    });

    // Start alles
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    animate();
});