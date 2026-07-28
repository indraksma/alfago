<?php
namespace App\Livewire\Admin;
use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
class OrderManager extends Component {
    use WithPagination;
    public ?Order $order=null; public string $status='';
    public function mount(?Order $order=null):void { if($order){$this->order=$order->load('user','kelas','items.vendor');} }
    public function confirm():void { abort_unless($this->order?->status===OrderStatus::Pending,422); $this->order->update(['status'=>OrderStatus::Processing,'confirmed_at'=>now()]); $this->order->refresh(); session()->flash('success','Pesanan dikonfirmasi.'); }
    public function complete():void { abort_unless($this->order?->status===OrderStatus::Processing,422); $this->order->update(['status'=>OrderStatus::Completed]); $this->order->refresh(); session()->flash('success','Pesanan diselesaikan.'); }
    public function whatsappGroups():array {
        if(!$this->order)return [];
        return $this->order->items->groupBy('vendor_id')->map(function($items){
            $vendor=$items->first()->vendor; $lines=['*PESANAN BARU - ALFAGO*','Kode: '.$this->order->kode_pesanan,'Kelas: '.$this->order->kelas->nama,'Pembayaran: '.strtoupper($this->order->metode_pembayaran->value),'','*Vendor: '.$vendor->nama.'*'];
            foreach($items->values() as $i=>$item)$lines[]=($i+1).'. '.$item->nama_produk.' x'.$item->qty.' - Rp'.number_format((float)$item->subtotal,0,',','.');
            $lines[]='Subtotal Vendor: Rp'.number_format((float)$items->sum('subtotal'),0,',','.'); $lines[]=''; $lines[]='Catatan: '.($this->order->catatan?:'-');
            return ['vendor'=>$vendor->nama,'link'=>$vendor->whatsapp_group_link,'text'=>implode("\n",$lines)];
        })->values()->all();
    }
    public function render() {
        if($this->order)return view('livewire.admin.order-detail',['groups'=>$this->whatsappGroups()])->layout('components.layouts.admin',['title'=>$this->order->kode_pesanan]);
        $orders=Order::with('user','kelas')->when($this->status,fn($q)=>$q->where('status',$this->status))->latest()->paginate(15);
        return view('livewire.admin.orders',['orders'=>$orders])->layout('components.layouts.admin',['title'=>'Pesanan']);
    }
}
