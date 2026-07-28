<?php
namespace App\Livewire;
use Livewire\Component;
use Livewire\WithPagination;
class OrderHistory extends Component {
    use WithPagination; public string $status='';
    public function render() { $orders=auth()->user()->orders()->with('kelas')->when($this->status,fn($q)=>$q->where('status',$this->status))->latest()->paginate(10); return view('livewire.order-history',['orders'=>$orders])->layout('components.layouts.app',['title'=>'Pesanan Saya']); }
}
