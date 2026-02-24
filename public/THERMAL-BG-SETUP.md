# Thermal Gradient — Setup & Usage Guide

A GPU-driven WebGL background with a Y2K thermal camera aesthetic.
No dependencies. Runs entirely in the browser via raw WebGL (GLSL shaders).

---

## File Structure

```
public/
├── bg.html                  ← demo page (open this in a browser)
├── js/
│   └── thermal-gradient.js  ← the WebGL class + shaders
└── THERMAL-BG-SETUP.md      ← this file

resources/views/
└── bg.blade.php             ← Laravel Blade partial (@include('bg'))
```

---

## Running the Demo

The demo page (`bg.html`) is a **plain HTML file** — no build step required.

### Option A — Laravel Herd (recommended for this project)

The file is already inside `public/`, so Herd serves it automatically.
Open in your browser:

```
http://frontend.test/bg.html
```

### Option B — Any static server

```bash
# Python (3.x)
python -m http.server 8000 --directory public
# → open http://localhost:8000/bg.html

# Node (npx)
npx serve public
# → open the URL it prints
```

### Option C — Direct file open *(may not work)*

Opening `bg.html` via `file://` will likely be blocked by CORS restrictions on
the script import. Use one of the server options above instead.

---

## Using in a Laravel View

Include the Blade partial in any layout:

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    @include('bg')   {{-- drops in the canvas + script --}}

    <main>
        <!-- your page content here — sits on top of the background -->
    </main>
</body>
</html>
```

The canvas is `position: fixed; z-index: -1` so it never interferes with layout
or pointer events.

---

## Live Configuration (browser console)

All parameters can be updated at runtime without reloading the page:

```js
// Slow it down
thermalBg.updateConfig({ speed: 0.1 });

// Crank the grain for extra Y2K crunch
thermalBg.updateConfig({ grainIntensity: 0.22 });

// Move the hot band to the lower third
thermalBg.updateConfig({ bandPosition: 0.65 });

// Make the band tighter and hotter-looking
thermalBg.updateConfig({ bandWidth: 0.05, glowSpread: 5.0 });

// Swap the orange to a neon cyan for a different thermal palette
thermalBg.updateConfig({ colors: { orange: [0.0, 1.0, 0.9] } });

// Pause / resume
thermalBg.stop();
thermalBg.start();

// Full teardown (removes RAF loop + ResizeObserver + WebGL resources)
thermalBg.destroy();
```

---

## Configuration Reference

All config keys and their defaults are in `thermal-gradient.js` (`DEFAULT_CONFIG`).

| Key | Default | Description |
|-----|---------|-------------|
| `colors.background` | `[0.008, 0.039, 0.051]` | Near-black void (`#020A0D`) |
| `colors.teal` | `[0.000, 0.188, 0.250]` | Cold ambient halo (`#003040`) |
| `colors.orange` | `[1.000, 0.271, 0.000]` | Neon band stripe (`#FF4500`) |
| `colors.hot` | `[1.000, 0.549, 0.031]` | Super-heated core (`#FF8C08`) |
| `grainIntensity` | `0.14` | Film grain strength (0 – 0.3) |
| `waveAmplitude` | `0.18` | Max vertical wave displacement (0 – 0.5) |
| `waveFrequency` | `2.2` | Noise spatial frequency |
| `speed` | `0.25` | Animation speed multiplier |
| `bandPosition` | `0.5` | Band vertical center (0 = top, 1 = bottom) |
| `bandWidth` | `0.08` | Core half-width of the hot stripe |
| `glowSpread` | `3.8` | Teal halo radius (× bandWidth) |

Colors are **linear RGB**, each channel in `[0.0, 1.0]`.
Convert hex: `#FF4500` → `[255/255, 69/255, 0/255]` → `[1.0, 0.271, 0.0]`.

---

## How It Works

```
Vertex Shader
  └─ Full-screen triangle (3 verts, 1 draw call)
  └─ Passes UV coords to fragment shader

Fragment Shader
  ├─ Domain-warped FBM (2-level warp, 4 octaves each)
  │   └─ Gives the wave its self-folding, never-repeating motion
  ├─ Smooth color masks (3 nested smoothstep zones)
  │   └─ teal halo → orange band → hot core
  ├─ Film grain (3 pixel-scale hash layers @ 24 fps)
  │   └─ Chunky Y2K-era clumping, not smooth static
  └─ Subtle vignette (UV-edge darkening, no extra uniforms)
```

The canvas is DPR-aware (capped at 2×) and uses `ResizeObserver` for responsive
resizing without polling.

---

## Browser Support

Requires WebGL 1.0 (GLSL ES 1.00). Supported in all modern browsers since 2011.
Falls back gracefully — the page body has `background-color: #020a0d` as a static
fallback if WebGL is unavailable.
