@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px" id="settingsTabs" role="tablist">
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600 tab-button active"
                    data-tab="general" type="button">
                General Settings
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="theme" type="button">
                Theme Settings
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="payment" type="button">
                Payment Methods
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="languages" type="button">
                Languages
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="email" type="button">
                Email Settings
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="video" type="button">
                Video Settings
            </button>
            <button class="px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-button"
                    data-tab="notifications" type="button">
                Notifications
            </button>
        </nav>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
        @csrf
        @method('PUT')

        <!-- General Settings Tab -->
        <div id="tab-general" class="tab-content p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">General Settings</h2>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                        <input type="text" name="system_settings[site_name]"
                               value="{{ \App\Models\SystemSetting::getValue('site_name', config('app.name')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                        <input type="email" name="system_settings[site_email]"
                               value="{{ \App\Models\SystemSetting::getValue('site_email', config('mail.from.address')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                        <input type="url" name="system_settings[site_url]"
                               value="{{ \App\Models\SystemSetting::getValue('site_url', config('app.url')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
                        <input type="text" name="system_settings[default_currency]"
                               value="{{ \App\Models\SystemSetting::getValue('default_currency', 'PKR') }}"
                               placeholder="PKR, USD, EUR"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select name="system_settings[timezone]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Asia/Karachi" {{ \App\Models\SystemSetting::getValue('timezone', 'Asia/Karachi') == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi (PKT)</option>
                            <option value="UTC" {{ \App\Models\SystemSetting::getValue('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ \App\Models\SystemSetting::getValue('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                        <select name="system_settings[date_format]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Y-m-d" {{ \App\Models\SystemSetting::getValue('date_format', 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d/m/Y" {{ \App\Models\SystemSetting::getValue('date_format') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="m/d/Y" {{ \App\Models\SystemSetting::getValue('date_format') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                    <textarea name="system_settings[site_description]" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ \App\Models\SystemSetting::getValue('site_description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Theme Settings Tab -->
        <div id="tab-theme" class="tab-content p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Theme Settings</h2>

            <div class="space-y-6">
                @if(isset($themeSettings['branding']))
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-3">Branding</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($themeSettings['branding'] ?? [] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $setting['name'] }}
                                        @if($setting['description'])
                                            <span class="text-xs text-gray-500">({{ $setting['description'] }})</span>
                                        @endif
                                    </label>
                                    @if($setting['type'] === 'color')
                                        <input type="color" name="theme_settings[{{ $setting['key'] }}]"
                                               value="{{ $setting['value'] ?? '#3B82F6' }}"
                                               class="w-full h-10 border border-gray-300 rounded-lg">
                                    @elseif($setting['type'] === 'image')
                                        <input type="file" name="theme_settings[{{ $setting['key'] }}]"
                                               accept="image/*"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        @if($setting['value'])
                                            <img src="{{ \Storage::url($setting['value']) }}" alt="{{ $setting['name'] }}" class="mt-2 h-16">
                                        @endif
                                    @else
                                        <input type="text" name="theme_settings[{{ $setting['key'] }}]"
                                               value="{{ $setting['value'] ?? '' }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($themeSettings['layout']))
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-3">Layout</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($themeSettings['layout'] ?? [] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['name'] }}</label>
                                    <input type="{{ $setting['type'] === 'number' ? 'number' : 'text' }}"
                                           name="theme_settings[{{ $setting['key'] }}]"
                                           value="{{ $setting['value'] ?? '' }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Methods Tab -->
        <div id="tab-payment" class="tab-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Payment Methods</h2>
                <a href="{{ route('admin.settings.payment-methods.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Add New Payment Method
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Fee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($paymentMethods as $method)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                @if($method->icon)
                                    <img src="{{ \Storage::url($method->icon) }}" alt="{{ $method->name }}" class="h-8 w-8 mr-2">
                                @endif
                                        <span class="font-medium">{{ $method->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $method->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $method->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $method->is_sandbox ? 'Sandbox' : 'Production' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $method->transaction_fee_percentage }}% + {{ number_format($method->transaction_fee_fixed, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.settings.payment-methods.edit', $method->id) }}"
                                       class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.settings.payment-methods.toggle-status', $method->id) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="text-{{ $method->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $method->is_active ? 'yellow' : 'green' }}-900">
                                            {{ $method->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.settings.payment-methods.destroy', $method->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-3">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No payment methods found. <a href="{{ route('admin.settings.payment-methods.create') }}" class="text-blue-600">Add one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Languages Tab -->
        <div id="tab-languages" class="tab-content p-6 hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Languages</h2>
                <a href="{{ route('admin.settings.languages.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Add New Language
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flag</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Direction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($languages as $language)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-2xl">{{ $language->flag ?? '🌐' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ $language->name }}</div>
                                    @if($language->native_name)
                                        <div class="text-sm text-gray-500">{{ $language->native_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($language->code) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($language->direction) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $language->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $language->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($language->is_default)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Default</span>
                                    @else
                                        <form action="{{ route('admin.settings.languages.set-default', $language->id) }}"
                                              method="POST" class="inline">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">Set Default</button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.settings.languages.edit', $language->id) }}"
                                       class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.settings.languages.toggle-status', $language->id) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="text-{{ $language->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $language->is_active ? 'yellow' : 'green' }}-900">
                                            {{ $language->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if(!$language->is_default)
                                        <form action="{{ route('admin.settings.languages.destroy', $language->id) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this language?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 ml-3">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No languages found. <a href="{{ route('admin.settings.languages.create') }}" class="text-blue-600">Add one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Email Settings Tab -->
        <div id="tab-email" class="tab-content p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Email Settings</h2>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver</label>
                        <select name="system_settings[mail_driver]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="smtp" {{ \App\Models\SystemSetting::getValue('mail_driver', 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="mailgun" {{ \App\Models\SystemSetting::getValue('mail_driver') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ \App\Models\SystemSetting::getValue('mail_driver') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Host</label>
                        <input type="text" name="system_settings[mail_host]"
                               value="{{ \App\Models\SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Port</label>
                        <input type="number" name="system_settings[mail_port]"
                               value="{{ \App\Models\SystemSetting::getValue('mail_port', config('mail.mailers.smtp.port')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Username</label>
                        <input type="text" name="system_settings[mail_username]"
                               value="{{ \App\Models\SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Password</label>
                        <input type="password" name="system_settings[mail_password]"
                               placeholder="{{ \App\Models\SystemSetting::getValue('mail_password') ? 'Password is set (leave blank to keep current)' : 'Enter mail password' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               autocomplete="new-password">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Encryption</label>
                        <select name="system_settings[mail_encryption]"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="tls" {{ \App\Models\SystemSetting::getValue('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ \App\Models\SystemSetting::getValue('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="">None</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Email Address</label>
                    <input type="email" name="system_settings[mail_from_address]"
                           value="{{ \App\Models\SystemSetting::getValue('mail_from_address', config('mail.from.address')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                    <input type="text" name="system_settings[mail_from_name]"
                           value="{{ \App\Models\SystemSetting::getValue('mail_from_name', config('mail.from.name')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Video Settings Tab -->
        <div id="tab-video" class="tab-content p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Video Settings</h2>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">YouTube API Key</label>
                        <input type="password" name="system_settings[youtube_api_key]"
                               placeholder="{{ \App\Models\SystemSetting::getValue('youtube_api_key') ? 'API key is set (leave blank to keep current)' : 'Enter YouTube API key' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               autocomplete="off">
                        <p class="text-xs text-gray-500 mt-1">Get your API key from Google Cloud Console. Leave blank to keep current key.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bunny Stream API Key</label>
                        <input type="password" name="system_settings[bunny_api_key]"
                               placeholder="{{ \App\Models\SystemSetting::getValue('bunny_api_key') ? 'API key is set (leave blank to keep current)' : 'Enter Bunny Stream API key' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               autocomplete="off">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current API key</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bunny Stream Library ID</label>
                        <input type="text" name="system_settings[bunny_library_id]"
                               value="{{ \App\Models\SystemSetting::getValue('bunny_library_id', config('services.bunny.library_id')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bunny CDN Hostname</label>
                        <input type="text" name="system_settings[bunny_cdn_hostname]"
                               value="{{ \App\Models\SystemSetting::getValue('bunny_cdn_hostname', config('services.bunny.cdn_hostname')) }}"
                               placeholder="your-cdn.b-cdn.net"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Video Upload Size (MB)</label>
                    <input type="number" name="system_settings[max_video_upload_size]"
                           value="{{ \App\Models\SystemSetting::getValue('max_video_upload_size', 100) }}"
                           min="1" max="1024"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Video Formats</label>
                    <input type="text" name="system_settings[allowed_video_formats]"
                           value="{{ \App\Models\SystemSetting::getValue('allowed_video_formats', 'mp4,avi,mov,wmv,flv') }}"
                           placeholder="mp4,avi,mov"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Comma-separated list of allowed video formats</p>
                </div>
            </div>
        </div>

        <!-- Notifications Tab (Admin email preferences) -->
        <div id="tab-notifications" class="tab-content p-6 hidden">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Notification Preferences</h2>
            <p class="text-sm text-gray-600 mb-4">Choose which emails you receive as an administrator.</p>
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="hidden" name="notification_settings[email_new_students]" value="0">
                    <input type="checkbox" name="notification_settings[email_new_students]" value="1" @checked($adminNotification->email_new_students ?? true) class="mr-2 rounded border-gray-300">
                    <span>Email when a new student is registered or added</span>
                </label>
                <label class="flex items-center">
                    <input type="hidden" name="notification_settings[email_new_teachers]" value="0">
                    <input type="checkbox" name="notification_settings[email_new_teachers]" value="1" @checked($adminNotification->email_new_teachers ?? true) class="mr-2 rounded border-gray-300">
                    <span>Email when a new teacher is added</span>
                </label>
                <label class="flex items-center">
                    <input type="hidden" name="notification_settings[email_new_courses]" value="0">
                    <input type="checkbox" name="notification_settings[email_new_courses]" value="1" @checked($adminNotification->email_new_courses ?? true) class="mr-2 rounded border-gray-300">
                    <span>Email when a new course is created</span>
                </label>
                <label class="flex items-center">
                    <input type="hidden" name="notification_settings[email_course_updates]" value="0">
                    <input type="checkbox" name="notification_settings[email_course_updates]" value="1" @checked($adminNotification->email_course_updates ?? true) class="mr-2 rounded border-gray-300">
                    <span>Email when a course is updated (chapters, lessons, details)</span>
                </label>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Save All Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Add active class to clicked button and show corresponding content
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + targetTab).classList.remove('hidden');
        });
    });
});
</script>
@endpush
@endsection
