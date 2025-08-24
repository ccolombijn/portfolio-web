import { createNoise3D } from 'simplex-noise';

type Noise3D = (x: number, y: number, z: number) => number;

interface RGBColor {
    r: number;
    g: number;
    b: number;
}

interface Particle {
    x: number;
    y: number;
    radius: number;
    opacity: number;
    color_choice: 1 | 2;
    noise_offset_x: number;
    noise_offset_y: number;
    vx: number;
    vy: number;
    blur: number;
}

interface MousePosition {
    x: number | null;
    y: number | null;
}

interface ColorState {
    c1: RGBColor;
    c2: RGBColor;
    t1: RGBColor;
    t2: RGBColor;
}

type QualityTier = {
    count: number;
    blurMultiplier: number;
};

/**
 * 
 */
export const headerBackgroundEffects = (): void => {
    document.addEventListener('DOMContentLoaded', () => {

        const noise3D: Noise3D = createNoise3D(Math.random);

        const canvas = document.getElementById('dot-particles-canvas') as HTMLCanvasElement;
        const gradientBg = document.querySelector('.animated-gradient-bg') as HTMLElement;
        
        if (!canvas || !gradientBg) {
            console.warn("headerBackgroundEffects: One of the required elements (.animated-gradient-bg or #dot-particles-canvas) not found. Aborting.");
            return;
        }

        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('headerBackgroundEffects: Could not acquire 2d context of canvas. Aborting.');
            return;
        }

        // --- Adaptive Performance Setup ---
        const qualityTiers: { [key: string]: QualityTier } = {
            high:   { count: 250, blurMultiplier: 2.0 },
            medium: { count: 150, blurMultiplier: 1.5 },
            low:    { count: 75,  blurMultiplier: 0 }
        };
        let currentTier: keyof typeof qualityTiers = 'high';
        let lastTime: number = 0;
        let frameSamples: number[] = [];
        let performanceChecked: boolean = false;
        // --- End Adaptive Performance Setup ---

        let time: number = 0;
        let particles: Particle[] = [];
        const mouse: MousePosition = { x: null, y: null };

        // --- CUSTOMIZATION ---
        const PARTICLE_SPEED: number = 0.2;
        const NOISE_SCALE: number = 1000;
        const MAX_RADIUS: number = 1.5;
        const COLOR_TRANSITION_SPEED: number = 0.005;
        const INTERACTION_RADIUS: number = 200;
        const ATTRACTION_FORCE: number = 0.002;
        const ORBITAL_FORCE: number = 0.05;
        const LERP_SPEED: number = 0.05;
        // -------------------
        /**
         * 
         * @returns {array}
         */
        function generateCompatibleColorPair(): [RGBColor, RGBColor] {
            const h = Math.random();
            const s = 0.5 + Math.random() * 0.2;
            const l = 0.45 + Math.random() * 0.1;
            const color1 = hslToRgb(h, s, l);
            const secondHue = (h + 0.45 + Math.random() * 0.1) % 1.0;
            const color2 = hslToRgb(secondHue, s, l);
            return [color1, color2];
        }
        /**
         * 
         * @param h 
         * @param s 
         * @param l 
         * @returns {object}
         */
        function hslToRgb(h: number, s: number, l: number): RGBColor {
            let r: number, g: number, b: number;
            if (s === 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p: number, q: number, t: number): number => {
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
        let colorState: ColorState = {
            c1: initialColors[0],
            c2: initialColors[1],
            t1: generateCompatibleColorPair()[0],
            t2: generateCompatibleColorPair()[1]
        };
        /**
         * 
         * @param start 
         * @param end 
         * @param amount 
         * @returns {number}
         */
        function lerp(start: number, end: number, amount: number): number {
            return start + (end - start) * amount;
        }
        /**
         * @returns {void}
         */
        function setupParticles(): void {
            particles = [];
            const settings = qualityTiers[currentTier]; // Use settings from the current tier
            for (let i = 0; i < settings.count; i++) {
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
                    blur: radius * settings.blurMultiplier
                });
            }
        }
        /**
         * @returns {void}
         */
        function resizeCanvas(): void {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            setupParticles();
        }
        /**
         * 
         * @returns {void}
         */
        function checkAndAdaptPerformance(): void {
            if (performanceChecked || frameSamples.length < 120) return;
            performanceChecked = true;
            const averageDelta = frameSamples.reduce((a, b) => a + b, 0) / frameSamples.length;
            const averageFPS = 1000 / averageDelta;
            console.log(`ℹ️ Performance benchmark: Average FPS is ${averageFPS.toFixed(1)}`);

            if (averageFPS < 45 && currentTier === 'high') {
                console.warn("Performance is low. Downgrading to Medium quality.");
                currentTier = 'medium';
                resizeCanvas();
            } else if (averageFPS < 30 && currentTier === 'medium') {
                console.warn("Performance is still low. Downgrading to Low quality.");
                currentTier = 'low';
                resizeCanvas();
            }
        }
        /**
         * 
         * @param currentTime 
         * @returns {void}
         */
        function animate(currentTime: number): void {
            // Performance measurement for the first few seconds
            if (!performanceChecked) {
                const deltaTime = currentTime - lastTime;
                lastTime = currentTime;
                if (deltaTime > 0 && deltaTime < 100) { // Ignore large gaps
                    frameSamples.push(deltaTime);
                    if (frameSamples.length > 120) frameSamples.shift();
                }
            }

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

                if (mouse.x !== null && mouse.y !== null) {
                    const dx = p.x - mouse.x;
                    const dy = p.y - mouse.y;
                    const distanceSq = dx * dx + dy * dy;

                    if (distanceSq < INTERACTION_RADIUS * INTERACTION_RADIUS) {
                        const distance = Math.sqrt(distanceSq);
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

                if (p.blur > 0.1) {
                    ctx.shadowBlur = p.blur;
                    ctx.shadowColor = colorString;
                }

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${Math.floor(color.r)}, ${Math.floor(color.g)}, ${Math.floor(color.b)}, ${p.opacity})`;
                ctx.fill();

                if (p.blur > 0.1) {
                    ctx.shadowBlur = 0;
                }
            });
            
            time += 0.001;
            requestAnimationFrame(animate);
        }

        window.addEventListener('mousemove', (event: MouseEvent) => {
            mouse.x = event.clientX;
            mouse.y = event.clientY;
        });

        canvas.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        animate(0); // Start the animation loop
        setTimeout(checkAndAdaptPerformance, 3000);
    });
}