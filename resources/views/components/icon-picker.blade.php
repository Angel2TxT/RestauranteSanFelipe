@php
    $icons = [
        'dinner' => 'Comida / Cena',
        'dinner2' => 'Plato especial',
        'chef' => 'Chef',
        'pizza' => 'Pizza',
        'coffee-cup' => 'Café',
        'coffee-bean' => 'Grano de café',
        'teacup' => 'Té',
        'glass' => 'Bebida',
        'glass2' => 'Vaso',
        'glass-cocktail' => 'Cóctel',
        'bottle' => 'Botella',
        'bottle2' => 'Botella 2',
        'ice-cream' => 'Helado',
        'ice-cream2' => 'Helado 2',
        'cake' => 'Pastel',
        'carrot' => 'Verduras',
        'apple' => 'Fruta',
        'cherry' => 'Postre / Cereza',
        'sun' => 'Desayuno / Mañana',
        'sun2' => 'Sol',
        'moon' => 'Noche',
        'heart' => 'Favoritos',
        'star' => 'Destacado',
        'star-empty' => 'Especial',
        'leaf' => 'Natural / Light',
        'fire' => 'Picante',
        'gift' => 'Promoción',
        'store' => 'Local',
        'cart' => 'Para llevar',
        'bag' => 'Pedido',
        'truck' => 'Delivery',
        'bicycle' => 'Reparto',
        'home' => 'Inicio',
        'users' => 'Familiar',
        'clock' => 'Rápido',
        'tag' => 'Oferta',
        'thumbs-up' => 'Recomendado',
        'smile' => 'Feliz',
        'map-marker' => 'Ubicación',
        'phone' => 'Contacto',
    ];

    $selected = old('icon', $selected ?? '');
@endphp

<style>
    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 10px;
        max-height: 280px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        background: #f8f9fc;
    }

    .icon-option {
        position: relative;
        cursor: pointer;
        margin: 0;
    }

    .icon-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .icon-option-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 84px;
        padding: 10px 6px;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        background: #fff;
        text-align: center;
        transition: all .15s ease;
    }

    .icon-option-card i,
    .icon-option-card span.linearicons-preview {
        font-size: 1.6rem;
        line-height: 1;
        color: #4e73df;
    }

    .icon-option-card small {
        font-size: .72rem;
        color: #858796;
        line-height: 1.2;
    }

    .icon-option input:checked + .icon-option-card {
        border-color: #4e73df;
        box-shadow: 0 0 0 2px rgba(78, 115, 223, .2);
        background: #eaecf4;
    }

    .icon-option:hover .icon-option-card {
        border-color: #4e73df;
    }
</style>

<label class="d-block mb-2">Icono*</label>
<input type="hidden" name="icon" id="icon" value="{{ $selected }}" required>

<div class="icon-picker-grid" id="icon-picker">
    @foreach ($icons as $value => $label)
        <label class="icon-option">
            <input type="radio" name="icon_choice" value="{{ $value }}"
                {{ $selected === $value ? 'checked' : '' }}>
            <div class="icon-option-card">
                <span class="linearicons-{{ $value }} linearicons-preview" aria-hidden="true"></span>
                <small>{{ $label }}</small>
            </div>
        </label>
    @endforeach
</div>
<small class="form-text text-muted mt-2">Selecciona un icono para mostrar en la tienda.</small>

<script>
    (function () {
        const hidden = document.getElementById('icon');
        document.querySelectorAll('input[name="icon_choice"]').forEach((input) => {
            input.addEventListener('change', function () {
                hidden.value = this.value;
            });
        });

        const checked = document.querySelector('input[name="icon_choice"]:checked');
        if (checked) {
            hidden.value = checked.value;
        }
    })();
</script>
