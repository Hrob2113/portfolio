/* ══════════════════════════════════════════════════
   THREE.JS FLUID VEIN SHADER — Hero background
═══════════════════════════════════════════════════ */
import * as THREE from 'three';

const vertexShader = `void main() { gl_Position = vec4(position, 1.0); }`;

const fragmentShader = `
precision highp float;

uniform float uTime;
uniform vec2 uResolution;
uniform vec2 uMouse;

uniform float uZoom, uSpeed, uFlowScale, uFlowEase;
uniform float uFlowDistortionA, uFlowDistortionB;
uniform float uHorizontalPressure, uVerticalPressure;
uniform float uWaveFrequencyX, uWaveFrequencyY, uWaveAmplitude;
uniform float uVeinStart, uVeinEnd;
uniform float uShadows, uHighlights, uColorBrightness, uColorSaturation, uColorBlending;
uniform float uBaseTealMix, uCrimsonMix, uOrangeBright;
uniform float uMouseInfluence, uMouseRadius, uMouseStrength, uMouseDecay, uMouseDarken;
uniform vec2 uLogoCenter;
uniform vec2 uLogoSize;
uniform float uGrainIntensity, uGrainScale, uGrainSpeed, uGrainSparsity;
uniform float uYOffset, uYOffsetWave, uYOffsetColor, uYOffsetFlow;
uniform float uVeinFalloff, uCoreRatio, uMidRatio, uCenterRatio;
uniform float uFilterFilmGrain, uFilmGrainIntensity, uFilmGrainScale, uFilmGrainSpeed, uFilmGrainVoidOnly;
uniform float uFilterVignette, uVignetteStrength, uVignetteRadius, uVignetteSoftness;
uniform vec3 uColVoid, uColProceduralBg, uColShadow;
uniform vec3 uColWarmOuter, uColWarmMid, uColWarmCenter;
uniform vec3 uColCoolOuter, uColCoolMid, uColCoolCenter;

vec4 mod289(vec4 x){return x-floor(x*(1.0/289.0))*289.0;}
vec3 mod289(vec3 x){return x-floor(x*(1.0/289.0))*289.0;}
vec2 mod289(vec2 x){return x-floor(x*(1.0/289.0))*289.0;}
float mod289(float x){return x-floor(x*(1.0/289.0))*289.0;}
vec4 permute(vec4 x){return mod289(((x*34.0)+10.0)*x);}
vec3 permute(vec3 x){return mod289(((x*34.0)+10.0)*x);}
vec4 taylorInvSqrt(vec4 r){return 1.79284291400159-0.85373472095314*r;}

float snoise(vec3 v){
    const vec2 C=vec2(1.0/6.0,1.0/3.0);
    const vec4 D=vec4(0.0,0.5,1.0,2.0);
    vec3 i=floor(v+dot(v,C.yyy));
    vec3 x0=v-i+dot(i,C.xxx);
    vec3 g=step(x0.yzx,x0.xyz);
    vec3 l=1.0-g;
    vec3 i1=min(g.xyz,l.zxy);
    vec3 i2=max(g.xyz,l.zxy);
    vec3 x1=x0-i1+C.xxx;
    vec3 x2=x0-i2+C.yyy;
    vec3 x3=x0-D.yyy;
    i=mod289(i);
    vec4 p=permute(permute(permute(
        i.z+vec4(0.0,i1.z,i2.z,1.0))
      +i.y+vec4(0.0,i1.y,i2.y,1.0))
      +i.x+vec4(0.0,i1.x,i2.x,1.0));
    float n_=0.142857142857;
    vec3 ns=n_*D.wyz-D.xzx;
    vec4 j=p-49.0*floor(p*ns.z*ns.z);
    vec4 x_=floor(j*ns.z);
    vec4 y_=floor(j-7.0*x_);
    vec4 x=x_*ns.x+ns.yyyy;
    vec4 y=y_*ns.x+ns.yyyy;
    vec4 h=1.0-abs(x)-abs(y);
    vec4 b0=vec4(x.xy,y.xy);
    vec4 b1=vec4(x.zw,y.zw);
    vec4 s0=floor(b0)*2.0+1.0;
    vec4 s1=floor(b1)*2.0+1.0;
    vec4 sh=-step(h,vec4(0.0));
    vec4 a0=b0.xzyw+s0.xzyw*sh.xxyy;
    vec4 a1=b1.xzyw+s1.xzyw*sh.zzww;
    vec3 p0=vec3(a0.xy,h.x);
    vec3 p1=vec3(a0.zw,h.y);
    vec3 p2=vec3(a1.xy,h.z);
    vec3 p3=vec3(a1.zw,h.w);
    vec4 norm=taylorInvSqrt(vec4(dot(p0,p0),dot(p1,p1),dot(p2,p2),dot(p3,p3)));
    p0*=norm.x;p1*=norm.y;p2*=norm.z;p3*=norm.w;
    vec4 m=max(0.6-vec4(dot(x0,x0),dot(x1,x1),dot(x2,x2),dot(x3,x3)),0.0);
    m=m*m;
    return 42.0*dot(m*m,vec4(dot(p0,x0),dot(p1,x1),dot(p2,x2),dot(p3,x3)));
}

float fbm(vec3 p){
    float v=0.0,a=0.5,f=1.0;
    for(int i=0;i<3;i++){v+=a*snoise(p*f);f*=2.0;a*=0.5;}
    return v;
}

float pattern(vec2 p,float t,vec2 mouseOff){
    vec3 p3=vec3(p,t);
    vec2 q=vec2(fbm(p3),fbm(p3+vec3(5.2,1.3,0.0)));
    vec3 q3=vec3(q,0.0);
    vec3 mo=vec3(mouseOff,0.0);
    vec2 r=vec2(
        fbm(p3+uFlowScale*q3+vec3(1.7,9.2,0.0)+mo),
        fbm(p3+uFlowScale*q3+vec3(8.3,2.8,0.0)+mo)
    );
    return fbm(p3+vec3(uFlowScale*r,0.0));
}

vec3 adjustSaturation(vec3 col,float sat){
    float gray=dot(col,vec3(0.2126,0.7152,0.0722));
    return mix(vec3(gray),col,1.0+sat);
}

float contourVein(float n,float level,float width){
    float dist=abs(n-level);
    return 1.0-smoothstep(0.0,width,dist);
}

void main(){
    vec2 uv=gl_FragCoord.xy/uResolution;
    float aspect=uResolution.x/uResolution.y;
    vec2 uvOffset=uv;
    uvOffset.y+=uYOffset;
    vec2 p=vec2(uvOffset.x*aspect*(uHorizontalPressure/5.0),uvOffset.y*(uVerticalPressure/4.0))*uZoom;
    p.x+=sin(uvOffset.y*uWaveFrequencyY*6.2832)*uWaveAmplitude*0.1;
    p.y+=sin(uvOffset.x*uWaveFrequencyX*6.2832)*uWaveAmplitude*0.1;
    float t=uTime*uSpeed*0.1;
    vec2 flowP=p+vec2(t*uFlowDistortionA*uFlowEase,t*uFlowDistortionB*uFlowEase);
    flowP+=vec2(0.0,uYOffset*uYOffsetFlow*0.1);
    vec2 mouseUV=uMouse/uResolution;
    float mouseDist=length(uv-mouseUV);
    float mouseInfl=uMouseInfluence*exp(-mouseDist*mouseDist/(uMouseRadius*uMouseRadius));
    vec2 mouseOffset=normalize(uv-mouseUV+0.001)*mouseInfl*uMouseStrength;
    // Logo distortion — same idea as mouse but persistent at the logo position
    vec2 logoUV=uLogoCenter/uResolution;
    vec2 logoRad=uLogoSize/uResolution*0.5;
    vec2 dLogo=(uv-logoUV)/max(logoRad,vec2(0.001));
    float logoDist=length(dLogo);
    float logoInfl=0.8*exp(-logoDist*logoDist*0.8);
    vec2 logoOffset=normalize(uv-logoUV+0.001)*logoInfl*0.06;
    float n=pattern(flowP,t,mouseOffset+logoOffset);
    float veinWidth=uVeinStart*0.15+0.02;
    float spacing=uVeinEnd*0.12+0.08;
    float bestVein=0.0;
    float bestWarm=0.0;
    for(int i=0;i<8;i++){
        float level=-0.6+float(i)*spacing;
        float v=contourVein(n,level,veinWidth);
        if(v>bestVein){bestVein=v;bestWarm=mod(float(i),2.0);}
    }
    float veinShaped=pow(bestVein,uVeinFalloff);
    float outerZone=smoothstep(0.0,1.0-uCoreRatio,veinShaped);
    float midZone=smoothstep(1.0-uCoreRatio,1.0-uMidRatio,veinShaped);
    float coreZone=smoothstep(1.0-uMidRatio,1.0-uCenterRatio,veinShaped);
    float centerZone=smoothstep(1.0-uCenterRatio,1.0,veinShaped);
    float blendSharp=uColorBlending*0.3;
    float warmZone=smoothstep(0.5-blendSharp*0.1,0.5+blendSharp*0.1,bestWarm);
    vec3 outerCol=mix(uColCoolOuter,uColWarmOuter,warmZone);
    vec3 midCol=mix(uColCoolMid,uColWarmMid,warmZone);
    vec3 centerCol=mix(uColCoolCenter,uColWarmCenter,warmZone);
    vec3 col=mix(uColVoid,uColProceduralBg,0.1);
    float vn=n*0.5+0.5;
    col*=mix(1.0-uShadows*0.03,1.0+uHighlights*0.01,vn);
    col=mix(col,outerCol,outerZone*uBaseTealMix);
    col=mix(col,midCol,midZone*uCrimsonMix);
    col=mix(col,centerCol,coreZone*uOrangeBright);
    col=mix(col,centerCol,centerZone*uOrangeBright);
    float darkenArea=exp(-mouseDist*mouseDist/(uMouseRadius*uMouseRadius*2.0));
    col*=1.0-darkenArea*uMouseDarken;
    col*=uColorBrightness;
    col=adjustSaturation(col,uColorSaturation);
    if(uGrainIntensity>0.001){
        vec2 grainUV=uv*uResolution/uGrainScale;
        float grain=fract(sin(dot(grainUV+fract(uTime*uGrainSpeed*7.123),vec2(12.9898,78.233)))*43758.5453);
        grain=smoothstep(uGrainSparsity,1.0,grain);
        col=mix(col,col*(0.5+grain),uGrainIntensity*0.15);
    }
    if(uFilterFilmGrain>0.5){
        vec2 fgUV=uv*uResolution/uFilmGrainScale;
        float t1=fract(uTime*uFilmGrainSpeed*5.37);
        float t2=fract(uTime*uFilmGrainSpeed*3.91);
        float fg1=fract(sin(dot(fgUV+t1,vec2(12.9898,78.233)))*43758.5453);
        float fg2=fract(sin(dot(fgUV*1.7+t2,vec2(39.346,11.135)))*28462.6341);
        float fg=fg1*0.6+fg2*0.4;
        float voidMask=mix(1.0,1.0-veinShaped,uFilmGrainVoidOnly);
        float grainEffect=(fg-0.5)*2.0;
        col+=col*grainEffect*uFilmGrainIntensity*voidMask*0.5;
    }
    if(uFilterVignette>0.5){
        vec2 vc=uv-0.5;
        float vDist=length(vc);
        float vig=smoothstep(uVignetteRadius,uVignetteRadius-uVignetteSoftness,vDist);
        col*=mix(1.0,vig,uVignetteStrength);
    }
    col=clamp(col,0.0,1.0);
    gl_FragColor=vec4(col,1.0);
}
`;

try {
  const canvas = document.getElementById('hero-neat');
  if (!canvas) throw new Error('Canvas not found');

  function hexToVec3(hex) {
    return new THREE.Vector3(
      parseInt(hex.slice(1,3),16)/255,
      parseInt(hex.slice(3,5),16)/255,
      parseInt(hex.slice(5,7),16)/255
    );
  }

  const isMobile = window.innerWidth < 768;
  const dpr = Math.min(window.devicePixelRatio, isMobile ? 1 : 1.5);

  const renderer = new THREE.WebGLRenderer({ canvas, alpha: false, antialias: false });
  renderer.setPixelRatio(dpr);
  renderer.setSize(window.innerWidth, window.innerHeight);

  const scene = new THREE.Scene();
  const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);

  const uniforms = {
    uTime:              { value: 0 },
    uResolution:        { value: new THREE.Vector2(window.innerWidth * dpr, window.innerHeight * dpr) },
    uMouse:             { value: new THREE.Vector2(0, 0) },
    uZoom:              { value: 0.51 },
    uSpeed:             { value: 0.06 },
    uFlowScale:         { value: 0.5 },
    uFlowEase:          { value: 1.0 },
    uFlowDistortionA:   { value: 1.7 },
    uFlowDistortionB:   { value: 0.1 },
    uHorizontalPressure:{ value: 5.0 },
    uVerticalPressure:  { value: 4.0 },
    uWaveFrequencyX:    { value: 0.0 },
    uWaveFrequencyY:    { value: 0.0 },
    uWaveAmplitude:     { value: 0.0 },
    uVeinStart:         { value: 0.1 },
    uVeinEnd:           { value: 0.6 },
    uShadows:           { value: 5.0 },
    uHighlights:        { value: 4.0 },
    uColorBrightness:   { value: 1.0 },
    uColorSaturation:   { value: 0.0 },
    uColorBlending:     { value: 3.0 },
    uBaseTealMix:       { value: 0.6 },
    uCrimsonMix:        { value: 0.9 },
    uOrangeBright:      { value: 0.5 },
    uMouseInfluence:    { value: 0.8 },
    uMouseRadius:       { value: 0.08 },
    uMouseStrength:     { value: 0.06 },
    uMouseDecay:        { value: 0.9 },
    uMouseDarken:       { value: 0.24 },
    uLogoCenter:        { value: new THREE.Vector2(0, 0) },
    uLogoSize:          { value: new THREE.Vector2(0, 0) },
    uGrainIntensity:    { value: 0.575 },
    uGrainScale:        { value: 2.0 },
    uGrainSpeed:        { value: 0.1 },
    uGrainSparsity:     { value: 0.0 },
    uYOffset:           { value: 0.0 },
    uYOffsetWave:       { value: 20.0 },
    uYOffsetColor:      { value: 20.0 },
    uYOffsetFlow:       { value: 20.0 },
    uVeinFalloff:       { value: 6.0 },
    uCoreRatio:         { value: 0.95 },
    uMidRatio:          { value: 0.34 },
    uCenterRatio:       { value: 0.29 },
    uFilterFilmGrain:   { value: 1.0 },
    uFilmGrainIntensity:{ value: 0.35 },
    uFilmGrainScale:    { value: 1.5 },
    uFilmGrainSpeed:    { value: 0.5 },
    uFilmGrainVoidOnly: { value: 0.85 },
    uFilterVignette:    { value: 1.0 },
    uVignetteStrength:  { value: 0.4 },
    uVignetteRadius:    { value: 0.8 },
    uVignetteSoftness:  { value: 0.5 },
    uColVoid:           { value: hexToVec3('#060303') },
    uColProceduralBg:   { value: hexToVec3('#0e0707') },
    uColShadow:         { value: hexToVec3('#051014') },
    uColWarmOuter:      { value: hexToVec3('#002547') },
    uColWarmMid:        { value: hexToVec3('#441d09') },
    uColWarmCenter:     { value: hexToVec3('#ff0000') },
    uColCoolOuter:      { value: hexToVec3('#053748') },
    uColCoolMid:        { value: hexToVec3('#032f3f') },
    uColCoolCenter:     { value: hexToVec3('#ff0000') },
  };

  const material = new THREE.ShaderMaterial({
    vertexShader, fragmentShader, uniforms,
    depthTest: false, depthWrite: false,
  });
  scene.add(new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material));

  /* Mouse tracking — passive listener */
  const mouseTarget = { x: 0, y: 0 };
  const mouseCurrent = { x: 0, y: 0 };

  document.addEventListener('mousemove', (e) => {
    mouseTarget.x = e.clientX * dpr;
    mouseTarget.y = (window.innerHeight - e.clientY) * dpr;
  }, { passive: true });

  /* Debounced resize for Three.js */
  let threeResizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(threeResizeTimer);
    threeResizeTimer = setTimeout(() => {
      const d = Math.min(window.devicePixelRatio, 2);
      renderer.setSize(window.innerWidth, window.innerHeight);
      renderer.setPixelRatio(d);
      uniforms.uResolution.value.set(window.innerWidth * d, window.innerHeight * d);
      updateLogoPos();
    }, 150);
  });

  const startTime = performance.now();
  const decayLerp = () => 1.0 - uniforms.uMouseDecay.value;
  const mark = document.querySelector('.h-mark');

  function updateLogoPos() {
    if (!mark) return;
    const mr = mark.getBoundingClientRect();
    const cr = canvas.getBoundingClientRect();
    const cx = (mr.left - cr.left + mr.width / 2) * dpr;
    const cy = (cr.height - (mr.top - cr.top + mr.height / 2)) * dpr;
    uniforms.uLogoCenter.value.set(cx, cy);
    uniforms.uLogoSize.value.set(mr.width * dpr, mr.height * dpr);
  }
  updateLogoPos();
  let logoRaf = false;
  window.addEventListener('scroll', () => {
    if (!logoRaf) { logoRaf = true; requestAnimationFrame(() => { updateLogoPos(); logoRaf = false; }); }
  }, { passive: true });

  /* Pause Three.js rendering when hero is not visible */
  let heroVisible = true;
  const heroEl = document.getElementById('hero');
  if (heroEl) {
    const heroObs = new IntersectionObserver(([entry]) => {
      heroVisible = entry.isIntersecting;
    }, { threshold: 0 });
    heroObs.observe(heroEl);
  }

  /* Throttle WebGL to ~30fps — heavy fragment shader doesn't need 60fps */
  let lastFrame = 0;
  const frameBudget = 1000 / 30;

  (function animate(ts) {
    requestAnimationFrame(animate);
    if (!heroVisible || document.hidden) return;
    if (ts - lastFrame < frameBudget) return;
    lastFrame = ts;
    uniforms.uTime.value = (performance.now() - startTime) / 1000;
    const lerp = decayLerp();
    mouseCurrent.x += (mouseTarget.x - mouseCurrent.x) * lerp;
    mouseCurrent.y += (mouseTarget.y - mouseCurrent.y) * lerp;
    uniforms.uMouse.value.set(mouseCurrent.x, mouseCurrent.y);
    renderer.render(scene, camera);
  })(0);
} catch(e) {
  console.warn('Three.js shader failed to load', e);
}
