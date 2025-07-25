import { createNoise3D } from 'simplex-noise';
export const headerBackgroundEffects = () => {
    document.addEventListener('DOMContentLoaded', () => {

        const noise3D = createNoise3D(Math.random);

        const canvas = document.getElementById('dot-particles-canvas');
        const gradientBg = document.querySelector('.animated-gradient-bg');
        if (!canvas || !gradientBg) {
            console.error("Een van de benodigde elementen (.animated-gradient-bg of #dot-particles-canvas) is niet gevonden.");
            return;
        }

        const ctx = canvas.getContext('2d');
        let time = 0;
        let particles = [];
        const mouse = { x: null, y: null };

        // --- CUSTOMIZATION ---
        const PARTICLE_COUNT = 200;
        const PARTICLE_SPEED = 0.2;
        const NOISE_SCALE = 1000;
        const MAX_RADIUS = 1.5;
        const BLUR_MULTIPLIER = 4.0;
        const COLOR_TRANSITION_SPEED = 0.005;
        const INTERACTION_RADIUS = 200;
        const ATTRACTION_FORCE = 0.002;
        const ORBITAL_FORCE = 0.05;
        const LERP_SPEED = 0.05;
        // -------------------

        function generateCompatibleColorPair() {
            const h = Math.random();
            const s = 0.5 + Math.random() * 0.2;
            const l = 0.45 + Math.random() * 0.1;
            const color1 = hslToRgb(h, s, l);
            const secondHue = (h + 0.45 + Math.random() * 0.1) % 1.0;
            const color2 = hslToRgb(secondHue, s, l);
            return [color1, color2];
        }
        
        function hslToRgb(h, s, l) {
            let r, g, b;
            if (s == 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                };
                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }
            return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
        }
    
        const initialColors = generateCompatibleColorPair();
        let colorState = {
            c1: initialColors[0],
            c2: initialColors[1],
            t1: generateCompatibleColorPair()[0],
            t2: generateCompatibleColorPair()[1]
        };

        function lerp(start, end, amount) {
            return start + (end - start) * amount;
        }

        function setupParticles() {
            particles = [];
            for (let i = 0; i < PARTICLE_COUNT; i++) {
                const radius = Math.random() * MAX_RADIUS + 0.3;
                particles.push({
                    x: Math.random() * window.innerWidth,
                    y: Math.random() * window.innerHeight,
                    radius: radius,
                    opacity: Math.random() * 0.5 + 0.2,
                    color_choice: Math.random() > 0.5 ? 1 : 2,
                    noise_offset_x: Math.random() * NOISE_SCALE,
                    noise_offset_y: Math.random() * NOISE_SCALE,
                    vx: 0,
                    vy: 0,
                    blur: radius * BLUR_MULTIPLIER
                });
            }
        }
    
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            setupParticles();
        }
    
        function animate() {
            // Color animation
            colorState.c1.r = lerp(colorState.c1.r, colorState.t1.r, COLOR_TRANSITION_SPEED);
            colorState.c1.g = lerp(colorState.c1.g, colorState.t1.g, COLOR_TRANSITION_SPEED);
            colorState.c1.b = lerp(colorState.c1.b, colorState.t1.b, COLOR_TRANSITION_SPEED);
            colorState.c2.r = lerp(colorState.c2.r, colorState.t2.r, COLOR_TRANSITION_SPEED);
            colorState.c2.g = lerp(colorState.c2.g, colorState.t2.g, COLOR_TRANSITION_SPEED);
            colorState.c2.b = lerp(colorState.c2.b, colorState.t2.b, COLOR_TRANSITION_SPEED);
            gradientBg.style.setProperty('--color1', `rgb(${colorState.c1.r}, ${colorState.c1.g}, ${colorState.c1.b})`);
            gradientBg.style.setProperty('--color2', `rgb(${colorState.c2.r}, ${colorState.c2.g}, ${colorState.c2.b})`);
            
            if (Math.abs(colorState.c1.r - colorState.t1.r) < 1) {
                const newPair = generateCompatibleColorPair();
                colorState.t1 = newPair[0];
                colorState.t2 = newPair[1];
            }
    
            // Particle animation
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
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
                const colorString = `rgb(${Math.floor(color.r)}, ${Math.floor(color.g)}, ${Math.floor(color.b)})`;

                ctx.shadowBlur = p.blur;
                ctx.shadowColor = colorString;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${Math.floor(color.r)}, ${Math.floor(color.g)}, ${Math.floor(color.b)}, ${p.opacity})`;
                ctx.fill();

                ctx.shadowBlur = 0;
            });
            
            time += 0.001;
            requestAnimationFrame(animate);
        }

        // Event Listeners for the mouse
        window.addEventListener('mousemove', (event) => {
            mouse.x = event.clientX;
            mouse.y = event.clientY;
        });
        canvas.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        animate();
    });
}