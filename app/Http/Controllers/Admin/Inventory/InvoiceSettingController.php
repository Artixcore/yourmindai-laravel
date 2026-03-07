<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateInvoiceSettingRequest;
use App\Models\InvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceSettingController extends Controller
{
    public function edit()
    {
        $setting = InvoiceSetting::get();
        return view('admin.inventory.settings.edit', compact('setting'));
    }

    public function update(UpdateInvoiceSettingRequest $request)
    {
        $setting = InvoiceSetting::get();

        $data = $request->only(['company_name', 'address', 'phone', 'email', 'footer_text', 'tax_rate']);
        $data['tax_rate'] = $request->input('tax_rate', 0) ?: 0;

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('invoice-assets', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($setting->signature_image_path) {
                Storage::disk('public')->delete($setting->signature_image_path);
            }
            $data['signature_image_path'] = $request->file('signature')->store('invoice-assets', 'public');
        }

        $setting->update($data);

        return redirect()->route('admin.inventory.settings.edit')
            ->with('success', 'Invoice settings saved successfully.');
    }
}
