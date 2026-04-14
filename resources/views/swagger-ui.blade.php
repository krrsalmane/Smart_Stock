<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStock API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0f0f1a;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Custom header */
        .custom-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 30px 40px;
            border-bottom: 2px solid rgba(0, 212, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .custom-header .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00d4ff, #7b2ff7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 212, 255, 0.3);
        }

        .custom-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .custom-header h1 span {
            background: linear-gradient(90deg, #00d4ff, #7b2ff7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .custom-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 4px;
        }

        /* Override Swagger UI styles for dark theme */
        .swagger-ui {
            background: #0f0f1a !important;
        }

        .swagger-ui .wrapper {
            max-width: 1400px;
            padding: 20px 40px;
        }

        /* Hide default Swagger info block (we have custom header) */
        .swagger-ui .info {
            display: none;
        }

        /* Tag groups */
        .swagger-ui .opblock-tag-section {
            margin-bottom: 8px;
        }

        .swagger-ui .opblock-tag {
            color: #e0e0e0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 14px 20px !important;
            font-size: 18px !important;
            background: rgba(255, 255, 255, 0.03) !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .swagger-ui .opblock-tag:hover {
            background: rgba(255, 255, 255, 0.06) !important;
        }

        .swagger-ui .opblock-tag small {
            color: rgba(255, 255, 255, 0.45) !important;
        }

        /* Operation blocks */
        .swagger-ui .opblock {
            border-radius: 8px !important;
            margin: 4px 0 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: none !important;
        }

        .swagger-ui .opblock .opblock-summary {
            border: none !important;
            padding: 10px 16px !important;
        }

        .swagger-ui .opblock .opblock-summary-method {
            border-radius: 6px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            min-width: 70px !important;
            padding: 6px 12px !important;
        }

        .swagger-ui .opblock .opblock-summary-path {
            color: #e0e0e0 !important;
            font-size: 14px !important;
        }

        .swagger-ui .opblock .opblock-summary-description {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 13px !important;
        }

        /* GET - blue */
        .swagger-ui .opblock-get {
            background: rgba(97, 175, 254, 0.08) !important;
            border-color: rgba(97, 175, 254, 0.2) !important;
        }
        .swagger-ui .opblock-get .opblock-summary-method { background: #61affe !important; }
        .swagger-ui .opblock-get .opblock-summary { border-color: rgba(97, 175, 254, 0.2) !important; }

        /* POST - green */
        .swagger-ui .opblock-post {
            background: rgba(73, 204, 144, 0.08) !important;
            border-color: rgba(73, 204, 144, 0.2) !important;
        }
        .swagger-ui .opblock-post .opblock-summary-method { background: #49cc90 !important; }

        /* PUT - orange */
        .swagger-ui .opblock-put {
            background: rgba(252, 161, 48, 0.08) !important;
            border-color: rgba(252, 161, 48, 0.2) !important;
        }
        .swagger-ui .opblock-put .opblock-summary-method { background: #fca130 !important; }

        /* DELETE - red */
        .swagger-ui .opblock-delete {
            background: rgba(249, 62, 62, 0.08) !important;
            border-color: rgba(249, 62, 62, 0.2) !important;
        }
        .swagger-ui .opblock-delete .opblock-summary-method { background: #f93e3e !important; }

        /* Expanded content */
        .swagger-ui .opblock-body {
            background: rgba(0, 0, 0, 0.3) !important;
        }

        .swagger-ui .opblock-body pre,
        .swagger-ui .opblock-body pre span {
            color: #e0e0e0 !important;
        }

        .swagger-ui table thead tr th,
        .swagger-ui table thead tr td,
        .swagger-ui .parameter__name,
        .swagger-ui .parameter__type,
        .swagger-ui .response-col_status,
        .swagger-ui .response-col_description,
        .swagger-ui .response-col_links {
            color: #c0c0c0 !important;
        }

        .swagger-ui .parameter__name.required span {
            color: #f93e3e !important;
        }

        .swagger-ui .parameter__name.required::after {
            color: rgba(249, 62, 62, 0.6) !important;
        }

        .swagger-ui table tbody tr td {
            color: #b0b0b0 !important;
            border-color: rgba(255, 255, 255, 0.06) !important;
        }

        .swagger-ui .model-title,
        .swagger-ui .model {
            color: #c0c0c0 !important;
        }

        .swagger-ui section.models {
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 8px !important;
            background: rgba(255, 255, 255, 0.02) !important;
        }

        .swagger-ui section.models h4 {
            color: #e0e0e0 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        .swagger-ui .model-box {
            background: rgba(0, 0, 0, 0.2) !important;
        }

        .swagger-ui .prop-type {
            color: #49cc90 !important;
        }

        /* Authorize button */
        .swagger-ui .btn.authorize {
            color: #49cc90 !important;
            border-color: #49cc90 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 8px 24px !important;
            transition: all 0.2s ease !important;
        }

        .swagger-ui .btn.authorize:hover {
            background: rgba(73, 204, 144, 0.1) !important;
        }

        .swagger-ui .btn.authorize svg {
            fill: #49cc90 !important;
        }

        /* Execute button */
        .swagger-ui .btn.execute {
            background: linear-gradient(135deg, #00d4ff, #7b2ff7) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 10px 28px !important;
        }

        /* Try it out button */
        .swagger-ui .btn.try-out__btn {
            border-color: rgba(0, 212, 255, 0.5) !important;
            color: #00d4ff !important;
            border-radius: 6px !important;
        }

        .swagger-ui .btn.try-out__btn:hover {
            background: rgba(0, 212, 255, 0.1) !important;
        }

        /* Input fields */
        .swagger-ui input[type=text],
        .swagger-ui textarea,
        .swagger-ui select {
            background: rgba(255, 255, 255, 0.06) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #e0e0e0 !important;
            border-radius: 6px !important;
        }

        .swagger-ui input[type=text]:focus,
        .swagger-ui textarea:focus {
            border-color: #00d4ff !important;
            box-shadow: 0 0 0 2px rgba(0, 212, 255, 0.15) !important;
        }

        .swagger-ui select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23999' d='M6 8L1 3h10z'/%3E%3C/svg%3E") !important;
        }

        /* Response section */
        .swagger-ui .responses-inner h4,
        .swagger-ui .responses-inner h5,
        .swagger-ui .response-col_status {
            color: #c0c0c0 !important;
        }

        .swagger-ui .response-col_description__inner p {
            color: #a0a0a0 !important;
        }

        .swagger-ui .highlight-code pre {
            background: rgba(0, 0, 0, 0.4) !important;
            border-radius: 8px !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Dialog / Modal */
        .swagger-ui .dialog-ux .modal-ux {
            background: #1a1a2e !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
        }

        .swagger-ui .dialog-ux .modal-ux-header h3 {
            color: #e0e0e0 !important;
        }

        .swagger-ui .dialog-ux .modal-ux-content p,
        .swagger-ui .dialog-ux .modal-ux-content label {
            color: #b0b0b0 !important;
        }

        .swagger-ui .auth-wrapper {
            display: flex;
            justify-content: flex-end;
        }

        /* Scheme container */
        .swagger-ui .scheme-container {
            background: rgba(255, 255, 255, 0.03) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            box-shadow: none !important;
            padding: 16px 20px !important;
        }

        /* SVG arrows */
        .swagger-ui svg.arrow {
            fill: #a0a0a0 !important;
        }

        /* Loading */
        .swagger-ui .loading-container {
            padding: 60px 0;
        }

        .swagger-ui .loading-container .loading::after {
            color: #00d4ff !important;
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <div class="logo">📦</div>
        <div>
            <h1>Smart<span>Stock</span> API</h1>
            <p>v1.0.0 — Intelligent Warehouse & Inventory Management System</p>
        </div>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js"></script>
    <script>
        SwaggerUIBundle({
            url: "/api/documentation",
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout",
            docExpansion: "list",
            filter: true,
            tryItOutEnabled: true,
            defaultModelsExpandDepth: 1,
            defaultModelExpandDepth: 2,
            persistAuthorization: true,
            syntaxHighlight: {
                activated: true,
                theme: "monokai"
            }
        });
    </script>
</body>
</html>
