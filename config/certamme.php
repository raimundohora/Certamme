<?php

return [

    /*
     | Domínio-base da plataforma. Os portais das organizadoras ficam em
     | {slug}.{base_domain} (ex.: access.certamme.com.br). Requer DNS wildcard
     | (*.base_domain) e certificado TLS wildcard no proxy (Caddy).
     */
    'base_domain' => env('CERTAMME_BASE_DOMAIN', 'certamme.sistemasdh.com.br'),

    /*
     | Organizadora usada quando o host é o próprio domínio-base (sem subdomínio)
     | ou um host legado. Em branco, cai na primeira organizadora ativa.
     */
    'default_tenant' => env('CERTAMME_DEFAULT_TENANT'),

    /*
     | Quando true, o domínio-base puro (apex/www) exibe a VITRINE do produto
     | Certamme em vez do portal de uma organizadora. Ligar só quando os portais
     | das organizadoras já estiverem acessíveis por subdomínio (DNS wildcard no ar).
     */
    'landing_on_apex' => (bool) env('CERTAMME_LANDING_ON_APEX', false),

];
