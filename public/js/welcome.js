/* ══════════════════════════════════════════════════
   CANVAS BG — Retro Grainy Liquid Gradient
   Dark cinematic · Amorphous silk blobs · Aurora Borealis bands
   Warm glowing cores → cool outer halos · Scroll-driven color story
   OPTIMISED: single rAF loop, half-res canvas, pre-baked grain patterns
═══════════════════════════════════════════════════ */
(function(){
  const cv=document.getElementById('bg');
  const cx=cv.getContext('2d');
  /* Half-res rendering — blurry bg at 22% opacity doesn't need full DPI */
  let W,H,dpr=Math.min(window.devicePixelRatio||1,1.5)*0.5;

  function resize(){
    W=window.innerWidth; H=window.innerHeight;
    cv.width=W*dpr; cv.height=H*dpr;
    cv.style.width=W+'px'; cv.style.height=H+'px';
    cx.scale(dpr,dpr);
  }

  /* Debounced resize */
  let resizeTimer;
  function debouncedResize(){
    clearTimeout(resizeTimer);
    resizeTimer=setTimeout(resize,150);
  }
  resize();
  window.addEventListener('resize',debouncedResize);

  const L=(t,a,b)=>a+(b-a)*t;
  const LA=(t,a,b)=>a.map((v,i)=>L(t,v,b[i]));

  /* ── Color palette ── */
  const P={
    crimson:[178, 14,  4],
    red:    [212, 28,  8],
    redOrg: [238, 52, 14],
    orange: [255, 78, 24],
    amber:  [208,106, 18],
    gold:   [228,162, 32],
    ink:    [  4,  4,  8],
    navy:   [  3, 10, 20],
    navyT:  [  2, 20, 36],
    deepT:  [  0, 36, 52],
    tealD:  [  4, 58, 76],
    teal:   [  6, 82,104],
    near:   [  6,  6, 10],
  };

  const KF=[
    {bg:[5,5,8],blobs:[
      {x:.03,y:.85,rx:.62,ry:.82, core:P.crimson,glow:P.deepT,  aC:.85,aG:.28,rot:-.04},
      {x:.12,y:.56,rx:.78,ry:1.28,core:P.redOrg, glow:P.deepT, aC:.50,aG:.15,rot:-.07},
      {x:.22,y:.30,rx:.98,ry:1.48,core:P.amber,  glow:P.navyT, aC:.26,aG:.07,rot:-.04},
      {x:.96,y:.06,rx:.88,ry:.78, core:P.tealD,  glow:P.ink,   aC:.40,aG:.08,rot:.06},
      {x:.98,y:.55,rx:.38,ry:1.78,core:P.deepT,  glow:P.ink,   aC:.26,aG:.04,rot:.0 },
    ]},
    {bg:[6,6,10],blobs:[
      {x:.40,y:.38,rx:2.75,ry:.34,core:P.amber,  glow:P.navyT, aC:.60,aG:.15,rot:-.20},
      {x:.04,y:.56,rx:.72,ry:1.88,core:P.tealD,  glow:P.ink,   aC:.50,aG:.09,rot:.06},
      {x:.92,y:.32,rx:.66,ry:1.20,core:P.crimson,glow:P.deepT, aC:.60,aG:.16,rot:-.04},
      {x:.54,y:.08,rx:.34,ry:.26, core:P.orange, glow:P.navyT, aC:.36,aG:.10,rot:.0 },
      {x:.74,y:.90,rx:.78,ry:.52, core:P.deepT,  glow:P.ink,   aC:.32,aG:.0, rot:.0 },
    ]},
    {bg:[6,6,10],blobs:[
      {x:.78,y:.40,rx:1.08,ry:1.62,core:P.crimson,glow:P.deepT, aC:.72,aG:.20,rot:-.06},
      {x:.36,y:.04,rx:2.28,ry:.28, core:P.orange, glow:P.navyT, aC:.46,aG:.12,rot:.10},
      {x:.06,y:.62,rx:.88,ry:1.70, core:P.teal,   glow:P.ink,   aC:.42,aG:.08,rot:.04},
      {x:.86,y:.82,rx:.66,ry:.58,  core:P.red,    glow:P.deepT, aC:.44,aG:.12,rot:.0 },
      {x:.16,y:.88,rx:.48,ry:.36,  core:P.amber,  glow:P.navyT, aC:.34,aG:.08,rot:.0 },
    ]},
    {bg:[6,6,10],blobs:[
      {x:.42,y:.10,rx:1.30,ry:.80, core:P.red,    glow:P.deepT, aC:.68,aG:.17,rot:.0 },
      {x:.86,y:.72,rx:1.08,ry:1.62,core:P.teal,   glow:P.ink,   aC:.44,aG:.08,rot:-.10},
      {x:.06,y:.50,rx:.56,ry:1.10, core:P.amber,  glow:P.navyT, aC:.50,aG:.13,rot:.06},
      {x:.58,y:.02,rx:.22,ry:.16,  core:P.gold,   glow:P.navyT, aC:.32,aG:.08,rot:.0 },
      {x:.10,y:.86,rx:.78,ry:.62,  core:P.deepT,  glow:P.ink,   aC:.38,aG:.0, rot:.0 },
    ]},
    {bg:[6,6,10],blobs:[
      {x:.72,y:.46,rx:1.22,ry:1.44,core:P.redOrg, glow:P.deepT, aC:.75,aG:.22,rot:.0 },
      {x:.70,y:.46,rx:.42,ry:.44,  core:P.gold,   glow:P.navyT, aC:.50,aG:.18,rot:.0 },
      {x:.04,y:.44,rx:.80,ry:1.82, core:P.teal,   glow:P.ink,   aC:.40,aG:.08,rot:.05},
      {x:.44,y:.02,rx:1.65,ry:.24, core:P.orange, glow:P.navyT, aC:.44,aG:.12,rot:-.05},
      {x:.96,y:.70,rx:.62,ry:1.15, core:P.deepT,  glow:P.ink,   aC:.38,aG:.0, rot:.0 },
    ]},
  ];

  function drawBlob(b,time,idx){
    cx.save();
    const ph=idx*1.618;
    const rate=0.08+idx*0.018;
    const wobX=0.022*Math.sin(time*rate*.9+ph);
    const wobY=0.018*Math.cos(time*rate+ph*1.4);
    const px=(b.x+wobX)*W;
    const py=(b.y+wobY)*H;
    const rot=(b.rot||0)+0.008*Math.sin(time*.06+ph);
    cx.translate(px,py);
    if(rot) cx.rotate(rot);
    cx.scale(b.rx,b.ry);
    const R=Math.min(W,H)*.48;
    const g=cx.createRadialGradient(0,0,0,0,0,R);
    const isWarm=b.core[0]>b.core[2];
    g.addColorStop(0,   `rgba(${b.core[0]},${b.core[1]},${b.core[2]},${b.aC})`);
    g.addColorStop(.10, `rgba(${b.core[0]},${b.core[1]},${b.core[2]},${b.aC*.82})`);
    if(isWarm){
      g.addColorStop(.22, `rgba(255,72,18,${b.aC*.52})`);
      g.addColorStop(.36, `rgba(88,30,4,${b.aC*.28})`);
    } else {
      const mid=b.core.map((v,i)=>v*.52+b.glow[i]*.48|0);
      g.addColorStop(.28, `rgba(${mid[0]},${mid[1]},${mid[2]},${b.aC*.38})`);
    }
    g.addColorStop(.50, `rgba(${b.glow[0]},${b.glow[1]},${b.glow[2]},${b.aG})`);
    g.addColorStop(.72, `rgba(${b.glow[0]},${b.glow[1]},${b.glow[2]},${b.aG*.26})`);
    g.addColorStop(1,   `rgba(${b.glow[0]},${b.glow[1]},${b.glow[2]},0)`);
    cx.globalCompositeOperation='screen';
    cx.fillStyle=g;
    const ext=R*1.08;
    cx.fillRect(-ext,-ext,ext*2,ext*2);
    cx.restore();
  }

  function drawSunSlices(time,heroFade=1){
    const N=9;
    const sx=W*.8, sy=H*.8, baseW=W*.2, bandH=H*.02, stride=H*.05;
    const shimmer=(.70+.30*Math.sin(time*1.35))*heroFade;
    cx.save();
    cx.globalCompositeOperation='screen';
    cx.filter='blur(11px)';
    for(let i=0;i<N;i++){
      const d=Math.abs(i-(N-1)/2)/((N-1)/2);
      const scale=1-d*.44;
      const alpha=(1-d*.50)*shimmer;
      const bw=baseW*scale;
      const bh=bandH*(.75+.25*scale);
      const y=sy+(i-(N-1)/2)*stride;
      const grd=cx.createLinearGradient(sx-bw,y,sx+bw,y);
      if(d>.6){
        grd.addColorStop(0,  `rgba(0,26,38,0)`);
        grd.addColorStop(.25,`rgba(0,26,38,${alpha*.5})`);
        grd.addColorStop(.5, `rgba(155,22,4,${alpha*.75})`);
        grd.addColorStop(.75,`rgba(0,26,38,${alpha*.5})`);
        grd.addColorStop(1,  `rgba(0,26,38,0)`);
      } else {
        grd.addColorStop(0,  `rgba(0,40,56,0)`);
        grd.addColorStop(.18,`rgba(0,52,70,${alpha*.55})`);
        grd.addColorStop(.40,`rgba(255,72,18,${alpha})`);
        grd.addColorStop(.50,`rgba(255,104,30,${alpha})`);
        grd.addColorStop(.60,`rgba(255,72,18,${alpha})`);
        grd.addColorStop(.82,`rgba(0,52,70,${alpha*.55})`);
        grd.addColorStop(1,  `rgba(0,40,56,0)`);
      }
      cx.beginPath();
      cx.ellipse(sx,y,bw,bh,0,0,Math.PI*2);
      cx.fillStyle=grd;
      cx.fill();
    }
    cx.restore();
  }

  /* ── Film grain — pre-baked patterns (avoid createPattern per frame) ── */
  const grainPatterns=(function(){
    const N=8,S=256;
    return Array.from({length:N},()=>{
      const gc=document.createElement('canvas');
      gc.width=gc.height=S;
      const gx=gc.getContext('2d');
      const id=gx.createImageData(S,S);
      const d=id.data;
      for(let i=0;i<d.length;i+=4){
        const v=Math.random()*255|0;
        d[i]=d[i+1]=d[i+2]=v;
        d[i+3]=255;
      }
      gx.putImageData(id,0,0);
      return cx.createPattern(gc,'repeat');
    });
  })();

  function drawGrain(time){
    const pat=grainPatterns[Math.floor(time*14)%grainPatterns.length];
    if(!pat) return;
    cx.save();
    cx.globalAlpha=.48;
    cx.globalCompositeOperation='overlay';
    cx.fillStyle=pat;
    cx.fillRect(0,0,W,H);
    cx.restore();
  }

  function lerpBlobs(A,B,t){
    const n=Math.max(A.length,B.length);
    return Array.from({length:n},(_,i)=>{
      const a=A[i]||A[A.length-1];
      const b=B[i]||B[B.length-1];
      return {
        x:L(t,a.x,b.x),y:L(t,a.y,b.y),
        rx:L(t,a.rx,b.rx),ry:L(t,a.ry,b.ry),
        core:LA(t,a.core,b.core).map(v=>v|0),
        glow:LA(t,a.glow,b.glow).map(v=>v|0),
        aC:L(t,a.aC,b.aC),aG:L(t,a.aG,b.aG),
        rot:L(t,a.rot||0,b.rot||0),
      };
    });
  }

  /* ── Shared scroll tracking (passive) ── */
  let scrollT=0,targetT=0;
  const nav=document.querySelector('nav');
  window.addEventListener('scroll',()=>{
    const ms=document.documentElement.scrollHeight-window.innerHeight;
    targetT=ms>0?window.scrollY/ms*.30:0;
    nav.classList.toggle('scrolled',window.scrollY>60);
  },{passive:true});

  /* ── Cursor refs ── */
  const curEl=document.getElementById('cur'), curR=document.getElementById('cur-r');
  let mx=0,my=0,crx=0,cry=0;
  document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY},{passive:true});

  /* ── Logo turbulence refs ── */
  const turb=document.getElementById('logo-turb');
  const turbBase=0.015,turbDrift=0.008,turbSpeed=0.12;

  /* ══════════════════════════════════════════════════
     SINGLE rAF LOOP — canvas BG + cursor + logo distortion
  ═══════════════════════════════════════════════════ */
  let last=0,time=0,tabVisible=true;
  document.addEventListener('visibilitychange',()=>{tabVisible=!document.hidden},{passive:true});

  function mainLoop(ts){
    requestAnimationFrame(mainLoop);
    if(!tabVisible) return;
    const dt=Math.min((ts-last)/1000,.05); last=ts; time+=dt;

    /* ── Cursor (transform3d — no layout thrashing) ── */
    crx+=(mx-crx)*.13; cry+=(my-cry)*.13;
    curEl.style.transform=`translate3d(${mx-4.5}px,${my-4.5}px,0)`;
    curR.style.transform=`translate3d(${crx-15}px,${cry-15}px,0)`;

    /* ── Logo SVG turbulence ── */
    if(turb){
      const tt=ts/1000*turbSpeed;
      const bx=turbBase+Math.sin(tt)*turbDrift;
      const by=turbBase+Math.cos(tt*0.7)*turbDrift;
      turb.setAttribute('baseFrequency',bx.toFixed(4)+' '+by.toFixed(4));
    }

    /* ── Canvas BG draw ── */
    scrollT+=(targetT-scrollT)*.012;
    const t=Math.max(0,Math.min(1,scrollT));
    const seg=t*(KF.length-1);
    const idx=Math.min(Math.floor(seg),KF.length-2);
    const alpha=seg-idx;
    const A=KF[idx],B=KF[idx+1];

    const bg=LA(alpha,A.bg,B.bg);
    cx.globalCompositeOperation='source-over';
    cx.fillStyle=`rgb(${bg[0]|0},${bg[1]|0},${bg[2]|0})`;
    cx.fillRect(0,0,W,H);
    lerpBlobs(A.blobs,B.blobs,alpha).forEach((b,i)=>drawBlob(b,time,i));
    const heroFade=idx===0?1-alpha:0;
    if(heroFade>0) drawSunSlices(time,heroFade);
    drawGrain(time);
  }
  requestAnimationFrame(mainLoop);
})();


/* ══════════════════════════════════════════════════
   PORTFOLIO FILTER
═══════════════════════════════════════════════════ */
(function(){
  const btns  = document.querySelectorAll('.f-btn');
  const cards = document.querySelectorAll('#portfolio-grid .pc');
  let current = 'all';

  btns.forEach(b => {
    b.addEventListener('click', () => {
      // Do not run filter logic if the carousel is active
      if (document.getElementById('portfolio-grid').classList.contains('carousel-active')) {
        return;
      }
      const f = b.dataset.filter;
      if(f === current) return;
      current = f;
      btns.forEach(x => x.classList.remove('active'));
      b.classList.add('active');

      cards.forEach(c => {
        const match = f === 'all' || c.dataset.cat === f;
        if(!match){
          c.classList.add('hiding');
          setTimeout(()=>c.classList.add('hidden'), 480);
        } else {
          c.classList.remove('hidden');
          requestAnimationFrame(()=>requestAnimationFrame(()=>c.classList.remove('hiding')));
        }
      });
    });
  });
})();

/* ══════════════════════════════════════════════════
   SCROLL REVEAL
═══════════════════════════════════════════════════ */
(function(){
  const io = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)} });
  },{threshold:.1});
  document.querySelectorAll('.rv,.rl,.rr').forEach(el=>io.observe(el));
})();

/* ══════════════════════════════════════════════════
   HERO ENTRY
═══════════════════════════════════════════════════ */
window.addEventListener('DOMContentLoaded',()=>{
  const nm = document.querySelector('.h-name');
  if(nm){ nm.style.cssText='opacity:0;transform:translateY(20px);transition:opacity .95s,transform .95s'; }
  setTimeout(()=>{ if(nm){nm.style.opacity='1';nm.style.transform='none'} },200);
});

/* ══════════════════════════════════════════════════
   CONTACT FORM POPUP
═══════════════════════════════════════════════════ */
(function(){
  const btn=document.getElementById('open-cf');
  const popup=document.getElementById('cf-popup');
  const closeBtn=document.getElementById('cf-close');
  const anchor=document.getElementById('cf-anchor');
  if(!btn||!popup||!closeBtn) return;

  let closing=false;
  function openPopup(){
    if(closing) return;
    popup.classList.remove('closing');
    popup.classList.add('open');
    requestAnimationFrame(()=>{
      popup.scrollIntoView({behavior:'smooth',block:'nearest'});
    });
    const first=popup.querySelector('.cf-input');
    if(first) setTimeout(()=>first.focus(),450);
  }
  function closePopup(){
    if(closing) return;
    closing=true;
    popup.classList.add('closing');
    setTimeout(()=>{
      popup.classList.remove('open','closing');
      closing=false;
    },400);
  }
  function isOpen(){return popup.classList.contains('open')&&!closing}

  btn.addEventListener('click',()=>{isOpen()?closePopup():openPopup()});
  closeBtn.addEventListener('click',closePopup);

  /* Scroll to contact heading + delayed popup open (shared by nav Contact & Hire me) */
  let popupTimer=null;
  function scrollToContactAndOpen(){
    if(popupTimer) clearTimeout(popupTimer);
    const hed=document.querySelector('.ct-hed');
    if(hed){
      const rect=hed.getBoundingClientRect();
      const target=window.scrollY+rect.top+rect.height/2-window.innerHeight/2+window.innerHeight*0.10;
      window.scrollTo({top:target,behavior:'smooth'});
    }
    popupTimer=setTimeout(()=>{
      if(!isOpen()) openPopup();
    },2000);
  }

  /* Nav "Contact" links (desktop + mobile) */
  document.querySelectorAll('.n-links a[href="#contact"], .mob-link[href="#contact"]').forEach(el=>{
    el.addEventListener('click',e=>{
      e.preventDefault();
      scrollToContactAndOpen();
    });
  });

  /* "Hire me" nav CTA */
  const hireCta=document.querySelector('.n-cta');
  if(hireCta){
    hireCta.addEventListener('click',e=>{
      e.preventDefault();
      scrollToContactAndOpen();
    });
  }
  document.addEventListener('keydown',e=>{
    if(e.key==='Escape'&&isOpen()) closePopup();
  });
  document.addEventListener('click',e=>{
    if(isOpen()&&anchor&&!anchor.contains(e.target)) closePopup();
  });
  /* Auto-close when popup scrolls out of view */
  const popupObs=new IntersectionObserver(([entry])=>{
    if(!entry.isIntersecting&&isOpen()) closePopup();
  },{threshold:0.15});
  popupObs.observe(popup);

  /* AJAX submit */
  const form=document.getElementById('cf-form');
  const status=document.getElementById('cf-status');
  const submitBtn=document.getElementById('cf-submit');
  if(form){
    form.addEventListener('submit',async e=>{
      e.preventDefault();
      submitBtn.disabled=true;
      submitBtn.style.opacity='.5';
      status.style.display='none';
      try{
        const res=await fetch(form.action,{
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
          body:JSON.stringify({name:form.name.value,email:form.email.value,subject:form.subject.value,message:form.message.value})
        });
        const data=await res.json();
        if(res.ok){
          status.style.display='block';status.style.background='rgba(16,185,129,.15)';status.style.color='#6ee7b7';
          status.textContent='Message sent successfully!';
          form.reset();
          setTimeout(closePopup,2000);
        }else{
          const errors=data.errors?Object.values(data.errors).flat().join(' '):'Something went wrong.';
          status.style.display='block';status.style.background='rgba(196,18,8,.15)';status.style.color='#fca5a5';
          status.textContent=errors;
        }
      }catch{
        status.style.display='block';status.style.background='rgba(196,18,8,.15)';status.style.color='#fca5a5';
        status.textContent='Network error. Please try again.';
      }finally{
        submitBtn.disabled=false;submitBtn.style.opacity='1';
      }
    });
  }
})();

/* ══════════════════════════════════════════════════
   MOBILE HAMBURGER MENU
═══════════════════════════════════════════════════ */
(function(){
  const ham=document.getElementById('ham');
  const menu=document.getElementById('mob-menu');
  if(!ham||!menu) return;

  let isOpen=false,closing=false;

  function open(){
    if(isOpen||closing) return;
    isOpen=true;
    menu.classList.remove('closing');
    menu.classList.add('open');
    ham.classList.add('active');
    document.body.style.overflow='hidden';
  }

  function close(){
    if(!isOpen||closing) return;
    closing=true;
    menu.classList.add('closing');
    menu.classList.remove('open');
    ham.classList.remove('active');
    setTimeout(()=>{
      menu.classList.remove('closing');
      closing=false;
      isOpen=false;
      document.body.style.overflow='';
    },650);
  }

  ham.addEventListener('click',()=>{ isOpen?close():open() });

  /* Close on link click */
  menu.querySelectorAll('.mob-link').forEach(link=>{
    link.addEventListener('click',()=>{
      close();
    });
  });

  /* Close on Escape */
  document.addEventListener('keydown',e=>{
    if(e.key==='Escape'&&isOpen) close();
  });
})();

/* ══════════════════════════════════════════════════
   Locale switch — animated overlay + form POST
═══════════════════════════════════════════════════ */
(function(){
  const toggle=document.querySelector('.lang-toggle');
  const overlay=document.getElementById('locale-overlay');
  if(!toggle||!overlay) return;

  const btns=toggle.querySelectorAll('.lang-btn');
  const csrfToken=document.querySelector('meta[name="csrf-token"]')?.content
    ||document.querySelector('input[name="_token"]')?.value||'';

  btns.forEach(btn=>{
    btn.addEventListener('click',()=>{
      const locale=btn.dataset.locale;
      if(btn.classList.contains('active')) return;

      /* Animate the sliding track */
      toggle.dataset.active=locale;
      btns.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');

      /* Show overlay after track finishes sliding */
      setTimeout(()=>{
        overlay.classList.add('active');

        /* Submit after overlay fades in */
        setTimeout(()=>{
          const form=document.createElement('form');
          form.method='POST';
          form.action='/locale/'+locale;
          const token=document.createElement('input');
          token.type='hidden';token.name='_token';token.value=csrfToken;
          form.appendChild(token);
          document.body.appendChild(form);
          form.submit();
        },350);
      },200);
    });
  });
})();

/* ══════════════════════════════════════════════════
   WORKS CAROUSEL — Advanced Peek Carousel
   - Bidirectional infinite scrolling (left and right)
   - Smooth animations with active item emphasis
   - CSS scroll-snap for native smooth snapping
   - Auto-play with intelligent pause/resume
   - IntersectionObserver for dynamic item states
   - Performance optimized with RAF and debouncing
═══════════════════════════════════════════════════ */
(function() {
    const container = document.getElementById('portfolio-grid');
    if (!container) return;
    const track = container.querySelector('.pg-track');
    if (!track) return;

    let isRunning = false;
    let autoPlayInterval = null;
    let resumeTimeout = null;
    let isUserInteracting = false;
    let activeObserver = null;
    let originalTrackHTML = '';
    let isTransitioning = false;
    const AUTO_PLAY_DELAY = 3500;
    const RESUME_DELAY = 4000;

    const setup = () => {
        // Store original HTML on first run
        if (originalTrackHTML === '') {
            originalTrackHTML = track.innerHTML;
        }

        // Check viewport size
        const isDesktop = window.innerWidth > 1024;

        // Clean up any existing setup
        cleanup();

        if (isDesktop) {
            // Desktop: restore original grid layout
            if (isRunning) {
                track.innerHTML = originalTrackHTML;
                container.style.scrollBehavior = '';
                container.scrollLeft = 0;
                isRunning = false;
            }
            return;
        }

        if (isRunning) return;

        // Mobile/Tablet: Setup carousel
        setupCarousel();
    };

    const cleanup = () => {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
        if (resumeTimeout) {
            clearTimeout(resumeTimeout);
            resumeTimeout = null;
        }
        if (activeObserver) {
            activeObserver.disconnect();
            activeObserver = null;
        }
        container.removeEventListener('scroll', handleScroll);
        container.removeEventListener('touchstart', handleTouchStart);
        container.removeEventListener('touchend', handleTouchEnd);
        container.removeEventListener('mousedown', handleMouseDown);
        container.removeEventListener('mouseup', handleMouseUp);
        container.removeEventListener('mouseenter', handleMouseEnter);
        container.removeEventListener('mouseleave', handleMouseLeave);
    };

    const setupCarousel = () => {
        // Start with clean slate
        track.innerHTML = originalTrackHTML;
        const items = Array.from(track.children);
        if (items.length < 2) return;

        // Create THREE sets of clones for seamless infinite scrolling
        // Prepend clones (for scrolling left)
        const prependClones = [];
        items.forEach(item => {
            const clone = item.cloneNode(true);
            clone.dataset.clone = 'prepend';
            prependClones.push(clone);
        });
        prependClones.reverse().forEach(clone => {
            track.insertBefore(clone, track.firstChild);
        });

        // Append clones (for scrolling right)
        items.forEach(item => {
            const clone = item.cloneNode(true);
            clone.dataset.clone = 'append';
            track.appendChild(clone);
        });

        // Calculate proper initial position (middle of the carousel)
        setTimeout(() => {
            const allItems = track.querySelectorAll('.pc');
            const itemWidth = allItems[0].offsetWidth + 16; // Include gap
            const initialPosition = itemWidth * items.length - 60; // Start at first original item
            container.scrollLeft = initialPosition;
        }, 50);

        // Setup IntersectionObserver for active/near-active item detection
        setupIntersectionObserver();

        // Add event listeners
        container.addEventListener('scroll', handleScroll, { passive: true });
        container.addEventListener('touchstart', handleTouchStart, { passive: true });
        container.addEventListener('touchend', handleTouchEnd, { passive: true });
        container.addEventListener('mousedown', handleMouseDown, { passive: true });
        container.addEventListener('mouseup', handleMouseUp, { passive: true });
        container.addEventListener('mouseenter', handleMouseEnter, { passive: true });
        container.addEventListener('mouseleave', handleMouseLeave, { passive: true });

        // Start auto-play after initial setup
        setTimeout(() => {
            startAutoPlay();
        }, 1500);

        isRunning = true;
    };

    const setupIntersectionObserver = () => {
        activeObserver = new IntersectionObserver((entries) => {
            const visibleItems = [];

            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    visibleItems.push({
                        element: entry.target,
                        ratio: entry.intersectionRatio
                    });
                }
            });

            // Sort by intersection ratio to find the most centered item
            visibleItems.sort((a, b) => b.ratio - a.ratio);

            // Clear all states
            track.querySelectorAll('.pc').forEach(item => {
                item.classList.remove('active', 'near-active');
            });

            // Set active item (most visible)
            if (visibleItems[0]) {
                visibleItems[0].element.classList.add('active');

                // Set near-active items (adjacent to active)
                const activeIndex = Array.from(track.children).indexOf(visibleItems[0].element);
                const prevItem = track.children[activeIndex - 1];
                const nextItem = track.children[activeIndex + 1];

                if (prevItem) prevItem.classList.add('near-active');
                if (nextItem) nextItem.classList.add('near-active');
            }
        }, {
            root: container,
            threshold: [0.3, 0.5, 0.7, 0.9]
        });

        // Observe all items
        track.querySelectorAll('.pc').forEach(item => {
            activeObserver.observe(item);
        });
    };

    let scrollEndTimer;
    const handleScroll = () => {
        // Pause auto-play on manual scroll
        if (!isUserInteracting) {
            pauseAutoPlay();
        }

        // Clear previous timer
        clearTimeout(scrollEndTimer);

        // Set new timer for scroll end detection
        scrollEndTimer = setTimeout(() => {
            if (!isTransitioning) {
                handleInfiniteLoop();
            }
            if (!isUserInteracting) {
                scheduleResume();
            }
        }, 100);
    };

    const handleInfiniteLoop = () => {
        if (isTransitioning) return;

        const scrollLeft = container.scrollLeft;
        const items = Array.from(track.querySelectorAll('.pc:not([data-clone])'));
        if (items.length === 0) return;

        const itemWidth = items[0].offsetWidth + 16;
        const totalOriginalWidth = itemWidth * items.length;

        // Define boundaries
        const leftBoundary = totalOriginalWidth - itemWidth;
        const rightBoundary = totalOriginalWidth * 2;

        // Check if we need to jump
        if (scrollLeft < leftBoundary) {
            // Scrolled too far left - jump forward
            isTransitioning = true;
            container.style.scrollBehavior = 'auto';
            container.scrollLeft = scrollLeft + totalOriginalWidth;

            requestAnimationFrame(() => {
                container.style.scrollBehavior = 'smooth';
                isTransitioning = false;
            });
        } else if (scrollLeft > rightBoundary) {
            // Scrolled too far right - jump backward
            isTransitioning = true;
            container.style.scrollBehavior = 'auto';
            container.scrollLeft = scrollLeft - totalOriginalWidth;

            requestAnimationFrame(() => {
                container.style.scrollBehavior = 'smooth';
                isTransitioning = false;
            });
        }
    };

    const handleTouchStart = () => {
        isUserInteracting = true;
        pauseAutoPlay();
    };

    const handleTouchEnd = () => {
        isUserInteracting = false;
        scheduleResume();
    };

    const handleMouseDown = () => {
        isUserInteracting = true;
        pauseAutoPlay();
    };

    const handleMouseUp = () => {
        isUserInteracting = false;
        scheduleResume();
    };

    const handleMouseEnter = () => {
        if (window.innerWidth > 768) { // Only on larger screens
            pauseAutoPlay();
        }
    };

    const handleMouseLeave = () => {
        if (window.innerWidth > 768 && !isUserInteracting) {
            scheduleResume();
        }
    };

    const startAutoPlay = () => {
        if (autoPlayInterval) return;

        autoPlayInterval = setInterval(() => {
            if (!isUserInteracting && !document.hidden && !isTransitioning) {
                // Smooth advance to next item
                const items = track.querySelectorAll('.pc');
                if (items.length > 0) {
                    const currentActive = track.querySelector('.pc.active');
                    const itemWidth = items[0].offsetWidth + 16;

                    // Use scrollTo for smoother animation
                    container.scrollTo({
                        left: container.scrollLeft + itemWidth,
                        behavior: 'smooth'
                    });
                }
            }
        }, AUTO_PLAY_DELAY);
    };

    const pauseAutoPlay = () => {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
        if (resumeTimeout) {
            clearTimeout(resumeTimeout);
            resumeTimeout = null;
        }
    };

    const scheduleResume = () => {
        if (resumeTimeout) {
            clearTimeout(resumeTimeout);
        }

        resumeTimeout = setTimeout(() => {
            if (!isUserInteracting && !document.hidden) {
                startAutoPlay();
            }
        }, RESUME_DELAY);
    };

    // Enhanced resize handler with better debouncing
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        // Pause during resize
        pauseAutoPlay();
        resizeTimer = setTimeout(() => {
            setup();
        }, 300);
    });

    // Handle visibility changes
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            pauseAutoPlay();
        } else if (isRunning && !isUserInteracting) {
            scheduleResume();
        }
    });

    // Initialize
    setup();
})();
