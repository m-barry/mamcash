@extends('layouts.admin')
@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2><i class="fas fa-users me-2"></i>Gestion des utilisateurs</h2>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
    <i class="fas fa-arrow-left me-1"></i>Tableau de bord
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-primary">
          <tr>
            <th>#</th><th>Prénom</th><th>Nom</th><th>Email</th>
            <th>Pays</th><th>Téléphone</th><th>Rôle</th><th>Statut</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->firstname }}</td>
            <td>{{ $u->lastname }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->country }}</td>
            <td>{{ $u->phone_number }}</td>
            <td><span class="badge bg-info text-dark">{{ $u->role?->name ?? 'N/A' }}</span></td>
            <td><span class="badge {{ $u->active ? 'bg-success' : 'bg-danger' }}">{{ $u->active ? 'Actif' : 'Inactif' }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.users.toggle', $u->id) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $u->active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $u->active ? 'Désactiver' : 'Activer' }}">
                  <i class="fas fa-{{ $u->active ? 'ban' : 'check' }}"></i>
                </button>
              </form>
              @if(!$u->isAdmin())
              <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline"
                    onsubmit="return confirm('Supprimer cet utilisateur ?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">{{ $users->links() }}</div>
</div>
@endsection
