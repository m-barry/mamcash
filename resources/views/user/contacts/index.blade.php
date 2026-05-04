@extends('layouts.app')
@section('title', 'Mes Bénéficiaires')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-9 col-12">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">
      <i class="fas fa-address-book me-2"></i>Mes Bénéficiaires (Afrique de l'Ouest)
    </h2>
    <a href="{{ route('user.contacts.create') }}" class="btn btn-success">
      <i class="fas fa-user-plus me-2"></i>Nouveau bénéficiaire
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      @if($contacts->isEmpty())
        <div class="text-center py-5 text-muted">
          <i class="fas fa-users fa-3x mb-3"></i>
          <p>Aucun bénéficiaire enregistré.</p>
          <a href="{{ route('user.contacts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter votre premier bénéficiaire
          </a>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-primary">
              <tr>
                <th>Prénom</th><th>Nom</th><th>Pays</th>
                <th>Téléphone</th><th>Lien de parenté</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($contacts as $contact)
              <tr>
                <td>{{ $contact->firstname }}</td>
                <td>{{ $contact->lastname }}</td>
                <td>{{ $contact->country }}</td>
                <td>{{ $contact->telephone ?? 'Non renseigné' }}</td>
                <td>
                  @if($contact->relationship)
                    <span class="badge bg-secondary">{{ $contact->relationship }}</span>
                  @else
                    <span class="text-muted">Non renseigné</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('user.contacts.edit', $contact->id) }}"
                       class="btn btn-outline-primary btn-sm" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('user.contacts.destroy', $contact->id) }}"
                          onsubmit="return confirm('Supprimer ce bénéficiaire ?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>{{-- /.col --}}
</div>{{-- /.row --}}
@endsection
