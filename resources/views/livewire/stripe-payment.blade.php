<div class="space-y-4">
    <div x-data="stripePayment(@js($stripeKey), @js($clientSecret))" x-init="init()">
        <div class="form-control w-full mb-6">
            <label class="label">
                <span class="label-text font-semibold">Dados do Cartão</span>
            </label>
            <div x-ref="cardElement" class="p-4 border border-base-300 rounded-lg bg-base-100 min-h-[50px] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all"></div>
        </div>

        {{-- Error Message --}}
        @if($errorMessage)
            <div class="alert alert-error mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        {{-- Pay Button --}}
        <div class="form-control w-full">
            <a
                href="#"
                @click.prevent="submitPayment()"
                class="btn btn-primary w-full btn-lg"
                :class="{ 'btn-disabled pointer-events-none': processing || $wire.processing }"
            >
                <span x-show="!processing && !$wire.processing" x-cloak>
                    Pagar {{ number_format($order->total, 2, ',', '.') }} €
                </span>
                <span x-show="processing || $wire.processing" class="flex items-center gap-2">
                    <span class="loading loading-spinner loading-sm"></span>
                    A processar...
                </span>
            </a>
        </div>
    </div>
</div>

@assets
<script src="https://js.stripe.com/v3/"></script>
@endassets

@script
<script>
    Alpine.data('stripePayment', (stripeKey, clientSecret) => ({
        stripe: null,
        cardElement: null,
        processing: false,

        getThemeColor(cssVar) {
            const value = getComputedStyle(document.documentElement).getPropertyValue(cssVar).trim();
            if (value.includes('oklch')) {
                return null;
            }
            return value || null;
        },

        init() {
            this.stripe = Stripe(stripeKey);
            const elements = this.stripe.elements();

            this.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        fontFamily: 'inherit',
                        fontSmoothing: 'antialiased',
                    },
                },
            });

            this.cardElement.mount(this.$refs.cardElement);
        },

        async submitPayment() {
            if (this.processing) return;

            this.processing = true;
            this.$wire.processing = true;
            this.$wire.set('errorMessage', '');

            try {
                const { error, paymentIntent } = await this.stripe.confirmCardPayment(
                    clientSecret,
                    { payment_method: { card: this.cardElement } }
                );

                if (error) {
                    await this.$wire.setError(error.message);
                    this.processing = false;
                } else if (paymentIntent.status === 'succeeded') {
                    await this.$wire.paymentSucceeded();
                }
            } catch (e) {
                await this.$wire.setError('Ocorreu um erro ao processar o pagamento. Tente novamente.');
                this.processing = false;
            }
        },
    }));
</script>
@endscript
