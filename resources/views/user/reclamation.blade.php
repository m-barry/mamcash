@extends('layouts.app')
@section('title', 'Nous contacter')

@section('content')
<div class="row justify-content-center py-4">
  <div class="col-md-8 col-lg-6">

    {{-- Formulaire de sélection --}}
    <div class="card shadow-sm">
      <div class="card-header text-center bg-white">
        <h4 class="mb-0">Formulaire de Réclamation</h4>
      </div>
      <div class="card-body">

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{-- Étape 1 : email + sélection --}}
        <div id="step1">
          <div class="mb-4">
            <label for="emailInput" class="form-label fw-bold">Adresse e-mail</label>
            <input type="email" class="form-control" id="emailInput"
                   placeholder="Entrez votre adresse e-mail"
                   value="{{ auth()->user()->email ?? '' }}">
            <div class="form-text text-warning">
              Nous ne partagerons jamais votre email avec quelqu'un d'autre.
            </div>
          </div>

          <div class="mb-4">
            <label for="reclamationSelect" class="form-label fw-bold">Sélectionnez une réclamation</label>
            <select class="form-select" id="reclamationSelect">
              <option value="" disabled selected>Choisissez une option</option>
              <option>Le bénéficiaire n'a pas reçu son argent</option>
              <option>Je me suis trompé de numéro de destinataire</option>
              <option>Changement de numéro du bénéficiaire</option>
              <option>Modification de paiement</option>
              <option>Annulation de paiement</option>
              <option>Demande de remboursement</option>
              <option>Paiement non abouti</option>
              <option>Problème avec l'application</option>
              <option>Double prélèvement</option>
              <option>Remboursement non reçu</option>
              <option>Autres</option>
            </select>
          </div>

          <div id="step1-error" class="alert alert-danger d-none mb-3">
            Veuillez renseigner votre e-mail et sélectionner une option.
          </div>

          <div class="text-center">
            <button type="button" class="btn btn-primary btn-sm" onclick="executerReclamation()">
              Envoyer
            </button>
          </div>
        </div>

        {{-- Étape 2a : Réclamation standard (tout sauf "Autres") --}}
        <div id="step2-reclam" class="mt-4 d-none">
          <h5>Réclamation</h5>
          <form method="POST" action="{{ route('user.reclamation.send') }}">
            @csrf
            <input type="hidden" name="to"      id="hiddenTo">
            <input type="hidden" name="subject" id="hiddenSubject">

            <div class="mb-3">
              <label class="form-label fw-bold">Objet</label>
              <input type="text" class="form-control" id="subjectDisplay" readonly>
            </div>

            <div class="mb-3">
              <label for="bodyReclam" class="form-label fw-bold">Message</label>
              <textarea id="bodyReclam" name="body" class="form-control" rows="3"
                        placeholder="Entrez votre message" required></textarea>
            </div>

            @error('body')
              <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="text-center">
              <button type="submit" class="btn btn-success btn-sm">Envoyer</button>
            </div>
          </form>
        </div>

        {{-- Étape 2b : Support (option "Autres") --}}
        <div id="step2-support" class="mt-4 d-none">
          <h5>Support</h5>
          <form method="POST" action="{{ route('user.reclamation.send') }}">
            @csrf
            <input type="hidden" name="to" id="hiddenToSupport">

            <div class="mb-3">
              <label for="subjectSupport" class="form-label fw-bold">Objet</label>
              <input type="text" name="subject" id="subjectSupport" class="form-control"
                     placeholder="Entrez l'objet" required>
            </div>

            <div class="mb-3">
              <label for="bodySupport" class="form-label fw-bold">Message</label>
              <textarea id="bodySupport" name="body" class="form-control" rows="3"
                        placeholder="Entrez votre message" required></textarea>
            </div>

            @error('subject')
              <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="text-center">
              <button type="submit" class="btn btn-success btn-sm">Envoyer</button>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function executerReclamation() {
  const email     = document.getElementById('emailInput').value.trim();
  const selection = document.getElementById('reclamationSelect').value;
  const errEl     = document.getElementById('step1-error');

  if (!email || !selection) {
    errEl.classList.remove('d-none');
    return;
  }
  errEl.classList.add('d-none');

  if (selection === 'Autres') {
    document.getElementById('hiddenToSupport').value = email;
    document.getElementById('step2-support').classList.remove('d-none');
    document.getElementById('step2-reclam').classList.add('d-none');
  } else {
    document.getElementById('hiddenTo').value      = email;
    document.getElementById('hiddenSubject').value = selection;
    document.getElementById('subjectDisplay').value = selection;
    document.getElementById('step2-reclam').classList.remove('d-none');
    document.getElementById('step2-support').classList.add('d-none');
  }
}
</script>
@endpush
