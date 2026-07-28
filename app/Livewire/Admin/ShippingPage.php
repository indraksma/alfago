<?php
namespace App\Livewire\Admin;
use App\Models\ShippingSetting;
use Livewire\Component;
class ShippingPage extends Component {
    public $nominal=0; public string $keterangan='';
    public function mount():void { $s=ShippingSetting::current(); $this->nominal=$s->nominal; $this->keterangan=$s->keterangan??''; }
    public function save():void { $data=$this->validate(['nominal'=>['required','numeric','min:0'],'keterangan'=>['nullable','string','max:255']]); ShippingSetting::current()->update($data); session()->flash('success','Pengaturan ongkir diperbarui.'); }
    public function render(){return view('livewire.admin.shipping-page')->layout('components.layouts.admin',['title'=>'Pengaturan Ongkir']);}
}
