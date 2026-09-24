<!-- CART -->
<table class="table table-responsive table-sm">
    <thead>
        <tr>
            <th scope="col">Imagen</th>
            <th scope="col">Nombre</th>
            <th scope="col">Precio</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Subtotal</th>
            <th scope="col">...</th>
        </tr>
    </thead>
    <tbody>
        @forelse (Cart::instance('shopping')->content() as $product)
    <form action="{{ route('cart.update', $product->rowId) }}" method="POST">
        @csrf
        @method('PATCH')
        <tr>
            <td>
                <img src="{{ asset($product->options->image ?? 'images/no-image.jpg') }}" width="40" alt="">
            </td>
            <td>{{ $product->name }}</td>
            <td>${{ $product->price }}</td>
            <td>
                <input type="number" name="qty" style="width: 50px;" min="1" max="5" value="{{ $product->qty }}" onblur="this.form.submit()" />
            </td>
            <td>${{ $product->price * $product->qty }}</td>
            <td>
                <a href="{{ route('cart.remove', $product->rowId) }}">
                    <span class="fas fa-trash"></span>
                </a>
            </td>
        </tr>
    </form>
@empty
    <tr>
        <td colspan="6">Sin productos</td>
    </tr>
@endforelse



        <tr>
            <td colspan="3"></td>
            <td>
                Total
            </td>
            <td>${{Cart::instance('shopping')->priceTotal()}}</td>
        </tr>

    </tbody>
</table>
