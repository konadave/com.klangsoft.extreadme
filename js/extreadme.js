(function($) {

  const extreadme = {

    /**
     * Fix images and links so the document works.
     * 
     * @param {jquery} $md A jquery object containing a single .markdown-body
     */
    fixItUp: function($md) {
      const docroot = $md.attr('data-docroot');
      const extroot = $md.attr('data-extroot');
      // relative images
      $md.find('img').each(function() {
        const $img = $(this);
        const src = $img.attr('src');
        if (src.indexOf('http') !== 0) {
          $img.attr('src', extroot + docroot + src);
        }
      });
      // links
      $md.find('a').each(function() {
        const $a = $(this);
        const href = $a.attr('href');
        // open external URLs in new tab/page
        if (href.indexOf('://') !== -1) {
          $a.attr('target', '_blank');
        }
        // load local markdown files inline
        else if (href.endsWith('.md')) {
          $a.attr('href', '#' + href) // so link displayed by browser isn't bogus
            .on('click', $md, extreadme._load);
        }
        // all other local links get fixed and open in new tab/page
        else {
          $a.attr({
            href: extroot + href,
            target: '_blank'
          })
        }
      });
    },

    /**
     * Load and fix a markdown document.
     * 
     * @param {object} evt Standard event object
     */
    load: function(evt) {
      evt.preventDefault();
      evt.stopPropagation();

      const $md = evt.data;
      $md.addClass('loading');

      CRM.api3('Extension', 'readme', {
        key: $md.attr('data-extkey'),
        readme: $md.attr('data-docroot') + $(evt.target).attr('href').substr(1)
      })
      .then((result) => {
        $md.removeClass('loading');
        if (result.is_error === 0) {
          $md.attr({
            'data-docroot': result.docroot,
            'data-extroot': result.extroot
          })
            .html(extreadme.sanitize(result.html))
            .prop('scrollTop', 0);
  
          extreadme.fixItUp($md);
        }
      });
    },
    _load: null,

    nop: function() {},

    sanitize: function(b64) {
      return DOMPurify.sanitize(marked.parse(atob(b64)));
    },

    /**
     * Set things up.
     */
    init: function() {
      this._load = this.load.bind(this);

      $('.markdown-body[data-docroot]').each(function() {
        const $md = $(this);
        const readme = $md.attr('data-extreadme');
        this.parentElement.classList.add('ext-readme-container');

        if (readme) {
          $md.attr({
            href: '',
            'data-extreadme': '',
            'data-docroot': readme,
          });
          extreadme.load({
            data: $md,
            target: $md[0],
            preventDefault: extreadme.nop,
            stopPropagation: extreadme.nop
          });
        }
        else {
          $md.html(extreadme.sanitize($md.html()));
          extreadme.fixItUp($md);
        }
      });
    }
  }

  extreadme.init();

}(CRM.$));
