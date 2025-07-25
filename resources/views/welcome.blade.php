<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $company->short_name }}</title>
		<link rel="icon" type="image/png" href="{{ $company->small_light_logo_url }}">
		<meta name="msapplication-TileImage" href="{{ $company->small_light_logo_url }}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700&display=swap">

        <style>
            html {
                font-family: sans-serif;
            }

            .loading-app-container {
                height: 100%;
                width: 100%;
                display: flex;
                position: fixed;
                align-items: center;
                justify-content: center;
                background: #f8f8ff;
            }

            .ant-result {
                padding: 48px 32px;
            }

            .ant-result-icon {
                margin-bottom: 24px;
                text-align: center;
            }

            .ant-result-title {
                color: #000000d9;
                font-size: 24px;
                line-height: 1.8;
                text-align: center;
            }

            .ant-result-extra {
                margin: 24px 0 0;
                text-align: center;
            }


            .anticon-spin,.anticon-spin:before {
                animation: loadingCircle 1s linear infinite;
                display: inline-block
            }

            @keyframes loadingCircle {
                to {
                    transform: rotate(1turn)
                }
            }
        </style>
    </head>
    <body>
        <div id="app">
            <div class="loading-app-container">
                <div class="ant-result ant-result-info">
                    <div class="ant-result-icon">
                        <img src="{{ $loadingImage }}"  style="width: 150px;">
                    </div>
                    @if(isset($loadingLangMessageLang))
                    <div class="ant-result-title">
                        <span style="color: rgb(118, 118, 227);">
                            {{ $loadingLangMessageLang }}
                        </span>
                    </div>
                    @endif
                    <div class="ant-result-extra">
                        <span role="img" aria-label="sync" class="anticon anticon-sync" style="font-size: 38px; color: rgb(82, 84, 207);">
                            <svg focusable="false" class="anticon-spin" data-icon="sync" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M168 504.2c1-43.7 10-86.1 26.9-126 17.3-41 42.1-77.7 73.7-109.4S337 212.3 378 195c42.4-17.9 87.4-27 133.9-27s91.5 9.1 133.8 27A341.5 341.5 0 01755 268.8c9.9 9.9 19.2 20.4 27.8 31.4l-60.2 47a8 8 0 003 14.1l175.7 43c5 1.2 9.9-2.6 9.9-7.7l.8-180.9c0-6.7-7.7-10.5-12.9-6.3l-56.4 44.1C765.8 155.1 646.2 92 511.8 92 282.7 92 96.3 275.6 92 503.8a8 8 0 008 8.2h60c4.4 0 7.9-3.5 8-7.8zm756 7.8h-60c-4.4 0-7.9 3.5-8 7.8-1 43.7-10 86.1-26.9 126-17.3 41-42.1 77.8-73.7 109.4A342.45 342.45 0 01512.1 856a342.24 342.24 0 01-243.2-100.8c-9.9-9.9-19.2-20.4-27.8-31.4l60.2-47a8 8 0 00-3-14.1l-175.7-43c-5-1.2-9.9 2.6-9.9 7.7l-.7 181c0 6.7 7.7 10.5 12.9 6.3l56.4-44.1C258.2 868.9 377.8 932 512.2 932c229.2 0 415.5-183.7 419.8-411.8a8 8 0 00-8-8.2z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <script>
            window.config = {
                'path': '{{ url('/') }}',
                'download_lang_csv_url': "{{ route('api.extra.langs.download') }}",
                'invoice_url': "{{ route('api.extra.pdf.v1', '') }}",
                'pos_invoice_css': "{{ asset('css/pos_invoice_css.css') }}",
                'verify_purchase_background': "{{ asset('images/verify_purchase_background.svg') }}",
                'login_background': "{{ asset('images/login_background.svg') }}",
                'product_sample_file': "{{ asset('images/sample_products.csv') }}",
                'category_sample_file': "{{ asset('images/sample_categories.csv') }}",
                'brand_sample_file': "{{ asset('images/sample_brands.csv') }}",
                'customer_sample_file': "{{ asset('images/sample_customers.csv') }}",
                'supplier_sample_file': "{{ asset('images/sample_suppliers.csv') }}",
                'staff_member_sample_file': "{{ asset('images/sample_staff_members.csv') }}",
                'translatioins_sample_file': "{{ asset('images/sample_translations.csv') }}",
                'perPage': 10,
				'product_name': "{{ $appName }}",
				'product_version': "{{ $appVersion }}",
				'modules': @json($enabledModules),
				'installed_modules': @json($installedModules),
				'theme_mode': "{{ $themeMode }}",
				'appChecking': true,
				'app_version': "{{ $appVersion }}",
				'app_env': "{{ $appEnv }}",
				'app_type': "{{ $appType }}",
				'frontStoreWarehouse': @json($frontStoreWarehouse),
				'frontStoreCompany': @json($frontStoreCompany),
				'frontStoreSettings': @json($frontStoreSettings),
				'warehouseCurrency': @json($warehouseCurrency),
                'defaultLangKey': "{{ $defaultLangKey }}",
            };
        </script>
        @if(app_type() == 'saas')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        @endif

        @vite('resources/js/app.js')
    </body>
</html>
