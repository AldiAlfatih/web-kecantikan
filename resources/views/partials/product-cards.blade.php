@if($products->count())
  @foreach ($products as $product)
    @php
      $search = strtolower(
        ($product->name ?? '') . ' ' .
        ($product->brand ?? '') . ' ' .
        ($product->category ?? '') . ' ' .
        ($product->skin_type ?? '')
      );
    @endphp
    

    <div class="product-card" data-search="{{ $search }}">
      <span class="badge {{ $product->badge ?? 'popular' }}">
        {{ $product->badge ?? 'Popular' }}
      </span>

      <a href="{{ route('produk.show', $product->id) }}" class="card-link">
        <div class="product-image">
          <img src="/assets/image/1.png" alt="{{ $product->name }}">
        </div>

        <div class="product-info">
          <small class="brand">{{ $product->brand }}</small>
          <h3>{{ $product->name }}</h3>
          <p class="skin-type">{{ $product->skin_type ?? 'Kulit Normal' }}</p>
          <div class="price">RP {{ number_format($product->price, 0, ',', '.') }}</div>
        </div>
      </a>
    </div>
  @endforeach
@else
  <div class="no-result">Produk tidak ditemukan</div>
@endif
