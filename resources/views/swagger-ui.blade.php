<!DOCTYPE html>
<html>
<head>
    <title>SmartStock API Documentation</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.css">
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            color: #3b4151;
        }

        .topbar {
            background-color: #1e1e1e;
            padding: 10px 20px;
            border-bottom: 2px solid #fbbf24;
        }

        .topbar h1 {
            margin: 0;
            color: #fbbf24;
            font-size: 24px;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <h1>🚀 SmartStock API Documentation</h1>
    </div>
    
    <div id="swagger-ui"></div>

    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui-bundle.js" charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
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
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                docExpansion: "list",
                filter: true,
                showRequestHeaders: true,
                supportedSubmitMethods: [
                    'get',
                    'post',
                    'put',
                    'delete',
                    'patch',
                    'options',
                    'head'
                ]
            })

            window.ui = ui
        }
    </script>
</body>
</html>
