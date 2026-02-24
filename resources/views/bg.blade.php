<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Background Preview</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; overflow: hidden; background: #0E0707; }

  #panel {
    position: fixed; top: 0; right: 0; z-index: 10;
    width: 340px; height: 100vh; overflow-y: auto;
    background: rgba(10, 5, 5, 0.92); backdrop-filter: blur(12px);
    border-left: 1px solid rgba(255,255,255,0.06);
    padding: 16px 14px; font-family: -apple-system, system-ui, sans-serif;
    color: #ccc; font-size: 11px; transition: transform 0.3s ease;
    scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent;
  }
  #panel::-webkit-scrollbar { width: 4px; }
  #panel::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
  #panel.collapsed { transform: translateX(100%); }

  #panel-toggle {
    position: fixed; top: 12px; right: 12px; z-index: 11;
    background: rgba(10,5,5,0.8); border: 1px solid rgba(255,255,255,0.1);
    color: #aaa; font-size: 11px; padding: 6px 12px; border-radius: 6px;
    cursor: pointer; font-family: inherit; transition: all 0.2s;
  }
  #panel-toggle:hover { background: rgba(30,15,15,0.9); color: #fff; }

  .section { margin-bottom: 14px; }
  .section-title {
    font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px;
    color: #555; margin-bottom: 8px; padding-bottom: 5px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
  }

  .control { margin-bottom: 7px; }
  .control label {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 2px; color: #888; font-size: 11px;
  }
  .control label span.val { color: #bbb; font-variant-numeric: tabular-nums; font-size: 10px; }

  .control input[type="range"] {
    -webkit-appearance: none; width: 100%; height: 3px;
    background: rgba(255,255,255,0.07); border-radius: 2px; outline: none;
  }
  .control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 12px; height: 12px;
    background: #c44; border-radius: 50%; cursor: pointer;
  }

  .control input[type="color"] {
    -webkit-appearance: none; border: 1px solid rgba(255,255,255,0.1);
    width: 100%; height: 24px; border-radius: 4px; cursor: pointer;
    background: transparent; padding: 0;
  }
  .control input[type="color"]::-webkit-color-swatch-wrapper { padding: 2px; }
  .control input[type="color"]::-webkit-color-swatch { border-radius: 3px; border: none; }

  .toggle-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 7px; color: #888; font-size: 11px;
  }
  .toggle-row input[type="checkbox"] { accent-color: #c44; }

  .filter-sub {
    padding-left: 8px; margin-bottom: 10px;
    border-left: 2px solid rgba(255,255,255,0.04);
    transition: opacity 0.2s, max-height 0.3s;
  }
  .filter-sub.disabled { opacity: 0.3; pointer-events: none; max-height: 0; overflow: hidden; }

  .btn-row { display: flex; gap: 6px; margin-top: 10px; }
  .btn {
    flex: 1; padding: 6px; font-size: 10px; border-radius: 5px;
    border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04);
    color: #aaa; cursor: pointer; font-family: inherit; transition: all 0.15s;
  }
  .btn:hover { background: rgba(255,255,255,0.08); color: #fff; }

  #export-output {
    margin-top: 8px; padding: 6px; font-size: 9px; font-family: monospace;
    background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.06);
    border-radius: 4px; color: #888; word-break: break-all; display: none;
    max-height: 100px; overflow-y: auto;
  }
</style>
</head>
<body>
<canvas id="bg"></canvas>

<button id="panel-toggle">Settings</button>

<div id="panel">

  <div class="section">
    <div class="section-title">Flow & Scale</div>
    <div class="control">
      <label>Zoom <span class="val" data-for="zoom">1.20</span></label>
      <input type="range" data-u="uZoom" id="zoom" min="0.1" max="8.0" step="0.01" value="1.20">
    </div>
    <div class="control">
      <label>Speed <span class="val" data-for="speed">0.50</span></label>
      <input type="range" data-u="uSpeed" id="speed" min="0.0" max="4.0" step="0.01" value="0.50">
    </div>
    <div class="control">
      <label>Flow Scale <span class="val" data-for="flowScale">5.00</span></label>
      <input type="range" data-u="uFlowScale" id="flowScale" min="0.5" max="20.0" step="0.1" value="5.00">
    </div>
    <div class="control">
      <label>Flow Ease <span class="val" data-for="flowEase">1.00</span></label>
      <input type="range" data-u="uFlowEase" id="flowEase" min="0.0" max="4.0" step="0.01" value="1.00">
    </div>
    <div class="control">
      <label>Flow Distortion A <span class="val" data-for="flowDistortionA">1.70</span></label>
      <input type="range" data-u="uFlowDistortionA" id="flowDistortionA" min="0.0" max="5.0" step="0.01" value="1.70">
    </div>
    <div class="control">
      <label>Flow Distortion B <span class="val" data-for="flowDistortionB">0.10</span></label>
      <input type="range" data-u="uFlowDistortionB" id="flowDistortionB" min="0.0" max="5.0" step="0.01" value="0.10">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Pressure & Waves</div>
    <div class="control">
      <label>Horizontal Pressure <span class="val" data-for="horizontalPressure">5.00</span></label>
      <input type="range" data-u="uHorizontalPressure" id="horizontalPressure" min="0.0" max="10.0" step="0.1" value="5.00">
    </div>
    <div class="control">
      <label>Vertical Pressure <span class="val" data-for="verticalPressure">4.00</span></label>
      <input type="range" data-u="uVerticalPressure" id="verticalPressure" min="0.0" max="10.0" step="0.1" value="4.00">
    </div>
    <div class="control">
      <label>Wave Freq X <span class="val" data-for="waveFrequencyX">0.00</span></label>
      <input type="range" data-u="uWaveFrequencyX" id="waveFrequencyX" min="0.0" max="10.0" step="0.1" value="0.00">
    </div>
    <div class="control">
      <label>Wave Freq Y <span class="val" data-for="waveFrequencyY">0.00</span></label>
      <input type="range" data-u="uWaveFrequencyY" id="waveFrequencyY" min="0.0" max="10.0" step="0.1" value="0.00">
    </div>
    <div class="control">
      <label>Wave Amplitude <span class="val" data-for="waveAmplitude">0.00</span></label>
      <input type="range" data-u="uWaveAmplitude" id="waveAmplitude" min="0.0" max="5.0" step="0.01" value="0.00">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Vein Thresholds</div>
    <div class="control">
      <label>Vein Start <span class="val" data-for="veinStart">0.10</span></label>
      <input type="range" data-u="uVeinStart" id="veinStart" min="0.0" max="4.0" step="0.01" value="0.10">
    </div>
    <div class="control">
      <label>Vein End <span class="val" data-for="veinEnd">0.60</span></label>
      <input type="range" data-u="uVeinEnd" id="veinEnd" min="0.05" max="8.0" step="0.01" value="0.60">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Color Mixing</div>
    <div class="control">
      <label>Shadows <span class="val" data-for="shadows">5.00</span></label>
      <input type="range" data-u="uShadows" id="shadows" min="0.0" max="10.0" step="0.1" value="5.00">
    </div>
    <div class="control">
      <label>Highlights <span class="val" data-for="highlights">4.00</span></label>
      <input type="range" data-u="uHighlights" id="highlights" min="0.0" max="10.0" step="0.1" value="4.00">
    </div>
    <div class="control">
      <label>Color Brightness <span class="val" data-for="colorBrightness">1.00</span></label>
      <input type="range" data-u="uColorBrightness" id="colorBrightness" min="0.0" max="3.0" step="0.01" value="1.00">
    </div>
    <div class="control">
      <label>Color Saturation <span class="val" data-for="colorSaturation">0.00</span></label>
      <input type="range" data-u="uColorSaturation" id="colorSaturation" min="-1.0" max="2.0" step="0.01" value="0.00">
    </div>
    <div class="control">
      <label>Color Blending <span class="val" data-for="colorBlending">3.00</span></label>
      <input type="range" data-u="uColorBlending" id="colorBlending" min="0.0" max="10.0" step="0.1" value="3.00">
    </div>
    <div class="control">
      <label>Outer Opacity <span class="val" data-for="baseTealMix">0.60</span></label>
      <input type="range" data-u="uBaseTealMix" id="baseTealMix" min="0.0" max="1.0" step="0.01" value="0.60">
    </div>
    <div class="control">
      <label>Core Opacity <span class="val" data-for="crimsonMix">0.90</span></label>
      <input type="range" data-u="uCrimsonMix" id="crimsonMix" min="0.0" max="1.0" step="0.01" value="0.90">
    </div>
    <div class="control">
      <label>Center Opacity <span class="val" data-for="orangeBright">0.50</span></label>
      <input type="range" data-u="uOrangeBright" id="orangeBright" min="0.0" max="1.0" step="0.01" value="0.50">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Mouse</div>
    <div class="control">
      <label>Distortion Strength <span class="val" data-for="mouseInfluence">2.00</span></label>
      <input type="range" data-u="uMouseInfluence" id="mouseInfluence" min="0.0" max="8.0" step="0.01" value="2.00">
    </div>
    <div class="control">
      <label>Distortion Radius <span class="val" data-for="mouseRadius">0.20</span></label>
      <input type="range" data-u="uMouseRadius" id="mouseRadius" min="0.01" max="2.0" step="0.01" value="0.20">
    </div>
    <div class="control">
      <label>Offset Strength <span class="val" data-for="mouseStrength">0.15</span></label>
      <input type="range" data-u="uMouseStrength" id="mouseStrength" min="0.0" max="1.0" step="0.01" value="0.15">
    </div>
    <div class="control">
      <label>Decay Rate <span class="val" data-for="mouseDecay">0.90</span></label>
      <input type="range" data-u="uMouseDecay" id="mouseDecay" min="0.5" max="0.99" step="0.01" value="0.90">
    </div>
    <div class="control">
      <label>Darken <span class="val" data-for="mouseDarken">0.24</span></label>
      <input type="range" data-u="uMouseDarken" id="mouseDarken" min="0.0" max="1.0" step="0.01" value="0.24">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Grain</div>
    <div class="control">
      <label>Grain Intensity <span class="val" data-for="grainIntensity">0.58</span></label>
      <input type="range" data-u="uGrainIntensity" id="grainIntensity" min="0.0" max="1.0" step="0.01" value="0.575">
    </div>
    <div class="control">
      <label>Grain Scale <span class="val" data-for="grainScale">2.00</span></label>
      <input type="range" data-u="uGrainScale" id="grainScale" min="0.5" max="8.0" step="0.1" value="2.00">
    </div>
    <div class="control">
      <label>Grain Speed <span class="val" data-for="grainSpeed">0.10</span></label>
      <input type="range" data-u="uGrainSpeed" id="grainSpeed" min="0.0" max="2.0" step="0.01" value="0.10">
    </div>
    <div class="control">
      <label>Grain Sparsity <span class="val" data-for="grainSparsity">0.00</span></label>
      <input type="range" data-u="uGrainSparsity" id="grainSparsity" min="0.0" max="1.0" step="0.01" value="0.00">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Y Offset</div>
    <div class="control">
      <label>Y Offset <span class="val" data-for="yOffset">0.00</span></label>
      <input type="range" data-u="uYOffset" id="yOffset" min="-1.0" max="1.0" step="0.01" value="0.00">
    </div>
    <div class="control">
      <label>Y Wave Multiplier <span class="val" data-for="yOffsetWave">20.00</span></label>
      <input type="range" data-u="uYOffsetWave" id="yOffsetWave" min="0.0" max="50.0" step="0.1" value="20.00">
    </div>
    <div class="control">
      <label>Y Color Multiplier <span class="val" data-for="yOffsetColor">20.00</span></label>
      <input type="range" data-u="uYOffsetColor" id="yOffsetColor" min="0.0" max="50.0" step="0.1" value="20.00">
    </div>
    <div class="control">
      <label>Y Flow Multiplier <span class="val" data-for="yOffsetFlow">20.00</span></label>
      <input type="range" data-u="uYOffsetFlow" id="yOffsetFlow" min="0.0" max="50.0" step="0.1" value="20.00">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Vein Gradient</div>
    <div class="control">
      <label>Falloff Power <span class="val" data-for="veinFalloff">1.80</span></label>
      <input type="range" data-u="uVeinFalloff" id="veinFalloff" min="0.2" max="6.0" step="0.01" value="1.80">
    </div>
    <div class="control">
      <label>Core Width <span class="val" data-for="coreRatio">0.55</span></label>
      <input type="range" data-u="uCoreRatio" id="coreRatio" min="0.05" max="0.95" step="0.01" value="0.55">
    </div>
    <div class="control">
      <label>Mid Width <span class="val" data-for="midRatio">0.40</span></label>
      <input type="range" data-u="uMidRatio" id="midRatio" min="0.05" max="0.9" step="0.01" value="0.40">
    </div>
    <div class="control">
      <label>Center Width <span class="val" data-for="centerRatio">0.25</span></label>
      <input type="range" data-u="uCenterRatio" id="centerRatio" min="0.02" max="0.8" step="0.01" value="0.25">
    </div>
    <div class="control">
      <label>Warm Outer</label>
      <input type="color" id="colWarmOuter" value="#6f1106">
    </div>
    <div class="control">
      <label>Warm Mid</label>
      <input type="color" id="colWarmMid" value="#ff4500">
    </div>
    <div class="control">
      <label>Warm Center</label>
      <input type="color" id="colWarmCenter" value="#055e7d">
    </div>
    <div class="control">
      <label>Cool Outer</label>
      <input type="color" id="colCoolOuter" value="#001a23">
    </div>
    <div class="control">
      <label>Cool Mid</label>
      <input type="color" id="colCoolMid" value="#055e7d">
    </div>
    <div class="control">
      <label>Cool Center</label>
      <input type="color" id="colCoolCenter" value="#001a23">
    </div>
  </div>

  <div class="section">
    <div class="section-title">Filters</div>

    <div class="toggle-row">
      <span>Film Grain (Void)</span>
      <input type="checkbox" id="filterFilmGrain" checked>
    </div>
    <div class="filter-sub" data-filter="filterFilmGrain">
      <div class="control">
        <label>Intensity <span class="val" data-for="filmGrainIntensity">0.35</span></label>
        <input type="range" data-u="uFilmGrainIntensity" id="filmGrainIntensity" min="0.0" max="1.0" step="0.01" value="0.35">
      </div>
      <div class="control">
        <label>Scale <span class="val" data-for="filmGrainScale">1.50</span></label>
        <input type="range" data-u="uFilmGrainScale" id="filmGrainScale" min="0.5" max="6.0" step="0.1" value="1.50">
      </div>
      <div class="control">
        <label>Speed <span class="val" data-for="filmGrainSpeed">0.50</span></label>
        <input type="range" data-u="uFilmGrainSpeed" id="filmGrainSpeed" min="0.0" max="3.0" step="0.01" value="0.50">
      </div>
      <div class="control">
        <label>Void Only <span class="val" data-for="filmGrainVoidOnly">0.85</span></label>
        <input type="range" data-u="uFilmGrainVoidOnly" id="filmGrainVoidOnly" min="0.0" max="1.0" step="0.01" value="0.85">
      </div>
    </div>

    <div class="toggle-row">
      <span>Vignette</span>
      <input type="checkbox" id="filterVignette" checked>
    </div>
    <div class="filter-sub" data-filter="filterVignette">
      <div class="control">
        <label>Strength <span class="val" data-for="vignetteStrength">0.40</span></label>
        <input type="range" data-u="uVignetteStrength" id="vignetteStrength" min="0.0" max="1.5" step="0.01" value="0.40">
      </div>
      <div class="control">
        <label>Radius <span class="val" data-for="vignetteRadius">0.80</span></label>
        <input type="range" data-u="uVignetteRadius" id="vignetteRadius" min="0.1" max="2.0" step="0.01" value="0.80">
      </div>
      <div class="control">
        <label>Softness <span class="val" data-for="vignetteSoftness">0.50</span></label>
        <input type="range" data-u="uVignetteSoftness" id="vignetteSoftness" min="0.01" max="1.5" step="0.01" value="0.50">
      </div>
    </div>

    <div class="toggle-row">
      <span>Chromatic Aberration</span>
      <input type="checkbox" id="filterChroma">
    </div>
    <div class="filter-sub" data-filter="filterChroma">
      <div class="control">
        <label>Amount <span class="val" data-for="chromaAmount">0.003</span></label>
        <input type="range" data-u="uChromaAmount" id="chromaAmount" min="0.0" max="0.02" step="0.0001" value="0.003">
      </div>
    </div>

    <div class="toggle-row">
      <span>Scanlines</span>
      <input type="checkbox" id="filterScanlines">
    </div>
    <div class="filter-sub" data-filter="filterScanlines">
      <div class="control">
        <label>Density <span class="val" data-for="scanlineDensity">300.00</span></label>
        <input type="range" data-u="uScanlineDensity" id="scanlineDensity" min="50.0" max="1000.0" step="1.0" value="300.00">
      </div>
      <div class="control">
        <label>Opacity <span class="val" data-for="scanlineOpacity">0.12</span></label>
        <input type="range" data-u="uScanlineOpacity" id="scanlineOpacity" min="0.0" max="0.5" step="0.01" value="0.12">
      </div>
    </div>

    <div class="toggle-row">
      <span>Glow</span>
      <input type="checkbox" id="filterGlow">
    </div>
    <div class="filter-sub" data-filter="filterGlow">
      <div class="control">
        <label>Intensity <span class="val" data-for="glowIntensity">0.30</span></label>
        <input type="range" data-u="uGlowIntensity" id="glowIntensity" min="0.0" max="1.0" step="0.01" value="0.30">
      </div>
      <div class="control">
        <label>Threshold <span class="val" data-for="glowThreshold">0.40</span></label>
        <input type="range" data-u="uGlowThreshold" id="glowThreshold" min="0.0" max="1.0" step="0.01" value="0.40">
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-title">Colors</div>
    <div class="control">
      <label>Void / Background</label>
      <input type="color" id="colVoid" value="#060303">
    </div>
    <div class="control">
      <label>Procedural BG</label>
      <input type="color" id="colProceduralBg" value="#0e0707">
    </div>
    <div class="control">
      <label>Shadow</label>
      <input type="color" id="colShadow" value="#051014">
    </div>
  </div>

  <div class="section">
    <div class="btn-row">
      <button class="btn" id="btn-reset">Reset</button>
      <button class="btn" id="btn-export">Export</button>
      <button class="btn" id="btn-import">Import</button>
      <button class="btn" id="btn-export-neat">Neat</button>
    </div>
    <div id="export-output"></div>
  </div>
</div>

<script type="importmap">
{
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@0.183.0/build/three.module.js"
  }
}
</script>
<script type="module">
import * as THREE from 'three';

const vertexShader = `
void main() {
    gl_Position = vec4(position, 1.0);
}
`;

const fragmentShader = `
precision highp float;

uniform float uTime;
uniform vec2 uResolution;
uniform vec2 uMouse;

uniform float uZoom;
uniform float uSpeed;
uniform float uFlowScale;
uniform float uFlowEase;
uniform float uFlowDistortionA;
uniform float uFlowDistortionB;

uniform float uHorizontalPressure;
uniform float uVerticalPressure;
uniform float uWaveFrequencyX;
uniform float uWaveFrequencyY;
uniform float uWaveAmplitude;

uniform float uVeinStart;
uniform float uVeinEnd;

uniform float uShadows;
uniform float uHighlights;
uniform float uColorBrightness;
uniform float uColorSaturation;
uniform float uColorBlending;
uniform float uBaseTealMix;
uniform float uCrimsonMix;
uniform float uOrangeBright;

uniform float uMouseInfluence;
uniform float uMouseRadius;
uniform float uMouseStrength;
uniform float uMouseDecay;
uniform float uMouseDarken;

uniform float uGrainIntensity;
uniform float uGrainScale;
uniform float uGrainSpeed;
uniform float uGrainSparsity;

uniform float uYOffset;
uniform float uYOffsetWave;
uniform float uYOffsetColor;
uniform float uYOffsetFlow;

uniform float uVeinFalloff;
uniform float uCoreRatio;
uniform float uMidRatio;
uniform float uCenterRatio;

// Filters
uniform float uFilterFilmGrain;
uniform float uFilmGrainIntensity;
uniform float uFilmGrainScale;
uniform float uFilmGrainSpeed;
uniform float uFilmGrainVoidOnly;

uniform float uFilterVignette;
uniform float uVignetteStrength;
uniform float uVignetteRadius;
uniform float uVignetteSoftness;

uniform float uFilterChroma;
uniform float uChromaAmount;

uniform float uFilterScanlines;
uniform float uScanlineDensity;
uniform float uScanlineOpacity;

uniform float uFilterGlow;
uniform float uGlowIntensity;
uniform float uGlowThreshold;

uniform vec3 uColVoid;
uniform vec3 uColProceduralBg;
uniform vec3 uColShadow;
uniform vec3 uColWarmOuter;
uniform vec3 uColWarmMid;
uniform vec3 uColWarmCenter;
uniform vec3 uColCoolOuter;
uniform vec3 uColCoolMid;
uniform vec3 uColCoolCenter;

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
    p0 *= norm.x; p1 *= norm.y; p2 *= norm.z; p3 *= norm.w;
    vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);
    m = m * m;
    return 42.0 * dot(m * m, vec4(dot(p0,x0), dot(p1,x1), dot(p2,x2), dot(p3,x3)));
}

float fbm(vec3 p) {
    float value = 0.0;
    float amplitude = 0.5;
    float frequency = 1.0;
    for (int i = 0; i < 4; i++) {
        value += amplitude * snoise(p * frequency);
        frequency *= 2.0;
        amplitude *= 0.5;
    }
    return value;
}

float pattern(vec2 p, float t, vec2 mouseOff) {
    vec3 p3 = vec3(p, t);

    vec2 q = vec2(
        fbm(p3),
        fbm(p3 + vec3(5.2, 1.3, 0.0))
    );

    vec3 q3 = vec3(q, 0.0);
    vec3 mo = vec3(mouseOff, 0.0);

    vec2 r = vec2(
        fbm(p3 + uFlowScale * q3 + vec3(1.7, 9.2, 0.0) + mo),
        fbm(p3 + uFlowScale * q3 + vec3(8.3, 2.8, 0.0) + mo)
    );

    return fbm(p3 + vec3(uFlowScale * r, 0.0));
}

vec3 adjustSaturation(vec3 col, float sat) {
    float gray = dot(col, vec3(0.2126, 0.7152, 0.0722));
    return mix(vec3(gray), col, 1.0 + sat);
}

// Contour vein: returns 0 in void, 1 at vein center
// width controls vein thickness, level is the contour threshold
float contourVein(float n, float level, float width) {
    float dist = abs(n - level);
    return 1.0 - smoothstep(0.0, width, dist);
}

void main() {
    vec2 uv = gl_FragCoord.xy / uResolution;
    float aspect = uResolution.x / uResolution.y;

    // Apply Y offset
    vec2 uvOffset = uv;
    uvOffset.y += uYOffset;

    // Apply pressure (stretch)
    vec2 p = vec2(uvOffset.x * aspect * (uHorizontalPressure / 5.0), uvOffset.y * (uVerticalPressure / 4.0)) * uZoom;

    // Apply wave distortion
    p.x += sin(uvOffset.y * uWaveFrequencyY * 6.2832) * uWaveAmplitude * 0.1;
    p.y += sin(uvOffset.x * uWaveFrequencyX * 6.2832) * uWaveAmplitude * 0.1;

    float t = uTime * uSpeed * 0.1;
    vec2 flowP = p + vec2(t * uFlowDistortionA * uFlowEase, t * uFlowDistortionB * uFlowEase);

    // Y offset multipliers
    flowP += vec2(0.0, uYOffset * uYOffsetFlow * 0.1);

    // Mouse
    vec2 mouseUV = uMouse / uResolution;
    float mouseDist = length(uv - mouseUV);
    float mouseInfl = uMouseInfluence * exp(-mouseDist * mouseDist / (uMouseRadius * uMouseRadius));
    vec2 mouseOffset = normalize(uv - mouseUV + 0.001) * mouseInfl * uMouseStrength;

    float n = pattern(flowP, t, mouseOffset);

    // Contour-line veins at fixed noise levels — consistent thickness
    float veinWidth = uVeinStart * 0.15 + 0.02;

    // Multiple contour levels spread across the noise range
    float spacing = uVeinEnd * 0.12 + 0.08;

    // For each pixel, find the strongest vein and its properties
    float bestVein = 0.0;
    float bestWarm = 0.0;

    for (int i = 0; i < 8; i++) {
        float level = -0.6 + float(i) * spacing;
        float v = contourVein(n, level, veinWidth);
        if (v > bestVein) {
            bestVein = v;
            bestWarm = mod(float(i), 2.0); // alternating warm/cool
        }
    }

    // Apply falloff power — controls how sharply vein edges fade to void
    float veinShaped = pow(bestVein, uVeinFalloff);

    // Build the vein gradient: void → outer → mid → center
    // veinShaped goes 0 (edge) to 1 (center line)
    float outerZone = smoothstep(0.0, 1.0 - uCoreRatio, veinShaped);
    float midZone = smoothstep(1.0 - uCoreRatio, 1.0 - uMidRatio, veinShaped);
    float coreZone = smoothstep(1.0 - uMidRatio, 1.0 - uCenterRatio, veinShaped);
    float centerZone = smoothstep(1.0 - uCenterRatio, 1.0, veinShaped);

    // Pick warm or cool palette based on contour alternation
    float blendSharp = uColorBlending * 0.3;
    float warmZone = smoothstep(0.5 - blendSharp * 0.1, 0.5 + blendSharp * 0.1, bestWarm);

    vec3 outerCol = mix(uColCoolOuter, uColWarmOuter, warmZone);
    vec3 midCol = mix(uColCoolMid, uColWarmMid, warmZone);
    vec3 centerCol = mix(uColCoolCenter, uColWarmCenter, warmZone);

    // Base void
    vec3 col = mix(uColVoid, uColProceduralBg, 0.1);

    // Shadow/highlight influence on void
    float vn = n * 0.5 + 0.5;
    col *= mix(1.0 - uShadows * 0.03, 1.0 + uHighlights * 0.01, vn);

    // Vein gradient: void → outer → mid → center
    col = mix(col, outerCol, outerZone * uBaseTealMix);
    col = mix(col, midCol, midZone * uCrimsonMix);
    col = mix(col, centerCol, coreZone * uOrangeBright);
    col = mix(col, centerCol, centerZone * uOrangeBright);

    // Mouse darken
    float darkenArea = exp(-mouseDist * mouseDist / (uMouseRadius * uMouseRadius * 2.0));
    col *= 1.0 - darkenArea * uMouseDarken;

    // Brightness & saturation
    col *= uColorBrightness;
    col = adjustSaturation(col, uColorSaturation);

    // Global grain (from Grain section)
    if (uGrainIntensity > 0.001) {
        vec2 grainUV = uv * uResolution / uGrainScale;
        float grain = fract(sin(dot(grainUV + fract(uTime * uGrainSpeed * 7.123), vec2(12.9898, 78.233))) * 43758.5453);
        grain = smoothstep(uGrainSparsity, 1.0, grain);
        col = mix(col, col * (0.5 + grain), uGrainIntensity * 0.15);
    }

    // --- FILTERS ---

    // Film Grain on Void — applied strongest in dark/void areas
    if (uFilterFilmGrain > 0.5) {
        vec2 fgUV = uv * uResolution / uFilmGrainScale;
        float t1 = fract(uTime * uFilmGrainSpeed * 5.37);
        float t2 = fract(uTime * uFilmGrainSpeed * 3.91);
        // Two-layer grain for more natural film look
        float fg1 = fract(sin(dot(fgUV + t1, vec2(12.9898, 78.233))) * 43758.5453);
        float fg2 = fract(sin(dot(fgUV * 1.7 + t2, vec2(39.346, 11.135))) * 28462.6341);
        float fg = fg1 * 0.6 + fg2 * 0.4;
        // How much to mask veins: 1 = void only, 0 = everywhere
        float voidMask = mix(1.0, 1.0 - veinShaped, uFilmGrainVoidOnly);
        // Darken + lighten grain (not just lighten)
        float grainEffect = (fg - 0.5) * 2.0; // -1 to 1
        col += col * grainEffect * uFilmGrainIntensity * voidMask * 0.5;
    }

    // Vignette
    if (uFilterVignette > 0.5) {
        vec2 vc = uv - 0.5;
        float vDist = length(vc);
        float vig = smoothstep(uVignetteRadius, uVignetteRadius - uVignetteSoftness, vDist);
        col *= mix(1.0, vig, uVignetteStrength);
    }

    // Chromatic Aberration
    if (uFilterChroma > 0.5) {
        vec2 dir = uv - 0.5;
        float dist = length(dir);
        vec2 offset = dir * uChromaAmount * dist;
        // We can't re-render the scene, but we shift the existing color channels
        // based on the vein data — shift warm toward edges, cool inward
        col.r = col.r * (1.0 + offset.x * 50.0);
        col.b = col.b * (1.0 - offset.x * 50.0);
    }

    // Scanlines
    if (uFilterScanlines > 0.5) {
        float scanline = sin(gl_FragCoord.y * 3.14159 * 2.0 / (uResolution.y / uScanlineDensity));
        scanline = scanline * 0.5 + 0.5;
        col *= 1.0 - (1.0 - scanline) * uScanlineOpacity;
    }

    // Glow — brightens areas above threshold
    if (uFilterGlow > 0.5) {
        float lum = dot(col, vec3(0.2126, 0.7152, 0.0722));
        float bloom = smoothstep(uGlowThreshold, 1.0, lum);
        col += col * bloom * uGlowIntensity;
    }

    col = clamp(col, 0.0, 1.0);
    gl_FragColor = vec4(col, 1.0);
}
`;

// --- Defaults (matching welcome.blade.php Neat config) ---
const DEFAULTS = {
    zoom: 1.2, speed: 0.5, flowScale: 5.0, flowEase: 1.0,
    flowDistortionA: 1.7, flowDistortionB: 0.1,
    horizontalPressure: 5.0, verticalPressure: 4.0,
    waveFrequencyX: 0.0, waveFrequencyY: 0.0, waveAmplitude: 0.0,
    veinStart: 0.1, veinEnd: 0.6,
    shadows: 5.0, highlights: 4.0, colorBrightness: 1.0,
    colorSaturation: 0.0, colorBlending: 3.0,
    baseTealMix: 0.6, crimsonMix: 0.9, orangeBright: 0.5,
    mouseInfluence: 2.0, mouseRadius: 0.2, mouseStrength: 0.15,
    mouseDecay: 0.9, mouseDarken: 0.24,
    grainIntensity: 0.575, grainScale: 2.0, grainSpeed: 0.1, grainSparsity: 0.0,
    yOffset: 0.0, yOffsetWave: 20.0, yOffsetColor: 20.0, yOffsetFlow: 20.0,
    veinFalloff: 1.8, coreRatio: 0.55, midRatio: 0.40, centerRatio: 0.25,
    filterFilmGrain: 1.0, filmGrainIntensity: 0.35, filmGrainScale: 1.5,
    filmGrainSpeed: 0.5, filmGrainVoidOnly: 0.85,
    filterVignette: 1.0, vignetteStrength: 0.4, vignetteRadius: 0.8, vignetteSoftness: 0.5,
    filterChroma: 0.0, chromaAmount: 0.003,
    filterScanlines: 0.0, scanlineDensity: 300.0, scanlineOpacity: 0.12,
    filterGlow: 0.0, glowIntensity: 0.3, glowThreshold: 0.4,
    colVoid: '#060303', colProceduralBg: '#0e0707', colShadow: '#051014',
    colWarmOuter: '#6f1106', colWarmMid: '#ff4500', colWarmCenter: '#055e7d',
    colCoolOuter: '#001a23', colCoolMid: '#055e7d', colCoolCenter: '#001a23',
};

function hexToVec3(hex) {
    return new THREE.Vector3(
        parseInt(hex.slice(1,3), 16) / 255,
        parseInt(hex.slice(3,5), 16) / 255,
        parseInt(hex.slice(5,7), 16) / 255
    );
}

// --- Setup ---
const canvas = document.getElementById('bg');
const dpr = Math.min(window.devicePixelRatio, 2);
const renderer = new THREE.WebGLRenderer({ canvas, alpha: false, antialias: false });
renderer.setPixelRatio(dpr);
renderer.setSize(window.innerWidth, window.innerHeight);
const scene = new THREE.Scene();
const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

const uniforms = {
    uTime: { value: 0 },
    uResolution: { value: new THREE.Vector2(window.innerWidth * dpr, window.innerHeight * dpr) },
    uMouse: { value: new THREE.Vector2(0, 0) },
    uZoom: { value: DEFAULTS.zoom },
    uSpeed: { value: DEFAULTS.speed },
    uFlowScale: { value: DEFAULTS.flowScale },
    uFlowEase: { value: DEFAULTS.flowEase },
    uFlowDistortionA: { value: DEFAULTS.flowDistortionA },
    uFlowDistortionB: { value: DEFAULTS.flowDistortionB },
    uHorizontalPressure: { value: DEFAULTS.horizontalPressure },
    uVerticalPressure: { value: DEFAULTS.verticalPressure },
    uWaveFrequencyX: { value: DEFAULTS.waveFrequencyX },
    uWaveFrequencyY: { value: DEFAULTS.waveFrequencyY },
    uWaveAmplitude: { value: DEFAULTS.waveAmplitude },
    uVeinStart: { value: DEFAULTS.veinStart },
    uVeinEnd: { value: DEFAULTS.veinEnd },
    uShadows: { value: DEFAULTS.shadows },
    uHighlights: { value: DEFAULTS.highlights },
    uColorBrightness: { value: DEFAULTS.colorBrightness },
    uColorSaturation: { value: DEFAULTS.colorSaturation },
    uColorBlending: { value: DEFAULTS.colorBlending },
    uBaseTealMix: { value: DEFAULTS.baseTealMix },
    uCrimsonMix: { value: DEFAULTS.crimsonMix },
    uOrangeBright: { value: DEFAULTS.orangeBright },
    uMouseInfluence: { value: DEFAULTS.mouseInfluence },
    uMouseRadius: { value: DEFAULTS.mouseRadius },
    uMouseStrength: { value: DEFAULTS.mouseStrength },
    uMouseDecay: { value: DEFAULTS.mouseDecay },
    uMouseDarken: { value: DEFAULTS.mouseDarken },
    uGrainIntensity: { value: DEFAULTS.grainIntensity },
    uGrainScale: { value: DEFAULTS.grainScale },
    uGrainSpeed: { value: DEFAULTS.grainSpeed },
    uGrainSparsity: { value: DEFAULTS.grainSparsity },
    uYOffset: { value: DEFAULTS.yOffset },
    uYOffsetWave: { value: DEFAULTS.yOffsetWave },
    uYOffsetColor: { value: DEFAULTS.yOffsetColor },
    uYOffsetFlow: { value: DEFAULTS.yOffsetFlow },
    uVeinFalloff: { value: DEFAULTS.veinFalloff },
    uCoreRatio: { value: DEFAULTS.coreRatio },
    uMidRatio: { value: DEFAULTS.midRatio },
    uCenterRatio: { value: DEFAULTS.centerRatio },
    uFilterFilmGrain: { value: DEFAULTS.filterFilmGrain },
    uFilmGrainIntensity: { value: DEFAULTS.filmGrainIntensity },
    uFilmGrainScale: { value: DEFAULTS.filmGrainScale },
    uFilmGrainSpeed: { value: DEFAULTS.filmGrainSpeed },
    uFilmGrainVoidOnly: { value: DEFAULTS.filmGrainVoidOnly },
    uFilterVignette: { value: DEFAULTS.filterVignette },
    uVignetteStrength: { value: DEFAULTS.vignetteStrength },
    uVignetteRadius: { value: DEFAULTS.vignetteRadius },
    uVignetteSoftness: { value: DEFAULTS.vignetteSoftness },
    uFilterChroma: { value: DEFAULTS.filterChroma },
    uChromaAmount: { value: DEFAULTS.chromaAmount },
    uFilterScanlines: { value: DEFAULTS.filterScanlines },
    uScanlineDensity: { value: DEFAULTS.scanlineDensity },
    uScanlineOpacity: { value: DEFAULTS.scanlineOpacity },
    uFilterGlow: { value: DEFAULTS.filterGlow },
    uGlowIntensity: { value: DEFAULTS.glowIntensity },
    uGlowThreshold: { value: DEFAULTS.glowThreshold },
    uColVoid: { value: hexToVec3(DEFAULTS.colVoid) },
    uColProceduralBg: { value: hexToVec3(DEFAULTS.colProceduralBg) },
    uColShadow: { value: hexToVec3(DEFAULTS.colShadow) },
    uColWarmOuter: { value: hexToVec3(DEFAULTS.colWarmOuter) },
    uColWarmMid: { value: hexToVec3(DEFAULTS.colWarmMid) },
    uColWarmCenter: { value: hexToVec3(DEFAULTS.colWarmCenter) },
    uColCoolOuter: { value: hexToVec3(DEFAULTS.colCoolOuter) },
    uColCoolMid: { value: hexToVec3(DEFAULTS.colCoolMid) },
    uColCoolCenter: { value: hexToVec3(DEFAULTS.colCoolCenter) },
};

const material = new THREE.ShaderMaterial({
    vertexShader, fragmentShader, uniforms,
    depthTest: false, depthWrite: false,
});
scene.add(new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material));
canvas.style.cssText = 'position:fixed;inset:0;width:100%;height:100%;display:block;';

// --- Wire all slider controls via data-u attribute ---
document.querySelectorAll('#panel input[type="range"]').forEach(el => {
    const uniformName = el.dataset.u;
    const valEl = el.closest('.control').querySelector('.val');
    if (!uniformName || !uniforms[uniformName]) return;
    el.addEventListener('input', () => {
        const v = parseFloat(el.value);
        uniforms[uniformName].value = v;
        if (valEl) {
            const step = el.getAttribute('step') || '0.01';
            const decimals = (step.split('.')[1] || '').length;
            valEl.textContent = v.toFixed(decimals);
        }
    });
});

// Wire color inputs
document.querySelectorAll('#panel input[type="color"]').forEach(el => {
    const id = el.id;
    // Map id to uniform: colVoid -> uColVoid, colOrange -> uColOrange etc
    const uniformName = 'u' + id.charAt(0).toUpperCase() + id.slice(1);
    if (!uniforms[uniformName]) return;
    el.addEventListener('input', () => {
        uniforms[uniformName].value = hexToVec3(el.value);
    });
});

// Wire filter checkboxes
document.querySelectorAll('.toggle-row input[type="checkbox"]').forEach(cb => {
    const filterId = cb.id;
    const uniformName = 'u' + filterId.charAt(0).toUpperCase() + filterId.slice(1);
    const subPanel = document.querySelector(`.filter-sub[data-filter="${filterId}"]`);

    const update = () => {
        const on = cb.checked;
        if (uniforms[uniformName]) uniforms[uniformName].value = on ? 1.0 : 0.0;
        if (subPanel) subPanel.classList.toggle('disabled', !on);
    };

    cb.addEventListener('change', update);
    update(); // apply initial state
});

// Toggle panel
document.getElementById('panel-toggle').addEventListener('click', () => {
    const panel = document.getElementById('panel');
    panel.classList.toggle('collapsed');
    document.getElementById('panel-toggle').textContent = panel.classList.contains('collapsed') ? 'Settings' : 'Hide';
});

// Reset
document.getElementById('btn-reset').addEventListener('click', () => {
    Object.entries(DEFAULTS).forEach(([key, val]) => {
        const el = document.getElementById(key);
        if (!el) return;
        if (el.type === 'checkbox') {
            el.checked = val >= 0.5;
            el.dispatchEvent(new Event('change'));
        } else {
            el.value = val;
            el.dispatchEvent(new Event('input'));
        }
    });
});

// Export
document.getElementById('btn-export').addEventListener('click', () => {
    const state = {};
    Object.keys(DEFAULTS).forEach(key => {
        const el = document.getElementById(key);
        if (!el) return;
        if (el.type === 'checkbox') state[key] = el.checked ? 1.0 : 0.0;
        else if (el.type === 'color') state[key] = el.value;
        else state[key] = parseFloat(el.value);
    });
    const out = document.getElementById('export-output');
    out.style.display = 'block';
    out.textContent = JSON.stringify(state, null, 2);
    navigator.clipboard.writeText(JSON.stringify(state)).catch(() => {});
});

// Export as Neat config (for welcome.blade.php)
document.getElementById('btn-export-neat').addEventListener('click', () => {
    const g = id => parseFloat(document.getElementById(id).value);
    const c = id => document.getElementById(id).value.toUpperCase();
    const neat = {
        colors: [
            { color: c('colWarmMid'), enabled: true },
            { color: c('colWarmOuter'), enabled: true },
            { color: c('colCoolOuter'), enabled: true },
            { color: c('colCoolMid'), enabled: true },
            { color: c('colCoolOuter'), enabled: true },
            { color: c('colShadow'), enabled: true },
        ],
        speed: g('speed'),
        horizontalPressure: g('horizontalPressure'),
        verticalPressure: g('verticalPressure'),
        waveFrequencyX: g('waveFrequencyX'),
        waveFrequencyY: g('waveFrequencyY'),
        waveAmplitude: g('waveAmplitude'),
        shadows: g('shadows'),
        highlights: g('highlights'),
        colorBrightness: g('colorBrightness'),
        colorSaturation: g('colorSaturation'),
        wireframe: false,
        colorBlending: g('colorBlending'),
        backgroundColor: '#202020',
        backgroundAlpha: 1,
        grainScale: g('grainScale'),
        grainSparsity: g('grainSparsity'),
        grainIntensity: g('grainIntensity'),
        grainSpeed: g('grainSpeed'),
        resolution: 2,
        yOffset: g('yOffset'),
        yOffsetWaveMultiplier: g('yOffsetWave'),
        yOffsetColorMultiplier: g('yOffsetColor'),
        yOffsetFlowMultiplier: g('yOffsetFlow'),
        flowDistortionA: g('flowDistortionA'),
        flowDistortionB: g('flowDistortionB'),
        flowScale: g('flowScale'),
        flowEase: g('flowEase'),
        flowEnabled: true,
        mouseDistortionStrength: g('mouseInfluence'),
        mouseDistortionRadius: g('mouseRadius') * 10,
        mouseDecayRate: g('mouseDecay'),
        mouseDarken: g('mouseDarken'),
        enableProceduralTexture: true,
        textureVoidLikelihood: 0.92,
        textureVoidWidthMin: 10,
        textureVoidWidthMax: 210,
        textureBandDensity: 0.4,
        textureColorBlending: 0.36,
        textureSeed: 186,
        textureEase: 0,
        proceduralBackgroundColor: c('colProceduralBg'),
        textureShapeTriangles: 97,
        textureShapeCircles: 15,
        textureShapeBars: 64,
        textureShapeSquiggles: 0,
    };
    const out = document.getElementById('export-output');
    out.style.display = 'block';
    out.textContent = JSON.stringify(neat, null, 2);
    navigator.clipboard.writeText(JSON.stringify(neat, null, 2)).catch(() => {});
});

// Import
document.getElementById('btn-import').addEventListener('click', () => {
    const json = prompt('Paste exported JSON:');
    if (!json) return;
    try {
        const state = JSON.parse(json);
        Object.entries(state).forEach(([key, val]) => {
            const el = document.getElementById(key);
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = val >= 0.5;
                el.dispatchEvent(new Event('change'));
            } else {
                el.value = val;
                el.dispatchEvent(new Event('input'));
            }
        });
    } catch (e) {
        alert('Invalid JSON');
    }
});

// --- Mouse ---
const mouseTarget = { x: 0, y: 0 };
const mouseCurrent = { x: 0, y: 0 };

window.addEventListener('mousemove', (e) => {
    mouseTarget.x = e.clientX * dpr;
    mouseTarget.y = (window.innerHeight - e.clientY) * dpr;
});

window.addEventListener('resize', () => {
    const d = Math.min(window.devicePixelRatio, 2);
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(d);
    uniforms.uResolution.value.set(window.innerWidth * d, window.innerHeight * d);
});

// --- Animate ---
const startTime = performance.now();
const decayLerp = () => 1.0 - uniforms.uMouseDecay.value;

(function animate() {
    requestAnimationFrame(animate);
    uniforms.uTime.value = (performance.now() - startTime) / 1000;
    const lerp = decayLerp();
    mouseCurrent.x += (mouseTarget.x - mouseCurrent.x) * lerp;
    mouseCurrent.y += (mouseTarget.y - mouseCurrent.y) * lerp;
    uniforms.uMouse.value.set(mouseCurrent.x, mouseCurrent.y);
    renderer.render(scene, camera);
})();
</script>
</body>
</html>
