@extends('layouts.master')

@section('title')
    {{ __('Instructions') }}
@endsection

@section('main_content')
<div class="erp-table-section domain-instructions">
    <div class="container-fluid">
        <div class="mt-2">
            <div class="accordion" id="codeAccordion">

                <!-- cPanel Domain Setup -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="headingCpanel">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseCpanel" aria-expanded="true"
                                aria-controls="collapseCpanel">
                            🌐 {{ __('cPanel Domain Setup Instructions') }}
                        </button>
                    </h6>
                    <div id="collapseCpanel" class="accordion-collapse collapse show"
                         aria-labelledby="headingCpanel" data-bs-parent="#codeAccordion">
                        <div class="accordion-body">
                            <h5 class="mb-3 text-primary fw-bold">{{ __('How to Add a Domain in cPanel') }}</h5>
                            <ol class="steps-list ms-3">
                                <li>{{ __('Login to your') }} <strong>{{ __('cPanel') }}</strong> {{ __('account') }}</li>
                                <li>{{ __('Go to') }} <code>{{ __('Domains → Addon Domains') }}</code></li>
                                <li>{{ __('Enter your new domain') }} (e.g., <code>pospro.com</code>)</li>
                                <li>{{ __('Leave the auto-generated subdirectory and FTP as default') }}</li>
                                <li>{{ __('Click') }} <strong>{{ __('Add Domain') }}</strong></li>
                                <li>{{ __('Update your domain’s DNS to point to your cPanel nameservers') }}</li>
                            </ol>

                            <div class="alert alert-info mt-3">
                                💡 <strong>{{ __('Tip') }}:</strong> {{ __('If you need SSL, go to') }}
                                <code>{{ __('SSL/TLS') }}</code> {{ __('in cPanel and issue a certificate for your new domain (Let’s Encrypt is free).') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- hPanel Domain Setup -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="headingHpanel">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseHpanel" aria-expanded="false"
                                aria-controls="collapseHpanel">
                            🌐 {{ __('hPanel (Hostinger) Domain Setup Instructions') }}
                        </button>
                    </h6>
                    <div id="collapseHpanel" class="accordion-collapse collapse"
                         aria-labelledby="headingHpanel" data-bs-parent="#codeAccordion">
                        <div class="accordion-body">
                            <h5 class="mb-3 text-primary fw-bold">{{ __('How to Add a Domain in hPanel') }}</h5>
                            <ol class="steps-list ms-3">
                                <li>{{ __('Login to your') }} <strong>{{ __('hPanel') }}</strong> {{ __('account') }}</li>
                                <li>{{ __('Go to') }} <code>{{ __('Websites → Manage') }}</code> {{ __('for your hosting plan') }}</li>
                                <li>{{ __('Click on') }} <strong>{{ __('Domains') }}</strong> → <strong>{{ __('Addon Domains') }}</strong></li>
                                <li>{{ __('Enter your domain name (e.g.,') }} <code>pospro.com</code>)</li>
                                <li>{{ __('Choose the directory (or keep the default)') }}</li>
                                <li>{{ __('Click') }} <strong>{{ __('Add Domain') }}</strong></li>
                                <li>{{ __('Now go to') }} <code>{{ __('DNS / Nameservers') }}</code> {{ __('and update your domain’s DNS to point to Hostinger nameservers') }}</li>
                            </ol>

                            <div class="alert alert-info mt-3">
                                💡 <strong>{{ __('Tip') }}:</strong> {{ __('For SSL, go to') }}
                                <code>{{ __('Websites → Manage → SSL') }}</code> {{ __('and activate the free SSL certificate (Let’s Encrypt).') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apache VPS -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="headingApache">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseApache" aria-expanded="false"
                                aria-controls="collapseApache">
                            ⚙️ {{ __('Apache VPS Configuration') }}
                        </button>
                    </h6>
                    <div id="collapseApache" class="accordion-collapse collapse"
                         aria-labelledby="headingApache" data-bs-parent="#codeAccordion">
                        <div class="accordion-body">
                            <h5 class="text-primary fw-bold mb-3">{{ __('Apache Wildcard Domain Setup') }}</h5>
                            <p>{{ __('Create a config file in') }} <code>/etc/apache2/sites-available/</code>:</p>

<pre class="config-file">
&lt;VirtualHost *:80&gt;
    ServerName default
    ServerAlias *
    UseCanonicalName Off
    DocumentRoot /var/www/your_project/public
    DirectoryIndex index.php

    &lt;Directory /var/www/your_project/public&gt;
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    &lt;/Directory&gt;

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
&lt;/VirtualHost&gt;

# SSL Configuration
&lt;VirtualHost *:443&gt;
    ServerName default
    ServerAlias *
    UseCanonicalName Off
    DocumentRoot /var/www/your_project/public

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/example.com.crt
    SSLCertificateKeyFile /etc/ssl/private/example.com.key
    SSLCertificateChainFile /etc/ssl/certs/example.ca-bundle
&lt;/VirtualHost&gt;
</pre>

                            <p class="mt-3">{{ __('Enable the site and reload Apache') }}:</p>
                            <code class="d-block mb-2">sudo a2ensite your_conf_file.conf</code>
                            <code class="d-block mb-2">sudo systemctl reload apache2</code>

                            <div class="alert alert-warning mt-4">
                                ⚠️ <strong>{{ __('Important') }}:</strong>
                                <ul class="mb-0">
                                    <li>{{ __('Always test config before reload') }}</li>
                                    <li>{{ __('Replace') }} <code>example.com</code> {{ __('with your actual domain') }}</li>
                                    <li>{{ __('Ensure correct file permissions (chown/chmod)') }}</li>
                                    <li>{{ __('Use') }} <strong>{{ __('Certbot') }}</strong> {{ __('for free SSL') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nginx VPS -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="headingNginx">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseNginx" aria-expanded="false"
                                aria-controls="collapseNginx">
                            ⚙️ {{ __('Nginx VPS Configuration') }}
                        </button>
                    </h6>
                    <div id="collapseNginx" class="accordion-collapse collapse"
                         aria-labelledby="headingNginx" data-bs-parent="#codeAccordion">
                        <div class="accordion-body">
                            <h5 class="text-primary fw-bold">{{ __('Nginx Wildcard Domain Setup') }}</h5>
                            <p>{{ __('Create a config file in') }} <code>/etc/nginx/sites-available/</code>:</p>

<pre class="config-file">
server {
    listen 80;
    listen [::]:80;

    server_name _;
    root /var/www/your_project/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}

# SSL Configuration
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    ssl_certificate /etc/ssl/certs/example.com.crt;
    ssl_certificate_key /etc/ssl/private/example.com.key;

    # ... repeat same config as port 80
}
</pre>

                            <p class="mt-3">{{ __('Enable the site and reload Nginx') }}:</p>
                            <code class="d-block mb-2">sudo ln -s /etc/nginx/sites-available/your_config /etc/nginx/sites-enabled/</code>
                            <code class="d-block mb-2">sudo nginx -t && sudo systemctl reload nginx</code>

                            <div class="alert alert-warning mt-4">
                                ⚠️ <strong>{{ __('Important') }}:</strong>
                                <ul class="mb-0">
                                    <li>{{ __('Always test config before reload') }}</li>
                                    <li>{{ __('Replace') }} <code>example.com</code> {{ __('with your actual domain') }}</li>
                                    <li>{{ __('Ensure correct file permissions') }}</li>
                                    <li>{{ __('Use') }} <strong>{{ __('Certbot') }}</strong> {{ __('for free SSL') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
