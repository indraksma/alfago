<?php
namespace App\Livewire\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
class Dashboard extends Component {
    public function render() {
        $base=Order::query();
        $cards=[
            'today'=>['count'=>(clone $base)->whereDate('created_at',today())->count(),'sales'=>(float)(clone $base)->whereDate('created_at',today())->sum('total')],
            'week'=>['count'=>(clone $base)->whereBetween('created_at',[now()->startOfWeek(),now()->endOfWeek()])->count(),'sales'=>(float)(clone $base)->whereBetween('created_at',[now()->startOfWeek(),now()->endOfWeek()])->sum('total')],
            'month'=>['count'=>(clone $base)->whereMonth('created_at',now()->month)->whereYear('created_at',now()->year)->count(),'sales'=>(float)(clone $base)->whereMonth('created_at',now()->month)->whereYear('created_at',now()->year)->sum('total')],
        ];
        $daily=collect(CarbonPeriod::create(now()->subDays(29)->startOfDay(),now()->startOfDay()))->map(fn($d)=>['label'=>$d->format('d M'),'value'=>(float)Order::whereDate('created_at',$d)->sum('total')]);
        $topProducts=OrderItem::select('nama_produk',DB::raw('SUM(qty) as total_qty'))->groupBy('nama_produk')->orderByDesc('total_qty')->limit(10)->get();
        $topClasses=Order::join('kelas','orders.kelas_id','=','kelas.id')->select('kelas.nama',DB::raw('COUNT(*) as total'))->groupBy('kelas.id','kelas.nama')->orderByDesc('total')->limit(10)->get();
        $status=Order::select('status',DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total','status');
        $payments=Order::select('metode_pembayaran',DB::raw('COUNT(*) as total'))->groupBy('metode_pembayaran')->pluck('total','metode_pembayaran');
        $vendors=OrderItem::join('vendors','order_items.vendor_id','=','vendors.id')->select('vendors.nama',DB::raw('SUM(order_items.subtotal) as total'))->groupBy('vendors.id','vendors.nama')->orderByDesc('total')->get();
        return view('livewire.admin.dashboard',compact('cards','daily','topProducts','topClasses','status','payments','vendors'))->layout('components.layouts.admin',['title'=>'Dashboard']);
    }
}
