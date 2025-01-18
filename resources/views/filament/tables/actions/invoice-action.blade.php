@php
    $items = $invoice->items;
    $totalItems = $items->count();
    // First chunk size
    $firstChunkAmount = $invoice->firstParty ? 10 : 12;
    $firstChunk = $items->take($firstChunkAmount);
    // Remaining items after the first chunk
    $remainingItems = $items->slice($firstChunkAmount);
    $remainingCount = $remainingItems->count();
    // Initialize variables for middle and last chunks
    $lastChunkAmount = 10;
    $middleChunks = collect();
    $lastChunk = collect();
    if ($remainingCount > $lastChunkAmount) {
        // Calculate middle chunk size
        $middleChunkSize = floor(($remainingCount - $lastChunkAmount) / ceil(($remainingCount - $lastChunkAmount) / 17));
        // Create middle chunks
        $middleChunks = $remainingItems->slice(0, $remainingCount - $lastChunkAmount)->chunk($middleChunkSize);
        // Last chunk
        $lastChunk = $remainingItems->slice($remainingCount - $lastChunkAmount);
    } else {
        // If remaining items are less than or equal to the last chunk amount, add them to the last chunk
        $lastChunk = $remainingItems;
    }
    // Combine the first chunk, middle chunks, and the last chunk
    $items_chunks = collect();
    if ($firstChunk->isNotEmpty()) {
        $items_chunks->push($firstChunk);
    }
    if ($middleChunks->isNotEmpty()) {
        $items_chunks = $items_chunks->merge($middleChunks);
    }
    if ($lastChunk->isNotEmpty()) {
        $items_chunks->push($lastChunk);
    }
    $globalIndex = 1;
@endphp
@foreach($items_chunks as $items)
    @php
        $invoice_loop = $loop->iteration ;
        $invoice_total_loop = $loop->count ;
    @endphp
    @if($invoice_loop == 1)
        <section class="w-full p-4 sm:p-10 bg-white shadow-md rounded-xl h-[295mm] overflow-hidden"
                 id="invoice_section_1">
            <!-- Grid -->
            <div class="flex justify-between">
                <div>
                    @if($invoice->logo)
                        <img src="{{ $invoice->logo }}" class="size-10" width="26" height="26">
                    @endif
                    <h1 class="mt-2 text-lg md:text-xl font-semibold text-blue-600">{{ $invoice->companyName }}</h1>
                </div>
                <!-- Col -->
                <div class="text-end">
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-800">{{__('Invoice')}} #</h2>
                    <span class="mt-1 block text-gray-500">{{ $invoice->serialNumber }}</span>
                    @if($invoice->companyInfo)
                        <address class="mt-4 not-italic text-gray-800">
                            @foreach($invoice->companyInfo as $info)
                                {{ $info }}<br>
                            @endforeach
                        </address>
                    @endif
                </div>
                <!-- Col -->
            </div>
            <!-- End Grid -->
            <!-- Grid -->
            <div class="mt-8 grid sm:grid-cols-2 gap-3">
                @if($invoice->firstParty)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{__("Bill to")}}:</h3>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $invoice->firstParty }}</h3>
                        @if($invoice->firstPartyDetails)
                            <address class="mt-2 not-italic text-gray-500">
                                @foreach($invoice->firstPartyDetails as $info)
                                    {{ $info }}<br>
                                @endforeach
                            </address>
                        @endif
                    </div>
                @endif
                @if($invoice->date || $invoice->dueDate || $invoice->status)
                    <div class="{{$invoice->firstParty ? 'sm:text-end' : ''}} space-y-2">
                        <!-- Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                            @if($invoice->status)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-3 font-semibold text-gray-800 font-bold text-2xl">{{__("Status")}}
                                        :
                                    </dt>
                                    <dd class="col-span-2 text-gray-500 font-bold text-2xl">{{ $invoice->status }}</dd>
                                </dl>
                            @endif
                            @if($invoice->date)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-3 font-semibold text-gray-800">{{__("Invoice date")}}:</dt>
                                    <dd class="col-span-2 text-gray-500">{{ $invoice->date }}</dd>
                                </dl>
                            @endif
                            @if($invoice->dueDate)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-3 font-semibold text-gray-800">{{__("Due date")}}:</dt>
                                    <dd class="col-span-2 text-gray-500">{{ $invoice->dueDate }}</dd>
                                </dl>
                            @endif
                        </div>
                        <!-- End Grid -->
                    </div>
                @endif
            </div>
            <!-- End Grid -->
            <!-- Table -->
            <div class="mt-6" id="invoice_table_div_1">
                <div class="border border-gray-200 p-4 rounded-lg space-y-4">
                    <table class="min-w-full" id="invoice_table_1">
                        <thead class="hidden sm:table-header-group">
                        <tr class="border-b border-gray-200">
                            @if($invoice->isIndex)
                                <th class="p-2 text-xs font-medium text-gray-500 uppercase text-start">{{ __('No') }}.
                                </th>
                            @endif
                            @foreach($invoice->headersAndColumns as $column => $header)
                                <th class="p-2 text-xs font-medium text-gray-500 uppercase text-start">{{ __($header) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody id="invoice_table_body_1">
                        @foreach($items as $item)
                            @php
                                $index = $globalIndex++ ;
                            @endphp
                            <tr id="invoice_table_tr" class="border-b border-gray-200 sm:border-none">
                                @if($invoice->isIndex)
                                    <td class="p-2">
                                        <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase">{{__('No.')}}</h5>
                                        <p class="font-medium text-gray-800">{{ $index }}</p>
                                    </td>
                                @endif
                                @foreach($invoice->headersAndColumns as  $column => $header)
                                    <td class="p-2">
                                        <h5 class="sm:hidden text-xs font-medium text-gray-500 ucppercase">{{$header}}</h5>
                                        <p class="font-medium text-gray-800">{{data_get($item, $column) }}</p>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Table -->
            <!-- Flex -->
            @if($loop->last)
                <div class="mt-8 flex sm:justify-end">
                    <div class="w-full max-w-2xl sm:text-end space-y-2">
                        <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                            @if($invoice->subtotal)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Subtotal")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->subtotal }}</dd>
                                </dl>
                            @endif
                            @if($invoice->discount)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Discount")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->discount }}</dd>
                                </dl>
                            @endif
                            @if($invoice->tax)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Tax")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->tax }}</dd>
                                </dl>
                            @endif
                            @if($invoice->total)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Total")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->total }}</dd>
                                </dl>
                            @endif
                            @if($invoice->amountPaid)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Amount paid")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->amountPaid }}</dd>
                                </dl>
                            @endif
                            @if($invoice->amountDue)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Amount due")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->amountDue }}</dd>
                                </dl>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <!-- End Flex -->
            <footer class=" mt-3 text-center text-gray-500 text-xs">
                <p>{{__("Thank you for your business")}}!</p>
                @if($invoice->contact)
                    <p>{{__("Contact")}}:
                        @foreach($invoice->contact as $info)
                            {{ $info }} {{ $loop->last ? '' : '|' }}
                        @endforeach
                    </p>
                @endif
                <p>{{__("This invoice was generated by")}} <span
                        class="text-black font-bold">{{ Auth::user()->name ?? '' }}</span> {{__("on")}}
                    <span class="text-black font-bold">{{ now()->format('Y-m-d H:i:s') }}</span></p>
            </footer>
            <div class="mt-4 text-gray-800 font-medium  text-xs">
                {{__("Page")}} {{ $invoice_loop }} {{__('of')}} {{ $invoice_total_loop }}
            </div>
        </section>
    @else
        <section class="w-full p-4 sm:p-10 bg-white shadow-md rounded-xl h-[295mm] overflow-hidden"
                 id="invoice_section_{{ $invoice_loop }}">
            <!-- Minimal Header -->
            <div class="flex justify-between">
                <div>
                    <h1 class="text-md font-semibold text-blue-600">{{ $invoice->companyName }}</h1>
                </div>
                <div class="text-end">
                    <h2 class="text-lg font-semibold text-gray-800">{{__('Invoice')}} #{{ $invoice->serialNumber }}</h2>
                    <span class="block text-gray-500">{{ $invoice->date }}</span>
                </div>
            </div>
            <!-- Items Table -->
            <div class="mt-6" id="invoice_table_div_{{ $invoice_loop }}">
                <div class="border border-gray-200 p-4 rounded-lg space-y-4">
                    <table class="min-w-full" id="invoice_table_{{ $invoice_loop }}">
                        <thead class="hidden sm:table-header-group">
                        <tr class="border-b border-gray-200">
                            @if($invoice->isIndex)
                                <th class="p-2 text-xs font-medium text-gray-500 uppercase text-start">{{ __('No') }}.
                                </th>
                            @endif
                            @foreach($invoice->headersAndColumns as $column => $header)
                                <th class="p-2 text-xs font-medium text-gray-500 uppercase text-start">{{ __($header) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody id="invoice_table_body_{{ $invoice_loop }}">
                        @foreach($items as $item)
                            @php
                                $index = $globalIndex++ ;
                            @endphp
                            <tr id="invoice_table_tr" class="border-b border-gray-200 sm:border-none">
                                @if($invoice->isIndex)
                                    <td class="p-2">
                                        <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase">{{__('No')}}
                                            .</h5>
                                        <p class="font-medium text-gray-800">{{ $index }}</p>
                                    </td>
                                @endif
                                @foreach($invoice->headersAndColumns as  $column => $header)
                                    <td class="p-2">
                                        <h5 class="sm:hidden text-xs font-medium text-gray-500 ucppercase">{{$header}}</h5>
                                        <p class="font-medium text-gray-800">{{data_get($item, $column) }}</p>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($loop->last)
                <div class="mt-8 flex sm:justify-end">
                    <div class="w-full max-w-2xl sm:text-end space-y-2">
                        <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                            @if($invoice->subtotal)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Subtotal")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->subtotal }}</dd>
                                </dl>
                            @endif
                            @if($invoice->discount)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Discount")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->discount }}</dd>
                                </dl>
                            @endif
                            @if($invoice->tax)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Tax")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->tax }}</dd>
                                </dl>
                            @endif
                            @if($invoice->total)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Total")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->total }}</dd>
                                </dl>
                            @endif
                            @if($invoice->amountPaid)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Amount paid")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->amountPaid }}</dd>
                                </dl>
                            @endif
                            @if($invoice->amountDue)
                                <dl class="grid sm:grid-cols-5 gap-x-3">
                                    <dt class="col-span-4 font-semibold text-gray-800">{{__("Amount due")}}:</dt>
                                    <dd class="col-span-1 text-gray-500">{{ $invoice->amountDu }}</dd>
                                </dl>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <footer class="mt-8 text-center text-gray-500 text-xs">
                <p>{{__("Thank you for your business")}}!</p>
                @if($invoice->contact)
                    <p>{{__("Contact")}}:
                        @foreach($invoice->contact as $info)
                            {{ $info }} {{ $loop->last ? '' : '|' }}
                        @endforeach
                    </p>
                @endif
                <p>{{__("This invoice was generated by")}} <span
                        class="text-black font-bold">{{ Auth::user()->name ?? '' }}</span> {{__("on")}}
                    <span class="text-black font-bold">{{ now()->format('Y-m-d H:i:s') }}</span></p>
            </footer>
            <div class="mt-4 text-gray-800 font-medium  text-xs">
                {{__("Page")}} {{ $invoice_loop }} {{__("of")}} {{ $invoice_total_loop }}
            </div>
        </section>
    @endif
@endforeach
