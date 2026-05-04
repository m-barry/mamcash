@extends('layouts.app')
@section('title', 'Mon tableau de bord')

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
</style>
@endpush

@section('content')
<div class="row justify-content-center">
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

    {{-- Choix du mode --}}
    <div class="d-flex justify-content-center gap-3 mb-3">
      <button id="btnClassique" class="btn btn-primary px-3 py-2" onclick="showMode('classique')" style="width:190px">
        <i class="fas fa-exchange-alt me-2"></i>Transfert classique
      </button>
      <button id="btnRecharge" class="btn btn-warning px-3 py-2" onclick="showMode('recharge')" style="width:190px">
        <i class="fas fa-mobile-alt me-2"></i>Recharge Mobile
      </button>
    </div>

    {{-- ============================================================
         TRANSFERT CLASSIQUE
    ============================================================ --}}
    <div id="mode-classique">
      {{-- Indicateur étapes --}}
      <div class="d-flex justify-content-center align-items-center mb-4">
        <div class="step-circle me-3 active" id="sc0">0</div>
        <div class="step-circle me-3" id="sc1">1</div>
        <div class="step-circle me-3" id="sc2">2</div>
        <div class="step-circle" id="sc3">3</div>
      </div>

      {{-- ── Étape 0 : Convertisseur ── --}}
      <div id="step-0" class="card shadow p-4 mb-3">
        <h5 class="text-primary mb-4 text-center"><i class="fas fa-calculator me-2"></i>Taux de change en GNF</h5>
        <div class="mb-3 text-center text-secondary small">
          <span class="text-success fw-bold" id="rateDisplay">1 EUR = {{ number_format($rates['EUR'], 0, ',', ' ') }} GNF</span>
        </div>
        <div class="d-flex justify-content-center gap-2 mb-4">
          <button type="button" class="btn btn-primary btn-currency" data-currency="EUR" data-rate="{{ $rates['EUR'] }}" onclick="selectCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="15" class="me-1">EUR
          </button>
          <button type="button" class="btn btn-secondary btn-currency" data-currency="USD" data-rate="{{ $rates['USD'] }}" onclick="selectCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" height="15" class="me-1">USD
          </button>
          <button type="button" class="btn btn-success btn-currency" data-currency="CAD" data-rate="{{ $rates['CAD'] }}" onclick="selectCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg" height="15" class="me-1">CAD
          </button>
        </div>
        <div class="mb-3">
          <label class="form-label">Montant à envoyer (<span id="currLabel">EUR</span>)</label>
          <div class="input-group">
            <input type="number" id="amountSend" class="form-control form-control-lg" value="100" min="1" oninput="calculate()">
            <span class="input-group-text" id="currSpan">
              <img id="flagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="currSpanTxt">EUR</strong>
            </span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Montant à recevoir en (GNF)</label>
          <div class="input-group">
            <input type="text" id="amountReceive" class="form-control form-control-lg bg-white" readonly>
            <span class="input-group-text">
              <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1"><strong>GNF</strong>
            </span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Frais d'envoi</label>
          <div class="input-group">
            <input type="text" id="tcFees" class="form-control bg-white" readonly>
            <span class="input-group-text" id="tcFeesCurrSpan">
              <img id="flagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="tcFeesCurr">EUR</strong>
            </span>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-bold">Montant total</label>
          <div class="input-group">
            <input type="text" id="tcTotal" class="form-control form-control-lg bg-white fw-bold text-primary" readonly>
            <span class="input-group-text" id="tcTotalCurrSpan">
              <img id="flagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="tcTotalCurr">EUR</strong>
            </span>
          </div>
        </div>
        <div class="d-grid">
          <button class="btn btn-primary btn-lg" onclick="goToStep(1)">
            Continuer <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </div>
      </div>

      {{-- ── Étape 1 : Point de retrait + Bénéficiaire ── --}}
      <div id="step-1" class="card shadow p-4 mb-3" style="display:none">

        {{-- Sub-vue A : sélection principale --}}
        <div id="s1-main">
          <h5 class="text-center fw-bold mb-4">Sélection du Point de Retrait<br>et du Destinataire</h5>
          <div class="mb-4">
            @php
              $opBrandingS1 = [
                'Orange Money' => ['bg'=>'#FF7900','text'=>'#fff','border'=>'#FF7900','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Orange_logo.svg/60px-Orange_logo.svg.png'],
                'MTN Mobile'   => ['bg'=>'#FFCC00','text'=>'#000','border'=>'#FFCC00','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/MTN_Logo.svg/60px-MTN_Logo.svg.png'],
                'Cellcom Money'=> ['bg'=>'#E31837','text'=>'#fff','border'=>'#E31837','logo'=>null],
              ];
              $visibleS1 = $operators->filter(fn($o) => $o->active || $o->show_coming_soon);
              $singleS1  = $visibleS1->count() === 1;
            @endphp
            <label class="form-label text-secondary d-block text-center">Sélectionnez un point de retrait</label>
            {{-- Input caché lu par le JS (ne pas changer l'id) --}}
            <input type="hidden" id="s1PointRetrait" value="">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
              @foreach($operators as $op)
                @php
                  $b   = $opBrandingS1[$op->name] ?? ['bg'=>'#6c757d','text'=>'#fff','border'=>'#6c757d','logo'=>null];
                  $dis = !$op->active;
                  if ($dis && !$op->show_coming_soon) continue;
                @endphp
                <div style="position:relative;display:inline-block">
                  <button type="button"
                    class="s1-op-btn"
                    {{ $dis ? 'disabled' : '' }}
                    data-opname="{{ $op->name }}"
                    onclick="selectS1Operator(this)"
                    style="border:2px solid {{ $b['border'] }};background:{{ $dis ? '#f0f0f0' : '#fff' }};border-radius:8px;padding:8px 14px;font-weight:700;color:{{ $dis ? '#aaa' : $b['bg'] }};opacity:{{ $dis ? '0.55' : '1' }};cursor:{{ $dis ? 'not-allowed' : 'pointer' }};width:150px;height:48px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .2s">
                    @if($b['logo'])
                      <img src="{{ $b['logo'] }}" height="18" style="vertical-align:middle" onerror="this.style.display='none'" alt="">
                    @else
                      <i class="fas fa-mobile-alt"></i>
                    @endif
                    {{ $op->name }}
                  </button>
                  @if($dis)
                    <span style="position:absolute;top:-8px;right:-6px;background:#6c757d;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;white-space:nowrap">Bientôt</span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
          <div id="s1-main-error" class="alert alert-danger d-none mb-3">Veuillez sélectionner un point de retrait.</div>
          <div class="d-grid gap-2 mb-3">
            <button class="btn btn-outline-primary btn-lg" onclick="tryShowS1List()">
              Choisir un bénéficiaire
            </button>
            <button class="btn btn-primary btn-lg" onclick="tryShowS1Create()">
              <i class="fas fa-user-plus me-2"></i>Créer un nouveau bénéficiaire
            </button>
          </div>
          <div class="d-grid">
            <button class="btn btn-secondary" onclick="goToStep(0)">Retour</button>
          </div>
        </div>

        {{-- Sub-vue B : liste des bénéficiaires --}}
        <div id="s1-list" style="display:none">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-primary mb-0"><i class="fas fa-user-friends me-2"></i>Choisir un bénéficiaire</h5>
            <button class="btn btn-sm btn-success" onclick="showS1Tab('create')">
              <i class="fas fa-user-plus me-1"></i>Ajouter
            </button>
          </div>

          @if($contacts->isEmpty())
          {{-- État vide : aucun bénéficiaire --}}
          <div class="alert alert-warning d-flex align-items-center gap-3 py-3" id="emptyBenefAlert">
            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
            <div>
              <strong>Aucun bénéficiaire enregistré.</strong><br>
              <span class="text-muted small">Veuillez en créer un pour continuer.</span>
            </div>
          </div>
          <div class="d-grid mb-3">
            <button class="btn btn-primary" onclick="showS1Tab('create')">
              <i class="fas fa-user-plus me-2"></i>Créer un nouveau bénéficiaire
            </button>
          </div>
          @else
          <div class="table-responsive mb-3">
            <table class="table table-hover align-middle">
              <thead class="table-primary"><tr>
                <th style="width:30px"></th><th>Prénom</th><th>Nom</th><th>Pays</th><th>Téléphone</th>
              </tr></thead>
              <tbody id="benefTableBody">
                @foreach($contacts as $contact)
                <tr style="cursor:pointer" onclick="selectBeneficiary({{ $contact->id }}, '{{ addslashes($contact->firstname . ' ' . $contact->lastname) }}', '{{ addslashes($contact->telephone) }}')">
                  <td><input type="radio" name="benef" id="benef{{ $contact->id }}"></td>
                  <td>{{ $contact->firstname }}</td>
                  <td>{{ $contact->lastname }}</td>
                  <td>{{ $contact->country }}</td>
                  <td>{{ $contact->telephone }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div id="s1-list-error" class="alert alert-danger d-none mb-2">
            <i class="fas fa-exclamation-triangle me-1"></i>Veuillez sélectionner un bénéficiaire dans la liste.
          </div>
          @endif

          <div class="d-flex justify-content-between mt-1">
            <button class="btn btn-outline-secondary" onclick="showS1Tab('main')"><i class="fas fa-arrow-left me-1"></i>Retour</button>
            @if(!$contacts->isEmpty())
            <button class="btn btn-primary" onclick="validateS1AndContinue()">Continuer <i class="fas fa-arrow-right ms-1"></i></button>
            @endif
          </div>
        </div>

        {{-- Sub-vue C : créer un bénéficiaire --}}
        <div id="s1-create" style="display:none">
          <div class="btn btn-primary w-100 mb-3 py-2 text-start" style="pointer-events:none">
            <i class="fas fa-user-plus me-2"></i>Créer un nouveau bénéficiaire
          </div>
          <div id="s1CreateAlert" class="d-none mb-2"></div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Nom</label>
            <input type="text" id="s1Lastname" class="form-control" placeholder="Nom de famille">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Prénom</label>
            <input type="text" id="s1Firstname" class="form-control" placeholder="Prénom">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Pays de destination</label>
            <div class="form-control d-flex align-items-center" style="background:#f8f9fa;cursor:default">
              <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-2 rounded-sm" style="border:1px solid #dee2e6">
              Guinée
            </div>
            <input type="hidden" id="s1Country" value="GN">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Téléphone <span class="text-muted small">(format Orange Money Guinée)</span></label>
            <div class="input-group">
              <span class="input-group-text" style="min-width:90px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-1 rounded-sm" style="border:1px solid #dee2e6"> +224
              </span>
              <input type="tel" id="s1Phone" class="form-control" placeholder="6XX XXX XXX" maxlength="9"
                oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
            </div>
            <div class="form-text text-muted">Saisir 9 chiffres commençant par 6 (ex: 620123456)</div>
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Confirmer le téléphone</label>
            <div class="input-group">
              <span class="input-group-text" style="min-width:90px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-1 rounded-sm" style="border:1px solid #dee2e6"> +224
              </span>
              <input type="tel" id="s1PhoneConfirm" class="form-control" placeholder="6XX XXX XXX" maxlength="9"
                oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small text-secondary">Lien de parenté</label>
            <select id="s1Relationship" class="form-select">
              <option value="">Choisissez...</option>
              <option value="Père">Père</option>
              <option value="Mère">Mère</option>
              <option value="Frère">Frère</option>
              <option value="Sœur">Sœur</option>
              <option value="Fils">Fils</option>
              <option value="Fille">Fille</option>
              <option value="Époux/Épouse">Époux/Épouse</option>
              <option value="Ami(e)">Ami(e)</option>
              <option value="Autre">Autre</option>
            </select>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-success" onclick="createAndSelectBenef()">
              Créer et sélectionner
            </button>
            <button class="btn btn-outline-secondary" onclick="showS1Tab('main')">Retour</button>
          </div>
        </div>

      </div>

      {{-- ── Étape 2 : Paiement ── --}}
      <div id="step-2" class="card shadow p-4 mb-3" style="display:none">
        <h5 class="text-primary mb-4 text-center fw-bold"><i class="fas fa-file-invoice-dollar me-2"></i>Récapitulatif de la Transaction</h5>
        <div class="card border-0 bg-light p-3 mb-4">
          <table class="table table-borderless mb-0">
            <tr>
              <td class="text-muted">Montant à envoyer :</td>
              <td class="fw-bold text-end">
                <img id="s2FlagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="summaryAmount">—</span>
              </td>
            </tr>
            <tr>
              <td class="text-muted">Montant à recevoir :</td>
              <td class="fw-bold text-end">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1">
                <span id="summaryGNF">—</span> GNF
              </td>
            </tr>
            <tr>
              <td class="text-muted">Frais d'envoi :</td>
              <td class="fw-bold text-end">
                <img id="s2FlagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="summaryFees">—</span>
              </td>
            </tr>
            <tr class="border-top">
              <td class="fw-bold">Montant total :</td>
              <td class="fw-bold text-primary text-end fs-5">
                <img id="s2FlagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="summaryTotal">—</span>
              </td>
            </tr>
            <tr>
              <td class="text-muted">Point de retrait :</td>
              <td class="fw-bold text-end" id="summaryOperator">—</td>
            </tr>
            <tr>
              <td class="text-muted">Bénéficiaire :</td>
              <td class="fw-bold text-end" id="benefName">—</td>
            </tr>
            <tr>
              <td class="text-muted">Téléphone :</td>
              <td class="fw-bold text-end" id="summaryPhone">—</td>
            </tr>
          </table>
        </div>
        <form method="POST" action="{{ route('user.transaction.store') }}" id="paymentForm">
          @csrf
          <input type="hidden" name="amount" id="hiddenAmount">
          <input type="hidden" name="amount_sent" id="hiddenAmountSent">
          <input type="hidden" name="receiver" id="hiddenReceiver">
          <input type="hidden" name="receiver_number_phone" id="hiddenPhone">
          <input type="hidden" name="operator" id="hiddenOperator">
          <input type="hidden" name="currency" id="hiddenCurrency" value="EUR">
          <input type="hidden" name="type" value="TRANSFERT">
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)"><i class="fas fa-arrow-left me-1"></i>Retour</button>
            <button type="submit" class="btn btn-primary btn-lg px-4">
              <i class="fas fa-paper-plane me-2"></i>Procéder au paiement
            </button>
          </div>
        </form>
      </div>

      {{-- ── Étape 3 : Confirmation ── --}}
      <div id="step-3" class="card shadow p-4 mb-3 text-center" style="display:none">
        <div class="mb-3"><i class="fas fa-check-circle fa-4x text-success"></i></div>
        <h4 class="text-success mb-3">Transfert effectué !</h4>
        <p class="text-muted">Votre virement a été enregistré. Vous recevrez une confirmation par email.</p>
        <a href="{{ route('user.history') }}" class="btn btn-primary">
          <i class="fas fa-history me-2"></i>Voir l'historique
        </a>
      </div>
    </div>

    {{-- ============================================================
         RECHARGE MOBILE — Tunnel 4 étapes
    ============================================================ --}}
    <div id="mode-recharge" style="display:none">

      {{-- Étape RS1 : Sélection opérateur --}}
      <div id="rs1" class="card shadow p-4 mb-3">
        <h5 class="text-primary text-center mb-4"><i class="fas fa-mobile-alt me-2"></i>Sélectionnez un opérateur</h5>
        @php
          $opBranding = [
            'Orange Money' => ['bg'=>'#FF7900','text'=>'#fff','border'=>'#FF7900','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Orange_logo.svg/60px-Orange_logo.svg.png'],
            'MTN Mobile'   => ['bg'=>'#FFCC00','text'=>'#000','border'=>'#FFCC00','logo'=>'https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/MTN_Logo.svg/60px-MTN_Logo.svg.png'],
            'Cellcom Money'=> ['bg'=>'#E31837','text'=>'#fff','border'=>'#E31837','logo'=>null],
          ];
        @endphp
        <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
          @forelse($operators as $op)
            @php
              $b   = $opBranding[$op->name] ?? ['bg'=>'#6c757d','text'=>'#fff','border'=>'#6c757d','logo'=>null];
              $dis = !$op->active;
              if ($dis && !$op->show_coming_soon) continue;
            @endphp
            <div style="position:relative;display:inline-block">
              <button type="button"
                class="operator-btn"
                {{ $dis ? 'disabled' : '' }}
                data-opname="{{ $op->name }}"
                data-opcolor="{{ $b['bg'] }}"
                onclick="selectOperator(this)"
                style="border:2px solid {{ $b['border'] }};background:{{ $dis ? '#f0f0f0' : '#fff' }};border-radius:8px;padding:8px 14px;font-weight:700;color:{{ $dis ? '#aaa' : $b['bg'] }};opacity:{{ $dis ? '0.55' : '1' }};cursor:{{ $dis ? 'not-allowed' : 'pointer' }};width:150px;height:48px;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .2s">
                @if($b['logo'])
                  <img src="{{ $b['logo'] }}" height="18" style="vertical-align:middle" onerror="this.style.display='none'" alt="">
                @else
                  <i class="fas fa-mobile-alt"></i>
                @endif
                {{ $op->name }}
              </button>
              @if($dis)
                <span style="position:absolute;top:-8px;right:-6px;background:#6c757d;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;white-space:nowrap">Bientôt disponible</span>
              @endif
            </div>
          @empty
            <p class="text-muted">Aucun opérateur disponible pour le moment.</p>
          @endforelse
        </div>
        <div id="rs1-error" class="alert alert-danger d-none text-center">Veuillez sélectionner un opérateur.</div>
      </div>

      {{-- Étape RS2 : Montant + devise --}}
      <div id="rs2" class="card shadow p-4 mb-3" style="display:none">
        <h5 class="text-primary text-center mb-4">Montant à recharger</h5>
        <div class="text-center text-secondary small mb-3">
          Opérateur sélectionné : <strong id="rs-opLabel">—</strong>
        </div>
        {{-- Sélecteur devise --}}
        <div class="d-flex justify-content-center gap-2 mb-4">
          <button type="button" class="btn btn-primary btn-rc-currency active" data-rcurrency="EUR" data-rrate="{{ $rates['EUR'] }}" onclick="selectRCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="15" class="me-1">EUR
          </button>
          <button type="button" class="btn btn-secondary btn-rc-currency" data-rcurrency="USD" data-rrate="{{ $rates['USD'] }}" onclick="selectRCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" height="15" class="me-1">USD
          </button>
          <button type="button" class="btn btn-success btn-rc-currency" data-rcurrency="CAD" data-rrate="{{ $rates['CAD'] }}" onclick="selectRCurrency(this)">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg" height="15" class="me-1">CAD
          </button>
        </div>
        <div class="mb-3">
          <label class="form-label">Montant à envoyer (<span id="rcCurrLabel">EUR</span>)</label>
          <div class="input-group">
            <input type="number" id="rcAmount" class="form-control form-control-lg" min="1" value="0" oninput="calculateRecharge()">
            <span class="input-group-text">
              <img id="rcFlagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="rcCurrSpan">EUR</strong>
            </span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Montant à recevoir en (GNF)</label>
          <div class="input-group">
            <input type="text" id="rcGNF" class="form-control form-control-lg bg-white" readonly>
            <span class="input-group-text">
              <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1"><strong>GNF</strong>
            </span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Frais d'envoi</label>
          <div class="input-group">
            <input type="text" id="rcFees" class="form-control bg-white" readonly>
            <span class="input-group-text">
              <img id="rcFlagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="rcFeesCurr">EUR</strong>
            </span>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-bold">Montant total</label>
          <div class="input-group">
            <input type="text" id="rcTotal" class="form-control form-control-lg bg-white fw-bold text-primary" readonly>
            <span class="input-group-text">
              <img id="rcFlagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1"><strong id="rcTotalCurr">EUR</strong>
            </span>
          </div>
        </div>
        <div id="rs2-error" class="alert alert-danger d-none text-center">Veuillez saisir un montant valide (minimum 1).</div>
        <div class="d-flex justify-content-between mt-3">
          <button class="btn btn-outline-secondary" onclick="goToRS(1)"><i class="fas fa-arrow-left me-1"></i>Retour</button>
          <button class="btn btn-primary px-4" onclick="validateRS2()">Suivant <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
      </div>

      {{-- Étape RS3 : Bénéficiaire --}}
      <div id="rs3" class="card shadow p-4 mb-3" style="display:none">

        {{-- RS3 Main --}}
        <div id="rs3-main">
          <h5 class="text-primary text-center mb-4"><i class="fas fa-user-friends me-2"></i>Sélection du destinataire</h5>
          <p class="text-center text-muted small mb-4">Opérateur : <strong id="rs3-opLabel">—</strong></p>
          <div id="rs3-main-error" class="alert alert-danger d-none text-center mb-3"></div>
          <div class="d-grid mb-3">
            <button class="btn btn-primary btn-lg py-3" onclick="showRS3Tab('list')">
              <i class="fas fa-list me-2"></i>Choisir un bénéficiaire
            </button>
          </div>
          <div class="d-grid mb-4">
            <button class="btn btn-outline-success py-3" onclick="showRS3Tab('create')">
              <i class="fas fa-user-plus me-2"></i>Créer un nouveau bénéficiaire
            </button>
          </div>
          <div class="d-flex justify-content-start">
            <button class="btn btn-outline-secondary" onclick="goToRS(2)"><i class="fas fa-arrow-left me-1"></i>Retour</button>
          </div>
        </div>

        {{-- RS3 List --}}
        <div id="rs3-list" style="display:none">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0 text-primary"><i class="fas fa-list me-2"></i>Choisir un bénéficiaire</h6>
            <button class="btn btn-success btn-sm" onclick="showRS3Tab('create')"><i class="fas fa-plus me-1"></i>Ajouter</button>
          </div>
          <div id="rs3-list-error" class="alert alert-danger d-none mb-2">Veuillez sélectionner un bénéficiaire.</div>
          @if($contacts->isEmpty())
            <div class="alert alert-warning text-center" id="rsEmptyBenefAlert">
              Aucun bénéficiaire enregistré. Créez-en un d'abord.
            </div>
            <div class="d-flex justify-content-between mt-3">
              <button class="btn btn-outline-secondary" onclick="showRS3Tab('main')"><i class="fas fa-arrow-left me-1"></i>Retour</button>
            </div>
          @else
            <div class="table-responsive mb-3">
              <table class="table table-hover align-middle">
                <thead class="table-primary">
                  <tr><th style="width:30px"></th><th>Prénom</th><th>Nom</th><th>Pays</th><th>Téléphone</th></tr>
                </thead>
                <tbody id="rsBenefTableBody">
                  @foreach($contacts as $c)
                  <tr style="cursor:pointer" onclick="selectRSBenef({{ $c->id }}, '{{ addslashes($c->firstname) }} {{ addslashes($c->lastname) }}', '{{ $c->telephone }}')">
                    <td><input type="radio" name="rsbenef" id="rsbenef{{ $c->id }}"></td>
                    <td>{{ $c->firstname }}</td>
                    <td>{{ $c->lastname }}</td>
                    <td>{{ $c->country }}</td>
                    <td>{{ $c->telephone }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between">
              <button class="btn btn-outline-secondary" onclick="showRS3Tab('main')"><i class="fas fa-arrow-left me-1"></i>Retour</button>
              <button class="btn btn-primary" onclick="validateRS3AndContinue()">Continuer <i class="fas fa-arrow-right ms-1"></i></button>
            </div>
          @endif
        </div>

        {{-- RS3 Create --}}
        <div id="rs3-create" style="display:none">
          <div class="bg-primary text-white rounded p-3 mb-3 d-flex align-items-center">
            <i class="fas fa-user-plus me-2 fs-5"></i>
            <span class="fw-bold">Créer un nouveau bénéficiaire</span>
          </div>
          <div id="rs3CreateAlert" class="d-none mb-3"></div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Nom <span class="text-danger">*</span></label>
            <input type="text" id="rs3Lastname" class="form-control" placeholder="Nom de famille">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Prénom <span class="text-danger">*</span></label>
            <input type="text" id="rs3Firstname" class="form-control" placeholder="Prénom">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Pays de destination <span class="text-danger">*</span></label>
            <div class="form-control d-flex align-items-center" style="background:#f8f9fa;cursor:default">
              <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-2 rounded-sm" style="border:1px solid #dee2e6">
              Guinée
            </div>
            <input type="hidden" id="rs3Country" value="GN">
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Téléphone <span class="text-muted small">(format Orange Money Guinée)</span></label>
            <div class="input-group">
              <span class="input-group-text" style="min-width:90px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-1 rounded-sm" style="border:1px solid #dee2e6"> +224
              </span>
              <input type="tel" id="rs3Phone" class="form-control" placeholder="6XX XXX XXX" maxlength="9"
                oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
            </div>
            <div class="form-text text-muted">Saisir 9 chiffres commençant par 6 (ex: 620123456)</div>
          </div>
          <div class="mb-2">
            <label class="form-label small text-secondary">Confirmer le téléphone</label>
            <div class="input-group">
              <span class="input-group-text" style="min-width:90px">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="18" class="me-1 rounded-sm" style="border:1px solid #dee2e6"> +224
              </span>
              <input type="tel" id="rs3PhoneConfirm" class="form-control" placeholder="6XX XXX XXX" maxlength="9"
                oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small text-secondary">Lien de parenté</label>
            <select id="rs3Relationship" class="form-select">
              <option value="">Sélectionner...</option>
              <option>Père</option><option>Mère</option><option>Frère</option><option>Sœur</option>
              <option>Oncle</option><option>Tante</option><option>Cousin(e)</option>
              <option>Ami(e)</option><option>Autre</option>
            </select>
          </div>
          <div class="d-flex justify-content-between">
            <button class="btn btn-outline-secondary" onclick="showRS3Tab('main')"><i class="fas fa-arrow-left me-1"></i>Retour</button>
            <button class="btn btn-success" onclick="createAndSelectRSBenef()">
              <i class="fas fa-user-check me-1"></i>Créer et sélectionner
            </button>
          </div>
        </div>

      </div>

      {{-- Étape RS4 : Récapitulatif --}}
      <div id="rs4" class="card shadow p-4 mb-3" style="display:none">
        <h5 class="text-primary text-center mb-4 fw-bold"><i class="fas fa-file-invoice me-2"></i>Récapitulatif de la recharge</h5>
        <div class="card border-0 bg-light p-3 mb-4">
          <table class="table table-borderless mb-0">
            <tr>
              <td class="text-muted">Montant à envoyer :</td>
              <td class="fw-bold text-end">
                <img id="rs4FlagSend" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="rs4-amount">—</span>
              </td>
            </tr>
            <tr>
              <td class="text-muted">Recharge à recevoir :</td>
              <td class="fw-bold text-end">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/ed/Flag_of_Guinea.svg" height="16" class="me-1">
                <span id="rs4-gnf">—</span>
              </td>
            </tr>
            <tr>
              <td class="text-muted">Frais d'envoi :</td>
              <td class="fw-bold text-end">
                <img id="rs4FlagFees" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="rs4-fees">—</span>
              </td>
            </tr>
            <tr class="border-top">
              <td class="fw-bold">Montant total :</td>
              <td class="fw-bold text-primary text-end fs-5">
                <img id="rs4FlagTotal" src="https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg" height="16" class="me-1">
                <span id="rs4-total">—</span>
              </td>
            </tr>
            <tr>
              <td class="text-muted">Recharge Mobile :</td>
              <td class="fw-bold text-end" id="rs4-op">—</td>
            </tr>
            <tr>
              <td class="text-muted">Bénéficiaire :</td>
              <td class="fw-bold text-end" id="rs4-benef">—</td>
            </tr>
            <tr>
              <td class="text-muted">Téléphone :</td>
              <td class="fw-bold text-end" id="rs4-phone">—</td>
            </tr>
          </table>
        </div>
        <form method="POST" action="{{ route('user.transaction.store') }}" id="rechargeForm">
          @csrf
          <input type="hidden" name="type" value="RECHARGE">
          <input type="hidden" name="operator" id="rsHiddenOp">
          <input type="hidden" name="amount" id="rsHiddenAmount">
          <input type="hidden" name="currency" id="rsHiddenCurrency" value="EUR">
          <input type="hidden" name="receiver_number_phone" id="rsHiddenPhone">
          <input type="hidden" name="receiver" id="rsHiddenReceiver">
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="goToRS(3)"><i class="fas fa-arrow-left me-1"></i>Retour</button>
            <button type="submit" class="btn btn-success btn-lg px-4">
              <i class="fas fa-check me-2"></i>Confirmer
            </button>
          </div>
        </form>
      </div>

    </div>{{-- /mode-recharge --}}

  </div>
</div>
@endsection

@push('scripts')
<script>
const currencyFlags = {
  'EUR': 'https://upload.wikimedia.org/wikipedia/commons/b/b7/Flag_of_Europe.svg',
  'USD': 'https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg',
  'CAD': 'https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Canada.svg'
};

let currentRate = {{ $rates['EUR'] }};
let currentCurrency = 'EUR';
let selectedBenefId = null;
let selectedBenefName = '';
let selectedBenefPhone = '';

function setFlag(imgId, textId, currency) {
  const img = document.getElementById(imgId);
  const txt = document.getElementById(textId);
  if (img) img.src = currencyFlags[currency] || '';
  if (txt) txt.textContent = currency;
}

function selectCurrency(btn) {
  document.querySelectorAll('.btn-currency').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentRate     = parseFloat(btn.dataset.rate);
  currentCurrency = btn.dataset.currency;
  document.getElementById('currLabel').textContent = currentCurrency;
  document.getElementById('rateDisplay').textContent = '1 ' + currentCurrency + ' = ' + currentRate.toLocaleString('fr-FR') + ' GNF';
  setFlag('flagSend',  'currSpanTxt',  currentCurrency);
  setFlag('flagFees',  'tcFeesCurr',   currentCurrency);
  setFlag('flagTotal', 'tcTotalCurr',  currentCurrency);
  calculate();
}

const promoTransferDiscount = {{ isset($promotionTransfer) && $promotionTransfer && $promotionTransfer->active ? $promotionTransfer->discount : 0 }};
const promoRechargeDiscount = {{ isset($promotionRecharge) && $promotionRecharge && $promotionRecharge->active ? $promotionRecharge->discount : 0 }};

function calculateTransferFees(amount) {
  const tiers = @json($feeTiers);
  const limits = Object.keys(tiers).filter(k => !isNaN(k)).map(Number).sort((a, b) => a - b);
  let f = 0;
  let matched = false;
  for (const limit of limits) {
    if (amount <= limit) { f = tiers[String(limit)]; matched = true; break; }
  }
  if (!matched) {
    const base     = tiers['above_base']      ?? 20;
    const step     = tiers['above_step']      ?? 50;
    const inc      = tiers['above_increment'] ?? 2;
    const maxLimit = limits[limits.length - 1] ?? 500;
    f = base + Math.ceil((amount - maxLimit) / step) * inc;
  }
  if (promoTransferDiscount > 0) f = parseFloat((f * (1 - promoTransferDiscount / 100)).toFixed(2));
  return f;
}

function calculate() {
  const amt  = parseFloat(document.getElementById('amountSend').value) || 0;
  const fees = calculateTransferFees(amt);
  const gnf  = amt > 0 ? amt * currentRate : 0;
  document.getElementById('amountReceive').value = gnf.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
  document.getElementById('tcFees').value  = fees.toFixed(2);
  document.getElementById('tcTotal').value = (amt + fees).toFixed(2);
}

function goToStep(n) {
  for (let i = 0; i <= 3; i++) {
    const el = document.getElementById('step-' + i);
    if (el) el.style.display = i === n ? 'block' : 'none';
    const sc = document.getElementById('sc' + i);
    if (sc) sc.classList.toggle('active', i === n);
  }
  if (n === 1) {
    showS1Tab('main');
  }
  if (n === 2) {
    const amt  = parseFloat(document.getElementById('amountSend').value) || 0;
    const gnf  = document.getElementById('amountReceive').value;
    const fees = calculateTransferFees(amt);
    const flag = currencyFlags[currentCurrency] || '';
    document.getElementById('s2FlagSend').src  = flag;
    document.getElementById('s2FlagFees').src  = flag;
    document.getElementById('s2FlagTotal').src = flag;
    document.getElementById('summaryAmount').textContent   = amt.toFixed(2) + ' ' + currentCurrency;
    document.getElementById('summaryGNF').textContent      = gnf;
    document.getElementById('summaryFees').textContent     = fees.toFixed(2) + ' ' + currentCurrency;
    document.getElementById('summaryTotal').textContent    = (amt + fees).toFixed(2) + ' ' + currentCurrency;
    document.getElementById('summaryOperator').textContent = selectedWithdrawalPoint || '—';
    document.getElementById('benefName').textContent       = selectedBenefName || '—';
    document.getElementById('summaryPhone').textContent    = selectedBenefPhone || '—';
    document.getElementById('hiddenAmount').value      = amt;
    document.getElementById('hiddenAmountSent').value  = Math.round(amt * currentRate);
    document.getElementById('hiddenReceiver').value    = selectedBenefName;
    document.getElementById('hiddenPhone').value       = selectedBenefPhone;
    document.getElementById('hiddenOperator').value    = selectedWithdrawalPoint;
    document.getElementById('hiddenCurrency').value    = currentCurrency;
  }
}

function selectBeneficiary(id, name, phone) {
  selectedBenefId = id;
  selectedBenefName = name;
  selectedBenefPhone = phone;
  document.querySelectorAll('input[name="benef"]').forEach(r => r.checked = false);
  const rb = document.getElementById('benef' + id);
  if (rb) rb.checked = true;
}

let selectedWithdrawalPoint = '';

function selectS1Operator(btn) {
  // Réinitialiser tous les boutons
  document.querySelectorAll('.s1-op-btn').forEach(b => {
    const color = b.dataset.opname ? b.style.color : '#6c757d';
    b.style.background = '#fff';
    b.style.boxShadow  = 'none';
  });
  // Activer le bouton sélectionné
  btn.style.background = btn.style.color + '18'; // couleur légère en fond
  btn.style.boxShadow  = '0 0 0 3px ' + btn.style.color;
  // Mettre à jour l'input caché
  document.getElementById('s1PointRetrait').value = btn.dataset.opname;
  document.getElementById('s1-main-error').classList.add('d-none');
}

function showS1Tab(tab) {
  ['main','list','create'].forEach(t => {
    document.getElementById('s1-' + t).style.display = t === tab ? 'block' : 'none';
  });
  if (tab === 'main') {
    document.getElementById('s1-main-error').classList.add('d-none');
  }
}

function tryShowS1List() {
  const point = document.getElementById('s1PointRetrait').value;
  if (!point) {
    document.getElementById('s1-main-error').classList.remove('d-none');
    return;
  }
  document.getElementById('s1-main-error').classList.add('d-none');
  selectedWithdrawalPoint = point;
  showS1Tab('list');
}

function tryShowS1Create() {
  const point = document.getElementById('s1PointRetrait').value;
  if (!point) {
    document.getElementById('s1-main-error').classList.remove('d-none');
    return;
  }
  document.getElementById('s1-main-error').classList.add('d-none');
  selectedWithdrawalPoint = point;
  showS1Tab('create');
}

function validateS1AndContinue() {
  if (!selectedBenefId) {
    document.getElementById('s1-list-error').classList.remove('d-none');
    return;
  }
  document.getElementById('s1-list-error').classList.add('d-none');
  goToStep(2);
}

function createAndSelectBenef() {
  const lastname     = document.getElementById('s1Lastname').value.trim();
  const firstname    = document.getElementById('s1Firstname').value.trim();
  const country      = document.getElementById('s1Country').value;
  const phone        = document.getElementById('s1Phone').value.trim();
  const phoneConfirm = document.getElementById('s1PhoneConfirm').value.trim();
  const relationship = document.getElementById('s1Relationship').value;
  const alertEl      = document.getElementById('s1CreateAlert');

  if (!firstname || !lastname || !country || !phone) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Veuillez remplir tous les champs obligatoires.';
    return;
  }
  // Validation format Orange Money Guinée: 9 chiffres commençant par 6
  if (!/^6\d{8}$/.test(phone)) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Numéro invalide. Saisissez 9 chiffres commençant par 6 (ex: 620123456).';
    return;
  }
  if (phone !== phoneConfirm) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Les numéros de téléphone ne correspondent pas.';
    return;
  }

  const telephone = '+224' + phone;

  fetch('{{ route("user.contacts.quick-store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
    body: JSON.stringify({ firstname, lastname, telephone, country, relationship }),
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) throw new Error('Erreur serveur');
    const c = data.contact;
    addContactRowToTables(c);
    selectBeneficiary(c.id, c.firstname + ' ' + c.lastname, c.telephone);
    alertEl.className = 'd-none';
    ['s1Lastname','s1Firstname','s1Phone','s1PhoneConfirm'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('s1Country').value = 'GN';
    document.getElementById('s1Relationship').value = '';
    showS1Tab('list');
  })
  .catch(() => {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Une erreur est survenue. Veuillez réessayer.';
  });
}

function showMode(mode) {
  document.getElementById('mode-classique').style.display = mode === 'classique' ? 'block' : 'none';
  document.getElementById('mode-recharge').style.display  = mode === 'recharge'  ? 'block' : 'none';
  if (mode === 'recharge') goToRS(1);
}

// ============================================================
// RECHARGE MOBILE
// ============================================================
let rcRate      = {{ $rates['EUR'] }};
let rcCurrency  = 'EUR';
let rsOperator  = '';
let rsSelectedPhone = '';
let rsSelectedName  = '';
let rsCurrentTab    = '';

function selectRCurrency(btn) {
  document.querySelectorAll('.btn-rc-currency').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  rcRate     = parseFloat(btn.dataset.rrate);
  rcCurrency = btn.dataset.rcurrency;
  document.getElementById('rcCurrLabel').textContent = rcCurrency;
  setFlag('rcFlagSend',  'rcCurrSpan',  rcCurrency);
  setFlag('rcFlagFees',  'rcFeesCurr',  rcCurrency);
  setFlag('rcFlagTotal', 'rcTotalCurr', rcCurrency);
  calculateRecharge();
}

function calculateRechargeFees(amount) {
  let f = 0;
  if      (amount <= 20)  f = 1.5;
  else if (amount <= 40)  f = 2;
  else if (amount <= 60)  f = 3.5;
  else if (amount <= 80)  f = 4;
  else if (amount <= 100) f = 4.5;
  else if (amount <= 120) f = 5;
  else if (amount <= 140) f = 5.5;
  else if (amount <= 160) f = 6;
  else if (amount <= 180) f = 6.5;
  else                    f = 7;
  if (promoRechargeDiscount > 0) f = parseFloat((f * (1 - promoRechargeDiscount / 100)).toFixed(2));
  return f;
}

function calculateRecharge() {
  const amt  = parseFloat(document.getElementById('rcAmount').value) || 0;
  const fees = calculateRechargeFees(amt);
  const gnf  = amt > 0 ? amt * rcRate : 0;
  document.getElementById('rcGNF').value   = gnf.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
  document.getElementById('rcFees').value  = fees.toFixed(2);
  document.getElementById('rcTotal').value = (amt + fees).toFixed(2);
}

function selectOperator(btn) {
  rsOperator = btn.dataset.opname;
  // Visual feedback
  document.querySelectorAll('.operator-btn').forEach(b => b.style.opacity = '0.5');
  btn.style.opacity = '1';
  btn.style.transform = 'scale(1.05)';
  document.getElementById('rs1-error').classList.add('d-none');
  // Short delay for visual then go next
  setTimeout(() => goToRS(2), 250);
}

function goToRS(step) {
  for (let i = 1; i <= 4; i++) {
    const el = document.getElementById('rs' + i);
    if (el) el.style.display = i === step ? 'block' : 'none';
  }
  if (step === 2) {
    document.getElementById('rs-opLabel').textContent = rsOperator;
    calculateRecharge();
  }
  if (step === 3) {
    rsSelectedBenefId = null;
    rsSelectedName    = '';
    rsSelectedPhone   = '';
    const rsOpLbl = document.getElementById('rs3-opLabel');
    if (rsOpLbl) rsOpLbl.textContent = rsOperator;
    showRS3Tab('main');
  }
  if (step === 4) {
    const amt  = parseFloat(document.getElementById('rcAmount').value) || 0;
    const fees = calculateRechargeFees(amt);
    const flag = currencyFlags[rcCurrency] || '';
    document.getElementById('rs4FlagSend').src  = flag;
    document.getElementById('rs4FlagFees').src  = flag;
    document.getElementById('rs4FlagTotal').src = flag;
    document.getElementById('rs4-amount').textContent = amt.toFixed(2) + ' ' + rcCurrency;
    document.getElementById('rs4-gnf').textContent    = (amt * rcRate).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' GNF';
    document.getElementById('rs4-fees').textContent   = fees.toFixed(2) + ' ' + rcCurrency;
    document.getElementById('rs4-total').textContent  = (amt + fees).toFixed(2) + ' ' + rcCurrency;
    document.getElementById('rs4-op').textContent     = rsOperator;
    document.getElementById('rs4-benef').textContent  = rsSelectedName || '—';
    document.getElementById('rs4-phone').textContent  = rsSelectedPhone || '—';
    document.getElementById('rsHiddenOp').value       = rsOperator;
    document.getElementById('rsHiddenAmount').value   = amt;
    document.getElementById('rsHiddenCurrency').value = rcCurrency;
    document.getElementById('rsHiddenPhone').value    = rsSelectedPhone;
    document.getElementById('rsHiddenReceiver').value = rsSelectedName;
  }
}

function validateRS2() {
  const amt = parseFloat(document.getElementById('rcAmount').value) || 0;
  if (amt < 1) {
    document.getElementById('rs2-error').classList.remove('d-none');
    return;
  }
  document.getElementById('rs2-error').classList.add('d-none');
  goToRS(3);
}

// ============================================================
// RS3 — Logique bénéficiaire (même logique que Step 1 transfert)
// ============================================================
let rsSelectedBenefId = null;

function showRS3Tab(tab) {
  ['main','list','create'].forEach(t => {
    const el = document.getElementById('rs3-' + t);
    if (el) el.style.display = t === tab ? 'block' : 'none';
  });
}

function selectRSBenef(id, name, phone) {
  rsSelectedBenefId = id;
  rsSelectedName    = name;
  rsSelectedPhone   = phone;
  document.querySelectorAll('input[name="rsbenef"]').forEach(r => r.checked = false);
  const rb = document.getElementById('rsbenef' + id);
  if (rb) rb.checked = true;
}

function validateRS3AndContinue() {
  if (!rsSelectedBenefId) {
    document.getElementById('rs3-list-error').classList.remove('d-none');
    return;
  }
  document.getElementById('rs3-list-error').classList.add('d-none');
  goToRS(4);
}

function createAndSelectRSBenef() {
  const lastname     = document.getElementById('rs3Lastname').value.trim();
  const firstname    = document.getElementById('rs3Firstname').value.trim();
  const country      = document.getElementById('rs3Country').value;
  const phone        = document.getElementById('rs3Phone').value.trim();
  const phoneConfirm = document.getElementById('rs3PhoneConfirm').value.trim();
  const relationship = document.getElementById('rs3Relationship').value;
  const alertEl      = document.getElementById('rs3CreateAlert');

  if (!firstname || !lastname || !country || !phone) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Veuillez remplir tous les champs obligatoires.';
    return;
  }
  if (!/^6\d{8}$/.test(phone)) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Numéro invalide. Saisissez 9 chiffres commençant par 6 (ex: 620123456).';
    return;
  }
  if (phone !== phoneConfirm) {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Les numéros de téléphone ne correspondent pas.';
    return;
  }

  const telephone = '+224' + phone;

  fetch('{{ route("user.contacts.quick-store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
    body: JSON.stringify({ firstname, lastname, telephone, country, relationship }),
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) throw new Error('Erreur serveur');
    const c = data.contact;
    addContactRowToTables(c);
    selectRSBenef(c.id, c.firstname + ' ' + c.lastname, c.telephone);
    alertEl.className = 'd-none';
    ['rs3Lastname','rs3Firstname','rs3Phone','rs3PhoneConfirm'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('rs3Country').value = 'GN';
    document.getElementById('rs3Relationship').value = '';
    showRS3Tab('list');
  })
  .catch(() => {
    alertEl.className = 'alert alert-danger';
    alertEl.textContent = 'Une erreur est survenue. Veuillez réessayer.';
  });
}

// Fonction partagée : ajoute le contact dans les 2 tableaux (transfert + recharge)
function addContactRowToTables(c) {
  // ── Tableau Transfert classique (s1-list) ──
  if (!document.getElementById('benefTableBody')) {
    const emptyAlert = document.getElementById('emptyBenefAlert');
    if (emptyAlert) emptyAlert.style.display = 'none';
    const s1list = document.getElementById('s1-list');
    const tableHtml = `<div class="table-responsive mb-3" id="dynamicBenefTable">
      <table class="table table-hover align-middle">
        <thead class="table-primary"><tr>
          <th style="width:30px"></th><th>Prénom</th><th>Nom</th><th>Pays</th><th>Téléphone</th>
        </tr></thead>
        <tbody id="benefTableBody"></tbody>
      </table>
    </div>`;
    s1list.insertAdjacentHTML('afterbegin', tableHtml);
    const navDiv = s1list.querySelector('.d-flex.justify-content-between');
    if (navDiv && !navDiv.querySelector('.btn-primary')) {
      navDiv.insertAdjacentHTML('beforeend', '<button class="btn btn-primary" onclick="validateS1AndContinue()">Continuer <i class="fas fa-arrow-right ms-1"></i></button>');
    }
    const createBtnGrid = s1list.querySelector('.d-grid.mb-3');
    if (createBtnGrid) createBtnGrid.style.display = 'none';
  }
  const tbody1 = document.getElementById('benefTableBody');
  if (tbody1 && !tbody1.querySelector(`#benef${c.id}`)) {
    const tr = document.createElement('tr');
    tr.style.cursor = 'pointer';
    tr.setAttribute('onclick', `selectBeneficiary(${c.id}, '${c.firstname} ${c.lastname}', '${c.telephone}')`);
    tr.innerHTML = `<td><input type="radio" name="benef" id="benef${c.id}"></td>
      <td>${c.firstname}</td><td>${c.lastname}</td><td>${c.country}</td><td>${c.telephone}</td>`;
    tbody1.appendChild(tr);
  }

  // ── Tableau Recharge Mobile (rs3-list) ──
  if (!document.getElementById('rsBenefTableBody')) {
    const emptyAlert = document.getElementById('rsEmptyBenefAlert');
    if (emptyAlert) emptyAlert.style.display = 'none';
    const rs3list = document.getElementById('rs3-list');
    const tableHtml = `<div class="table-responsive mb-3" id="dynamicRsBenefTable">
      <table class="table table-hover align-middle">
        <thead class="table-primary"><tr>
          <th style="width:30px"></th><th>Prénom</th><th>Nom</th><th>Pays</th><th>Téléphone</th>
        </tr></thead>
        <tbody id="rsBenefTableBody"></tbody>
      </table>
    </div>`;
    const navDiv = rs3list.querySelector('.d-flex.justify-content-between');
    navDiv.insertAdjacentHTML('beforebegin', tableHtml);
    navDiv.insertAdjacentHTML('beforeend', '<button class="btn btn-primary" onclick="validateRS3AndContinue()">Continuer <i class="fas fa-arrow-right ms-1"></i></button>');
  }
  const tbody2 = document.getElementById('rsBenefTableBody');
  if (tbody2 && !tbody2.querySelector(`#rsbenef${c.id}`)) {
    const tr = document.createElement('tr');
    tr.style.cursor = 'pointer';
    tr.setAttribute('onclick', `selectRSBenef(${c.id}, '${c.firstname} ${c.lastname}', '${c.telephone}')`);
    tr.innerHTML = `<td><input type="radio" name="rsbenef" id="rsbenef${c.id}"></td>
      <td>${c.firstname}</td><td>${c.lastname}</td><td>${c.country}</td><td>${c.telephone}</td>`;
    tbody2.appendChild(tr);
  }
}

calculate();
calculateRecharge();
</script>
@endpush
