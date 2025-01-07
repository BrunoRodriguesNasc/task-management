<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }

        .swagger-ui .topbar {
            background-color: #1b1b1b;
        }

        .swagger-ui .info .title small.version-stamp {
            background-color: #1b1b1b;
        }

        .swagger-ui .opblock.opblock-get .opblock-summary-method {
            background-color: #61affe;
        }

        .swagger-ui .opblock.opblock-post .opblock-summary-method {
            background-color: #49cc90;
        }

        .swagger-ui .opblock.opblock-put .opblock-summary-method {
            background-color: #fca130;
        }

        .swagger-ui .opblock.opblock-delete .opblock-summary-method {
            background-color: #f93e3e;
        }

        .swagger-ui .btn.execute {
            background-color: #4990e2;
            border-color: #4990e2;
        }

        .swagger-ui .btn.execute:hover {
            background-color: #357abd;
            border-color: #357abd;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.18.3/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "/docs/openapi.yaml",
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
                persistAuthorization: true,
                theme: {
                    colors: {
                        primary: {
                            main: '#1b1b1b'
                        }
                    }
                }
            });
            window.ui = ui;
        };
    </script>
</body>
</html>