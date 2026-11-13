const KEY='mybusiness.v1';
const defaults={
 settings:{ps:1602,abifin:1280,visio:1770,taxRate:21,personalTransfer:3000,taxReserve:6000,familyIncome:2000},
 movements:[],
 deadlines:[
  {date:'2026-12-28',label:'F24 piano 2 · rata 1',amount:619.87,category:'F24 personale'},
  {date:'2027-02-01',label:'F24 piano 1 · rata 7',amount:648.16,category:'F24 personale'},
  {date:'2027-03-31',label:'F24 piano 2 · rata 2',amount:628.79,category:'F24 personale'},
  {date:'2027-04-30',label:'F24 piano 1 · rata 8',amount:653.38,category:'F24 personale'},
  {date:'2027-06-30',label:'F24 piano 2 · rata 3',amount:634.20,category:'F24 personale'},
  {date:'2027-08-20',label:'F24 piano 1 · rata 9',amount:658.79,category:'F24 personale'},
  {date:'2027-09-30',label:'F24 piano 2 · rata 4',amount:639.67,category:'F24 personale'},
  {date:'2027-11-02',label:'F24 piano 1 · rata 10',amount:664.19,category:'F24 personale'},
  {date:'2027-12-31',label:'F24 piano 2 · rata 5',amount:645.14,category:'F24 personale'}
 ],
 familyExpenses:[
  {label:'Mutuo',amount:755},{label:'Assicurazione casa',amount:60.83},{label:'Internet',amount:22.99},
  {label:'PAC Riccardo',amount:100},{label:'Allarme',amount:5.58},{label:'Alimentari e casa',amount:433.33},
  {label:'Elettricità',amount:85},{label:'AIMAG',amount:35},{label:'Condominio',amount:190}
 ]
};
let state=load();
function load(){try{return {...structuredClone(defaults),...JSON.parse(localStorage.getItem(KEY)||'{}')};}catch{return structuredClone(defaults)}}
function save(){localStorage.setItem(KEY,JSON.stringify(state));}
const €=v=>new Intl.NumberFormat('it-IT',{style:'currency',currency:'EUR',maximumFractionDigits:0}).format(Number(v)||0);
const num=v=>Number(v)||0;
const dateIT=s=>new Date(s+'T12:00:00').toLocaleDateString('it-IT');
const app=document.querySelector('#app');
const pages={dashboard:'Panoramica',business:'Partita IVA',transactions:'Movimenti',family:'Famiglia',deadlines:'Scadenze',settings:'Impostazioni'};
let page='dashboard';
document.querySelector('#today').textContent=new Date().toLocaleDateString('it-IT',{weekday:'short',day:'numeric',month:'short',year:'numeric'});
document.querySelectorAll('.nav-item').forEach(b=>b.addEventListener('click',()=>go(b.dataset.page)));
function go(p){page=p;document.querySelectorAll('.nav-item').forEach(b=>b.classList.toggle('active',b.dataset.page===p));document.querySelector('#page-title').textContent=pages[p];render();}
function kpis(items){return `<div class="grid kpis">${items.map(x=>`<div class="card"><div class="kpi-label">${x[0]}</div><div class="kpi-value ${x[2]||''}">${x[1]}</div><div class="kpi-foot">${x[3]||''}</div></div>`).join('')}</div>`}
function table(headers,rows){return `<div class="table-wrap"><table class="table"><thead><tr>${headers.map(h=>`<th>${h}</th>`).join('')}</tr></thead><tbody>${rows.length?rows.join(''):`<tr><td colspan="${headers.length}" class="empty">Nessun dato presente</td></tr>`}</tbody></table></div>`}
function render(){const s=state.settings;const revenue=num(s.ps)+num(s.abifin)+num(s.visio);const tax=revenue*num(s.taxRate)/100;const famCost=state.familyExpenses.reduce((a,x)=>a+num(x.amount),0);
 if(page==='dashboard'){
  const mov=state.movements.slice().sort((a,b)=>b.date.localeCompare(a.date)).slice(0,6);
  app.innerHTML=kpis([['Ricavi professionali medi',€(revenue),'blue','PS Company · Abifin · Visiospot'],['Fondo Fisco & INPS',€(s.taxReserve),'','Saldo iniziale configurato'],['Accantonamento mensile',€(tax),'','Aliquota simulata '+s.taxRate+'%'],['Margine dopo prelievo',€(revenue-tax-num(s.personalTransfer)),revenue-tax-num(s.personalTransfer)>=0?'positive':'negative','Prima di altri costi e riserve']])+`
  <div class="grid two-col"><div class="card"><div class="section-head"><h2>Entrate professionali</h2><span class="pill">Mese tipo</span></div><div class="bar-list">${[['PS Company',s.ps],['Abifin',s.abifin],['Visiospot',s.visio]].map(([n,v])=>`<div class="bar-row"><span>${n}</span><div class="bar-track"><div class="bar-fill" style="width:${Math.max(1,num(v)/revenue*100)}%"></div></div><span class="bar-amount">${€(v)}</span></div>`).join('')}</div><div class="metric-line"><span>Ricavi annui teorici</span><b>${€(revenue*12)}</b></div><div class="metric-line"><span>Trasferimento personale</span><b>${€(s.personalTransfer)}</b></div></div>
  <div class="card"><div class="section-head"><h2>Bilancio familiare</h2><span class="pill">Mensile</span></div><div class="metric-line"><span>Entrate conto comune</span><b>${€(s.familyIncome)}</b></div><div class="metric-line"><span>Spese note</span><b>${€(famCost)}</b></div><div class="metric-line"><span>Margine teorico</span><b class="${num(s.familyIncome)-famCost>=0?'positive':'negative'}">${€(num(s.familyIncome)-famCost)}</b></div><p class="muted small">Escluse spese variabili per Riccardo e imprevisti.</p></div></div>
  <div class="card" style="margin-top:16px"><div class="section-head"><h2>Ultimi movimenti</h2><button class="btn" onclick="go('transactions')">Tutti i movimenti →</button></div>${table(['Data','Descrizione','Area','Importo'],mov.map(m=>`<tr><td>${dateIT(m.date)}</td><td>${esc(m.description)}</td><td><span class="pill">${areaName(m.area)}</span></td><td class="${m.type==='income'?'positive':'negative'}">${m.type==='income'?'+':'−'} ${€(m.amount)}</td></tr>`))}</div>
  <div class="notice">Nota: i valori iniziali sono quelli comunicati in chat. Le simulazioni non sono una liquidazione fiscale; verificare saldo e acconti con il commercialista.</div>`;
 }
 if(page==='business'){
  app.innerHTML=`<div class="card"><div class="section-head"><h2>Ricavi ricorrenti medi</h2><span class="pill">Modifica i valori</span></div><div class="field-grid">${field('PS Company','ps',s.ps)}${field('Abifin','abifin',s.abifin)}${field('Visiospot','visio',s.visio)}${field('Aliquota accantonamento %','taxRate',s.taxRate)}${field('Trasferimento personale €','personalTransfer',s.personalTransfer)}${field('Fondo fiscale attuale €','taxReserve',s.taxReserve)}</div><div class="actions"><button class="btn btn-primary" id="save-settings">Salva impostazioni</button></div></div>
  ${kpis([['Ricavi medi mensili',€(revenue),'blue'],['Accantonamento teorico',€(tax)],['Dopo 21% e trasferimento',€(revenue-tax-num(s.personalTransfer)),revenue-tax-num(s.personalTransfer)>=0?'positive':'negative'],['Ricavi annui teorici',€(revenue*12)]])}
  <div class="card"><h2>Regole operative</h2><div class="metric-line"><span>Accantonamento su incassi</span><b>${s.taxRate}% (provvisorio)</b></div><div class="metric-line"><span>Trasferimento personale</span><b>${€(s.personalTransfer)} / mese</b></div><div class="metric-line"><span>Arretrati Abifin da considerare nel 2026</span><b>8.960 €</b></div><div class="notice">Gli arretrati Abifin sono già sette mensilità da 1.280 €. Il possibile nuovo bando da circa 10.000 € è escluso finché non sarà certo. Non sommare due volte gli stessi compensi.</div></div>`;
  document.querySelector('#save-settings').onclick=()=>{['ps','abifin','visio','taxRate','personalTransfer','taxReserve'].forEach(k=>s[k]=num(document.querySelector('#f-'+k).value));save();render();toast('Impostazioni salvate');};
 }
 if(page==='transactions'){
  const rows=state.movements.slice().sort((a,b)=>b.date.localeCompare(a.date)).map((m,i)=>`<tr><td>${dateIT(m.date)}</td><td>${esc(m.description)}</td><td>${areaName(m.area)}</td><td>${m.type==='income'?'Entrata':'Uscita'}</td><td class="${m.type==='income'?'positive':'negative'}">${m.type==='income'?'+':'−'} ${€(m.amount)}</td><td><button class="btn" data-delete="${i}">Elimina</button></td></tr>`);
  const inc=state.movements.filter(m=>m.type==='income').reduce((a,m)=>a+num(m.amount),0),out=state.movements.filter(m=>m.type==='expense').reduce((a,m)=>a+num(m.amount),0);
  app.innerHTML=kpis([['Entrate registrate',€(inc),'positive'],['Uscite registrate',€(out),'negative'],['Saldo movimenti',€(inc-out),inc>=out?'positive':'negative'],['Movimenti',state.movements.length]])+`<div class="card"><div class="section-head"><h2>Registro movimenti</h2><button class="btn btn-primary" id="add-inline">＋ Aggiungi</button></div>${table(['Data','Descrizione','Area','Tipo','Importo',''],rows)}</div>`;
  document.querySelector('#add-inline').onclick=openDialog;
  document.querySelectorAll('[data-delete]').forEach(b=>b.onclick=()=>{state.movements.splice(Number(b.dataset.delete),1);save();render();});
 }
 if(page==='family'){
  app.innerHTML=`<div class="card"><div class="section-head"><h2>Entrate conto comune</h2></div>${field('Contributo complessivo mensile (€)','familyIncome',s.familyIncome)}<button class="btn btn-primary" id="save-family">Salva</button></div><div class="card" style="margin-top:16px"><div class="section-head"><h2>Spese familiari mensili</h2><span class="pill">Modificabili</span></div><div id="family-list">${state.familyExpenses.map((x,i)=>`<div class="metric-line"><span>${esc(x.label)}</span><span><input data-family="${i}" type="number" step=".01" value="${x.amount}" style="width:110px;padding:7px;border:1px solid var(--line);border-radius:7px"> €</span></div>`).join('')}</div><div class="actions"><button class="btn btn-primary" id="save-family-costs">Salva spese</button></div><div class="metric-line"><b>Totale spese note</b><b>${€(famCost)}</b></div><div class="metric-line"><b>Margine teorico</b><b class="${num(s.familyIncome)-famCost>=0?'positive':'negative'}">${€(num(s.familyIncome)-famCost)}</b></div></div>`;
  document.querySelector('#save-family').onclick=()=>{s.familyIncome=num(document.querySelector('#f-familyIncome').value);save();render();toast('Entrate salvate');};
  document.querySelector('#save-family-costs').onclick=()=>{document.querySelectorAll('[data-family]').forEach(el=>state.familyExpenses[Number(el.dataset.family)].amount=num(el.value));save();render();toast('Spese salvate');};
 }
 if(page==='deadlines'){
  const sorted=state.deadlines.slice().sort((a,b)=>a.date.localeCompare(b.date));
  app.innerHTML=`<div class="card"><div class="section-head"><h2>Scadenze e rate</h2><span class="pill amber">${sorted.length} registrate</span></div>${table(['Scadenza','Descrizione','Categoria','Importo'],sorted.map(d=>`<tr><td>${dateIT(d.date)}</td><td>${esc(d.label)}</td><td>${esc(d.category)}</td><td>${€(d.amount)}</td></tr>`))}<div class="notice">Le rate F24 personali qui elencate sono da sostenere con il trasferimento personale di 3.000 €, non con il fondo Fisco & INPS dell'attività. Le scadenze sono quelle ricostruite dai documenti condivisi; controllare gli F24 prima del pagamento.</div></div>`;
 }
 if(page==='settings'){
  app.innerHTML=`<div class="page-grid"><div class="card"><h2>Backup dei dati</h2><p class="muted small">Esporta un file JSON con movimenti, impostazioni, scadenze e budget. Conservalo in un luogo sicuro.</p><div class="actions"><button class="btn btn-primary" id="export">Esporta backup</button><label class="btn">Importa backup<input id="import" type="file" accept="application/json" hidden></label></div></div><div class="card"><h2>Privacy e archiviazione</h2><p class="muted small">I dati sono salvati in localStorage nel browser. Non vengono inviati a un server. Se cancelli i dati del browser, usa prima Esporta backup.</p><button class="btn" id="reset">Ripristina dati iniziali</button></div></div><div class="notice">Questa applicazione è una prima versione locale: non è collegata a Revolut, Generali, Fatture in Cloud o Agenzia delle Entrate. Inserisci solo dati che desideri conservare su questo dispositivo.</div>`;
  document.querySelector('#export').onclick=()=>{const blob=new Blob([JSON.stringify(state,null,2)],{type:'application/json'});download(blob,'my-business-backup.json');};
  document.querySelector('#import').onchange=e=>{const f=e.target.files[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{state=JSON.parse(r.result);save();render();toast('Backup importato');}catch{alert('File non valido');}};r.readAsText(f);};
  document.querySelector('#reset').onclick=()=>{if(confirm('Ripristinare tutti i dati iniziali? I movimenti registrati saranno eliminati.')){state=structuredClone(defaults);save();render();}};
 }
}
function field(label,key,value){return `<div class="field"><label for="f-${key}">${label}</label><input id="f-${key}" type="number" step="0.01" value="${value}"></div>`}
function areaName(a){return ({business:'Partita IVA',personal:'Personale',family:'Famiglia',tax:'Fisco & INPS'})[a]||a}
function esc(s){return String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function toast(msg){const n=document.createElement('div');n.textContent=msg;n.style='position:fixed;bottom:22px;right:22px;background:#172033;color:white;padding:12px 18px;border-radius:9px;z-index:20;font-size:12px';document.body.append(n);setTimeout(()=>n.remove(),2200);}
function openDialog(){const d=document.querySelector('#movement-dialog'),f=document.querySelector('#movement-form');f.reset();f.date.value=new Date().toISOString().slice(0,10);d.showModal();}
document.querySelector('#quick-add').onclick=openDialog;
document.querySelector('#movement-form').addEventListener('submit',e=>{e.preventDefault();const f=e.currentTarget;const m=Object.fromEntries(new FormData(f));m.amount=num(m.amount);state.movements.push(m);save();document.querySelector('#movement-dialog').close();render();toast('Movimento registrato');});
document.querySelector('#movement-form').querySelector('#save-movement').onclick=()=>{};
function download(blob,name){const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=name;a.click();URL.revokeObjectURL(a.href);}
window.go=go;render();
