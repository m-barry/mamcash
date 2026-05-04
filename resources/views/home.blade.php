@extends('layouts.app')
@section('title', "Envoyer de l'argent en Afrique de l'Ouest")

@push('styles')
<style>
  .mam-banner-wrap {
    display: flex;
    align-items: center;
    background: #cff4fc;
    border: 1px solid #9eeaf9;
    color: #055160;
    padding: .45rem 0;
    border-radius: .375rem;
    overflow: hidden;
  }
  .mam-banner-icon {
    flex-shrink: 0;
    padding: 0 .85rem;
    font-size: 1.1rem;
    color: #0c6374;
  }
  .mam-banner-track {
    flex: 1;
    overflow: hidden;
  }
  .mam-banner-inner {
    display: inline-block;
    white-space: nowrap;
    font-weight: 700;
    font-size: .95rem;
    color: #055160;
    animation: mam-marquee 14s linear infinite;
  }
  .mam-banner-inner:hover { animation-play-state: paused; }
  .mam-banner-inner a { color: #055160; text-decoration: none; }
  @keyframes mam-marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }

  .home-switch-btn {
    width: 170px;
    min-height: 42px;
    font-size: .92rem;
    padding: .4rem .65rem;
  }

  .home-card {
    border-radius: .85rem;
  }

  .home-cta-sticky {
    position: sticky;
    bottom: 0;
    background: #fff;
    padding-top: .55rem;
    margin-top: .4rem;
  }

  @media (max-width: 768px) {
    .mam-banner-wrap {
      padding: .3rem 0;
      margin-bottom: .5rem !important;
    }

    .mam-banner-icon {
      padding: 0 .55rem;
      font-size: .95rem;
    }

    .mam-banner-inner {
      font-size: .85rem;
    }

    .home-switch {
      gap: .45rem !important;
      margin-bottom: .65rem !important;
    }

    .home-switch-btn {
      width: 48%;
      min-height: 40px;
      font-size: .85rem;
      padding: .35rem .45rem;
    }

    #steps {
      margin-bottom: .65rem !important;
    }

    .step-circle {
      width: 30px;
      height: 30px;
      font-size: .78rem;
    }

    .home-card {
      padding: .8rem !important;
      max-height: calc(100vh - 230px);
      overflow-y: auto;
    }

    .home-card h5 {
      margin-bottom: .5rem !important;
      font-size: .98rem;
    }

    .home-card .mb-4 {
      margin-bottom: .65rem !important;
    }

    .home-card .mb-3 {
      margin-bottom: .55rem !important;
    }

    .btn-currency,
    .btn-rc-currency {
      padding: .35rem .5rem;
      font-size: .8rem;
      min-height: 36px;
    }

    .home-cta-sticky .btn {
      min-height: 42px;
    }
  }
</style>
@endpush

@section('content')
<div class="row justify-content-center mt-2">
  <div class="col-xl-4 col-lg-5 col-md-6 col-sm-9">

    {{-- Bannière admin marquee --}}
    @if(isset($banner) && $banner['active'] && $banner['text'])
    <div class="mam-banner-wrap mb-3">
      <span class="mam-banner-icon"><i class="fas fa-bullhorn"></i></span>
      <div class="mam-banner-track">
        <span class="mam-banner-inner">
          @if($banner['image_url'])<img src="{{ $banner['image_url'] }}" height="62" class="me-2" style="vertical-align:middle;border-radius:4px" onerror="this.style.display='none'" alt="">@endif
          @if($banner['link'])<a href="{{ $banner['link'] }}" target="_blank" rel="noopener noreferrer">{{ $banner['text'] }}</a>@else{{ $banner['text'] }}@endif
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          @if($banner['image_url'])<img src="{{ $banner['image_url'] }}" height="62" class="me-2" style="vertical-align:middle;border-radius:4px" onerror="this.style.display='none'" alt="">@endif
          @if($banner['link'])<a href="{{ $banner['link'] }}" target="_blank" rel="noopener noreferrer">{{ $banner['text'] }}</a>@else{{ $banner['text'] }}@endif
        </span>
      </div>
    </div>
    @endif

    <div class="d-flex justify-content-center home-switch gap-3 mb-4">
      <button id="btnClassique" class="btn btn-primary home-switch-btn" onclick="showTab('classique')">
        <i class="fas fa-exchange-alt me-2"></i>Transfert classique
      </button>
      <button id="btnRecharge" class="btn btn-warning home-switch-btn" onclick="showTab('recharge')">
        <i class="fas fa-mobile-alt me-2"></i>Recharge Mobile
      </button>
    </div>
    <div class="d-flex justify-content-center align-items-center mb-4" id="steps">
      <div class="step-circle me-3 active" id="step0">0</div>
      <div class="step-circle me-3" id="step1">1</div>
      <div class="step-circle me-3" id="step2">2</div>
      <div class="step-circle" id="step3">3</div>
    </div>
    <div id="tab-classique" class="card shadow p-4 home-card">
      <h5 class="text-primary mb-3 text-center"><i class="fas fa-exchange-alt me-2"></i>Taux de change en GNF</h5>
      <div class="mb-3 text-center text-secondary">
        Taux de change : <span class="text-success fw-bold" id="rateDisplay">1 EUR = {{ number_format($rates['EUR'] ?? 10000, 0, ',', ' ') }} GNF</span>
      </div>
      <div class="d-flex justify-content-center gap-2 mb-4">
        <button type="button" class="btn btn-primary btn-currency active-currency" data-currency="EUR" data-rate="{{ $rates['EUR'] ?? 10000 }}" onclick="selectCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">EUR
        </button>
        <button type="button" class="btn btn-secondary btn-currency" data-currency="USD" data-rate="{{ $rates['USD'] ?? 9300 }}" onclick="selectCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" height="16" class="me-1">USD
        </button>
        <button type="button" class="btn btn-success btn-currency" data-currency="CAD" data-rate="{{ $rates['CAD'] ?? 6800 }}" onclick="selectCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg" height="16" class="me-1">CAD
        </button>
      </div>
      <div class="mb-3">
        <label class="form-label">Montant a envoyer (<span id="currLabel">EUR</span>)</label>
        <div class="input-group">
          <input type="number" id="amountSend" class="form-control form-control-lg" value="100" min="1" oninput="calculate()" placeholder="Montant">
          <span class="input-group-text">
            <img id="flagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="currSpan">EUR</strong>
          </span>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Montant a recevoir en GNF</label>
        <div class="input-group">
          <input type="text" id="amountReceive" class="form-control form-control-lg bg-white" readonly>
          <span class="input-group-text">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1"><strong>GNF</strong>
          </span>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Frais d''envoi</label>
        <div class="input-group">
          <input type="text" id="homeFees" class="form-control bg-white" readonly>
          <span class="input-group-text">
            <img id="flagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="homeFeesLabel">EUR</strong>
          </span>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-bold">Montant total</label>
        <div class="input-group">
          <input type="text" id="homeTotal" class="form-control form-control-lg bg-white fw-bold text-primary" readonly>
          <span class="input-group-text">
            <img id="flagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="homeTotalLabel">EUR</strong>
          </span>
        </div>
      </div>
      <div class="d-grid home-cta-sticky">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
          <i class="fas fa-paper-plane me-2"></i>Envoyer maintenant
        </a>
      </div>
    </div>
    <div id="tab-recharge" class="card shadow p-4 home-card" style="display:none">
      <h5 class="text-warning mb-3 text-center"><i class="fas fa-mobile-alt me-2"></i>Recharge Mobile Money</h5>

      {{-- Opérateurs --}}
      @php
        $opBranding = [
          'Orange Money' => ['bg'=>'#FF7900','text'=>'#fff','border'=>'#FF7900','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Orange_logo.svg/60px-Orange_logo.svg.png'],
          'MTN Mobile'   => ['bg'=>'#FFCC00','text'=>'#000','border'=>'#FFCC00','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/MTN_Logo.svg/60px-MTN_Logo.svg.png'],
          'Cellcom Money'=> ['bg'=>'#E31837','text'=>'#fff','border'=>'#E31837','logo'=>null],
        ];
      @endphp
      <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
        @forelse($operators as $op)
          @php
            $b   = $opBranding[$op->name] ?? ['bg'=>'#6c757d','text'=>'#fff','border'=>'#6c757d','logo'=>null];
            $dis = !$op->active;
            // Si inactif et show_coming_soon=false → on ne l'affiche pas
            if ($dis && !$op->show_coming_soon) continue;
          @endphp
          <div style="position:relative;display:inline-block">
            <button type="button"
              class="btn btn-sm rc-op-btn {{ $dis ? '' : '' }}"
              {{ $dis ? 'disabled' : 'onclick=selectRCOp(this)' }}
              style="border:2px solid {{ $b['border'] }};background:{{ $dis ? '#f0f0f0' : '#fff' }};border-radius:8px;padding:8px 14px;font-weight:bold;color:{{ $dis ? '#aaa' : $b['bg'] }};opacity:{{ $dis ? '0.6' : '1' }};cursor:{{ $dis ? 'not-allowed' : 'pointer' }};min-width:130px">
              @if($b['logo'])
                <img src="{{ $b['logo'] }}" height="18" class="me-1" style="vertical-align:middle" onerror="this.style.display='none'" alt="">
              @else
                <i class="fas fa-mobile-alt me-1"></i>
              @endif
              {{ $op->name }}
            </button>
            @if($dis)
              <span style="position:absolute;top:-8px;right:-6px;background:#6c757d;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;white-space:nowrap">Bientôt</span>
            @endif
          </div>
        @empty
          <p class="text-muted small">Aucun opérateur disponible pour le moment.</p>
        @endforelse
      </div>

      {{-- Taux de change --}}
      <div class="mb-2 text-center text-secondary small">
        Taux de change : <span class="text-success fw-bold" id="rcRateDisplay">1 EUR = {{ number_format($rates['EUR'] ?? 10000, 0, ',', ' ') }} GNF</span>
      </div>

      {{-- Sélecteur devise --}}
      <div class="d-flex justify-content-center gap-2 mb-4">
        <button type="button" class="btn btn-primary btn-rc-currency active-rc-currency" data-rcurrency="EUR" data-rrate="{{ $rates['EUR'] ?? 10000 }}" onclick="selectRCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="15" class="me-1">EUR
        </button>
        <button type="button" class="btn btn-secondary btn-rc-currency" data-rcurrency="USD" data-rrate="{{ $rates['USD'] ?? 9300 }}" onclick="selectRCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" height="15" class="me-1">USD
        </button>
        <button type="button" class="btn btn-success btn-rc-currency" data-rcurrency="CAD" data-rrate="{{ $rates['CAD'] ?? 6800 }}" onclick="selectRCurrency(this)">
          <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg" height="15" class="me-1">CAD
        </button>
      </div>

      <div class="mb-3">
        <label class="form-label">Montant à envoyer (<span id="rcCurrLabel">EUR</span>)</label>
        <div class="input-group">
          <input type="number" id="rcAmountSend" class="form-control form-control-lg" value="20" min="1" oninput="calculateRC()">
          <span class="input-group-text">
            <img id="rcFlagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="rcCurrSpan">EUR</strong>
          </span>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Montant à recevoir en GNF</label>
        <div class="input-group">
          <input type="text" id="rcAmountGNF" class="form-control form-control-lg bg-white" readonly>
          <span class="input-group-text">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1"><strong>GNF</strong>
          </span>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Frais d'envoi</label>
        <div class="input-group">
          <input type="text" id="rcFeesDisplay" class="form-control bg-white" readonly>
          <span class="input-group-text">
            <img id="rcFlagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="rcFeesLabel">EUR</strong>
          </span>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-bold">Montant total</label>
        <div class="input-group">
          <input type="text" id="rcTotalDisplay" class="form-control form-control-lg bg-white fw-bold text-warning" readonly>
          <span class="input-group-text">
            <img id="rcFlagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
            <strong id="rcTotalLabel">EUR</strong>
          </span>
        </div>
      </div>
      <div class="d-grid home-cta-sticky">
        <a href="{{ route('login') }}" class="btn btn-warning btn-lg">
          <i class="fas fa-mobile-alt me-2"></i>Recharger maintenant
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let currentRate = {{ $rates['EUR'] ?? 10000 }};
let currentCurrency = 'EUR';
const FLAGS = {
  EUR: 'https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg',
  USD: 'https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg',
  CAD: 'https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg'
};
function calculateFees(amount) {
  const tiers = @json($feeTiers);
  const limits = Object.keys(tiers).filter(k => !isNaN(k)).map(Number).sort((a, b) => a - b);
  for (const limit of limits) {
    if (amount <= limit) return tiers[String(limit)];
  }
  const base     = tiers['above_base']      ?? 20;
  const step     = tiers['above_step']      ?? 50;
  const inc      = tiers['above_increment'] ?? 2;
  const maxLimit = limits[limits.length - 1] ?? 500;
  return base + Math.ceil((amount - maxLimit) / step) * inc;
}
function selectCurrency(btn) {
  document.querySelectorAll('.btn-currency').forEach(b => b.classList.remove('active-currency'));
  btn.classList.add('active-currency');
  currentRate     = parseFloat(btn.dataset.rate);
  currentCurrency = btn.dataset.currency;
  document.getElementById('currLabel').textContent      = currentCurrency;
  document.getElementById('currSpan').textContent       = currentCurrency;
  document.getElementById('homeFeesLabel').textContent  = currentCurrency;
  document.getElementById('homeTotalLabel').textContent = currentCurrency;
  document.getElementById('flagSend').src  = FLAGS[currentCurrency];
  document.getElementById('flagFees').src  = FLAGS[currentCurrency];
  document.getElementById('flagTotal').src = FLAGS[currentCurrency];
  document.getElementById('rateDisplay').textContent = '1 ' + currentCurrency + ' = ' + currentRate.toLocaleString('fr-FR') + ' GNF';
  calculate();
}
const promoTransferDiscount = {{ isset($promotionTransfer) && $promotionTransfer && $promotionTransfer->active ? $promotionTransfer->discount : 0 }};
const promoRechargeDiscount = {{ isset($promotionRecharge) && $promotionRecharge && $promotionRecharge->active ? $promotionRecharge->discount : 0 }};

function calculate() {
  const amt  = parseFloat(document.getElementById('amountSend').value) || 0;
  let fees = calculateFees(amt);
  if (promoTransferDiscount > 0) fees = parseFloat((fees * (1 - promoTransferDiscount / 100)).toFixed(2));
  document.getElementById('amountReceive').value = amt > 0
    ? (amt * currentRate).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2})
    : '';
  document.getElementById('homeFees').value  = fees.toFixed(2);
  document.getElementById('homeTotal').value = (amt + fees).toFixed(2);
}
function showTab(tab) {
  document.getElementById('tab-classique').style.display = tab === 'classique' ? 'block' : 'none';
  document.getElementById('tab-recharge').style.display  = tab === 'recharge'  ? 'block' : 'none';
  document.getElementById('steps').style.display         = tab === 'classique' ? 'flex'  : 'none';
  if (tab === 'recharge') calculateRC();
}

// ── Recharge Mobile ──────────────────────────────────────────────────────
let rcRate = {{ $rates['EUR'] ?? 10000 }};
let rcCurrency = 'EUR';

function calculateRCFees(amount) {
  if      (amount <= 20)  return 1.5;
  else if (amount <= 40)  return 2;
  else if (amount <= 60)  return 3.5;
  else if (amount <= 80)  return 4;
  else if (amount <= 100) return 4.5;
  else if (amount <= 120) return 5;
  else if (amount <= 140) return 5.5;
  else if (amount <= 160) return 6;
  else if (amount <= 180) return 6.5;
  else                    return 7;
}
function selectRCurrency(btn) {
  document.querySelectorAll('.btn-rc-currency').forEach(b => b.classList.remove('active-rc-currency'));
  btn.classList.add('active-rc-currency');
  rcRate     = parseFloat(btn.dataset.rrate);
  rcCurrency = btn.dataset.rcurrency;
  const flag = FLAGS[rcCurrency];
  document.getElementById('rcCurrLabel').textContent    = rcCurrency;
  document.getElementById('rcCurrSpan').textContent     = rcCurrency;
  document.getElementById('rcFeesLabel').textContent    = rcCurrency;
  document.getElementById('rcTotalLabel').textContent   = rcCurrency;
  document.getElementById('rcFlagSend').src  = flag;
  document.getElementById('rcFlagFees').src  = flag;
  document.getElementById('rcFlagTotal').src = flag;
  document.getElementById('rcRateDisplay').textContent = '1 ' + rcCurrency + ' = ' + rcRate.toLocaleString('fr-FR') + ' GNF';
  calculateRC();
}
function selectRCOp(btn) {
  document.querySelectorAll('.rc-op-btn').forEach(b => b.style.opacity = '0.5');
  btn.style.opacity = '1';
  btn.style.boxShadow = '0 0 0 3px rgba(0,0,0,0.15)';
}
function calculateRC() {
  const amt  = parseFloat(document.getElementById('rcAmountSend').value) || 0;
  let fees = calculateRCFees(amt);
  if (promoRechargeDiscount > 0) fees = parseFloat((fees * (1 - promoRechargeDiscount / 100)).toFixed(2));
  document.getElementById('rcAmountGNF').value   = amt > 0
    ? (amt * rcRate).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2})
    : '';
  document.getElementById('rcFeesDisplay').value  = fees.toFixed(2);
  document.getElementById('rcTotalDisplay').value = (amt + fees).toFixed(2);
}
calculate();
calculateRC();
</script>
@endpush
