<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    private function normalizeKeyValuePairs(?array $pairs): array
    {
        if (!$pairs) {
            return [];
        }

        $normalized = [];

        // Handle associative array already in key => value format.
        $isDirect = collect($pairs)->every(function ($value, $key) {
            return !preg_match('/^(key|value)\d+$/', (string) $key);
        });

        if ($isDirect) {
            foreach ($pairs as $key => $value) {
                if (!empty($key) && $value !== null && $value !== '') {
                    $normalized[$key] = $value;
                }
            }

            return $normalized;
        }

        $keys = [];
        $values = [];

        foreach ($pairs as $key => $value) {
            if (preg_match('/^key(\d+)$/', (string) $key, $matches)) {
                $keys[$matches[1]] = $value;
            } elseif (preg_match('/^value(\d+)$/', (string) $key, $matches)) {
                $values[$matches[1]] = $value;
            }
        }

        foreach ($keys as $index => $pairKey) {
            $pairValue = $values[$index] ?? null;
            if (!empty($pairKey) && $pairValue !== null && $pairValue !== '') {
                $normalized[$pairKey] = $pairValue;
            }
        }

        return $normalized;
    }

    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('order')->get();
        return view('admin.settings.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.settings.payment-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|max:512',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'transaction_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'transaction_fee_fixed' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => strtolower($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'is_sandbox' => $request->has('is_sandbox') ? true : false,
            'transaction_fee_percentage' => $request->transaction_fee_percentage ?? 0,
            'transaction_fee_fixed' => $request->transaction_fee_fixed ?? 0,
            'instructions' => $request->instructions,
            'order' => PaymentMethod::max('order') + 1,
        ];

        // Handle credentials (JSON)
        if ($request->has('credentials')) {
            $credentials = $this->normalizeKeyValuePairs($request->credentials);
            $data['credentials'] = !empty($credentials) ? $credentials : null;
        }

        // Handle config (JSON)
        if ($request->has('config')) {
            $config = $this->normalizeKeyValuePairs($request->config);
            $data['config'] = !empty($config) ? $config : null;
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('payment-methods', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.settings.payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('admin.settings.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|max:512',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'transaction_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'transaction_fee_fixed' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => strtolower($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
            'is_sandbox' => $request->has('is_sandbox') ? true : false,
            'transaction_fee_percentage' => $request->transaction_fee_percentage ?? 0,
            'transaction_fee_fixed' => $request->transaction_fee_fixed ?? 0,
            'instructions' => $request->instructions,
        ];

        // Handle credentials
        if ($request->has('credentials')) {
            $credentials = $this->normalizeKeyValuePairs($request->credentials);
            $data['credentials'] = !empty($credentials) ? $credentials : null;
        }

        // Handle config
        if ($request->has('config')) {
            $config = $this->normalizeKeyValuePairs($request->config);
            $data['config'] = !empty($config) ? $config : null;
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            if ($paymentMethod->icon) {
                Storage::disk('public')->delete($paymentMethod->icon);
            }
            $data['icon'] = $request->file('icon')->store('payment-methods', 'public');
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.settings.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($paymentMethod->icon) {
            Storage::disk('public')->delete($paymentMethod->icon);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.settings.payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return redirect()->back()
            ->with('success', 'Payment method status updated.');
    }
}
