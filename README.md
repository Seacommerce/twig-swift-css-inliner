twig-swift-css-inliner
======================

Create inlined html e-mails using your favorite template and mailer tool.

**Features:**
* Render e-mail templates using twig;
* Provide css though the same twig template;
* Provide any additional css from a file to load any base styles, for example;
* Optionally also provide a text/plain fallback body.


Installation
------------

Using composer

`composer require Seacommerce/twig-swift-css-inliner`


Examples
--------
Steps
1. Create a twig template for your e-mail body using the ```html``` block.
2. Optionally provide a subject using the ```subject``` block.
2. Optionally provide a text/plain body using the ```text``` block.
3. Provide CSS using the ```styles``` block.
4. Provide any additional CSS from a string or a file.
5. "Compile" the message.
6. Send it.

example.html.twig:

```twig
{% block subject %}
    Order confirmation #{{ orderNbr }}
{% endblock subject %}

{% block styles %}
    <style type="text/css">
        p {
            font-size: 16px;
        }
    </style>
{% endblock styles %}

{% block html %}
    <html>
    <body>
    <p>Dear {{ name }},<p>
    <p>Thank you for your order.</p>
    <table>
        <tr>
            <th>Order #</th>
            <td>{{ orderNbr }}</td>
        </tr>
        <tr>
            <th>Order date</th>
            <td>{{ orderDate|date('Y-m-d') }}</td>
        </tr>
        <tr>
            <th>Reference</th>
            <td>{{ reference }}</td>
        </tr>
    </table>
    <p class="greeting">
        Kind regards, <br />
        Inliners-R-us
    </p>
    </body>
    </html>

{% endblock html %}

{% block text %}
    Dear {{ name }},

    Thank you for your order.

    Order number: {{ orderNbr }}
    Order date: {{ orderDate|date('Y-m-d') }}
    Reference: {{ refference }}
{% endblock text %}
```

example.css
```css
body {
    background-color: aliceblue;
    padding: 10px;
    margin: 0;
}

table{
    width: 100%;
}

td, th {
    border: solid 1px #000;
}

th {
    text-align: left;
}

p {
    padding: 0;
    margin-bottom: 10px;
}

p.greeting {
    font-size: smaller;
    text-align: center;
}
```


example.php
```php

require_once('vendor/autoload.php');

use Seacommerce\TwigSwiftCssInliner\CssInliner;
use Twig_Environment;
use Twig_Loader_Filesystem;

$loader = new Twig_Loader_Filesystem('/Path/to/templates/folder');
$twig = new Twig_Environment($loader);
$inliner = new CssInliner($twig);
$additionalCssFile = '/path/to/additional.css';

$viewData = [
    'name' => 'Sil',
    'orderNbr' => 223423,
    'reference' => 'ABC12',
    'orderDate' => new \DateTime('2013-09-28', new \DateTimeZone('utc')),
];
$message = $inliner->createEmailFromTemplateFile('example.html.twig', $viewData, $additionalCssFile);

// send the message...

```

Output message

```eml
Message-ID: <adb9c4cf72fc807445f0d3cd4afc9db9@test.generated>
Date: Sat, 28 Sep 2013 00:00:00 +0000
Subject: Order confirmation #223423
From: 
MIME-Version: 1.0
Content-Type: multipart/alternative; boundary=__test_phpunit_aWSqkye88HQhRMbg


--__test_phpunit_aWSqkye88HQhRMbg
Content-Type: text/plain; charset=utf-8
Content-Transfer-Encoding: quoted-printable

    Dear Sil,

    Thank you for your order.

    Order number: 223=
423
    Order date: 2013-09-28
    Reference:=20


--__test_phpunit_aWSqkye88HQhRMbg
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: quoted-printable

<!DOCTYPE html>
<html>
<head><meta http-equiv=3D"Content-Type" content=
=3D"text/html; charset=3Dutf-8"></head>
    <body style=3D"background-col=
or: aliceblue; padding: 10px; margin: 0;">
    <p style=3D"padding: 0; ma=
rgin-bottom: 10px; font-size: 16px;">Dear Sil,</p>
<p style=3D"padding: 0=
; margin-bottom: 10px; font-size: 16px;">
    </p>
<p style=3D"padding:=
 0; margin-bottom: 10px; font-size: 16px;">Thank you for your order.</p>
=
    <table style=3D"width: 100%;">
        <tr>
            <th style=
=3D"border: solid 1px #000; text-align: left;">Order #</th>
            <=
td style=3D"border: solid 1px #000;">223423</td>
        </tr>
        =
<tr>
            <th style=3D"border: solid 1px #000; text-align: left;">=
Order date</th>
            <td style=3D"border: solid 1px #000;">2013-09=
-28</td>
        </tr>
        <tr>
            <th style=3D"border: =
solid 1px #000; text-align: left;">Reference</th>
            <td style=
=3D"border: solid 1px #000;">ABC12</td>
        </tr>
    </table>
  =
  <p class=3D"greeting" style=3D"padding: 0; margin-bottom: 10px; font-size=
: smaller; text-align: center;">
        Kind regards, <br>
        Inl=
iners-R-us
    </p>
    </body>
    </html>


--__test_phpunit_aWSqkye88HQhRMbg--

```


