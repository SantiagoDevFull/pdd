<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class VoucherMailOficial extends Mailable
{

    protected $item;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function build()
    {
        return $this->from('facturacion@puntodedistribucion.com', $this->item->company_name)
        ->subject($this->item->voucher_type_name.' '.$this->item->serie_number.'-'.$this->item->voucher_number)
        ->attach(asset($this->item->nombre_ruta_pdf))
        ->view('backend.mail.voucher_mail')
        ->with(['mail_info' => $this->item]);
    }
}
