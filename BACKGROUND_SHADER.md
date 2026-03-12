# Case Study: Procedural WebGL Background Shader

The hero background is a real-time procedural animation running entirely on the GPU. No video, no sprite sheet, no CSS animation — every frame is computed from scratch by a GLSL fragment shader executing per-pixel on the graphics card.

---

## Architecture

```
Browser
  └─ Three.js (WebGL context)
       ├─ OrthographicCamera (-1, 1, 1, -1)   ← no perspective, no transforms
       ├─ PlaneGeometry (2, 2)                 ← full clip-space quad
       ├─ Trivial vertex shader                ← just passes position through
       └─ Fragment shader                      ← all logic lives here
            ├─ snoise()    3D Simplex noise
            ├─ fbm()       Fractional Brownian Motion
            ├─ pattern()   Triple domain warping
            ├─ contourVein()  Iso-line extraction
            ├─ Color mixing & gradient zones
            ├─ Mouse & logo distortion
            └─ Post-processing filters
```

The vertex shader does nothing:
```glsl
void main() { gl_Position = vec4(position, 1.0); }
```
All computation happens in the fragment shader, which runs once per screen pixel on every rendered frame.

---

## Two Versions

| | `bg.blade.php` (configurator) | `public/js/hero-shader.js` (production) |
|---|---|---|
| Purpose | Interactive design tool | Embedded in welcome.blade.php |
| fBm octaves | 4 | 3 (performance) |
| Frame rate | uncapped | throttled to 30 fps |
| Pause when hidden | no | yes (IntersectionObserver) |
| Logo distortion | no | yes (`uLogoCenter` / `uLogoSize`) |
| Filters | film grain, vignette, chroma, scanlines, glow | film grain, vignette |
| UI | 60+ sliders, color pickers, export/import | none |
| Export | JSON → "Neat" format compatible with Neat.js | — |

The configurator is the authoring environment. The production shader is a stripped, tuned version with the final values baked in as uniform defaults.

---

## The Noise Foundation

### 3D Simplex Noise — `snoise(vec3 v)`

Simplex noise is a gradient noise algorithm by Ken Perlin (2001), replacing the original Perlin noise grid with a simplex lattice (tetrahedra in 3D). This eliminates axis-aligned artifacts and is faster to compute.

The implementation used here is Stefan Gustavson's GLSL port:

1. **Skew** input point into simplex cell coordinates
2. **Find** which simplex tetrahedron the point is in
3. **Compute** 4 corner gradients via a permutation polynomial (`mod289 + permute`)
4. **Blend** them with a quintic falloff kernel: `max(0.6 - dot(x,x), 0.0)^4`
5. **Return** in range `[-1, 1]`

The `mod289` trick avoids integer overflow on the GPU:
```glsl
vec4 mod289(vec4 x) { return x - floor(x * (1.0/289.0)) * 289.0; }
```
Permutation polynomial `((x * 34.0) + 10.0) * x mod 289` produces a pseudo-random scatter without a texture lookup. `taylorInvSqrt` approximates `1/sqrt(r)` cheaply.

The third dimension of `vec3 v` is the time axis — as `t` increases, the noise field evolves smoothly.

### Fractional Brownian Motion — `fbm(vec3 p)`

fBm stacks multiple layers of noise at increasing frequencies and decreasing amplitudes:

```glsl
float fbm(vec3 p) {
    float value = 0.0, amplitude = 0.5, frequency = 1.0;
    for (int i = 0; i < 4; i++) {   // 3 octaves in production
        value += amplitude * snoise(p * frequency);
        frequency *= 2.0;    // each octave is twice as fine
        amplitude *= 0.5;    // each octave is half as strong
    }
    return value;
}
```

The result is self-similar multi-scale turbulence — looks like clouds, smoke, or fluid at many scales simultaneously. Output range is approximately `[-1, 1]`.

---

## Triple Domain Warping — `pattern(vec2 p, float t, vec2 mouseOff)`

This is the core visual algorithm. The technique is Inigo Quilez's "domain warping" applied three times:

```glsl
float pattern(vec2 p, float t, vec2 mouseOff) {
    vec3 p3 = vec3(p, t);

    // Level 1: sample two independent fBm values → flow field q
    vec2 q = vec2(
        fbm(p3),
        fbm(p3 + vec3(5.2, 1.3, 0.0))
    );

    // Level 2: sample fBm offset by q (+ mouse) → r
    vec3 q3 = vec3(q, 0.0);
    vec3 mo  = vec3(mouseOff, 0.0);
    vec2 r = vec2(
        fbm(p3 + uFlowScale * q3 + vec3(1.7, 9.2, 0.0) + mo),
        fbm(p3 + uFlowScale * q3 + vec3(8.3, 2.8, 0.0) + mo)
    );

    // Level 3: final fBm offset by r → the pattern value
    return fbm(p3 + vec3(uFlowScale * r, 0.0));
}
```

Each level uses the previous level's output as a positional offset for the next sample. The result is complex, swirling, turbulent flow that is impossible to achieve with a single noise evaluation. The `vec3(5.2, 1.3, 0.0)` and similar offsets decorrelate the two fBm channels so they produce independent-looking flow directions.

`uFlowScale` (default 0.5 in production) controls how far apart the warped samples are pulled — higher values produce more chaotic distortion.

**Temporal animation**: `flowP = p + vec2(t * uFlowDistortionA, t * uFlowDistortionB)` — the sample position drifts over time. Since both X and Y drift rates differ (`1.7` vs `0.1`), the field flows asymmetrically, creating the sense of slow organic movement rather than uniform scrolling.

---

## Coordinate System

Before the pattern is evaluated, screen coordinates are prepared:

```glsl
vec2 uv = gl_FragCoord.xy / uResolution;     // 0..1
float aspect = uResolution.x / uResolution.y;

vec2 p = vec2(
    uv.x * aspect * (uHorizontalPressure / 5.0),
    uv.y * (uVerticalPressure / 4.0)
) * uZoom;
```

- **Aspect correction** prevents the pattern from stretching non-uniformly on wide screens
- **Pressure** parameters act as independent X/Y scale multipliers — higher horizontal pressure compresses the pattern horizontally, increasing vein density in that axis
- **Zoom** scales the entire coordinate space — lower zoom shows more of the pattern (wider veins), higher zoom zooms into it (finer detail)

---

## Vein Extraction — Iso-Contour Lines

The final noise value `n` is a scalar field across the screen. Veins are extracted by finding where this field crosses specific threshold values (iso-contours):

```glsl
float contourVein(float n, float level, float width) {
    float dist = abs(n - level);
    return 1.0 - smoothstep(0.0, width, dist);
}
```

This returns 1.0 where `n == level` and falls off to 0.0 within `width` units. `smoothstep` gives a smooth, anti-aliased edge.

Eight contour levels are sampled across the noise range:

```glsl
float spacing = uVeinEnd * 0.12 + 0.08;   // ~0.152 with defaults

for (int i = 0; i < 8; i++) {
    float level = -0.6 + float(i) * spacing;
    float v = contourVein(n, level, veinWidth);
    if (v > bestVein) {
        bestVein = v;
        bestWarm = mod(float(i), 2.0);  // 0 or 1: alternates warm/cool
    }
}
```

Only the strongest (closest) vein survives per pixel. Odd-indexed contours are "warm", even-indexed are "cool" — this creates the alternating red/teal color pattern across adjacent veins.

---

## Vein Gradient: 4-Zone Coloring

The `bestVein` value (0 = void edge, 1 = vein centerline) is mapped to four nested zones using nested smoothstep calls:

```
veinShaped (after pow falloff)
  │
  0 ──────────┬──────────┬──────────┬────────── 1
   [void]  outerZone  midZone  coreZone  centerZone
```

```glsl
float veinShaped = pow(bestVein, uVeinFalloff);  // sharpens vein edges

float outerZone  = smoothstep(0.0,           1.0-uCoreRatio,   veinShaped);
float midZone    = smoothstep(1.0-uCoreRatio, 1.0-uMidRatio,   veinShaped);
float coreZone   = smoothstep(1.0-uMidRatio,  1.0-uCenterRatio, veinShaped);
float centerZone = smoothstep(1.0-uCenterRatio, 1.0,            veinShaped);
```

With production values (`uVeinFalloff: 6.0, uCoreRatio: 0.95, uMidRatio: 0.34, uCenterRatio: 0.29`), this creates a very sharp, thin core surrounded by a narrow transition ring — most of the vein is void, with a tight bright center. The high falloff power (6.0 vs 1.8 in configurator) is what makes production veins appear as thin glowing lines rather than broad bands.

Each zone blends toward a color from the dual palette:

```glsl
// warm or cool palette based on contour alternation
vec3 outerCol  = mix(uColCoolOuter,  uColWarmOuter,  warmZone);
vec3 midCol    = mix(uColCoolMid,    uColWarmMid,    warmZone);
vec3 centerCol = mix(uColCoolCenter, uColWarmCenter, warmZone);

vec3 col = mix(uColVoid, uColProceduralBg, 0.1);  // base void color
col = mix(col, outerCol,  outerZone  * uBaseTealMix);   // 0.6
col = mix(col, midCol,    midZone    * uCrimsonMix);     // 0.9
col = mix(col, centerCol, coreZone   * uOrangeBright);   // 0.5
col = mix(col, centerCol, centerZone * uOrangeBright);   // 0.5
```

### Production Color Palette

| Uniform | Hex | Role |
|---|---|---|
| `uColVoid` | `#060303` | Near-black background |
| `uColProceduralBg` | `#0E0707` | Subtle procedural base |
| `uColWarmOuter` | `#002547` | Deep navy outer edge |
| `uColWarmMid` | `#441D09` | Dark burnt mid transition |
| `uColWarmCenter` | `#FF0000` | Red/crimson vein center |
| `uColCoolOuter` | `#053748` | Deep teal outer edge |
| `uColCoolMid` | `#032F3F` | Muted teal mid |
| `uColCoolCenter` | `#FF0000` | Same red center |

The warm/cool split is nearly imperceptible at production values — both palettes converge on red centers with very dark outers. The visual result is thin red-core veins on a near-black teal-tinted void.

---

## Mouse Interaction

Mouse position is tracked in JavaScript and lerped toward the real cursor each frame:

```js
const lerp = 1.0 - uniforms.uMouseDecay.value;   // 0.1 with decay=0.9
mouseCurrent.x += (mouseTarget.x - mouseCurrent.x) * lerp;
```

Decay of 0.9 → lerp factor 0.1 → the shader position follows the cursor slowly (10% per frame). At 30fps this gives ~0.7 second lag for the offset to fully arrive.

Inside the shader, the mouse creates two distinct effects:

**1. Pattern distortion** — fed into `pattern()` as `mouseOffset`:
```glsl
float mouseDist  = length(uv - mouseUV);
float mouseInfl  = uMouseInfluence * exp(-mouseDist² / uMouseRadius²);
vec2  mouseOffset = normalize(uv - mouseUV) * mouseInfl * uMouseStrength;
```
A Gaussian kernel (`exp(-d²/r²)`) produces a smooth circular influence zone. The offset perturbs the domain warping at level 2, pulling the flow field toward/away from the cursor position.

**2. Darkening** — applied to final color:
```glsl
float darkenArea = exp(-mouseDist² / (uMouseRadius² * 2.0));
col *= 1.0 - darkenArea * uMouseDarken;
```
A wider Gaussian (2× radius) darkens the area under the cursor, creating the sense of depth or shadow under the pointer.

---

## Logo Distortion (Production Only)

The hero logo position is tracked via DOM bounding rect and passed to the shader:

```js
function updateLogoPos() {
    const mr = mark.getBoundingClientRect();
    const cx = (mr.left + mr.width / 2) * dpr;
    const cy = (canvas.height - (mr.top + mr.height / 2)) * dpr;   // flip Y
    uniforms.uLogoCenter.value.set(cx, cy);
    uniforms.uLogoSize.value.set(mr.width * dpr, mr.height * dpr);
}
```

The logo position updates on scroll via a rAF-throttled scroll listener. Inside the shader, a radial influence zone centered on the logo pushes the pattern outward — veins visually part around the logo mark as if repelled:

```glsl
vec2 dLogo    = (uv - logoUV) / max(logoRad, vec2(0.001));
float logoDist = length(dLogo);
float logoInfl = 0.8 * exp(-logoDist² * 0.8);
vec2  logoOffset = normalize(uv - logoUV) * logoInfl * 0.06;
```

`mouseOffset + logoOffset` are both fed into the domain warping. The logo effect is subtle (strength 0.06 vs mouse 0.06 at defaults) but consistent.

---

## Post-Processing Filters

Applied in sequence to the final color after all geometry is computed.

### Film Grain on Void

Two overlapping layers of hash-based noise at different frequencies and speeds:

```glsl
float fg1 = fract(sin(dot(fgUV + t1, vec2(12.9898, 78.233))) * 43758.5453);
float fg2 = fract(sin(dot(fgUV * 1.7 + t2, vec2(39.346, 11.135))) * 28462.6341);
float fg = fg1 * 0.6 + fg2 * 0.4;
```

`fract(sin(dot(...)) * largeNumber)` is the classic "hash" function — deterministic pseudo-random per pixel. Two temporally distinct `t1, t2` offsets (at incommensurate rates 5.37 and 3.91) prevent periodic flash patterns. The blended result is signed: `(fg - 0.5) * 2.0` → range `[-1, 1]`, applied multiplicatively so grain both brightens and darkens.

`uFilmGrainVoidOnly` (0.85) masks the grain to the void: `mix(1.0, 1.0 - veinShaped, 0.85)` — grain is almost fully suppressed on bright veins and strongest in the dark void between them, mimicking how film grain appears most in shadow regions.

### Vignette

Standard radial darkening toward screen edges:

```glsl
float vig = smoothstep(uVignetteRadius, uVignetteRadius - uVignetteSoftness, length(uv - 0.5));
col *= mix(1.0, vig, uVignetteStrength);
```

The `smoothstep` inverts (inner is 1, outer approaches 0) because the arguments are reversed — `(from_radius, to_radius - softness)` creates the falloff.

### Global Grain (Separate System)

A lighter, always-on grain system independent of the film grain filter:

```glsl
float grain = fract(sin(dot(grainUV + fract(uTime * uGrainSpeed * 7.123), vec2(12.9898, 78.233))) * 43758.5453);
grain = smoothstep(uGrainSparsity, 1.0, grain);
col = mix(col, col * (0.5 + grain), uGrainIntensity * 0.15);
```

`uGrainIntensity * 0.15` makes this layer subtle (max ~15% contribution). `smoothstep(sparsity, 1.0, grain)` with sparsity=0 passes all grain through; higher sparsity thresholds away the quieter grain values for a more sparse, coarse texture.

---

## Performance Architecture (Production)

### 30 fps Throttle

The fragment shader is computationally expensive — 3 levels of domain warping × 3 fBm calls × 3 octaves each = 9 `snoise()` evaluations per pixel, each involving a 3D tetrahedron search. At 1080p that's ~18 million noise evaluations per frame.

Throttling to 30fps halves GPU load vs 60fps with no perceptible quality difference for a slow ambient animation:

```js
let lastFrame = 0;
const frameBudget = 1000 / 30;

(function animate(ts) {
    requestAnimationFrame(animate);
    if (!heroVisible || document.hidden) return;
    if (ts - lastFrame < frameBudget) return;
    lastFrame = ts;
    // ... render
})(0);
```

### IntersectionObserver Pause

When the hero section scrolls off screen, rendering is suspended entirely via `heroVisible` flag. The `document.hidden` check also halts rendering when the tab is backgrounded.

### DPR Cap

`Math.min(window.devicePixelRatio, isMobile ? 1 : 1.5)` — on mobile the shader runs at native 1× pixel density (not 3× Retina), cutting fragment shader invocations to ~1/9 of a naive implementation on a 3× display.

### Debounced Resize

The Three.js resize handler has a 150ms debounce to avoid running the resolution change at 60fps while the browser resize event fires continuously.

---

## The Configurator (`/bg-configurator`)

The bg-configurator is a standalone authoring tool built on the same shader but with an interactive panel exposing all ~60 uniforms as real-time controls. It has no production dependencies.

**Control wiring** is data-driven — sliders carry a `data-u` attribute matching the uniform name, so a single loop wires all of them:

```js
document.querySelectorAll('input[type="range"]').forEach(el => {
    const uniformName = el.dataset.u;
    el.addEventListener('input', () => {
        uniforms[uniformName].value = parseFloat(el.value);
    });
});
```

**Export / Import**: The current state can be serialized to JSON and reimported. The "Neat" export button maps the shader uniforms to the Neat.js library format, which is what an older draft of welcome.blade.php used before the custom shader replaced it.

**Shader differences from production**: The configurator version runs 4 fBm octaves (vs 3), includes chromatic aberration, scanlines, and glow filters not present in production, uses different default values (zoom 1.2 vs 0.51, speed 0.5 vs 0.06), and has no logo distortion. It is a superset of the production shader's feature set.

---

## Data Flow Summary

```
JavaScript (every ~33ms)
  uTime       ← performance.now() / 1000
  uMouse      ← lerped cursor position (decay = 0.9)
  uLogoCenter ← DOM getBoundingClientRect() (on scroll)
  uResolution ← window dimensions × dpr
       ↓
Fragment Shader (per pixel)
  uv  → aspect-corrected pressure-scaled coordinates p
  p   → pattern(flowP, t, mouseOffset)
           ├─ q  = [fbm(p3), fbm(p3 + offset)]
           ├─ r  = [fbm(p3 + scale*q + mo), fbm(p3 + scale*q + mo)]
           └─ n  = fbm(p3 + scale*r)          ← scalar noise field
  n   → 8× contourVein() → bestVein + bestWarm
  bestVein → pow(falloff) → 4 zone smoothsteps
  zones → mix() with warm/cool palette → col
  col → grain → film grain → vignette → clamp → gl_FragColor
```

---

## Key Production Uniform Values

| Uniform | Value | Effect |
|---|---|---|
| `uZoom` | 0.51 | Very zoomed out — wide, sparse veins |
| `uSpeed` | 0.06 | Extremely slow drift |
| `uFlowScale` | 0.5 | Low warp intensity — subtle, not chaotic |
| `uVeinFalloff` | 6.0 | Very sharp falloff — thin, precise vein lines |
| `uCoreRatio` | 0.95 | Almost all vein width is "core" — no wide outer bloom |
| `uMouseInfluence` | 0.8 | Subtle cursor distortion |
| `uMouseDarken` | 0.24 | Mild cursor shadow |
| `uGrainIntensity` | 0.575 | Visible film texture |
| `uFilmGrainVoidOnly` | 0.85 | Grain concentrated in dark void regions |
| `uVignetteStrength` | 0.4 | Moderate edge darkening |
