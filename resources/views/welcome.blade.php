<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>STATIC NOISE — Robin Hrdlicka</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,700&family=Crimson+Pro:ital,wght@0,300;0,400;1,300;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #06060A;
  --teal:    #1A5060;
  --orange:  #E84020;
  --amber:   #C41208;
  --cream:   #EAD8B8;
  --text:    #EDE6DA;
  --mid:     rgba(237,230,218,.52);
  --low:     rgba(237,230,218,.26);
  --border:  rgba(237,230,218,.09);
  --surface: rgba(255,255,255,.04);
  --r-pill:  999px;
  --r-lg:    24px;
  --r-xl:    36px;
  --fd: 'Barlow Condensed', sans-serif;
  --fb: 'Crimson Pro', Georgia, serif;
  --fm: 'IBM Plex Mono', monospace;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  background:var(--bg);
  color:var(--text);
  font-family:var(--fb);
  font-size:18px;
  line-height:1.6;
  cursor:none;
  overflow-x:hidden;
}
a{color:inherit;text-decoration:none}
img{display:block;max-width:100%}

/* ── CANVAS BG ── */
#bg-neat{
  position:fixed;inset:0;z-index:0;
  width:100%;height:100%;
  pointer-events:none;display:block;
}
#bg{
  position:fixed;inset:0;z-index:1;
  width:100%;height:100%;
  pointer-events:none;
  mix-blend-mode:screen;opacity:.22;
}


/* ── CURSOR ── */
#cur,#cur-r{position:fixed;top:0;left:0;z-index:9999;pointer-events:none;border-radius:50%;will-change:transform}
#cur{width:9px;height:9px;background:var(--amber);mix-blend-mode:difference}
#cur-r{width:30px;height:30px;border:1px solid rgba(196,18,8,.5);z-index:9998;transition:width .2s,height .2s}

/* ── NAV — centered glass pill ── */
nav{
  position:fixed;top:20px;left:50%;transform:translateX(-50%);
  z-index:500;
  display:flex;align-items:center;gap:0;
  background:rgba(8,22,30,.5);
  backdrop-filter:blur(32px) saturate(160%);
  -webkit-backdrop-filter:blur(32px) saturate(160%);
  border:1px solid rgba(237,230,218,.1);
  border-radius:var(--r-pill);
  padding:9px 9px 9px 22px;
  box-shadow:0 8px 40px rgba(0,0,0,.5),inset 0 1px 0 rgba(255,255,255,.06);
  white-space:nowrap;
  transition:background .4s ease,backdrop-filter .4s ease,-webkit-backdrop-filter .4s ease,box-shadow .4s ease;
}
nav.scrolled{
  background:rgba(8,22,30,.72);
  backdrop-filter:blur(48px) saturate(180%);
  -webkit-backdrop-filter:blur(48px) saturate(180%);
  box-shadow:0 8px 40px rgba(0,0,0,.6),inset 0 1px 0 rgba(255,255,255,.06);
}
.n-brand{font-family:var(--fm);font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--text);margin-right:24px}
.n-brand b{background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:500}
.n-links{display:flex;gap:2px;list-style:none}
.n-links a{
  display:block;font-family:var(--fm);font-size:11px;
  letter-spacing:.1em;text-transform:uppercase;color:var(--mid);
  padding:7px 15px;border-radius:var(--r-pill);
  transition:background .2s,color .2s;
}
.n-links a:hover{background:rgba(255,255,255,.08);color:var(--text)}
.n-cta{
  margin-left:8px;font-family:var(--fm);font-size:11px;
  letter-spacing:.1em;text-transform:uppercase;
  background:#C41208;color:#f0e8e0;
  padding:8px 20px;border-radius:var(--r-pill);
  transition:opacity .2s,transform .2s;
}
.n-cta:hover{opacity:.85;transform:scale(.97)}

/* ── LANGUAGE TOGGLE — fixed top-right ── */
.lang-toggle{
  position:fixed;top:20px;right:20px;z-index:501;
  display:flex;align-items:center;
  background:rgba(8,22,30,.5);
  backdrop-filter:blur(32px) saturate(160%);
  -webkit-backdrop-filter:blur(32px) saturate(160%);
  border:1px solid rgba(237,230,218,.1);
  border-radius:var(--r-pill);
  padding:2px;gap:0;
  box-shadow:0 4px 20px rgba(0,0,0,.4);
}
.lang-toggle-track{
  position:absolute;top:2px;left:2px;
  width:calc(50% - 2px);height:calc(100% - 4px);
  background:rgba(196,18,8,.7);
  border-radius:var(--r-pill);
  transition:transform .3s cubic-bezier(.4,0,.2,1);
  pointer-events:none;z-index:0;
}
.lang-toggle[data-active="cs"] .lang-toggle-track{
  transform:translateX(100%);
}
.lang-btn{
  position:relative;z-index:1;
  font-family:var(--fm);font-size:10px;
  letter-spacing:.08em;text-transform:uppercase;
  color:var(--low);background:transparent;
  border:none;cursor:pointer;
  padding:5px 10px;border-radius:var(--r-pill);
  transition:color .3s ease;
}
.lang-btn:hover{color:var(--text)}
.lang-btn.active{color:#f0e8e0}

/* ── LOCALE TRANSITION OVERLAY ── */
.locale-overlay{
  position:fixed;inset:0;z-index:10000;
  pointer-events:none;opacity:0;
  background:var(--bg);
  transition:opacity .35s cubic-bezier(.4,0,.2,1);
}
.locale-overlay.active{
  opacity:1;pointer-events:all;
}
.locale-overlay-inner{
  position:absolute;inset:0;
  display:flex;align-items:center;justify-content:center;
}
.locale-tomb{
  width:48px;height:66px;
}
.locale-tomb .tomb-stroke{
  fill:none;stroke:rgba(237,230,218,.18);stroke-width:1.8;
}
.locale-tomb .tomb-fill-rect{
  transform:translateY(0);
  animation:none;
}
.locale-overlay.active .tomb-fill-rect{
  animation:tomb-fill .8s cubic-bezier(.4,0,.2,1) forwards;
}
@keyframes tomb-fill{
  from{transform:translateY(0)}
  to{transform:translateY(-44px)}
}

/* ── MAIN / SECTION WRAPPER ── */
main{position:relative;z-index:10}
section{position:relative}
.sl{
  font-family:var(--fm);font-size:10px;
  letter-spacing:.26em;text-transform:uppercase;
  background:linear-gradient(95deg,#C41208 0%,#003C52 100%);
  -webkit-background-clip:text;background-clip:text;color:transparent;
  margin-bottom:20px;
  display:flex;align-items:center;gap:12px;
}
.sl::before{content:'';width:24px;height:1px;background:linear-gradient(90deg,#C41208,#003C52)}

/* ── HERO ── */
#hero{
  min-height:100vh;display:flex;flex-direction:column;
  justify-content:flex-end;padding:0 60px 80px;
  overflow:hidden;position:relative;
}
#hero-neat{
  position:fixed;inset:0;width:100%;height:100%;
  z-index:0;pointer-events:none;display:block;
}
.h-name{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(90px,15vw,220px);
  line-height:.87;letter-spacing:-.025em;
  position:relative;z-index:2;
}
.h-a{color:var(--text);display:block}
.h-c{background:linear-gradient(125deg,#CC1C0A 5%,#7A0E04 90%);-webkit-background-clip:text;background-clip:text;color:transparent;display:block;filter:drop-shadow(0 2px 12px rgba(0,0,0,.5))}
.h-foot{display:flex;align-items:flex-end;gap:60px;margin-top:44px;position:relative;z-index:2}
.h-copy{max-width:380px}
.h-copy h2{
  font-family:var(--fb);font-size:clamp(18px,2.2vw,24px);
  font-weight:300;font-style:italic;color:var(--text);
  margin-bottom:10px;line-height:1.35;
}
.h-copy p{font-size:15px;font-weight:300;color:var(--mid);line-height:1.75}
.h-acts{margin-left:auto;display:flex;flex-direction:column;align-items:flex-end;gap:14px}
.btn-p{
  display:inline-flex;align-items:center;gap:10px;
  font-family:var(--fm);font-size:11px;
  letter-spacing:.14em;text-transform:uppercase;
  background:var(--text);color:#08141a;
  padding:15px 30px;border-radius:var(--r-pill);
  transition:opacity .2s,transform .2s;
}
.btn-p:hover{opacity:.85;transform:scale(.97)}
.btn-g{
  font-family:var(--fm);font-size:11px;
  letter-spacing:.14em;text-transform:uppercase;
  color:var(--low);padding:4px 0;
  border-bottom:1px solid rgba(237,230,218,.12);
  transition:color .2s,border-color .2s;
}
.btn-g:hover{color:var(--text);border-color:rgba(237,230,218,.4)}
.h-scroll{
  position:absolute;bottom:32px;right:60px;z-index:2;
  display:flex;flex-direction:column;align-items:center;gap:8px;
  font-family:var(--fm);font-size:9px;letter-spacing:.22em;text-transform:uppercase;color:var(--low);
}
.s-line{
  width:1px;height:44px;
  background:linear-gradient(to bottom,var(--amber),transparent);
  animation:pl 2.2s ease infinite;
}
@keyframes pl{0%,100%{opacity:.22;transform:scaleY(1)}50%{opacity:.8;transform:scaleY(.72)}}

/* ── TECH STACK STRIP ── */
#stack{
  border-top:1px solid var(--border);border-bottom:1px solid var(--border);
  background:rgba(2,4,8,0.85);position:relative;z-index:10;
}
.sk-inner{display:flex;padding:36px 60px;gap:0;align-items:stretch}
.sk-group{
  flex:1;padding:0 36px;border-right:1px solid var(--border);
  display:flex;flex-direction:column;gap:14px;
}
.sk-group:first-child{padding-left:0}
.sk-group:last-child{border-right:none;padding-right:0}
.sk-head{
  font-family:var(--fm);font-size:9px;
  letter-spacing:.22em;text-transform:uppercase;
  background:linear-gradient(95deg,#C41208 0%,#003C52 100%);
  -webkit-background-clip:text;background-clip:text;color:transparent;
  display:flex;align-items:center;gap:10px;
}
.sk-head::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(196,18,8,.24),rgba(0,60,82,.14))}
.sk-list{display:flex;flex-direction:column;gap:5px}
.sk-item{
  font-family:var(--fd);font-weight:700;font-size:16px;
  letter-spacing:.05em;text-transform:uppercase;
  color:var(--low);padding:2px 0;
  border-bottom:1px solid transparent;
  transition:color .2s,border-color .2s;
}
.sk-item:hover{color:var(--text);border-color:rgba(237,230,218,.18)}
.sk-item--hi{color:var(--mid);font-size:17px}
@media(max-width:900px){
  .sk-inner{flex-wrap:wrap;gap:0;padding:28px 24px}
  .sk-group{flex:calc(50% - 1px);padding:20px 0;border-right:none;border-bottom:1px solid var(--border)}
  .sk-group:last-child{border-bottom:none}
}

/* ─────────────────────────────── WORKS (PORTFOLIO) ── */
#works{padding:120px 60px}
.works-top{
  display:flex;justify-content:space-between;align-items:flex-end;
  margin-bottom:52px;
}
.works-hed{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(64px,10vw,140px);
  line-height:.88;letter-spacing:-.025em;
}
.works-hed span{color:transparent;-webkit-text-stroke:2px rgba(237,230,218,.25);display:block}
.works-sub{
  font-family:var(--fb);font-size:16px;font-weight:300;font-style:italic;
  color:var(--mid);max-width:280px;text-align:right;line-height:1.6;
}

/* Filter bar */
.filter-bar{
  display:flex;gap:6px;flex-wrap:wrap;
  margin-bottom:40px;
}
.f-btn{
  font-family:var(--fm);font-size:10px;
  letter-spacing:.12em;text-transform:uppercase;
  padding:8px 20px;border-radius:var(--r-pill);
  border:1px solid var(--border);color:var(--low);
  background:transparent;cursor:pointer;
  transition:all .25s;
}
.f-btn:hover{border-color:rgba(237,230,218,.25);color:var(--text);background:var(--surface)}
.f-btn.active{background:var(--amber);color:#f0e8e0;border-color:var(--amber)}

/* Portfolio grid */
.pg{
  display:grid;
  grid-template-columns:repeat(12,1fr);
  grid-auto-rows:200px;
  gap:10px;
}

/* card base */
.pc{
  position:relative;overflow:hidden;
  border-radius:var(--r-lg);
  background:var(--surface);
  border:1px solid var(--border);
  cursor:pointer;
  transition:opacity .5s ease,transform .5s ease;
}
.pc.hiding{opacity:0;transform:scale(.95);pointer-events:none}
.pc.hidden{display:none}

/* size variants */
.pc--featured{grid-column:span 7;grid-row:span 3}
.pc--tall    {grid-column:span 5;grid-row:span 3}
.pc--wide    {grid-column:span 8;grid-row:span 2}
.pc--sq      {grid-column:span 4;grid-row:span 2}
.pc--wide2   {grid-column:span 7;grid-row:span 2}
.pc--sq2     {grid-column:span 5;grid-row:span 2}
.pc--half    {grid-column:span 6;grid-row:span 2}
.pc--third   {grid-column:span 4;grid-row:span 2}

/* image fill */
.pc img{
  width:100%;height:100%;object-fit:cover;
  filter:grayscale(25%) contrast(1.05) brightness(.82);
  transition:transform .8s cubic-bezier(.25,.46,.45,.94),filter .5s;
}
.pc:hover img{transform:scale(1.05);filter:grayscale(0%) contrast(1.08) brightness(.88)}

/* overlay */
.pc-ov{
  position:absolute;inset:0;
  background:linear-gradient(to top, rgba(6,18,24,.92) 0%, rgba(6,18,24,.35) 45%, transparent 72%);
  transition:background .4s;
  border-radius:inherit;
}
.pc:hover .pc-ov{background:linear-gradient(to top, rgba(6,18,24,.96) 0%, rgba(6,18,24,.55) 55%, rgba(6,18,24,.12) 82%)}

/* gradient-only cards (no image) */
.pc-grad{
  width:100%;height:100%;
  transition:transform .4s;
}
.pc:hover .pc-grad{transform:scale(1.04)}

/* card info */
.pc-info{
  position:absolute;bottom:0;left:0;right:0;
  padding:24px 26px;
  transform:translateY(8px);transition:transform .35s ease;
}
.pc:hover .pc-info{transform:translateY(0)}
.pc-cat{
  font-family:var(--fm);font-size:9px;
  letter-spacing:.18em;text-transform:uppercase;
  background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:8px;display:block;
}
.pc-title{
  font-family:var(--fd);font-weight:800;
  font-size:clamp(22px,2.8vw,34px);
  line-height:.95;letter-spacing:-.01em;color:var(--text);
  margin-bottom:8px;
}
.pc-desc{
  font-size:13px;font-weight:300;color:var(--mid);
  line-height:1.65;max-width:360px;
  opacity:0;transform:translateY(6px);
  transition:opacity .35s .05s,transform .35s .05s;
}
.pc:hover .pc-desc{opacity:1;transform:translateY(0)}
.pc-tags{
  display:flex;flex-wrap:wrap;gap:5px;margin-top:12px;
  opacity:0;transition:opacity .3s .1s;
}
.pc:hover .pc-tags{opacity:1}
.pc-tag{
  font-family:var(--fm);font-size:9px;
  letter-spacing:.08em;text-transform:uppercase;
  border:1px solid rgba(237,230,218,.16);
  padding:3px 10px;border-radius:var(--r-pill);color:var(--low);
}

/* year badge */
.pc-yr{
  position:absolute;top:16px;right:16px;
  font-family:var(--fm);font-size:9px;letter-spacing:.14em;
  color:var(--mid);background:rgba(6,18,24,.65);
  padding:4px 10px;border-radius:var(--r-pill);
  backdrop-filter:blur(8px);border:1px solid var(--border);
}

/* view arrow */
.pc-arrow{
  position:absolute;top:16px;left:16px;
  width:34px;height:34px;border-radius:50%;
  background:rgba(196,18,8,.15);border:1px solid rgba(196,18,8,.3);
  display:flex;align-items:center;justify-content:center;
  font-size:14px;color:var(--amber);
  opacity:0;transform:scale(.7);
  transition:opacity .3s,transform .3s;
}
.pc:hover .pc-arrow{opacity:1;transform:scale(1)}

/* text-only card */
.pc-text-body{
  position:absolute;inset:0;padding:32px 28px;
  display:flex;flex-direction:column;justify-content:flex-end;
}
.pc-big-word{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(48px,6vw,88px);
  line-height:.9;letter-spacing:-.03em;
  color:transparent;
  -webkit-text-stroke:1.5px rgba(237,230,218,.18);
  position:absolute;top:20px;left:22px;
  transition:color .3s,-webkit-text-stroke .3s;
  user-select:none;
}
.pc:hover .pc-big-word{color:rgba(237,230,218,.06);-webkit-text-stroke:1.5px rgba(237,230,218,.3)}

/* ── SERVICES ── */
#services{padding:100px 60px;border-top:1px solid var(--border)}
.sv-wrap{display:grid;grid-template-columns:1fr 2fr;gap:80px;align-items:start}
.sv-left{}
.sv-big{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(44px,7vw,92px);
  line-height:.9;letter-spacing:-.025em;color:var(--text);
}
.sv-big b{background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900;display:block}
.sv-grid{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.sc{
  background:var(--surface);border:1px solid var(--border);
  padding:36px 30px;border-radius:16px;
  position:relative;overflow:hidden;
  transition:background .3s,transform .3s;
}
.sc:first-child{border-top-left-radius:36px}
.sc:nth-child(2){border-top-right-radius:36px}
.sc:nth-child(3){border-bottom-left-radius:36px}
.sc:last-child{border-bottom-right-radius:36px}
.sc::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 0% 0%, rgba(196,18,8,.07) 0%,transparent 60%);
  opacity:0;transition:opacity .35s;border-radius:inherit;
}
.sc:hover{background:rgba(255,255,255,.07);transform:translateY(-3px)}
.sc:hover::before{opacity:1}
.sc-icon{font-family:var(--fm);font-size:20px;background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:18px;display:block}
.sc-name{font-family:var(--fd);font-weight:800;font-size:24px;letter-spacing:.03em;color:var(--text);margin-bottom:10px}
.sc-desc{font-size:14px;font-weight:300;color:var(--low);line-height:1.75}

/* ── ABOUT ── */
#about{padding:120px 60px;border-top:1px solid var(--border)}
.ab-wrap{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
.ab-img{position:relative}
.ab-port{border-radius:var(--r-xl);overflow:hidden;position:relative}
.ab-port::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(196,18,8,.12) 0%,transparent 55%);
  z-index:1;pointer-events:none;
}
.ab-port img{width:100%;height:500px;object-fit:cover;filter:grayscale(100%) contrast(1.1) brightness(.8);border-radius:var(--r-xl)}
.ab-photo-placeholder{
  width:100%;height:500px;border-radius:var(--r-xl);
  background:
    radial-gradient(ellipse at 50% 40%, rgba(196,18,8,.08) 0%, transparent 60%),
    radial-gradient(ellipse at 30% 70%, rgba(5,62,125,.06) 0%, transparent 50%),
    linear-gradient(160deg, #0a0e14 0%, #0d1218 50%, #080c10 100%);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;
  position:relative;overflow:hidden;
  border:1px solid rgba(255,255,255,.04);
}
.ab-photo-placeholder::before{
  content:'';position:absolute;inset:0;
  background:repeating-linear-gradient(
    0deg,transparent,transparent 49px,rgba(255,255,255,.02) 49px,rgba(255,255,255,.02) 50px
  );
  pointer-events:none;
}
.ab-ph-initials{
  font-family:var(--fd);font-weight:900;font-size:72px;letter-spacing:.06em;
  color:rgba(255,255,255,.06);line-height:1;
}
.ab-ph-hint{
  font-family:var(--fb);font-weight:300;font-size:11px;letter-spacing:.15em;text-transform:uppercase;
  color:rgba(255,255,255,.15);
}
.ab-float{
  position:absolute;bottom:22px;left:22px;right:22px;z-index:2;
  background:rgba(8,20,28,.68);backdrop-filter:blur(20px);
  border:1px solid rgba(237,230,218,.1);
  border-radius:20px;padding:16px 18px;
  display:flex;gap:8px;flex-wrap:wrap;
}
.ab-ghost{
  position:absolute;top:-30px;right:-50px;
  font-family:var(--fd);font-weight:900;font-size:160px;line-height:1;
  color:transparent;-webkit-text-stroke:1px rgba(237,230,218,.04);
  user-select:none;pointer-events:none;z-index:-1;
}
.ab-hed{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(38px,5.5vw,68px);
  line-height:.91;letter-spacing:-.025em;color:var(--text);margin-bottom:28px;
}
.ab-hed em{
  font-style:italic;font-family:var(--fb);font-weight:300;
  background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;font-size:.68em;display:block;
  line-height:1.4;letter-spacing:0;
}
.ab-copy{font-size:16px;font-weight:300;color:var(--mid);line-height:1.82;margin-bottom:26px}
.ab-mantra{
  font-family:var(--fd);font-weight:800;
  font-size:clamp(18px,2.8vw,30px);
  line-height:1.1;color:var(--text);
  padding:22px 0;
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
  margin-bottom:30px;
}
.ab-mantra b{background:linear-gradient(95deg,#C41208 0%,#003C52 100%);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:800}
.bdg-row{display:flex;flex-wrap:wrap;gap:8px}
.bdg{
  display:flex;align-items:center;gap:7px;
  font-family:var(--fm);font-size:10px;
  letter-spacing:.1em;text-transform:uppercase;
  background:var(--surface);border:1px solid var(--border);
  padding:7px 16px;border-radius:var(--r-pill);color:var(--mid);
  transition:border-color .2s,background .2s;
}
.bdg:hover{background:rgba(255,255,255,.07);border-color:rgba(237,230,218,.18)}
.bdg-d{width:5px;height:5px;border-radius:50%;background:var(--amber);flex-shrink:0}
.bdg-h{background:rgba(196,18,8,.1);border-color:rgba(196,18,8,.28);color:var(--amber)}
.bdg-h .bdg-d{background:var(--amber)}

/* ── PROCESS ── */
#process{padding:80px 60px;border-top:1px solid var(--border)}
.pr-hed{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(48px,7vw,96px);
  line-height:.9;letter-spacing:-.025em;color:var(--text);
  margin-bottom:48px;
}
.pr-hed span{color:transparent;-webkit-text-stroke:2px rgba(237,230,218,.25)}
.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:2px}
.st{
  background:var(--surface);border:1px solid var(--border);
  padding:38px 28px;border-radius:16px;
  transition:background .3s,transform .3s;position:relative;overflow:hidden;
}
.st:first-child{border-top-left-radius:36px;border-bottom-left-radius:36px}
.st:last-child{border-top-right-radius:36px;border-bottom-right-radius:36px}
.st:hover{background:rgba(255,255,255,.07);transform:translateY(-4px)}
.st-n{
  font-family:var(--fd);font-weight:900;font-size:64px;
  color:transparent;-webkit-text-stroke:1.5px rgba(196,18,8,.4);
  line-height:1;margin-bottom:14px;display:block;
}
.st-name{font-family:var(--fd);font-weight:800;font-size:22px;letter-spacing:.04em;color:var(--text);margin-bottom:10px}
.st-desc{font-size:13px;font-weight:300;color:var(--low);line-height:1.72}

/* ── CONTACT ── */
#contact{padding:120px 60px;border-top:1px solid var(--border);position:relative;overflow:hidden}
.ct-ghost{
  position:absolute;top:50%;left:50%;
  transform:translate(-50%,-50%);
  font-family:var(--fd);font-weight:900;
  font-size:clamp(120px,22vw,320px);
  letter-spacing:-.04em;
  color:transparent;-webkit-text-stroke:1px rgba(237,230,218,.03);
  white-space:nowrap;user-select:none;pointer-events:none;
}
.ct-in{
  position:relative;z-index:2;
  max-width:860px;margin:0 auto;
  display:flex;flex-direction:column;align-items:center;text-align:center;
}
.ct-eye{
  font-family:var(--fm);font-size:10px;
  letter-spacing:.28em;text-transform:uppercase;
  color:var(--low);margin-bottom:28px;
  display:flex;align-items:center;gap:16px;
}
.ct-eye::before,.ct-eye::after{content:'';flex:1;height:1px;background:var(--border)}
.ct-hed{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(60px,12vw,154px);
  line-height:.86;letter-spacing:-.04em;color:var(--text);margin-bottom:32px;
}
.ct-sub{
  font-size:19px;font-weight:300;font-style:italic;
  color:var(--mid);max-width:500px;line-height:1.65;margin-bottom:44px;
}
.ct-email{
  font-family:var(--fd);font-weight:700;
  font-size:clamp(20px,3.5vw,42px);
  color:var(--mid);margin-bottom:44px;
  padding-bottom:6px;
  border-bottom:1px solid rgba(237,230,218,.15);
  transition:color .2s,border-color .2s;
}
.ct-email:hover{color:var(--text);border-color:rgba(237,230,218,.4)}
.btn-ct{
  display:inline-flex;align-items:center;gap:12px;
  font-family:var(--fm);font-size:12px;
  letter-spacing:.14em;text-transform:uppercase;
  background:rgba(196,18,8,.1);border:1px solid rgba(196,18,8,.3);
  color:var(--amber);padding:18px 44px;border-radius:var(--r-pill);
  margin-bottom:52px;cursor:pointer;
  transition:background .25s,border-color .25s,transform .2s;
}
.btn-ct:hover{background:rgba(196,18,8,.2);border-color:var(--amber);transform:scale(.97)}
.so-links{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-bottom:44px}
.so-links a{
  font-family:var(--fm);font-size:10px;letter-spacing:.12em;text-transform:uppercase;
  color:var(--low);padding:8px 18px;border:1px solid var(--border);
  border-radius:var(--r-pill);
  transition:border-color .2s,color .2s,background .2s;
}
.so-links a:hover{color:var(--text);border-color:rgba(237,230,218,.22);background:var(--surface)}
.ct-sign{font-family:var(--fb);font-size:13px;font-style:italic;color:var(--low);display:flex;align-items:center;gap:8px}

/* ── CONTACT FORM POPUP (inline popover) ── */
.cf-anchor{position:relative;display:inline-flex;flex-direction:column;align-items:center}

/* Border-sweep keyframe — draws a glow line around the popup on open */
@keyframes cf-border-sweep{
  0%{opacity:0;clip-path:inset(100% 50% 0% 50%)}
  30%{opacity:1}
  100%{opacity:1;clip-path:inset(0% 0% 0% 0%)}
}
/* Subtle glow pulse after open */
@keyframes cf-glow-pulse{
  0%,100%{box-shadow:0 24px 80px rgba(0,0,0,.65),0 0 0 rgba(196,18,8,0)}
  50%{box-shadow:0 24px 80px rgba(0,0,0,.65),0 0 50px rgba(196,18,8,.1)}
}

.cf-popup{
  position:absolute;bottom:calc(100% + 22px);left:50%;
  transform-origin:bottom center;
  transform:translateX(-50%) translateY(20px) scaleX(.15) scaleY(.04);
  width:min(624px,94vw);
  background:rgba(10,12,16,.82);
  backdrop-filter:blur(28px) saturate(140%);
  -webkit-backdrop-filter:blur(28px) saturate(140%);
  border:1.5px solid transparent;
  border-radius:var(--r-xl);
  padding:48px 44px 38px;
  box-shadow:0 24px 80px rgba(0,0,0,.65),0 0 60px rgba(196,18,8,.06);
  opacity:0;pointer-events:none;
  z-index:100;
  /* Close transition — snappy collapse back into button */
  transition:
    opacity .3s cubic-bezier(.55,.06,.68,.19),
    transform .35s cubic-bezier(.55,.06,.68,.19),
    border-color .3s ease;
}
/* Glowing border overlay — animates separately */
.cf-popup::before{
  content:'';position:absolute;inset:-1.5px;
  border:1.5px solid rgba(196,18,8,.38);
  border-radius:inherit;
  opacity:0;pointer-events:none;
  transition:opacity .3s ease;
}
.cf-popup.open::before{
  animation:cf-border-sweep .6s cubic-bezier(.22,.61,.36,1) forwards;
}

.cf-popup.open{
  opacity:1;pointer-events:auto;
  border-color:rgba(196,18,8,.38);
  transform:translateX(-50%) translateY(0) scaleX(1) scaleY(1);
  /* Open transition — smooth cinematic expansion */
  transition:
    opacity .5s cubic-bezier(.22,.61,.36,1),
    transform .6s cubic-bezier(.16,1,.3,1),
    border-color .4s ease .1s;
  animation:cf-glow-pulse 2.5s ease .6s 1;
}

/* Arrow pointing down to the button */
.cf-popup::after{
  content:'';position:absolute;
  bottom:-10px;left:50%;transform:translateX(-50%) rotate(45deg);
  width:20px;height:20px;
  background:rgba(10,12,16,.82);
  backdrop-filter:blur(28px);
  -webkit-backdrop-filter:blur(28px);
  border-right:1.5px solid rgba(196,18,8,.38);
  border-bottom:1.5px solid rgba(196,18,8,.38);
  border-radius:0 0 5px 0;
  opacity:0;
  transition:opacity .2s ease;
}
.cf-popup.open::after{
  opacity:1;
  transition:opacity .3s ease .25s;
}

/* Staggered content reveals — all children start hidden */
.cf-popup .cf-accent,
.cf-popup .cf-close,
.cf-popup .cf-head,
.cf-popup .cf-subhead,
.cf-popup .cf-row,
.cf-popup .cf-submit,
.cf-popup .cf-note{
  opacity:0;
  transform:translateY(12px);
  transition:opacity .2s ease,transform .2s ease;
  transition-delay:0s;
}
/* Open state — staggered cascade waterfall */
.cf-popup.open .cf-accent{opacity:1;transform:none;transition:.35s ease .18s}
.cf-popup.open .cf-close{opacity:1;transform:none;transition:.3s ease .22s}
.cf-popup.open .cf-head{opacity:1;transform:none;transition:.35s ease .28s}
.cf-popup.open .cf-subhead{opacity:1;transform:none;transition:.35s ease .36s}
.cf-popup.open .cf-row:nth-of-type(1){opacity:1;transform:none;transition:.3s ease .42s}
.cf-popup.open .cf-row:nth-of-type(2){opacity:1;transform:none;transition:.3s ease .48s}
.cf-popup.open .cf-row:nth-of-type(3){opacity:1;transform:none;transition:.3s ease .54s}
.cf-popup.open .cf-submit{opacity:1;transform:none;transition:.35s ease .60s}
.cf-popup.open .cf-note{opacity:1;transform:none;transition:.35s ease .66s}

/* Closing state — reverse stagger: content collapses bottom-up, then shell shrinks */
.cf-popup.closing{
  opacity:0;pointer-events:none;
  transform:translateX(-50%) translateY(16px) scaleX(.2) scaleY(.06);
  transition:
    opacity .25s cubic-bezier(.55,.06,.68,.19) .15s,
    transform .3s cubic-bezier(.55,.06,.68,.19) .12s,
    border-color .15s ease;
  border-color:transparent;
}
.cf-popup.closing::before{opacity:0;transition:opacity .12s ease}
.cf-popup.closing::after{opacity:0;transition:opacity .1s ease}
.cf-popup.closing .cf-note{opacity:0;transform:translateY(-6px) scale(.97);transition:.12s ease 0s}
.cf-popup.closing .cf-submit{opacity:0;transform:translateY(-6px) scale(.97);transition:.12s ease .02s}
.cf-popup.closing .cf-row:nth-of-type(3){opacity:0;transform:translateY(-6px);transition:.1s ease .04s}
.cf-popup.closing .cf-row:nth-of-type(2){opacity:0;transform:translateY(-6px);transition:.1s ease .06s}
.cf-popup.closing .cf-row:nth-of-type(1){opacity:0;transform:translateY(-6px);transition:.1s ease .08s}
.cf-popup.closing .cf-subhead{opacity:0;transform:translateY(-6px);transition:.1s ease .09s}
.cf-popup.closing .cf-head{opacity:0;transform:translateY(-8px) scale(.96);transition:.12s ease .1s}
.cf-popup.closing .cf-close{opacity:0;transform:scale(.5);transition:.1s ease .04s}
.cf-popup.closing .cf-accent{opacity:0;transition:.1s ease .1s}

.cf-close{
  position:absolute;top:18px;right:18px;
  width:34px;height:34px;border-radius:50%;
  background:rgba(255,255,255,.04);border:1.5px solid rgba(196,18,8,.38);
  color:var(--low);font-size:16px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:background .2s,color .2s,border-color .2s;
}
.cf-close:hover{background:rgba(196,18,8,.15);color:var(--text);border-color:rgba(196,18,8,.5)}
.cf-head{
  font-family:var(--fd);font-weight:900;
  font-size:clamp(28px,5vw,40px);
  line-height:.92;letter-spacing:-.02em;
  color:var(--text);margin-bottom:8px;
}
.cf-subhead{
  font-family:var(--fb);font-size:15px;font-weight:300;font-style:italic;
  color:var(--mid);margin-bottom:28px;line-height:1.6;
}
.cf-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.cf-field{display:flex;flex-direction:column;gap:6px;margin-bottom:0}
.cf-field.full{grid-column:span 2}
.cf-label{
  font-family:var(--fm);font-size:9px;
  letter-spacing:.18em;text-transform:uppercase;
  color:var(--low);
}
.cf-input,.cf-textarea{
  font-family:var(--fb);font-size:15px;font-weight:300;
  color:var(--text);
  background:rgba(255,255,255,.03);
  border:1.5px solid rgba(196,18,8,.38);
  border-radius:12px;padding:14px 16px;
  outline:none;
  transition:border-color .3s ease,background .3s ease,box-shadow .3s ease;
}
.cf-input::placeholder,.cf-textarea::placeholder{color:rgba(237,230,218,.18)}
.cf-input:focus,.cf-textarea:focus{
  border-color:rgba(196,18,8,.55);
  background:rgba(255,255,255,.05);
  box-shadow:0 0 20px rgba(196,18,8,.08);
}
.cf-textarea{resize:vertical;min-height:120px;line-height:1.6}
.cf-submit{
  width:100%;margin-top:10px;
  font-family:var(--fm);font-size:12px;
  letter-spacing:.14em;text-transform:uppercase;
  background:linear-gradient(95deg,rgba(196,18,8,.9) 0%,rgba(196,18,8,.7) 100%);
  color:#f0e8e0;border:none;
  padding:18px 0;border-radius:var(--r-pill);
  cursor:pointer;
  transition:opacity .2s,transform .2s,box-shadow .3s;
  position:relative;overflow:hidden;
}
.cf-submit::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);
  transform:translateX(-100%);
  transition:transform .5s ease;
}
.cf-submit:hover::before{transform:translateX(100%)}
.cf-submit:hover{opacity:.92;transform:scale(.98);box-shadow:0 4px 24px rgba(196,18,8,.2)}
.cf-note{
  font-family:var(--fm);font-size:9px;
  letter-spacing:.1em;text-transform:uppercase;
  color:rgba(237,230,218,.18);text-align:center;
  margin-top:14px;
}
.cf-accent{
  position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,transparent,rgba(196,18,8,.6) 30%,rgba(196,18,8,.6) 70%,transparent);
  border-radius:var(--r-xl) var(--r-xl) 0 0;
}
@media(max-width:600px){
  .cf-popup{padding:32px 24px 28px;width:min(500px,94vw)}
  .cf-row{grid-template-columns:1fr}
  .cf-field.full{grid-column:span 1}
}

/* ── FOOTER ── */
footer{
  position:relative;z-index:10;
  border-top:1px solid var(--border);
  padding:26px 60px;
  display:flex;align-items:center;justify-content:space-between;
}
footer>*{position:relative;z-index:1}
.ft-brand{font-family:var(--fm);font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--low)}
.ft-brand span{color:var(--low)}
.ft-copy{font-family:var(--fm);font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:rgba(237,230,218,.2)}
.ft-top{font-family:var(--fm);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--low);display:flex;align-items:center;gap:6px;transition:color .2s}
.ft-top:hover{color:var(--text)}


/* ── SECTION BACKGROUNDS: hero stays clear, stack onward gets darkened ── */
#stack,#works,#services,#about,#process,#contact{
  background:rgba(4,4,8,0.88);
  backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
}
.footer-wrap{background:rgba(4,4,8,0.88)}
/* ── HROB ICON ── */
.n-brand{display:flex;align-items:center;gap:9px;margin-right:24px;color:var(--text)}
.n-icon{width:18px;height:25px;color:#C41208;flex-shrink:0}
.n-label{font-family:var(--fd);font-weight:900;font-size:16px;letter-spacing:.1em;color:var(--text)}

/* ── HERO HROB WATERMARK ── */
.h-mark{
  position:absolute;right:6%;top:50%;transform:translateY(-50%);
  z-index:1;pointer-events:none;opacity:1;
  width:clamp(240px,28vw,420px);
}
.h-mark svg{width:100%;height:auto}

/* ── HERO PORTRAIT ── */
.h-portrait{
  position:absolute;right:12%;bottom:30%;
  z-index:2;pointer-events:none;
  width:clamp(240px,28vw,420px);
  display:flex;align-items:center;justify-content:center;
}
.h-portrait-frame{
  width:90%;aspect-ratio:3/4;
  border-radius:16px;overflow:hidden;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 20px 60px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.03);
  backdrop-filter:blur(2px);
}
.h-portrait-frame img{width:100%;height:100%;object-fit:cover;filter:grayscale(100%) contrast(1.1) brightness(.8)}

.footer-wrap{position:relative;z-index:10}
footer{position:relative;z-index:2}

/* ── REVEALS ── */
.rv,.rl,.rr{opacity:0;transition:opacity .8s ease,transform .8s ease}
.rv{transform:translateY(30px)}
.rl{transform:translateX(-34px)}
.rr{transform:translateX(34px)}
.rv.in,.rl.in,.rr.in{opacity:1;transform:none}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  nav{padding:9px 9px 9px 18px;max-width:95vw}
  .n-links{display:none}
  #hero,#works,#services,#about,#process,#contact{padding-left:24px;padding-right:24px}
  #hero{padding-bottom:52px}
  .h-name{max-width:100%}
  .h-foot{flex-direction:column;align-items:flex-start;gap:24px}
  .h-acts{margin-left:0;align-items:flex-start}
  .pg{grid-template-columns:repeat(4,1fr)}
  .pc--featured,.pc--wide,.pc--wide2{grid-column:span 4}
  .pc--tall,.pc--sq,.pc--sq2,.pc--half,.pc--third{grid-column:span 2}
  .h-portrait{display:none}
  .sv-wrap,.ab-wrap{grid-template-columns:1fr}
  .steps{grid-template-columns:1fr 1fr}
  footer{flex-direction:column;gap:12px;text-align:center}
}
</style>
</head>
<body>

<canvas id="bg"></canvas>
<div id="cur"></div>
<div id="cur-r"></div>

<canvas id="hero-neat" aria-hidden="true"></canvas>

<!-- NAV -->
<nav role="navigation">
  <a href="#hero" class="n-brand">
    <svg class="n-icon" viewBox="0 0 32 44" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
      <line x1="6" y1="25" x2="26" y2="25"/>
      <line x1="16" y1="25" x2="26" y2="41"/>
      <line x1="2" y1="41" x2="30" y2="41"/>
    </svg>
    <span class="n-label">HROB</span>
  </a>
  <ul class="n-links">
    <li><a href="#works">{{ __('nav.works') }}</a></li>
    <li><a href="#services">{{ __('nav.services') }}</a></li>
    <li><a href="#about">{{ __('nav.about') }}</a></li>
  </ul>
  <a href="#contact" class="n-cta">{{ __('nav.contact') }}</a>
</nav>

<div class="lang-toggle" data-active="{{ app()->getLocale() }}">
    <div class="lang-toggle-track"></div>
    <button type="button" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" data-locale="en">EN</button>
    <button type="button" class="lang-btn {{ app()->getLocale() === 'cs' ? 'active' : '' }}" data-locale="cs">CZ</button>
</div>

<div class="locale-overlay{{ session('locale_switched') ? ' active' : '' }}" id="locale-overlay">
  <div class="locale-overlay-inner">
    <svg class="locale-tomb" viewBox="0 0 32 44">
      <defs>
        <mask id="tomb-mask">
          <rect x="0" y="0" width="32" height="44" fill="black"/>
          <g stroke="white" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
            <line x1="6" y1="25" x2="26" y2="25"/>
            <line x1="16" y1="25" x2="26" y2="41"/>
            <line x1="2" y1="41" x2="30" y2="41"/>
          </g>
        </mask>
      </defs>
      <g class="tomb-stroke" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
        <line x1="6" y1="25" x2="26" y2="25"/>
        <line x1="16" y1="25" x2="26" y2="41"/>
        <line x1="2" y1="41" x2="30" y2="41"/>
      </g>
      <rect class="tomb-fill-rect" mask="url(#tomb-mask)" x="0" y="44" width="32" height="44" fill="var(--amber)"/>
    </svg>
  </div>
</div>
@if(session('locale_switched'))
<script>
requestAnimationFrame(()=>{
  setTimeout(()=>{
    document.getElementById('locale-overlay')?.classList.remove('active');
  },150);
});
</script>
@endif

<main>

<!-- HERO -->
<section id="hero">
  <div class="h-portrait">
    <div class="h-portrait-frame">
      <img src="{{ asset('robin-2.jpg') }}" alt="Robin Hrdlicka">
    </div>
  </div>
  <div class="h-mark" aria-hidden="true">
    <svg viewBox="0 0 32 44" fill="none" stroke-linecap="round" stroke-linejoin="round">


      <!-- Main stroke -->
      <g filter="url(#logo-distort)" stroke="#ffffff" stroke-width="0.6" opacity="0.9">
        <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
        <line x1="6" y1="25" x2="26" y2="25"/>
        <line x1="16" y1="25" x2="26" y2="41"/>
        <line x1="2" y1="41" x2="30" y2="41"/>
      </g>
    </svg>
  </div>
  <div class="h-name" aria-label="Robin Hrdlička">
    <span class="h-a">ROBIN</span>
    <span class="h-c">HRDLIČKA</span>
  </div>
  <div class="h-foot">
    <div class="h-copy rv">
      <h2>{{ __('hero.tagline') }}</h2>
      <p>{{ __('hero.desc') }}</p>
    </div>
    <div class="h-acts rv" style="transition-delay:.14s">
      <a href="#works" class="btn-p">{{ __('hero.cta1') }}</a>
      <a href="#contact" class="btn-g">{{ __('hero.cta2') }}</a>
    </div>
  </div>
  <div class="h-scroll" aria-hidden="true">
    <div class="s-line"></div><span>{{ __('hero.scroll') }}</span>
  </div>
</section>

<!-- STACK -->
<section id="stack">
  <div class="sk-inner">
    <div class="sk-group">
      <span class="sk-head">{{ __('stack.backend') }}</span>
      <div class="sk-list">
        <span class="sk-item sk-item--hi">Laravel</span>
        <span class="sk-item sk-item--hi">PHP 8.4</span>
        <span class="sk-item">REST APIs</span>
        <span class="sk-item">Queues &amp; Jobs</span>
      </div>
    </div>
    <div class="sk-group">
      <span class="sk-head">{{ __('stack.frontend') }}</span>
      <div class="sk-list">
        <span class="sk-item sk-item--hi">React</span>
        <span class="sk-item sk-item--hi">Livewire</span>
        <span class="sk-item">Tailwind CSS</span>
        <span class="sk-item">TypeScript</span>
      </div>
    </div>
    <div class="sk-group">
      <span class="sk-head">{{ __('stack.design') }}</span>
      <div class="sk-list">
        <span class="sk-item sk-item--hi">Figma</span>
        <span class="sk-item sk-item--hi">Adobe CC</span>
        <span class="sk-item">Motion Graphics</span>
        <span class="sk-item">Brand Systems</span>
      </div>
    </div>
    <div class="sk-group">
      <span class="sk-head">{{ __('stack.data') }}</span>
      <div class="sk-list">
        <span class="sk-item sk-item--hi">PostgreSQL</span>
        <span class="sk-item sk-item--hi">Redis</span>
        <span class="sk-item">Docker</span>
        <span class="sk-item">Git / CI</span>
      </div>
    </div>
  </div>
</section>

<!-- WORKS -->
<section id="works">
  <div class="works-top rv">
    <div>
      <div class="sl">{{ __('works.label') }}</div>
      <h2 class="works-hed">{!! __('works.heading') !!}</h2>
    </div>
    <p class="works-sub">{{ __('works.sub') }}</p>
  </div>

  <div class="filter-bar rv" style="transition-delay:.1s">
    <button class="f-btn active" data-filter="all">{{ __('works.filterAll') }}</button>
    <button class="f-btn" data-filter="web">{{ __('works.filterWeb') }}</button>
    <button class="f-btn" data-filter="graphic">{{ __('works.filterGraphic') }}</button>
    <button class="f-btn" data-filter="brand">{{ __('works.filterBrand') }}</button>
    <button class="f-btn" data-filter="ui">{{ __('works.filterUi') }}</button>
  </div>

  <div class="pg rv" style="transition-delay:.18s" id="portfolio-grid">

    <!-- 1: Featured web -->
    <div class="pc pc--featured" data-cat="web">
      <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&auto=format&fit=crop&q=75" alt="Archivos">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2024</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.archivos.cat') }}</span>
        <div class="pc-title">ARCHIVOS</div>
        <p class="pc-desc">{{ __('card.archivos.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">Laravel</span><span class="pc-tag">React</span><span class="pc-tag">AI/ML</span>
        </div>
      </div>
    </div>

    <!-- 2: Tall brand -->
    <div class="pc pc--tall" data-cat="brand">
      <img src="https://images.unsplash.com/photo-1634986666676-ec8fd927c23d?w=800&auto=format&fit=crop&q=75" alt="Vela Brand">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2024</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.vela.cat') }}</span>
        <div class="pc-title">VELA STUDIO</div>
        <p class="pc-desc">{{ __('card.vela.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">Logo</span><span class="pc-tag">Typography</span><span class="pc-tag">Print</span>
        </div>
      </div>
    </div>

    <!-- 3: Wide UI -->
    <div class="pc pc--wide" data-cat="ui">
      <img src="https://images.unsplash.com/photo-1487017159836-4e23ece2e4cf?w=1200&auto=format&fit=crop&q=75" alt="Reflex">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2023</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.reflex.cat') }}</span>
        <div class="pc-title">REFLEX ANALYTICS</div>
        <p class="pc-desc">{{ __('card.reflex.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">Figma</span><span class="pc-tag">Prototype</span><span class="pc-tag">Design System</span>
        </div>
      </div>
    </div>

    <!-- 4: Square graphic -->
    <div class="pc pc--sq" data-cat="graphic">
      <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?w=800&auto=format&fit=crop&q=75" alt="Poster Series">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2024</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.signal.cat') }}</span>
        <div class="pc-title">SIGNAL SERIES</div>
        <p class="pc-desc">{{ __('card.signal.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">Poster</span><span class="pc-tag">Print</span>
        </div>
      </div>
    </div>

    <!-- 5: Wide web -->
    <div class="pc pc--wide2" data-cat="web">
      <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&auto=format&fit=crop&q=75" alt="Kasida">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2024</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.kasida.cat') }}</span>
        <div class="pc-title">KASIDA SHOP</div>
        <p class="pc-desc">{{ __('card.kasida.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">Laravel</span><span class="pc-tag">Livewire</span><span class="pc-tag">Stripe</span>
        </div>
      </div>
    </div>

    <!-- 6: Gradient text card -->
    <div class="pc pc--sq2" data-cat="graphic">
      <div class="pc-grad" style="background:radial-gradient(ellipse 80% 80% at 30% 70%, rgba(232,64,32,.7) 0%, rgba(20,80,100,.5) 55%, transparent 80%),radial-gradient(ellipse 60% 60% at 80% 20%, rgba(26,80,96,.6) 0%, transparent 65%)"></div>
      <div class="pc-big-word">TYPE</div>
      <div class="pc-text-body">
        <span class="pc-cat">{{ __('card.type.cat') }}</span>
        <div class="pc-title">TYPE STUDIES</div>
        <p class="pc-desc">{{ __('card.type.desc') }}</p>
        <div class="pc-tags" style="opacity:1;margin-top:10px">
          <span class="pc-tag">Editorial</span><span class="pc-tag">Experimental</span>
        </div>
      </div>
    </div>

    <!-- 7: Half UI -->
    <div class="pc pc--half" data-cat="ui">
      <img src="https://images.unsplash.com/photo-1512314889357-e157c22f938d?w=900&auto=format&fit=crop&q=75" alt="Forma App">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2023</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.forma.cat') }}</span>
        <div class="pc-title">FORMA APP</div>
        <p class="pc-desc">{{ __('card.forma.desc') }}</p>
        <div class="pc-tags">
          <span class="pc-tag">iOS</span><span class="pc-tag">Motion</span>
        </div>
      </div>
    </div>

    <!-- 8: Third brand -->
    <div class="pc pc--third" data-cat="brand">
      <div class="pc-grad" style="background:radial-gradient(ellipse 70% 70% at 60% 40%, rgba(196,18,8,.65) 0%, rgba(200,64,24,.3) 50%, transparent 75%),radial-gradient(ellipse 50% 60% at 20% 80%, rgba(15,70,90,.6) 0%, transparent 65%)"></div>
      <div class="pc-big-word">MARK</div>
      <div class="pc-text-body">
        <span class="pc-cat">{{ __('card.comet.cat') }}</span>
        <div class="pc-title">COMET LABS</div>
        <p class="pc-desc">{{ __('card.comet.desc') }}</p>
        <div class="pc-tags" style="opacity:1;margin-top:10px">
          <span class="pc-tag">Logo</span><span class="pc-tag">Motion</span>
        </div>
      </div>
    </div>

    <!-- 9: Third graphic -->
    <div class="pc pc--third" data-cat="graphic">
      <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&auto=format&fit=crop&q=75" alt="Terrain">
      <div class="pc-ov"></div>
      <div class="pc-arrow">→</div>
      <span class="pc-yr">2023</span>
      <div class="pc-info">
        <span class="pc-cat">{{ __('card.terrain.cat') }}</span>
        <div class="pc-title">TERRAIN</div>
        <p class="pc-desc">{{ __('card.terrain.desc') }}</p>
      </div>
    </div>

    <!-- 10: Third web -->
    <div class="pc pc--third" data-cat="web">
      <div class="pc-grad" style="background:radial-gradient(ellipse 75% 75% at 40% 50%, rgba(20,80,100,.65) 0%, rgba(232,64,32,.25) 60%, transparent 78%),radial-gradient(ellipse 45% 50% at 85% 20%, rgba(232,80,24,.4) 0%, transparent 60%)"></div>
      <div class="pc-big-word">API</div>
      <div class="pc-text-body">
        <span class="pc-cat">{{ __('card.pulse.cat') }}</span>
        <div class="pc-title">PULSE API</div>
        <p class="pc-desc">{{ __('card.pulse.desc') }}</p>
        <div class="pc-tags" style="opacity:1;margin-top:10px">
          <span class="pc-tag">PHP</span><span class="pc-tag">Redis</span>
        </div>
      </div>
    </div>

  </div><!-- /pg -->
</section>

<section id="services">
  <div class="sv-wrap">
    <div class="sv-left rl">
      <div class="sl">{{ __('services.label') }}</div>
      <div class="sv-big">{!! __('services.heading') !!}</div>
    </div>
    <div class="sv-grid rr">
      <div class="sc"><span class="sc-icon">[ ]</span><div class="sc-name">{{ __('services.backends') }}</div><p class="sc-desc">{{ __('services.backendsDesc') }}</p></div>
      <div class="sc"><span class="sc-icon">◈</span><div class="sc-name">{{ __('services.frontends') }}</div><p class="sc-desc">{{ __('services.frontendsDesc') }}</p></div>
      <div class="sc"><span class="sc-icon">⬡</span><div class="sc-name">{{ __('services.visual') }}</div><p class="sc-desc">{{ __('services.visualDesc') }}</p></div>
      <div class="sc"><span class="sc-icon">✦</span><div class="sc-name">{{ __('services.consulting') }}</div><p class="sc-desc">{{ __('services.consultingDesc') }}</p></div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="ab-wrap">
    <div class="ab-img rl">
      <div class="ab-ghost" aria-hidden="true">ROBIN</div>
      <div class="ab-port">
          <img src="{{ asset('robin.jpeg') }}" alt="Robin Hrdlicka">
        <div class="ab-float">
          <span class="bdg bdg-h"><span class="bdg-d"></span><span>{{ __('about.badge1') }}</span></span>
          <span class="bdg"><span class="bdg-d"></span><span>{{ __('about.badge2') }}</span></span>
          <span class="bdg"><span class="bdg-d"></span><span>{{ __('about.badge3') }}</span></span>
        </div>
      </div>
    </div>
    <div class="rr">
      <div class="sl">{{ __('about.label') }}</div>
      <h2 class="ab-hed">{!! __('about.heading') !!}</h2>
      <p class="ab-copy">{{ __('about.copy1') }}</p>
      <p class="ab-copy" style="margin-bottom:0">{{ __('about.copy2') }}</p>
      <div class="ab-mantra">{!! __('about.mantra') !!}</div>
      <div class="bdg-row">
        <div class="bdg"><span class="bdg-d"></span><span>{{ __('about.bdg1') }}</span></div>
        <div class="bdg bdg-h"><span class="bdg-d"></span><span>{{ __('about.bdg2') }}</span></div>
        <div class="bdg"><span class="bdg-d"></span><span>{{ __('about.bdg3') }}</span></div>
        <div class="bdg"><span class="bdg-d"></span><span>{{ __('about.bdg4') }}</span></div>
      </div>
    </div>
  </div>
</section>

<!-- PROCESS -->
<section id="process">
  <div class="rv">
    <div class="sl">{{ __('process.label') }}</div>
    <h2 class="pr-hed">{!! __('process.heading') !!}</h2>
  </div>
  <div class="steps rv" style="transition-delay:.1s">
    <div class="st"><span class="st-n">01</span><div class="st-name">{{ __('process.s1name') }}</div><p class="st-desc">{{ __('process.s1desc') }}</p></div>
    <div class="st"><span class="st-n">02</span><div class="st-name">{{ __('process.s2name') }}</div><p class="st-desc">{{ __('process.s2desc') }}</p></div>
    <div class="st"><span class="st-n">03</span><div class="st-name">{{ __('process.s3name') }}</div><p class="st-desc">{{ __('process.s3desc') }}</p></div>
    <div class="st"><span class="st-n">04</span><div class="st-name">{{ __('process.s4name') }}</div><p class="st-desc">{{ __('process.s4desc') }}</p></div>
  </div>
</section>

<!-- CONTACT -->
<section id="contact">
  <div class="ct-ghost" aria-hidden="true">HELLO</div>
  <div class="ct-in">
    <div class="ct-eye">{{ __('contact.eye') }}</div>
    <h2 class="ct-hed">{!! __('contact.heading') !!}</h2>
    <p class="ct-sub">{{ __('contact.sub') }}</p>
    <a href="mailto:{{ $settings['contact_email'] ?? 'hello@robinhrdlicka.dev' }}" class="ct-email">{{ $settings['contact_email'] ?? 'hello@robinhrdlicka.dev' }}</a>
    <div class="cf-anchor" id="cf-anchor">
      <div class="cf-popup" id="cf-popup">
        <div class="cf-accent"></div>
        <button type="button" class="cf-close" id="cf-close" aria-label="Close">&times;</button>
        <div class="cf-head">{{ __('contact.formHead') }}</div>
        <p class="cf-subhead">{{ __('contact.formSub') }}</p>
        <form id="cf-form" action="{{ route('contact.store') }}" method="post">
          <div class="cf-row">
            <div class="cf-field">
              <label class="cf-label" for="cf-name">{{ __('contact.labelName') }}</label>
              <input class="cf-input" type="text" id="cf-name" name="name" placeholder="{{ __('contact.phName') }}" required>
            </div>
            <div class="cf-field">
              <label class="cf-label" for="cf-email">{{ __('contact.labelEmail') }}</label>
              <input class="cf-input" type="email" id="cf-email" name="email" placeholder="you@company.com" required>
            </div>
          </div>
          <div class="cf-row">
            <div class="cf-field full">
              <label class="cf-label" for="cf-subject">{{ __('contact.labelSubject') }}</label>
              <input class="cf-input" type="text" id="cf-subject" name="subject" placeholder="{{ __('contact.phSubject') }}">
            </div>
          </div>
          <div class="cf-row">
            <div class="cf-field full">
              <label class="cf-label" for="cf-msg">{{ __('contact.labelMsg') }}</label>
              <textarea class="cf-textarea" id="cf-msg" name="message" placeholder="{{ __('contact.phMsg') }}" required></textarea>
            </div>
          </div>
          <button type="submit" class="cf-submit" id="cf-submit">{{ __('contact.send') }}</button>
        </form>
        <div class="cf-status" id="cf-status" style="display:none;padding:12px 16px;margin-top:12px;border-radius:8px;font-family:var(--fm);font-size:12px;letter-spacing:.04em"></div>
        <div class="cf-note">{{ __('contact.note') }}</div>
      </div>
      <button type="button" class="btn-ct" id="open-cf">{{ __('contact.writeBtn') }}</button>
    </div>
    <div class="so-links">
      <a href="{{ $settings['github_url'] ?? '#' }}" target="_blank" rel="noopener">GitHub</a><a href="{{ $settings['linkedin_url'] ?? '#' }}" target="_blank" rel="noopener">LinkedIn</a><a href="{{ $settings['dribbble_url'] ?? '#' }}" target="_blank" rel="noopener">Dribbble</a><a href="{{ $settings['twitter_url'] ?? '#' }}" target="_blank" rel="noopener">Twitter / X</a>
    </div>
    <div class="ct-sign">{{ __('contact.sign') }}</div>
  </div>
</section>

</main>

<div class="footer-wrap">
  <footer>
    <div class="ft-brand">
      <svg style="width:14px;height:19px;margin-right:7px;vertical-align:middle" viewBox="0 0 32 44" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
        <line x1="6" y1="25" x2="26" y2="25"/>
        <line x1="16" y1="25" x2="26" y2="41"/>
        <line x1="2" y1="41" x2="30" y2="41"/>
      </svg>
      <span>HROB</span>&ensp;<span style="color:var(--low)">/ Robin Hrdlicka</span>
    </div>
    <div class="ft-copy">{{ __('footer.copy') }}</div>
    <a href="#hero" class="ft-top">{{ __('footer.top') }}</a>
  </footer>
</div>

<script>
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
  let last=0,time=0;
  function mainLoop(ts){
    requestAnimationFrame(mainLoop);
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

  /* Nav "Contact" link */
  const navContact=document.querySelector('.n-links a[href="#contact"]');
  if(navContact){
    navContact.addEventListener('click',e=>{
      e.preventDefault();
      scrollToContactAndOpen();
    });
  }

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
</script>

<script type="importmap">
{ "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.183.0/build/three.module.js" } }
</script>
<script type="module">
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
    for(int i=0;i<4;i++){v+=a*snoise(p*f);f*=2.0;a*=0.5;}
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

  const dpr = Math.min(window.devicePixelRatio, 2);

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
  window.addEventListener('scroll', updateLogoPos, { passive: true });

  /* Pause Three.js rendering when hero is not visible */
  let heroVisible = true;
  const heroEl = document.getElementById('hero');
  if (heroEl) {
    const heroObs = new IntersectionObserver(([entry]) => {
      heroVisible = entry.isIntersecting;
    }, { threshold: 0 });
    heroObs.observe(heroEl);
  }

  (function animate() {
    requestAnimationFrame(animate);
    if (!heroVisible) return;
    uniforms.uTime.value = (performance.now() - startTime) / 1000;
    const lerp = decayLerp();
    mouseCurrent.x += (mouseTarget.x - mouseCurrent.x) * lerp;
    mouseCurrent.y += (mouseTarget.y - mouseCurrent.y) * lerp;
    uniforms.uMouse.value.set(mouseCurrent.x, mouseCurrent.y);
    renderer.render(scene, camera);
  })();
} catch(e) {
  console.warn('Three.js shader failed to load', e);
}
</script>
</body>
</html>
