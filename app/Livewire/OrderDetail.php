<?php
namespace App\Livewire;
use App\Models\Order;
use Livewire\Component;
class OrderDetail extends Component {
    public Order $order;
    public function mount(Order $order): void { abort_unless($order->user_id===auth()->id(),403); $this->order=$order->load('items.vendor','kelas'); }
    public function render(){ return view('livewire.order-detail')->layout('components.layouts.app',['title'=>$this->order->kode_pesanan]); }
}
