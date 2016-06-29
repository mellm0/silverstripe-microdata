# Microdata Provider

This module allows you to add Microdata via your templates. This module uses **[schema.org](http://www.schema.org/)**, but you can easily change the url using the yaml config.

## About microdata

Microdata are additional attributes and tags added to describe and provide additional information about the content of your web page.

Some search engines (in particular Google) have adopted this technology and use it to enhance their search engine results (and the
appearance of your search engine result as well!)

For more information see: [Google Webmaster Tools - About microdata](https://support.google.com/webmasters/answer/176035)

## Requirements

*  SilverStripe 3.1

## Author

*  Mellisa Hankins [mell@milkywaymultimedia.com.au]

## Install using composer

```
composer require milkyway/silverstripe-microdata:*
```

## Example Code

### Page.ss
```
<div class="product" $microData.Product>
    <div class="description" $microData.Description>Description</div>
    <div class="pricing" $microData.Offers>
        <strong $microData.Price>$Price</strong>
        <% if $inStock %>$microData.InStock<% else %>$microData.NoStock<% else %>
    </div>
</div>

<% loop $Reviews %>
<div class="review" $microData.getAttributes('review','Review',1)>
    $Content
    $microData.metaTag('rating','9.5/10')
</div>
<% end_loop %>
```

This module adds a new variable: **$microData** to the global scope that creates all the necessary attributes. Some are predefined,
but there are methods provided allowing you to add custom attributes. If you use a variable that does not exist on the MicrodataProvider class,
it will assume it is an itemprop attribute and will add the attribute accordingly.