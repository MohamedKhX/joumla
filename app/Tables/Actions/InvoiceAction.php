<?php
namespace App\Tables\Actions;
use Carbon\CarbonInterface;
use Closure;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;
class InvoiceAction extends Action
{
    protected bool|Closure|null $download = false;
    protected bool|Closure|null $print = false;
    protected string|\Closure|null $firstParty = null;
    protected string|\Closure|null $companyName = null;
    protected array|\Closure|null $firstPartyDetails = [];
    protected array|\Closure|null $companyInfo = [];
    protected string|\Closure|null $logo = null;
    protected string|\Closure|null $status = null;
    protected string|\Closure|null $serialNumber = null;
    protected string|CarbonInterface|\Closure|null $date = null;
    protected string|CarbonInterface|\Closure|null $dueDate = null;
    protected string|\Closure|null $subtotal = null;
    protected string|\Closure|null $discount = null;
    protected string|\Closure|null $tax = null;
    protected string|\Closure|null $total = null;
    protected string|\Closure|null $amountPaid = null;
    protected string|\Closure|null $amountDue = null;
    protected string|\Closure|null $signature = null;
    protected array|\Closure|null|Collection $invoiceItems = [];
    protected array|Closure|null $headersAndColumns = [];
    protected Closure|bool $index = false;
    protected array|\Closure|null $contact = [];
    public function contact(array|\Closure|null $contact): static
    {
        $this->contact = $contact;
        return $this;
    }
    /**
     * @return array|Closure|null
     */
    public function getContact(): array|Closure|null
    {
        return $this->evaluate($this->contact);
    }
    public function index(bool|Closure $index = true): static
    {
        $this->index = $index;
        return $this;
    }
    /**
     * @return bool|Closure
     */
    public function getIndex(): bool|Closure
    {
        return $this->evaluate($this->index);
    }
    public function headersAndColumns(array|Closure|null $headersAndColumns): static
    {
        $this->headersAndColumns = $headersAndColumns;
        return $this;
    }
    public function getHeadersAndColumns(): array|Closure|null
    {
        return $this->evaluate($this->headersAndColumns);
    }
    public function invoiceItems(array|\Closure|null|Collection $invoiceItems): static
    {
        $this->invoiceItems = $invoiceItems;
        return $this;
    }
    public function getInvoiceItems(): array|Closure|null|Collection
    {
        return $this->evaluate($this->invoiceItems);
    }
    public function companyInfo(array|\Closure|null $companyInfo): static
    {
        $this->companyInfo = $companyInfo;
        return $this;
    }
    /**
     * @return array|Closure|null
     */
    public function getCompanyInfo(): array|Closure|null
    {
        return $this->evaluate($this->companyInfo);
    }
    public function companyName(string|\Closure|null $companyName): static
    {
        $this->companyName = $companyName;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getCompanyName(): string|Closure|null
    {
        return $this->evaluate($this->companyName);
    }
    public function signature(string|\Closure|null $signature): static
    {
        $this->signature = $signature;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getSignature(): string|Closure|null
    {
        return $this->evaluate($this->signature);
    }
    public function amountDue(string|\Closure|null $amountDue): static
    {
        $this->amountDue = $amountDue;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getAmountDue(): string|Closure|null
    {
        return $this->evaluate($this->amountDue);
    }
    public function amountPaid(string|\Closure|null $amountPaid): static
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getAmountPaid(): string|Closure|null
    {
        return $this->evaluate($this->amountPaid);
    }
    public function total(string|\Closure|null $total): static
    {
        $this->total = $total;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getTotal(): string|Closure|null
    {
        return $this->evaluate($this->total);
    }
    public function tax(string|\Closure|null $tax): static
    {
        $this->tax = $tax;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getTax(): string|Closure|null
    {
        return $this->evaluate($this->tax);
    }
    public function discount(string|\Closure|null $discount): static
    {
        $this->discount = $discount;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getDiscount(): string|Closure|null
    {
        return $this->evaluate($this->discount);
    }
    public function subTotal(string|\Closure|null $subtotal): static
    {
        $this->subtotal = $subtotal;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getSubtotal(): string|Closure|null
    {
        return $this->evaluate($this->subtotal);
    }
    public function date(string|CarbonInterface|\Closure|null $date): static
    {
        $this->date = $date;
        return $this;
    }
    /**
     * @return CarbonInterface|Closure|string|null
     */
    public function getDate(): CarbonInterface|string|Closure|null
    {
        return $this->evaluate($this->date);
    }
    public function dueDate(string|CarbonInterface|\Closure|null $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }
    /**
     * @return CarbonInterface|Closure|string|null
     */
    public function getDueDate(): CarbonInterface|string|Closure|null
    {
        return $this->evaluate($this->dueDate);
    }
    public function serialNumber(string|\Closure|null $serialNumber): static
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }
    public function getSerialNumber(): string|Closure|null
    {
        return $this->evaluate($this->serialNumber);
    }
    public function logo(string|\Closure|null $logo): static
    {
        $this->logo = $logo;
        return $this;
    }
    public function status(string|\Closure|null $status): static
    {
        $this->status = $status;
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getStatus(): string|Closure|null
    {
        return $this->evaluate($this->status);
    }
    /**
     * @return Closure|string|null
     */
    public function getLogo(): string|Closure|null
    {
        return $this->evaluate($this->logo);
    }
    public function firstParty(string|\Closure|null $identifier, array|Closure $details = []): static
    {
        $this->firstParty = $identifier; // Store the identifier (e.g., 'seller')
        $this->firstPartyDetails = $details; // Store the details
        return $this;
    }
    /**
     * @return Closure|string|null
     */
    public function getFirstParty(): string|Closure|null
    {
        return $this->evaluate($this->firstParty);
    }
    /**
     * @return array|Closure|null
     */
    public function getFirstPartyDetails(): array|Closure|null
    {
        return $this->evaluate($this->firstPartyDetails);
    }
    /**
     * @return array|Closure|null
     */
    public function getSecondPartyDetails(): array|Closure|null
    {
        return $this->evaluate($this->secondPartyDetails);
    }
    public function download(bool|Closure|null $download = true): static
    {
        $this->download = $download;
        return $this;
    }
    public function print(bool|Closure|null $print = true): static
    {
        $this->print = $print;
        return $this;
    }
    public function getDownload(): bool|Closure|null
    {
        return $this->evaluate($this->download);
    }
    public function getPrint(): bool|Closure|null
    {
        return $this->evaluate($this->print);
    }
    protected function setUp(): void
    {
        parent::setUp();
        $this->modalContent(function ($livewire) {
            $livewire->dispatch('Invoice_loaded');
            return view('filament.tables.actions.invoice-action', ['invoice' => $this->collectInvoiceData()]);
        });
        $this->modalSubmitAction(false);
        $this->stickyModalFooter();
        $this->extraModalFooterActions([
            $this->downloadButtonAction(),
            $this->printButtonAction(),
        ]);
    }
    protected function collectInvoiceData(): object
    {
        // Merge provided details with defaults to ensure all properties exist
        $firstPartyDetails = (array)$this->getFirstPartyDetails();
        $companyInfo = (array)$this->getCompanyInfo();
        $items = $this->getInvoiceItems();
        $invoiceData = [
            'logo' => $this->getLogo(),
            'companyName' => $this->getCompanyName(),
            'serialNumber' => $this->getSerialNumber(),
            'date' => $this->getDate(),
            'dueDate' => $this->getDueDate(),
            'companyInfo' => (object)$companyInfo,
            'firstParty' => $this->getFirstParty(),
            'firstPartyDetails' => (object)$firstPartyDetails,
            'headersAndColumns' => $this->getHeadersAndColumns() ?? ['Description', 'Units', 'Qty', 'Price', 'Discount', 'Sub total'],
            'items' => is_iterable($items) ? collect($items) : collect([$items]),
            'isIndex' => $this->getIndex(),
            'subtotal' => $this->getSubtotal(),
            'discount' => $this->getDiscount(),
            'total' => $this->getTotal(),
            'tax' => $this->getTax(),
            'amountPaid' => $this->getAmountPaid(),
            'amountDue' => $this->getAmountDue(),
            'status' => $this->getStatus(),
            'contact' => $this->getContact(),
//            'currentChunkIndex' => 0, // initialize as 0
//            'totalChunks' => is_iterable($items) ? ceil(count($items) / 17) : 1,
            'signature' => $this->getSignature(),
        ];
        return (object)$invoiceData;
    }
    private function downloadButtonAction(): Html2MediaAction
    {
        return Html2MediaAction::make('download')
            ->visible(fn() => $this->getDownload())
            ->savePdf()
            ->content(fn() => view('filament.tables.actions.invoice-action', ['invoice' => $this->collectInvoiceData()]));
    }
    private function printButtonAction(): Html2MediaAction
    {
        return Html2MediaAction::make('print')
            ->format('a4')
            ->visible(fn() => $this->getPrint())
            ->print()
            ->content(fn() => view('filament.tables.actions.invoice-action', ['invoice' => $this->collectInvoiceData()]));
    }
}
