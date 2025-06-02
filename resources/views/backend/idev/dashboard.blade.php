@extends("backend.parent")

@section("content")
@vite('resources/css/app.css')
<div class="pc-container p-2">
  <div class="grid grid-cols-4 grid-rows-[4rem_1fr] gap-3 rounded-md h-[420px]">
    <div class="col-span-1 bg-white rounded-md flex flex-col pl-3 justify-center shadow-xl">
      <span class="font-bold">{{ $transactionQuantity }}</span>
      <span class="text-sm text-gray-500 font-medium">Total Quantity Transaction</span>
    </div>

    <div class="col-span-1 bg-white rounded-md flex flex-col pl-3 justify-center  shadow-xl">
      <span class="font-bold">Rp {{ number_format($transactionRupiah, 0, ',', '.') }}</span>
      <span class="text-sm text-gray-500 font-medium">Total Transaction</span>
    </div>

    <div class="col-span-2 row-start-2 h-full">
      <div class="bg-white h-full rounded-md flex items-center justify-center shadow-xl">
        @include('chart.quantity_chart')
      </div>
    </div>
    <div class="col-span-2 row-start-2 h-full">
      <div class="bg-white h-full rounded-md flex items-center justify-center shadow-xl">
        @include('chart.price_chart')
      </div>
    </div>
    
  </div>
</div>

@endsection
