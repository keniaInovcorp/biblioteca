{{-- Rating --}}
<div class="form-control mb-4">
    <label class="label pb-1">
        <span class="label-text font-semibold">Classificação</span>
    </label>
    <div class="flex items-center gap-1">
        @for($i = 1; $i <= 5; $i++)
            <a 
                href="#"
                wire:click.prevent="setRating({{ $i }})"
                class="p-0.5 hover:scale-110 transition-transform"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 {{ $i <= $rating ? 'text-warning' : 'text-base-300' }} transition-colors" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </a>
        @endfor
        <span class="ml-2 text-sm font-medium text-base-content/60">({{ $rating }}/5)</span>
    </div>
    @error('rating') 
        <label class="label py-1">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </label>
    @enderror
</div>

{{-- Comment --}}
<div class="form-control">
    <label class="label pb-1">
        <span class="label-text font-semibold">Comentário</span>
    </label>
    <textarea
        wire:model="comment"
        class="textarea textarea-bordered h-28 text-sm"
        placeholder="Escreva a sua opinião sobre o livro..."
        maxlength="1000"
    ></textarea>
    @error('comment') 
        <label class="label py-1">
            <span class="label-text-alt text-error">{{ $message }}</span>
        </label>
    @enderror
</div>
