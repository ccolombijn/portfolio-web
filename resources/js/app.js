import './bootstrap';
document.addEventListener('DOMContentLoaded', () => {

    // =================================================================
    //  SIMPLEX NOISE IMPLEMENTATION
    // =================================================================
    const simplex = (function() {
        const F2 = 0.5 * (Math.sqrt(3.0) - 1.0), G2 = (3.0 - Math.sqrt(3.0)) / 6.0;
        const F3 = 1.0 / 3.0, G3 = 1.0 / 6.0;
        const p = new Uint8Array([151,160,137,91,90,15,131,13,201,95,96,53,194,233,7,225,140,36,103,30,69,142,8,99,37,240,21,10,23,190, 6,148,247,120,234,75,0,26,197,62,94,252,219,203,117,35,11,32,57,177,33,88,237,149,56,87,174,20,125,136,171,168, 68,175,74,165,71,134,139,48,27,166,77,146,158,231,83,111,229,122,60,211,133,230,220,105,92,41,55,46,245,40,244,102,143,54, 65,25,63,161, 1,216,80,73,209,76,132,187,208, 89,18,169,200,196,135,130,116,188,159,86,164,100,109,198,173,186, 3,64,52,217,226,250,124,123,5,202,38,147,118,126,255,82,85,212,207,206,59,227,47,16,58,17,182,189,28,42,223,183,170,213,119,248,152, 2,44,154,163, 70,221,153,101,155,167, 43,172,9,129,22,39,253, 19,98,108,110,79,113,224,232,178,185, 112,104,218,246,97,228,251,34,242,193,238,210,144,12,191,179,162,241, 81,51,145,235,249,14,239,107,49,192,214, 31,181,199,106,157,184, 84,204,176,115,121,50,45,127, 4,150,254,138,236,205,93,222,114,67,29,24,72,243,141,128,195,78,66,215,61,156,180]);
        const perm = new Uint8Array(512), grad3 = new Float32Array([1,1,0,-1,1,0,1,-1,0,-1,-1,0,1,0,1,-1,0,1,1,0,-1,-1,0,-1,0,1,1,0,-1,1,0,1,-1,0,-1,-1]);
        for(let i=0; i<512; i++) perm[i] = p[i & 255];
        function noise3D(x, y, z) {
            let n0, n1, n2, n3; 
            const s = (x + y + z) * F3;
            const i = Math.floor(x + s), j = Math.floor(y + s), k = Math.floor(z + s);
            const t = (i + j + k) * G3;
            const x0 = x - (i - t), y0 = y - (j - t), z0 = z - (k - t);
            let i1, j1, k1, i2, j2, k2;
            if(x0 >= y0) {
                if(y0 >= z0) { i1=1; j1=0; k1=0; i2=1; j2=1; k2=0; }
                else if(x0 >= z0) { i1=1; j1=0; k1=0; i2=1; j2=0; k2=1; }
                else { i1=0; j1=0; k1=1; i2=1; j2=0; k2=1; }
            } else {
                if(y0 < z0) { i1=0; j1=0; k1=1; i2=0; j2=1; k2=1; }
                else if(x0 < z0) { i1=0; j1=1; k1=0; i2=0; j2=1; k2=1; }
                else { i1=0; j1=1; k1=0; i2=1; j2=1; k2=0; }
            }
            const x1 = x0 - i1 + G3, y1 = y0 - j1 + G3, z1 = z0 - k1 + G3;
            const x2 = x0 - i2 + 2.0 * G3, y2 = y0 - j2 + 2.0 * G3, z2 = z0 - k2 + 2.0 * G3;
            const x3 = x0 - 1.0 + 3.0 * G3, y3 = y0 - 1.0 + 3.0 * G3, z3 = z0 - 1.0 + 3.0 * G3;
            const ii = i & 255, jj = j & 255, kk = k & 255;
            let t0 = 0.6 - x0*x0 - y0*y0 - z0*z0;
            if(t0 < 0) n0 = 0.0;
            else { const g = grad3[perm[ii+perm[jj+perm[kk]]] % 12 * 3]; t0 *= t0; n0 = t0 * t0 * (g * x0 + grad3[perm[ii+perm[jj+perm[kk]]] % 12 * 3 + 1] * y0 + grad3[perm[ii+perm[jj+perm[kk]]] % 12 * 3 + 2] * z0); }
            let t1 = 0.6 - x1*x1 - y1*y1 - z1*z1;
            if(t1 < 0) n1 = 0.0;
            else { const g = grad3[perm[ii+i1+perm[jj+j1+perm[kk+k1]]] % 12 * 3]; t1 *= t1; n1 = t1 * t1 * (g * x1 + grad3[perm[ii+i1+perm[jj+j1+perm[kk+k1]]] % 12 * 3 + 1] * y1 + grad3[perm[ii+i1+perm[jj+j1+perm[kk+k1]]] % 12 * 3 + 2] * z1); }
            let t2 = 0.6 - x2*x2 - y2*y2 - z2*z2;
            if(t2 < 0) n2 = 0.0;
            else { const g = grad3[perm[ii+i2+perm[jj+j2+perm[kk+k2]]] % 12 * 3]; t2 *= t2; n2 = t2 * t2 * (g * x2 + grad3[perm[ii+i2+perm[jj+j2+perm[kk+k2]]] % 12 * 3 + 1] * y2 + grad3[perm[ii+i2+perm[jj+j2+perm[kk+k2]]] % 12 * 3 + 2] * z2); }
            let t3 = 0.6 - x3*x3 - y3*y3 - z3*z3;
            if(t3 < 0) n3 = 0.0;
            else { const g = grad3[perm[ii+1+perm[jj+1+perm[kk+1]]] % 12 * 3]; t3 *= t3; n3 = t3 * t3 * (g * x3 + grad3[perm[ii+1+perm[jj+1+perm[kk+1]]] % 12 * 3 + 1] * y3 + grad3[perm[ii+1+perm[jj+1+perm[kk+1]]] % 12 * 3 + 2] * z3); }
            return 32.0 * (n0 + n1 + n2 + n3);
        }
        return { noise3D: noise3D };
    })();
    // =================================================================
    //  EINDE NOISE IMPLEMENTATION
    // =================================================================

    const canvas = document.getElementById('dot-wave-canvas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let time = 0;

    // --- AANGEPASTE INSTELLINGEN ---
    const NOISE_AMPLITUDE = 150;     // VERHOOGD: Voor hogere, meer zichtbare golven
    const NOISE_ZOOM = 0.022;      // Iets aangepast voor een goede golf-grootte
    const NOISE_SPEED = 0.005;      

    const DOT_SPACING = 28;        // VERLAAGD: Voor meer puntjes (dichter op elkaar)
    const DOT_SIZE_MULTIPLIER = 3.5; // NIEUW: Om de puntjes groter te maken
    const PERSPECTIVE = 280;
    // -------------------

    let projection_center_x, projection_center_y;

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        projection_center_x = canvas.width / 2;
        projection_center_y = canvas.height * 0.65;
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let iy = 0; iy < canvas.height + DOT_SPACING; iy += DOT_SPACING) {
            for (let ix = 0; ix < canvas.width + DOT_SPACING; ix += DOT_SPACING) {
                
                const perspective_factor = PERSPECTIVE / (PERSPECTIVE + (canvas.height - iy));
                const projected_x = (ix - projection_center_x) * perspective_factor + projection_center_x;
                const projected_y = (iy - projection_center_y) * perspective_factor + projection_center_y;

                const noise_val = simplex.noise3D(ix * NOISE_ZOOM, iy * NOISE_ZOOM, time);
                
                const final_y = projected_y + (noise_val * NOISE_AMPLITUDE) * perspective_factor;
                
                const scale_factor = (noise_val + 1) / 2;
                
                // AANGEPAST: De grootte wordt nu extra vermenigvuldigd
                const size = DOT_SIZE_MULTIPLIER * perspective_factor * scale_factor;
                const opacity = 0.8 * perspective_factor * scale_factor;

                if (size < 0.2) continue;

                ctx.beginPath();
                ctx.arc(projected_x, final_y, size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
                ctx.fill();
            }
        }
    }

    function animate() {
        draw();
        time += NOISE_SPEED;
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    animate();
});