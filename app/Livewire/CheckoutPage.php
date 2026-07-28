<?php
namespace App\Livewire;
use App\Models\Kelas;
use App\Models\ShippingSetting;
use App\Services\CheckoutService;
use Livewire\Component;
class CheckoutPage extends Component {
    public ?int $kelas_id = null; public string $metode_pembayaran = 'cash'; public string $catatan = '';
    public function mount(): void { $this->kelas_id = auth()->user()->kelas_id; }
    public function checkout(CheckoutService $service): mixed {
        $data=$this->validate(['kelas_id'=>['required','exists:kelas,id'],'metode_pembayaran'=>['required','in:cash,qris'],'catatan'=>['nullable','string','max:1000']]);
        $order=$service->create(auth()->user(),$data['kelas_id'],$data['metode_pembayaran'],$data['catatan']);
        session()->flash('success','Pesanan berhasil dibuat.');
        return $this->redirectRoute('orders.show',['order'=>$order],navigate:true);
    }
    public function render() {
        $items=auth()->user()->cart?->items()->with('product.vendor')->get()??collect(); $subtotal=$items->sum(fn($i)=>(float)$i->product->harga*$i->qty); $shipping=ShippingSetting::current();
        return view('livewire.checkout-page',['items'=>$items,'classes'=>Kelas::where('is_active',true)->orderBy('nama')->get(),'subtotal'=>$subtotal,'shipping'=>$shipping])->layout('components.layouts.app',['title'=>'Checkout']);
    }
}
