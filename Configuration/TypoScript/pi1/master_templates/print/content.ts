plugin.tx_pdfcontroller_pi1 {
    // empty statement for proper comments only
  master_templates {
  }
    // print
  master_templates =
  master_templates {
      // empty statement for proper comments only
    print {
    }
      // content
    print = COA
    print {
        // config, typeNum, CONTENT
      content = PAGE
      content {
        config {
          absRefPrefix          = {$plugin.pdfcontroller.url}
          admPanel              = 0
          disableAllHeaderCode  = {$plugin.pdfcontroller.config.disableAllHeaderCode}
          doctype               = xhtml_strict
          metaCharset           = UTF-8
          xhtml_cleaning        = 0
        }
        typeNum = {$plugin.pdfcontroller.pages.print.typeNum}
          // header, content, footer
        10 = COA
        10 {
            // header
          10 = TEXT
          10 {
            value = {$plugin.pdfcontroller.pages.print.header}
            wrap = <div id="header">|</div>
          }
          10 >
            // hr
          11 = TEXT
          11 {
            value = <hr />
          }
          11 >
            // content
          20 = COA
          20 {
            10 < styles.content.get
            wrap = <div id="content">|</div>
          }
            // hr
          21 = TEXT
          21 {
            value = <hr />
          }
          21 >
            // footer
          30 = COA
          30 {
            wrap = <div id="footer">|</div>
              // url
            10 = TEXT
            10 {
              typolink {
                forceAbsoluteUrl = 1
                forceAbsoluteUrl {
                  //scheme = https
                }
                parameter = {page:uid},0
                parameter {
                  insertData = 1
                }
                //returnLast = url
              }
              noTrimWrap = || |
              stdWrap {
                cObject = TEXT
                cObject {
                  typolink {
                    forceAbsoluteUrl = 1
                    forceAbsoluteUrl {
                      //scheme = https
                    }
                    parameter = {page:uid},0
                    parameter {
                      insertData = 1
                    }
                    returnLast = url
                  }
                }
              }
            }
              // (c)
            20 = TEXT
            20 {
              value = {$plugin.pdfcontroller.pages.print.copyright}
              noTrimWrap = || |
            }
              // date and time
            30 = TEXT
            30 {
              data = date:U
              strftime = {$plugin.pdfcontroller.pages.print.strftime}
            }
          }
          30 >
        }
      }
    }
  }
}