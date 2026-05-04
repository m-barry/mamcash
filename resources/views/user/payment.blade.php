@extends('layouts.app')
@section('title', 'Paiement sécurisé')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">

    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-header rounded-top-4 py-3" style="background:linear-gradient(135deg,#1B365D,#2c4a6b)">
        <h5 class="mb-0 text-white text-center">
          <i class="fas fa-lock me-2" style="color:#FFD700"></i>Paiement sécurisé
        </h5>
      </div>
      <div class="card-body p-4">

        {{-- Récapitulatif --}}
        <div class="rounded-3 p-3 mb-4" style="background:#f8f9fa;border:1px solid #e0e0e0">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Montant envoyé</span>
            <strong>{{ number_format($transaction->amount, 2) }} €</strong>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted small">Frais de service</span>
            <strong>{{ number_format($transaction->fee, 2) }} €</strong>
          </div>
          <hr class="my-2">
          <div class="d-flex justify-content-between">
            <span class="fw-bold">Total débité</span>
            <strong style="color:#1B365D;font-size:1.1rem">{{ number_format($transaction->amount + $transaction->fee, 2) }} €</strong>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <span class="text-muted small">Bénéficiaire</span>
            <span class="small">{{ $transaction->receiver ?? $transaction->receiver_number_phone }}</span>
          </div>
        </div>

        {{-- Formulaire Stripe Elements --}}
        <form id="payment-form">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold" for="cardholder-name">Nom sur la carte</label>
            <input type="text" id="cardholder-name" class="form-control"
                   placeholder="Jean Dupont" autocomplete="cc-name" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Numéro de carte</label>
            <div id="card-element" class="form-control" style="height:42px;padding-top:10px"></div>
            <div id="card-errors" class="text-danger small mt-1" role="alert"></div>
          </div>

          <button id="submit-btn" type="submit" class="btn w-100 fw-bold py-2 mt-2"
                  style="background:linear-gradient(135deg,#1B365D,#2c4a6b);color:#FFD700;border:none;border-radius:8px;font-size:1rem">
            <span id="btn-text">
              <i class="fas fa-credit-card me-2"></i>Payer {{ number_format($transaction->amount + $transaction->fee, 2) }} €
            </span>
            <span id="btn-spinner" class="d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status"></span>Traitement…
            </span>
          </button>
        </form>

        <p class="text-center text-muted small mt-3">
          <i class="fas fa-shield-alt me-1"></i>Paiement sécurisé par <strong>Stripe</strong> — vos données ne sont jamais stockées sur nos serveurs.
        </p>

      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
(function () {
  const stripe = Stripe('{{ $publishableKey }}');
  const elements = stripe.elements();

  const cardElement = elements.create('card', {
    hidePostalCode: true,
    style: {
      base: {
        fontSize: '16px',
        color: '#1B365D',
        '::placeholder': { color: '#aab7c4' }
      },
      invalid: { color: '#dc3545' }
    }
  });
  cardElement.mount('#card-element');

  cardElement.on('change', function (event) {
    const errDiv = document.getElementById('card-errors');
    errDiv.textContent = event.error ? event.error.message : '';
  });

  const form     = document.getElementById('payment-form');
  const btnText  = document.getElementById('btn-text');
  const spinner  = document.getElementById('btn-spinner');
  const submitBtn = document.getElementById('submit-btn');

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    submitBtn.disabled = true;
    btnText.classList.add('d-none');
    spinner.classList.remove('d-none');

    const cardholderName = document.getElementById('cardholder-name').value;

    const { error, paymentIntent } = await stripe.confirmCardPayment(
      '{{ $clientSecret }}',
      {
        payment_method: {
          card: cardElement,
          billing_details: { name: cardholderName }
        },
        return_url: '{{ route("user.stripe.return") }}'
      }
    );

    if (error) {
      document.getElementById('card-errors').textContent = error.message;
      submitBtn.disabled = false;
      btnText.classList.remove('d-none');
      spinner.classList.add('d-none');
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
      // Redirection manuelle si pas de redirect automatique
      window.location.href = '{{ route("user.stripe.return") }}?payment_intent=' + paymentIntent.id + '&redirect_status=succeeded';
    }
  });
})();
</script>
@endpush
