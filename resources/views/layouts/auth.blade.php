<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Authentification' }} — EbaTestManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f9fafb;
    --panel:#ffffff;
    --panel-line:#e5e7eb;
    --text:#111827;
    --muted:#6b7280;
    --card:#ffffff;
    --card-line:#e5e7eb;
    --card-shadow: 0 1px 3px rgba(0,0,0,0.06);
    --input-bg:#ffffff;
    --input-line:#d1d5db;
    --scene-bg: radial-gradient(ellipse 65% 55% at 50% 45%, rgba(139,0,0,0.14), transparent 72%), #f3f4f6;
    --grid-line: rgba(0,0,0,0.035);
    --ticket-line: rgba(139,0,0,0.16);
    --red:#8b0000;
    --red-light:#b23a3a;
    --red-ring:#CC0000;
    --green:#16a34a;
  }
  html.dark{
    --bg:#111827;
    --panel:#1f2937;
    --panel-line:#374151;
    --text:#f9fafb;
    --muted:#9ca3af;
    --card:#1f2937;
    --card-line:#374151;
    --card-shadow: 0 1px 3px rgba(0,0,0,0.3);
    --input-bg:#111827;
    --input-line:#374151;
    --scene-bg: radial-gradient(ellipse 65% 55% at 50% 45%, rgba(139,0,0,0.30), transparent 72%), #0f172a;
    --grid-line: rgba(255,255,255,0.035);
    --ticket-line: rgba(196,60,60,0.28);
    --red-light:#d16565;
    --green:#22c55e;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html,body{height:100%;}
  body{
    background:var(--bg);
    color:var(--text);
    font-family:'Inter', sans-serif;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    transition: background .25s ease, color .25s ease;
  }
  .brand-bar{
    height:4px; width:100%;
    background:linear-gradient(90deg, var(--red), #b23a3a, var(--red));
    flex-shrink:0;
  }
  .split{ flex:1; display:flex; min-height:0; }
  @media (prefers-reduced-motion: reduce){
    .orbit, .beam{ animation: none !important; }
  }

  .theme-toggle{
    position:absolute; top:40px; right:40px; z-index:10;
    width:38px; height:38px; border-radius:10px;
    border:1px solid var(--panel-line);
    background:var(--panel);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer;
  }
  .theme-toggle svg{ width:18px; height:18px; }
  .theme-toggle .sun{ color:#f59e0b; display:none; }
  .theme-toggle .moon{ color:#9ca3af; display:block; }
  html.dark .theme-toggle .sun{ display:block; }
  html.dark .theme-toggle .moon{ display:none; }

  .scene-side{
    position:relative;
    flex:1.15;
    display:flex; align-items:center; justify-content:center;
    background:var(--scene-bg);
    overflow:hidden;
    border-right:1px solid var(--panel-line);
  }
  .scene-side::before{
    content:"";
    position:absolute; inset:0;
    background-image:
      linear-gradient(var(--grid-line) 1px, transparent 1px),
      linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
    background-size: 46px 46px;
    mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 20%, transparent 75%);
  }

  .brand-mark{
    position:absolute; top:44px; left:48px;
    display:flex; align-items:center; gap:10px; z-index:5;
  }
  .brand-mark .glyph{
    width:60px; height:60px; border-radius:9px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:12px; color:var(--red);
    flex-shrink:0; overflow:hidden;
  }
  .brand-mark .glyph img{
    width:170%; height:170%; object-fit:contain; padding:6px;
  }
  .brand-mark .name{ font-size:10px; letter-spacing:0.14em; text-transform:uppercase; color:var(--muted); font-weight:600;}
  .brand-mark .name b{ color:var(--text); display:block; font-size:14px; letter-spacing:0; text-transform:none; font-weight:700; margin-top:2px;}

  .stage-wrap{ perspective:1400px; width:600px; height:600px; display:flex; align-items:center; justify-content:center; }
  .stage{
    position:relative; width:1px; height:1px;
    transform-style:preserve-3d;
    transition: transform .25s ease-out;
  }

  .orbit{
    position:absolute; top:0; left:0; width:1px; height:1px;
    transform-style:preserve-3d;
    animation: spin 26s linear infinite;
  }
  @keyframes spin{ from{ transform:rotateY(0deg);} to{ transform:rotateY(360deg);} }

  .hub{
    position:absolute; top:50%; left:50%;
    width:84px; height:84px; margin:-42px 0 0 -42px;
    border-radius:20px;
    background:linear-gradient(155deg, #a10000, var(--red) 60%, #6b0000);
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 0 46px rgba(139,0,0,0.45), 0 6px 20px rgba(0,0,0,0.25);
    z-index:3;
  }
  .hub span{ font-weight:800; color:#fff; font-size:20px; }
  .hub-badge{
    width:56px; height:56px; border-radius:14px;
    background:#ffffff;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden;
  }
  .hub-badge img{ width:100%; height:100%; object-fit:contain; padding:8px; }

  .ticket{
    position:absolute; top:50%; left:50%;
    width:172px; height:100px; margin:-50px 0 0 -86px;
    background:var(--card);
    border:1px solid var(--ticket-line);
    border-radius:10px;
    padding:14px 16px;
    box-shadow: var(--card-shadow);
    backface-visibility:hidden;
    transition: border-color .5s ease, box-shadow .5s ease;
  }
  .ticket .id{ font-size:11px; color:var(--muted); font-weight:600; letter-spacing:0.03em; }
  .ticket .label{ font-size:12.5px; font-weight:500; margin-top:7px; line-height:1.3; color:var(--text); }
  .ticket .chip{
    margin-top:12px; display:inline-flex; align-items:center; gap:5px;
    font-size:10px; font-weight:700; letter-spacing:0.03em; text-transform:uppercase;
    color:var(--muted);
  }
  .ticket .chip::before{ content:""; width:6px; height:6px; border-radius:50%; background:var(--muted); }

  .ticket.is-active{
    border-color: rgba(139,0,0,0.45);
    box-shadow: var(--card-shadow), 0 0 26px rgba(139,0,0,0.18);
  }
  .ticket.validated .chip{ color:var(--green); }
  .ticket.validated .chip::before{ background:var(--green); box-shadow:0 0 8px rgba(34,197,94,0.7); }
  .ticket.validated .chip span::after{ content:" ✓"; }

  .beam{
    position:absolute; top:6%; bottom:6%;
    width:150px; left:50%; margin-left:-75px;
    background:linear-gradient(90deg, transparent, rgba(139,0,0,0.14) 45%, rgba(139,0,0,0.05) 50%, rgba(139,0,0,0.14) 55%, transparent);
    filter:blur(2px);
    animation: sweep 6s ease-in-out infinite;
    pointer-events:none; z-index:2;
  }
  @keyframes sweep{
    0%,100%{ transform:translateX(-230px); opacity:0; }
    10%{ opacity:1; }
    50%{ transform:translateX(230px); opacity:1; }
    90%{ opacity:1; }
  }

  .caption{ position:absolute; bottom:52px; left:0; right:0; text-align:center; z-index:5; padding:0 40px; }
  .caption h2{ font-weight:700; font-size:20px; color:var(--text); }
  .caption p{ margin-top:8px; font-size:13px; color:var(--muted); max-width:360px; margin-left:auto; margin-right:auto; line-height:1.5; }

  .form-side{ flex:0.85; display:flex; align-items:center; justify-content:center; background:var(--panel); position:relative; overflow-y:auto; }
  .form-wrap{ width:100%; max-width:360px; padding:32px 40px; }

  .form-wrap .eyebrow{ font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--red); margin-bottom:10px; }
  .form-wrap h1{ font-weight:800; font-size:27px; line-height:1.15; margin-bottom:8px; color:var(--text); position:relative; padding-bottom:14px; }
  .form-wrap h1::after{ content:""; position:absolute; left:0; bottom:0; width:36px; height:3px; border-radius:2px; background:var(--red); }
  .form-wrap .sub{ font-size:13.5px; color:var(--muted); margin-bottom:28px; line-height:1.5; }

  /* ── Alertes (erreurs / statut) ── */
  .alert-box{ margin-bottom:20px; border-radius:10px; padding:12px 16px; font-size:13px; line-height:1.5; }
  .alert-box .alert-title{ display:flex; align-items:center; gap:8px; font-weight:600; }
  .alert-box svg{ flex-shrink:0; }
  .alert-error{ background: rgba(220,38,38,0.08); border:1px solid rgba(220,38,38,0.25); color:#dc2626; }
  html.dark .alert-error{ background: rgba(220,38,38,0.12); border-color: rgba(220,38,38,0.35); color:#f87171; }
  .alert-status{ background: rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.25); color:#15803d; }
  html.dark .alert-status{ background: rgba(22,163,74,0.12); border-color: rgba(22,163,74,0.35); color:#4ade80; }

  .field{ margin-bottom:18px; }
  .field label{ display:block; font-size:12px; font-weight:600; color:var(--text); margin-bottom:7px; }
  .field input{
    width:100%; padding:11px 14px; border-radius:9px;
    background:var(--input-bg); border:1px solid var(--input-line);
    color:var(--text); font-size:14px; font-family:'Inter',sans-serif;
    outline:none; transition: border-color .2s ease, box-shadow .2s ease;
  }
  .field input::placeholder{ color:var(--muted); }
  .field input:focus{ border-color: var(--red-ring); box-shadow: 0 0 0 3px rgba(204,0,0,0.15); }
  .field-error{ font-size:12px; color:#dc2626; margin-top:6px; }
  html.dark .field-error{ color:#f87171; }

  .row-between{ display:flex; align-items:center; justify-content:center; margin-top:16px; font-size:12.5px; }
  .row-between a{ color:var(--red); text-decoration:none; font-weight:500; }
  .row-between a:hover{ text-decoration:underline; }

  .btn-submit{
    width:100%; padding:12px; border:none; border-radius:9px; cursor:pointer;
    background:linear-gradient(135deg, #a10000, var(--red) 70%);
    color:#fff; font-weight:600; font-size:14.5px; font-family:'Inter',sans-serif;
    box-shadow: 0 8px 22px rgba(139,0,0,0.32);
    transition: box-shadow .15s ease, transform .15s ease;
  }
  .btn-submit:hover{ box-shadow: 0 10px 26px rgba(139,0,0,0.45); transform:translateY(-1px); }
  .btn-submit:active{ transform:translateY(0); }

  .foot-note{ margin-top:26px; text-align:center; font-size:12px; color:var(--muted); }

  @media (max-width: 880px){
    body{ flex-direction:column; overflow:auto; height:auto; min-height:100vh; }
    .scene-side{ min-height:40vh; border-right:none; border-bottom:1px solid var(--panel-line); }
    .stage-wrap{ width:340px; height:340px; transform:scale(0.8); }
    .form-side{ flex:1; padding:32px 0; }
    .theme-toggle{ top:20px; right:20px; }
    .caption{ bottom:28px; }
    .caption h2{ font-size:17px; }
  }

  @media (max-width: 560px){
    .scene-side{ min-height:32vh; }
    .stage-wrap{ width:260px; height:260px; transform:scale(0.72); }
    .brand-mark{ top:20px; left:20px; }
    .brand-mark .name{ font-size:9px; }
    .brand-mark .name b{ font-size:12.5px; }
    .caption{ display:none; }
    .form-wrap{ padding:24px 24px; max-width:100%; }
    .form-wrap h1{ font-size:23px; }
  }

  @media (max-width: 360px){
    .theme-toggle{ width:34px; height:34px; top:14px; right:14px; }
    .brand-mark .glyph{ width:34px; height:34px; }
  }
  @livewireStyles
</style>
</head>
<body>

  {{ $slot }}

  @livewireScripts
</body>
</html>
