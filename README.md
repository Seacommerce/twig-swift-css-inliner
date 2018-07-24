twig-swift-css-inliner
======================

Create inlined html e-mails using your favorite template and mailer tool.


Installation
------------

Using composer

* `composer require Seacommerce/twig-swift-css-inliner`

Dependencies
------------
This component depends on the following packages:
* twig/twig ^2.5
* swiftmailer/swiftmailer ^6.1
* pelago/emogrifier ^2.0
* symfony/dom-crawler ^4.1
* symfony/css-selector ^4.1

Examples
--------

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