# Extension README

## How-to Include Markdown

This extension looks for HTML elements of class `markdown-body` that have a `data-docroot` attribute. For each such element, the relative images and links included in the document are *fixed* so that the document is functional within the page it's loaded. Links to relative Markdown documents will automatically load inline (adding class `loading` in the process), while other local and external links will open in a new browser tab/window.

### Markup Only
The simplest way to include a Markdown document is in straight HTML markup.

```html
<div class="markdown-body ext-readme"
  data-extkey="com.klangsoft.extreadme"
  data-extreadme="docs/simple.md"
  data-docroot
>
  You can specify default content that will be replaced by the Markdown document if this extension is installed and enabled. You might want to provide a link to where to view the document online and/or advise on installing this extension. The document is loaded after the initial page load, via an AJAX request.
</div>
```
`markdown-body` - a required class for this extension to kick into action.

`ext-readme` - an optional class that makes a good base for a document; floats right, 50% width, 75% vertical height, vertical scroll.

`data-extkey` - the key of the extension that contains the document to be loaded.

`data-extreadme` - the document to be loaded.

`data-docroot` - base URL for the Markdown document, used to fix local images and relative links. When the document is **not** preloaded, as in this case, this attribute need only be specified.

The above will render the document you're reading right now. Include something like that in your page/form template.


### A Programmatic Approach

```php
try {
  $api = civicrm_api3('Extension', 'readme', [
    'key' => 'com.klangsoft.extreadme',
    'readme' => 'docs/simple.md'
  ]);
  
  if (!$api['is_error']) {
    // you could inject some markup with the returned values
    CRM_Core_Region::instance('page-body')->add([
      'markup' => "<div class=\"markdown-body ext-readme\" data-extkey=\"com.klangsoft.extreadme\" data-docroot=\"{$api['root']}\">{$api['html']}</div>"
    ]);
  
    // or you could assign the values to the template ($form/$page)...
    $form->assign('extreadme_docroot', $api['root']);
    $form->assign('extreadme_content', $api['html']);
    // ...and then render them there with {$extreadme_docroot} and {$extreadme_content}
  }
}
catch (Exception $e) {
  // do nothing if the Extension README extension is not installed (i.e. api not defined)
}
```

The above produces the same result as the markup only approach, but is part of the initial page load.

---

[README](../README.md)