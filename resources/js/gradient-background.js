import * as THREE from 'three';

const vertexShader = /* glsl */ `
void main() {
    gl_Position = vec4(position, 1.0);
}
`;

const fragmentShader = /* glsl */ `
precision highp float;

uniform float uTime;
uniform vec2 uResolution;
uniform vec2 uMouse;

//
// Simplex 3D noise — Stefan Gustavson (Ashima Arts)
//
vec4 mod289(vec4 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
vec3 mod289(vec3 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
vec2 mod289(vec2 x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }
float mod289(float x) { return x - floor(x * (1.0 / 289.0)) * 289.0; }

vec4 permute(vec4 x) { return mod289(((x * 34.0) + 10.0) * x); }
vec3 permute(vec3 x) { return mod289(((x * 34.0) + 10.0) * x); }

vec4 taylorInvSqrt(vec4 r) { return 1.79284291400159 - 0.85373472095314 * r; }

float snoise(vec3 v) {
    const vec2 C = vec2(1.0 / 6.0, 1.0 / 3.0);
    const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);

    vec3 i  = floor(v + dot(v, C.yyy));
    vec3 x0 = v - i + dot(i, C.xxx);

    vec3 g = step(x0.yzx, x0.xyz);
    vec3 l = 1.0 - g;
    vec3 i1 = min(g.xyz, l.zxy);
    vec3 i2 = max(g.xyz, l.zxy);

    vec3 x1 = x0 - i1 + C.xxx;
    vec3 x2 = x0 - i2 + C.yyy;
    vec3 x3 = x0 - D.yyy;

    i = mod289(i);
    vec4 p = permute(permute(permute(
        i.z + vec4(0.0, i1.z, i2.z, 1.0))
      + i.y + vec4(0.0, i1.y, i2.y, 1.0))
      + i.x + vec4(0.0, i1.x, i2.x, 1.0));

    float n_ = 0.142857142857;
    vec3 ns = n_ * D.wyz - D.xzx;

    vec4 j = p - 49.0 * floor(p * ns.z * ns.z);

    vec4 x_ = floor(j * ns.z);
    vec4 y_ = floor(j - 7.0 * x_);

    vec4 x = x_ * ns.x + ns.yyyy;
    vec4 y = y_ * ns.x + ns.yyyy;
    vec4 h = 1.0 - abs(x) - abs(y);

    vec4 b0 = vec4(x.xy, y.xy);
    vec4 b1 = vec4(x.zw, y.zw);

    vec4 s0 = floor(b0) * 2.0 + 1.0;
    vec4 s1 = floor(b1) * 2.0 + 1.0;
    vec4 sh = -step(h, vec4(0.0));

    vec4 a0 = b0.xzyw + s0.xzyw * sh.xxyy;
    vec4 a1 = b1.xzyw + s1.xzyw * sh.zzww;

    vec3 p0 = vec3(a0.xy, h.x);
    vec3 p1 = vec3(a0.zw, h.y);
    vec3 p2 = vec3(a1.xy, h.z);
    vec3 p3 = vec3(a1.zw, h.w);

    vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2,p2), dot(p3,p3)));
    p0 *= norm.x;
    p1 *= norm.y;
    p2 *= norm.z;
    p3 *= norm.w;

    vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot(m * m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
}

// FBM — 6 octaves
float fbm(vec3 p) {
    float value = 0.0;
    float amplitude = 0.5;
    float frequency = 1.0;
    for (int i = 0; i < 6; i++) {
        value += amplitude * snoise(p * frequency);
        frequency *= 2.0;
        amplitude *= 0.5;
    }
    return value;
}

// Domain-warped pattern — two levels of warping
float pattern(vec2 p, float t) {
    vec3 p3 = vec3(p, t);

    // First warp layer (q)
    vec2 q = vec2(
        fbm(p3 + vec3(0.0, 0.0, 0.0)),
        fbm(p3 + vec3(5.2, 1.3, 0.0))
    );

    // Mouse distortion — push domain coords away from cursor
    vec2 uv = gl_FragCoord.xy / uResolution;
    vec2 mouseUV = uMouse / uResolution;
    float mouseDist = length(uv - mouseUV);
    float mouseInfluence = 2.0 * exp(-mouseDist * mouseDist / (0.2 * 0.2));
    vec2 mouseOffset = normalize(uv - mouseUV + 0.001) * mouseInfluence * 0.15;

    // Second warp layer (r)
    vec3 q3 = vec3(q, 0.0);
    vec3 mo = vec3(mouseOffset, 0.0);

    vec2 r = vec2(
        fbm(p3 + 4.0 * q3 + vec3(1.7, 9.2, 0.0) + mo),
        fbm(p3 + 4.0 * q3 + vec3(8.3, 2.8, 0.0) + mo)
    );

    return fbm(p3 + vec3(4.0 * r, 0.0));
}

void main() {
    vec2 uv = gl_FragCoord.xy / uResolution;
    float aspect = uResolution.x / uResolution.y;

    // Scale UV for noise sampling — flowScale=5
    vec2 p = vec2(uv.x * aspect, uv.y) * 5.0;

    // Slow animation — flowDistortionA=1.7, flowDistortionB=0.1
    float t = uTime * 0.05;
    vec2 flowP = p + vec2(t * 1.7, t * 0.1);

    float n = pattern(flowP, t);

    // Palette colors
    vec3 colVoid     = vec3(0.055, 0.027, 0.027);   // #0E0707
    vec3 colShadow   = vec3(0.020, 0.063, 0.078);   // #051014
    vec3 colCrimson   = vec3(0.435, 0.067, 0.024);   // #6F1106
    vec3 colDarkTeal  = vec3(0.000, 0.102, 0.137);   // #001A23
    vec3 colOrange    = vec3(1.000, 0.267, 0.000);   // #FF4500
    vec3 colTeal      = vec3(0.020, 0.369, 0.490);   // #055E7D

    // Remap noise from [-1,1] to [0,1] range
    float v = n * 0.5 + 0.5;

    // Color mapping with smoothstep bands — sharp transitions (colorBlending=3)
    // shadows=5, highlights=4: strong contrast
    vec3 col = colVoid;

    // Deep shadow layer
    col = mix(col, colShadow, smoothstep(0.0, 0.25, v));

    // Dark teal veins
    col = mix(col, colDarkTeal, smoothstep(0.20, 0.38, v) * 0.8);

    // Crimson vein body
    col = mix(col, colCrimson, smoothstep(0.35, 0.50, v));

    // Teal accent (sparingly)
    col = mix(col, colTeal, smoothstep(0.48, 0.58, v) * 0.4);

    // Bright orange-red highlights
    col = mix(col, colOrange, smoothstep(0.55, 0.72, v) * 0.7);

    // Push darks deeper (shadows=5)
    col *= mix(0.6, 1.0, smoothstep(0.0, 0.4, v));

    // Edge glow — detect high-frequency transitions via screen-space derivative
    float dx = dFdx(n);
    float dy = dFdy(n);
    float edge = length(vec2(dx, dy));
    // Amplify edges and map to glow
    float edgeGlow = smoothstep(0.02, 0.15, edge);
    col = mix(col, colOrange, edgeGlow * 0.65);

    // Additional bright core on strongest edges
    col += colOrange * smoothstep(0.10, 0.22, edge) * 0.3;

    // Film grain — grainScale=2
    vec2 grainUV = uv * uResolution / 2.0;
    float grain = fract(sin(dot(grainUV + fract(uTime * 7.123), vec2(12.9898, 78.233))) * 43758.5453);
    col = mix(col, vec3(grain), 0.06);

    gl_FragColor = vec4(col, 1.0);
}
`;

let renderer = null;
let scene = null;
let camera = null;
let material = null;
let animationId = null;
let mouseTarget = { x: 0, y: 0 };
let mouseCurrent = { x: 0, y: 0 };

function onMouseMove(e) {
    const dpr = Math.min(window.devicePixelRatio, 2);
    mouseTarget.x = e.clientX * dpr;
    mouseTarget.y = (window.innerHeight - e.clientY) * dpr;
}

function onResize() {
    if (!renderer || !material) return;
    const dpr = Math.min(window.devicePixelRatio, 2);
    const w = window.innerWidth;
    const h = window.innerHeight;
    renderer.setSize(w, h);
    renderer.setPixelRatio(dpr);
    material.uniforms.uResolution.value.set(w * dpr, h * dpr);
}

/**
 * Initialize the WebGL marbled-vein background.
 *
 * @param {HTMLCanvasElement} canvas - The canvas element to render into.
 */
export function initBackground(canvas) {
    const dpr = Math.min(window.devicePixelRatio, 2);

    renderer = new THREE.WebGLRenderer({ canvas, alpha: false, antialias: false });
    renderer.setPixelRatio(dpr);
    renderer.setSize(window.innerWidth, window.innerHeight);

    scene = new THREE.Scene();
    camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

    material = new THREE.ShaderMaterial({
        vertexShader,
        fragmentShader,
        uniforms: {
            uTime: { value: 0 },
            uResolution: { value: new THREE.Vector2(window.innerWidth * dpr, window.innerHeight * dpr) },
            uMouse: { value: new THREE.Vector2(0, 0) },
        },
        depthTest: false,
        depthWrite: false,
    });

    const mesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material);
    scene.add(mesh);

    // Style canvas
    canvas.style.position = 'fixed';
    canvas.style.inset = '0';
    canvas.style.zIndex = '-1';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.pointerEvents = 'none';
    canvas.style.display = 'block';

    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('resize', onResize);

    const startTime = performance.now();

    function animate() {
        animationId = requestAnimationFrame(animate);

        const elapsed = (performance.now() - startTime) / 1000;
        material.uniforms.uTime.value = elapsed;

        // Smooth mouse follow (decay rate ~0.9)
        mouseCurrent.x += (mouseTarget.x - mouseCurrent.x) * 0.1;
        mouseCurrent.y += (mouseTarget.y - mouseCurrent.y) * 0.1;
        material.uniforms.uMouse.value.set(mouseCurrent.x, mouseCurrent.y);

        renderer.render(scene, camera);
    }

    animate();
}

/**
 * Tear down the WebGL background and free resources.
 */
export function destroyBackground() {
    if (animationId !== null) {
        cancelAnimationFrame(animationId);
        animationId = null;
    }

    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('resize', onResize);

    if (material) {
        material.dispose();
        material = null;
    }
    if (renderer) {
        renderer.dispose();
        renderer = null;
    }
    scene = null;
    camera = null;
}
