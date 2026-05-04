@extends('layouts.app')
@section('title', $contact->id ? 'Modifier le bénéficiaire' : 'Nouveau bénéficiaire')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-5 col-lg-6 col-md-7 col-sm-10">
    <h3 class="mb-4">
      <i class="fas fa-user-{{ $contact->id ? 'edit' : 'plus' }} me-2"></i>
      {{ $contact->id ? 'Modifier le bénéficiaire' : 'Nouveau bénéficiaire' }}
    </h3>

    <div class="mb-3">
      <a href="{{ route('user.contacts') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        @foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach
      </div>
    @endif

    <div class="card shadow p-4">
      <form method="POST"
            action="{{ $contact->id ? route('user.contacts.update', $contact->id) : route('user.contacts.store') }}">
        @csrf
        @if($contact->id) @method('PUT') @endif

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
            <input type="text" name="firstname" value="{{ old('firstname', $contact->firstname) }}"
                   class="form-control form-control-lg" placeholder="Prénom" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
            <input type="text" name="lastname" value="{{ old('lastname', $contact->lastname) }}"
                   class="form-control form-control-lg" placeholder="Nom" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Pays de destination <span class="text-danger">*</span></label>
          <select name="country" class="form-select form-select-lg" required>
            <option value="">Sélectionnez un pays</option>
            @php
              $countries = ['GN' => 'Guinée','SN' => 'Sénégal','CI' => 'Côte d\'Ivoire',
                            'ML' => 'Mali','BF' => 'Burkina Faso','TG' => 'Togo',
                            'BJ' => 'Bénin','NE' => 'Niger','GW' => 'Guinée-Bissau',
                            'LR' => 'Liberia','SL' => 'Sierra Leone','GM' => 'Gambie',
                            'MR' => 'Mauritanie','CV' => 'Cap-Vert'];
            @endphp
            @foreach($countries as $code => $name)
              <option value="{{ $code }}" {{ old('country', $contact->country) === $code ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
          <input type="tel" name="telephone" value="{{ old('telephone', $contact->telephone) }}"
                 class="form-control form-control-lg" placeholder="+224 6XX XXX XXX" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Lien de parenté</label>
          <select name="relationship" class="form-select form-select-lg">
            <option value="">Non renseigné</option>
            @foreach(['Famille','Ami(e)','Collègue','Conjoint(e)','Parent','Enfant','Frère/Sœur','Autre'] as $rel)
              <option value="{{ $rel }}" {{ old('relationship', $contact->relationship) === $rel ? 'selected' : '' }}>
                {{ $rel }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Email (optionnel)</label>
          <input type="email" name="email" value="{{ old('email', $contact->email) }}"
                 class="form-control form-control-lg" placeholder="Email du bénéficiaire">
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <a href="{{ route('user.contacts') }}" class="btn btn-outline-danger px-4 rounded-pill">
            <i class="fas fa-times me-2"></i>Annuler
          </a>
          <button type="submit" class="btn btn-success px-4 rounded-pill">
            <i class="fas fa-check me-2"></i><strong>Enregistrer</strong>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
