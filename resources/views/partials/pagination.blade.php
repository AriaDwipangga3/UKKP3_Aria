{{--
  PAGINATION buatan sendiri: tombol "Sebelumnya", info halaman, dan "Berikutnya".
  Dipakai otomatis oleh $users->links() (diatur di AppServiceProvider).
--}}
@if ($paginator->hasPages())
  <nav class="pager">
    {{-- Tombol Sebelumnya: nonaktif kalau sudah di halaman pertama --}}
    @if ($paginator->onFirstPage())
      <span class="pager-btn disabled">&larr; Sebelumnya</span>
    @else
      <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}">&larr; Sebelumnya</a>
    @endif

    <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

    {{-- Tombol Berikutnya: nonaktif kalau sudah di halaman terakhir --}}
    @if ($paginator->hasMorePages())
      <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}">Berikutnya &rarr;</a>
    @else
      <span class="pager-btn disabled">Berikutnya &rarr;</span>
    @endif
  </nav>
@endif
