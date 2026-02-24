/**
 * ThermalGradient (Three.js) — Y2K Thermal Map Background
 *
 * Three.js ShaderMaterial rendered onto a subdivided PlaneGeometry.
 * Vertex shader: Ashima Arts 3D simplex noise drives the wave surface.
 * Fragment shader: smoothstep color mapping + Y2K pseudo-random film grain.
 * Interaction: spring-physics mouse tracking distorts waves and grain UVs.
 *
 * Requires THREE (global) — load three.min.js before this file.
 *
 * Usage:
 *   const bg = new ThermalGradient(document.getElementById('bg-canvas'));
 *   bg.init().start();
 *
 *   bg.updateConfig({ speed: 0.5, grainIntensity: 0.2 });
 *   bg.stop() / bg.start() / bg.destroy()
 */

// ─────────────────────────────────────────────────────────────────────────────
// Default Configuration
// ─────────────────────────────────────────────────────────────────────────────
const DEFAULT_CONFIG = {
    /**
     * Color palette. Values are linear RGB [0–1] arrays or hex strings.
     *   background — the near-black void    (#020A0D)
     *   teal       — cold ambient halo      (#003040)
     *   orange     — neon thermal band      (#FF4500)
     *   hot        — super-heated core      (#FF8C08)
     */
    colors: {
        background: [0.008, 0.039, 0.051],
        teal:       [0.000, 0.188, 0.250],
        orange:     [1.000, 0.271, 0.000],
        hot:        [1.000, 0.549, 0.031],
    },

    grainIntensity: 0.14,   // 0 = none | 0.25 = heavy Y2K crunch
    waveAmplitude:  0.20,   // max vertex Z displacement
    waveFrequency:  2.5,    // noise spatial frequency
    speed:          0.30,   // animation time multiplier

    /**
     * Mouse influence.
     *   fluidRadius   — exponential falloff exponent: exp(-dist * fluidRadius)
     *                   larger = tighter effect area, smaller = wider
     *   mouseStrength — max elevation pushed up by cursor
     */
    fluidRadius:    2.8,
    mouseStrength:  0.22,

    /** Spring physics. stiffness ↓ = floatier, damping ↓ = more oscillation. */
    spring: {
        stiffness: 0.055,
        damping:   0.82,
    },

    /** PlaneGeometry subdivision. Higher = smoother waves, more GPU cost. */
    segments: 256,
};

// ─────────────────────────────────────────────────────────────────────────────
// Vertex Shader
// ─────────────────────────────────────────────────────────────────────────────
// Three.js automatically injects: projectionMatrix, modelViewMatrix,
// position (attribute vec3), uv (attribute vec2).
const VERTEX_SHADER = /* glsl */`
precision highp float;

// ── Ashima Arts 3D Simplex Noise ──────────────────────────────────────────
// Source: https://github.com/ashima/webgl-noise (MIT licence)
// Authors: Stefan Gustavson, Ian McEwan, Ashima Arts

vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
vec4 permute(vec4 x) { return mod289(((x * 34.0) + 10.0) * x); }
vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }

float snoise(vec3 v) {
    const vec2 C = vec2(1.0/6.0, 1.0/3.0);
    const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);

    vec3 i  = floor(v + dot(v, C.yyy));
    vec3 x0 = v - i + dot(i, C.xxx);

    vec3 g  = step(x0.yzx, x0.xyz);
    vec3 l  = 1.0 - g;
    vec3 i1 = min(g.xyz, l.zxy);
    vec3 i2 = max(g.xyz, l.zxy);

    vec3 x1 = x0 - i1 + C.xxx;
    vec3 x2 = x0 - i2 + C.yyy;
    vec3 x3 = x0 - D.yyy;

    i = mod289(i);
    vec4 p = permute(permute(permute(
        i.z + vec4(0.0, i1.z, i2.z, 1.0)) +
        i.y + vec4(0.0, i1.y, i2.y, 1.0)) +
        i.x + vec4(0.0, i1.x, i2.x, 1.0));

    float n_ = 0.142857142857;
    vec3  ns = n_ * D.wyz - D.xzx;

    vec4 j  = p - 49.0 * floor(p * ns.z * ns.z);
    vec4 x_ = floor(j * ns.z);
    vec4 y_ = floor(j - 7.0 * x_);

    vec4 xs = x_ * ns.x + ns.yyyy;
    vec4 ys = y_ * ns.x + ns.yyyy;
    vec4 h  = 1.0 - abs(xs) - abs(ys);

    vec4 b0 = vec4(xs.xy, ys.xy);
    vec4 b1 = vec4(xs.zw, ys.zw);

    vec4 s0 = floor(b0) * 2.0 + 1.0;
    vec4 s1 = floor(b1) * 2.0 + 1.0;
    vec4 sh = -step(h, vec4(0.0));

    vec4 a0 = b0.xzyw + s0.xzyw * sh.xxyy;
    vec4 a1 = b1.xzyw + s1.xzyw * sh.zzww;

    vec3 p0 = vec3(a0.xy, h.x);
    vec3 p1 = vec3(a0.zw, h.y);
    vec3 p2 = vec3(a1.xy, h.z);
    vec3 p3 = vec3(a1.zw, h.w);

    vec4 norm = taylorInvSqrt(vec4(
        dot(p0,p0), dot(p1,p1), dot(p2,p2), dot(p3,p3)));
    p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;

    vec4 m = max(0.6 - vec4(
        dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot(m*m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
}
// ── End snoise ──────────────────────────────────────────────────────────────

// Uniforms set by ThermalGradient
uniform float uTime;
uniform float uWaveAmplitude;
uniform float uWaveFrequency;
uniform float uWaveSpeed;
uniform vec2  uMouse;          // spring-physics cursor (NDC: [-1,1] x [-1,1])
uniform float uFluidRadius;    // exponential falloff exponent for mouse
uniform float uMouseStrength;  // max elevation added by cursor

// Passed to fragment shader
varying float vElevation;
varying vec2  vUv;

void main() {
    vUv = uv;
    vec3 pos = position;        // PlaneGeometry(2,2,...) → pos.xy in [-1, 1]
    float t  = uTime * uWaveSpeed;

    // ── Animated band center ──────────────────────────────────────────────
    // A single snoise sample drives the horizontal undulation of the ridge.
    float bandCenter = snoise(vec3(pos.x * 0.65, 0.1, t * 0.45)) * 0.38;

    // ── Gaussian ridge profile ────────────────────────────────────────────
    // Concentrates heat near the animated center; falls off with distance.
    float bandDist = pos.y - bandCenter;
    float ridge    = exp(-bandDist * bandDist * 5.5) * uWaveAmplitude;

    // ── Surface detail: two snoise octaves ────────────────────────────────
    float n1 = snoise(vec3(pos.x * uWaveFrequency,
                            pos.y * uWaveFrequency,
                            t));
    float n2 = snoise(vec3(pos.x * uWaveFrequency * 1.9,
                            pos.y * uWaveFrequency * 2.1,
                            t * 1.4 + 3.7)) * 0.5;
    float detail = (n1 + n2) * uWaveAmplitude * 0.12;

    float elevation = ridge + detail;

    // ── Mouse spring distortion (vertex) ─────────────────────────────────
    // Computes exponential falloff around the spring-physics cursor position.
    // Vertices close to the cursor are pushed upward, simulating a fluid bump.
    vec2  toMouse    = pos.xy - uMouse;
    float mouseDist  = length(toMouse);
    float mouseInfl  = exp(-mouseDist * uFluidRadius);
    elevation       += mouseInfl * uMouseStrength;

    pos.z      = elevation;
    vElevation = elevation;

    gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);
}
`;

// ─────────────────────────────────────────────────────────────────────────────
// Fragment Shader
// ─────────────────────────────────────────────────────────────────────────────
const FRAGMENT_SHADER = /* glsl */`
precision highp float;

uniform float uTime;
uniform vec2  uMouse;
uniform float uFluidRadius;
uniform float uWaveAmplitude;
uniform float uMouseStrength;

// Color palette (vec3 via THREE.Color)
uniform vec3 uColorBlack;
uniform vec3 uColorTeal;
uniform vec3 uColorOrange;
uniform vec3 uColorHot;

uniform float uGrainIntensity;

varying float vElevation;
varying vec2  vUv;

// Standard pseudo-random hash referenced in Y2K / demo-scene grain shaders.
// Based on: fract(sin(dot(st, k)) * large_prime) by Patricio Gonzalez Vivo.
float random(vec2 st) {
    return fract(sin(dot(st.xy, vec2(12.9898, 78.233))) * 43758.5453123);
}

void main() {

    // ── Color Mapping via smoothstep / mix ───────────────────────────────
    // Normalize elevation to [0, 1].  Values above uWaveAmplitude come from
    // mouse bumps and are safely clamped at 1.
    float maxE = uWaveAmplitude + uMouseStrength;
    float e    = clamp(vElevation / maxE, 0.0, 1.0);

    // Three nested thermal zones, each blended with mix():
    //   black → teal (ambient cold halo)
    //   teal  → orange (main neon band)
    //   orange → hot (super-heated core)
    vec3 color = uColorBlack;
    color = mix(color, uColorTeal,   smoothstep(0.02, 0.42, e));
    color = mix(color, uColorOrange, smoothstep(0.36, 0.72, e));
    color = mix(color, uColorHot,    smoothstep(0.66, 0.93, e));

    // ── Mouse-Warped Grain UVs ────────────────────────────────────────────
    // Slightly pushes grain texture away from cursor → heat-shimmer feeling.
    vec2  toMouse   = vUv * 2.0 - 1.0 - uMouse;   // vUv remapped to NDC
    float mdist     = length(toMouse);
    float mInfl     = exp(-mdist * uFluidRadius) * 0.025;
    vec2  grainUv   = vUv + normalize(toMouse + 0.001) * mInfl;

    // ── Y2K Film Grain (pseudo-random, 24 fps cadence) ────────────────────
    // Quantise time so grain updates 24×/second — cinematic flicker, not
    // smooth video static. Two scales layered for chunky Y2K clumping.
    float gt = floor(uTime * 24.0) / 24.0;
    float g1 = random(grainUv               + gt);
    float g2 = random(grainUv * 0.47 + vec2(0.17, 0.83) + gt * 1.3);
    float grain = (g1 * 0.70 + g2 * 0.30) * 2.0 - 1.0;   // centre on 0

    // Reduce grain at the overexposed core (simulates bloom / clip).
    float hotMask = smoothstep(0.66, 0.93, e);
    color += grain * uGrainIntensity * (1.0 - hotMask * 0.5);

    gl_FragColor = vec4(clamp(color, 0.0, 1.0), 1.0);
}
`;

// ─────────────────────────────────────────────────────────────────────────────
// Spring Physics
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Simple Hooke's-Law spring that tracks a 2-D target position.
 *
 * Each call to update() advances the simulation by one frame:
 *   1. Compute restoring force proportional to displacement (F = −k·x)
 *   2. Accumulate force into velocity
 *   3. Apply damping to velocity (friction)
 *   4. Integrate position
 *
 * The result is an interruptible elastic animation: changing direction
 * mid-flight causes the spring to overshoot, then settle — giving the
 * "fluid inertia" feel the effect needs.
 */
class Spring {
    constructor({ stiffness = 0.055, damping = 0.82 } = {}) {
        this.stiffness = stiffness;
        this.damping   = damping;
        // Current state
        this.x  = 0;  this.y  = 0;   // spring position (sent to shader)
        this.vx = 0;  this.vy = 0;   // velocity
        // Target (raw cursor)
        this.tx = 0;  this.ty = 0;
    }

    setTarget(x, y) {
        this.tx = x;
        this.ty = y;
    }

    /** Advance one tick; call once per animation frame. */
    update() {
        // Restoring force
        this.vx += (this.tx - this.x) * this.stiffness;
        this.vy += (this.ty - this.y) * this.stiffness;
        // Friction
        this.vx *= this.damping;
        this.vy *= this.damping;
        // Integration
        this.x  += this.vx;
        this.y  += this.vy;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// ThermalGradient Class
// ─────────────────────────────────────────────────────────────────────────────

class ThermalGradient {
    // Three.js objects
    #renderer = null;
    #scene    = null;
    #camera   = null;
    #mesh     = null;
    #uniforms = null;

    // State
    #canvas;
    #config;
    #spring;
    #startTime;
    #rafId      = null;
    #isRunning  = false;
    #resizeObs  = null;
    #dpr        = 1;

    /**
     * @param {HTMLCanvasElement} canvas
     * @param {Partial<typeof DEFAULT_CONFIG>} config
     */
    constructor(canvas, config = {}) {
        if (!(canvas instanceof HTMLCanvasElement)) {
            throw new TypeError('[ThermalGradient] First argument must be an HTMLCanvasElement.');
        }
        this.#canvas    = canvas;
        this.#config    = this.#merge(DEFAULT_CONFIG, config);
        this.#spring    = new Spring(this.#config.spring);
        this.#dpr       = Math.min(window.devicePixelRatio ?? 1, 2);
        this.#startTime = performance.now();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /** Build the Three.js scene. Call once before start(). @returns {this} */
    init() {
        if (typeof THREE === 'undefined') {
            console.error('[ThermalGradient] THREE is not defined. Load three.min.js first.');
            return this;
        }

        // ── Renderer ───────────────────────────────────────────────────────
        this.#renderer = new THREE.WebGLRenderer({
            canvas:           this.#canvas,
            antialias:        false,
            alpha:            false,
            powerPreference:  'high-performance',
        });
        this.#renderer.setPixelRatio(this.#dpr);
        this.#renderer.outputColorSpace = THREE.LinearSRGBColorSpace;

        // ── Scene & Camera ─────────────────────────────────────────────────
        this.#scene  = new THREE.Scene();
        this.#camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 10);
        this.#camera.position.z = 2;

        // ── Uniforms ───────────────────────────────────────────────────────
        this.#uniforms = this.#buildUniforms();

        // ── Geometry + Material ────────────────────────────────────────────
        // High-subdivision plane fills the ortho frustum exactly (2 × 2 units).
        const seg      = this.#config.segments;
        const geometry = new THREE.PlaneGeometry(2, 2, seg, seg);
        const material = new THREE.ShaderMaterial({
            vertexShader:   VERTEX_SHADER,
            fragmentShader: FRAGMENT_SHADER,
            uniforms:       this.#uniforms,
        });

        this.#mesh = new THREE.Mesh(geometry, material);
        this.#scene.add(this.#mesh);

        // ── Events ─────────────────────────────────────────────────────────
        this.#setupMouse();
        this.#setupResize();
        this.#resize();   // initial size

        return this;
    }

    /** Start (or resume) the RAF loop. @returns {this} */
    start() {
        if (this.#isRunning || !this.#renderer) return this;
        this.#isRunning = true;
        this.#rafId = requestAnimationFrame(this.#tick.bind(this));
        return this;
    }

    /** Pause without destroying WebGL state. @returns {this} */
    stop() {
        this.#isRunning = false;
        if (this.#rafId !== null) { cancelAnimationFrame(this.#rafId); this.#rafId = null; }
        return this;
    }

    /**
     * Merge new values into the live config and push them to uniforms.
     * Supports partial updates; only provided keys are changed.
     * @param {Partial<typeof DEFAULT_CONFIG>} patch
     * @returns {this}
     */
    updateConfig(patch) {
        this.#config = this.#merge(this.#config, patch);
        if (this.#uniforms) this.#syncUniforms();
        if (patch.spring)   Object.assign(this.#spring, patch.spring);
        return this;
    }

    /** Cancel loop, disconnect observer, release Three.js resources. */
    destroy() {
        this.stop();
        this.#resizeObs?.disconnect();
        window.removeEventListener('mousemove', this.#onMouseMove);
        if (this.#renderer) {
            this.#mesh?.geometry.dispose();
            this.#mesh?.material.dispose();
            this.#renderer.dispose();
        }
        this.#renderer = this.#scene = this.#camera = this.#mesh = null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Setup
    // ─────────────────────────────────────────────────────────────────────────

    #buildUniforms() {
        const c = this.#config;
        const tc = (v) => Array.isArray(v)
            ? new THREE.Color(v[0], v[1], v[2])
            : new THREE.Color(v);

        return {
            uTime:          { value: 0.0 },
            uWaveAmplitude: { value: c.waveAmplitude },
            uWaveFrequency: { value: c.waveFrequency },
            uWaveSpeed:     { value: c.speed },
            uMouse:         { value: new THREE.Vector2(0, 0) },
            uFluidRadius:   { value: c.fluidRadius },
            uMouseStrength: { value: c.mouseStrength },
            uColorBlack:    { value: tc(c.colors.background) },
            uColorTeal:     { value: tc(c.colors.teal) },
            uColorOrange:   { value: tc(c.colors.orange) },
            uColorHot:      { value: tc(c.colors.hot) },
            uGrainIntensity:{ value: c.grainIntensity },
        };
    }

    #syncUniforms() {
        const c  = this.#config;
        const u  = this.#uniforms;
        const tc = (v) => Array.isArray(v)
            ? new THREE.Color(v[0], v[1], v[2])
            : new THREE.Color(v);

        u.uWaveAmplitude.value  = c.waveAmplitude;
        u.uWaveFrequency.value  = c.waveFrequency;
        u.uWaveSpeed.value      = c.speed;
        u.uFluidRadius.value    = c.fluidRadius;
        u.uMouseStrength.value  = c.mouseStrength;
        u.uGrainIntensity.value = c.grainIntensity;
        u.uColorBlack.value     = tc(c.colors.background);
        u.uColorTeal.value      = tc(c.colors.teal);
        u.uColorOrange.value    = tc(c.colors.orange);
        u.uColorHot.value       = tc(c.colors.hot);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Mouse / Spring
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Converts DOM pixel coords → NDC [-1, 1] and feeds the spring target.
     * Stored as an arrow function so we can remove the exact same reference
     * in destroy().
     */
    #onMouseMove = (e) => {
        this.#spring.setTarget(
             (e.clientX / window.innerWidth)  * 2 - 1,
            -((e.clientY / window.innerHeight) * 2 - 1),
        );
    };

    #setupMouse() {
        // Window-level listener: canvas has pointer-events:none so events
        // bubble to window regardless of where the cursor is on the page.
        window.addEventListener('mousemove', this.#onMouseMove, { passive: true });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Resize
    // ─────────────────────────────────────────────────────────────────────────

    #setupResize() {
        this.#resizeObs = new ResizeObserver(() => this.#resize());
        this.#resizeObs.observe(this.#canvas);
    }

    #resize() {
        const w = this.#canvas.clientWidth;
        const h = this.#canvas.clientHeight;
        if (this.#renderer) {
            this.#renderer.setSize(w, h, false); // false = don't touch CSS
            this.#renderer.setPixelRatio(this.#dpr);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Render Loop
    // ─────────────────────────────────────────────────────────────────────────

    #tick(timestamp) {
        if (!this.#isRunning) return;

        // ── Spring update ─────────────────────────────────────────────────
        // One integration step per frame. The spring position trails the
        // raw cursor with elastic inertia.
        this.#spring.update();

        // ── Upload uniforms ───────────────────────────────────────────────
        const elapsed = (timestamp - this.#startTime) * 0.001;
        this.#uniforms.uTime.value = elapsed;
        this.#uniforms.uMouse.value.set(this.#spring.x, this.#spring.y);

        // ── Render ────────────────────────────────────────────────────────
        this.#renderer.render(this.#scene, this.#camera);

        this.#rafId = requestAnimationFrame(this.#tick.bind(this));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Utility
    // ─────────────────────────────────────────────────────────────────────────

    /** Deep-merges two config objects; handles nested `colors` and `spring`. */
    #merge(base, overrides) {
        const merged = { ...base };
        for (const [k, v] of Object.entries(overrides)) {
            if ((k === 'colors' || k === 'spring') && v && typeof v === 'object') {
                merged[k] = { ...base[k], ...v };
            } else {
                merged[k] = v;
            }
        }
        return merged;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Export
// ─────────────────────────────────────────────────────────────────────────────
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ThermalGradient, DEFAULT_CONFIG };
} else {
    window.ThermalGradient  = ThermalGradient;
    window.THERMAL_DEFAULTS = DEFAULT_CONFIG;
}
