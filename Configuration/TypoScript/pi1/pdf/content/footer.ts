plugin.tx_pdfcontroller_pi1 {
    // empty statement for proper comments only
  pdf {
  }
    // content
  pdf =
  pdf {
      // empty statement for proper comments only
    content {
    }
      // footer
    content =
    content {
        // empty statement for proper comments only
      footer {
      }
        // placeholders: %page% for the current page, %pages% for the number of pages
      footer = TEXT
      footer {
        // placeholders: %page% for the current page, %pages% for the number of pages
        value = page %page% from %pages%
        lang {
          de = Seite %page% von %pages%
          en = page %page% from %pages%
        }
        wrap  = <div style="text-align:right;">|</div>
      }
    }
  }
}